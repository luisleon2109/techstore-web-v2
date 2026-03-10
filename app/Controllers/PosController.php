<?php
// app/Controllers/PosController.php  ← REEMPLAZAR el archivo existente
class PosController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador','Cajero']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $productos = $this->db->query("
            SELECT p.ID_Producto, p.SKU, p.Nombre, p.Precio_Venta,
                   i.Stock_Actual, t.Nombre as tipo,
                   ma.Nombre as marca, mo.Nombre as modelo
            FROM producto p
            JOIN inventario i ON p.ID_Producto=i.ID_Producto
            JOIN tipo_producto t ON p.ID_Tipo=t.ID_Tipo
            JOIN modelo mo ON p.ID_Modelo=mo.ID_Modelo
            JOIN marca ma ON mo.ID_Marca=ma.ID_Marca
            WHERE p.activo=1
            ORDER BY p.Nombre
        ")->fetchAll();

        $clientes = $this->db->query("
            SELECT ID_Cliente, Nombre, Apellido, CI, NIT, Razon_Social
            FROM cliente WHERE activo=1 ORDER BY Nombre
        ")->fetchAll();

        $metodos = $this->db->query("SELECT * FROM metodo_pago ORDER BY ID_Metodo")->fetchAll();

        // Config negocio para inyectar en JS
        $cfg = [];
        try {
            $rows = $this->db->query("SELECT clave, valor FROM configuracion")->fetchAll();
            foreach ($rows as $r) $cfg[$r['clave']] = $r['valor'];
        } catch (\Exception $e) { /* tabla aún no creada */ }

        include ROOT . '/app/Views/pos/index.php';
    }

    // ── POST /pos/vender ────────────────────────────────────────
    public function vender(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Response::error('Método no permitido', 405);

        $data       = json_decode(file_get_contents('php://input'), true);
        $items      = $data['items']     ?? [];
        $metodo_id  = (int)($data['metodo_id']  ?? 1);
        $cliente_id = (!empty($data['cliente_id']) && $data['cliente_id'] != 0)
                        ? (int)$data['cliente_id'] : null;
        $descuento  = (float)($data['descuento'] ?? 0);

        if (empty($items)) Response::error('El carrito está vacío');

        try {
            $this->db->beginTransaction();

            // Totales
            $subtotal = 0;
            foreach ($items as $item) $subtotal += $item['precio'] * $item['cantidad'];
            $iva   = round($subtotal * IVA_RATE, 2);
            $total = round($subtotal - $descuento + $iva, 2);

            // Número de venta
            $count  = $this->db->query("SELECT COUNT(*)+1 FROM venta")->fetchColumn();
            $numero = 'VNT-' . str_pad($count, 6, '0', STR_PAD_LEFT);

            // Empleado
            $emp = $this->db->prepare("SELECT ID_Empleado FROM empleado WHERE ID_Usuario=? LIMIT 1");
            $emp->execute([$_SESSION['user_id']]);
            $emp_id = $emp->fetchColumn() ?: null;

            // Venta
            $this->db->prepare("
                INSERT INTO venta (Numero,Subtotal,Descuento,IVA,Total,ID_Cliente,ID_Empleado,ID_Metodo)
                VALUES (?,?,?,?,?,?,?,?)
            ")->execute([$numero, $subtotal, $descuento, $iva, $total, $cliente_id, $emp_id, $metodo_id]);
            $venta_id = (int)$this->db->lastInsertId();

            // Detalles + stock + nombres para ticket
            $det = $this->db->prepare("INSERT INTO detalle_venta (Cantidad,Precio_Unitario,Subtotal,ID_Venta,ID_Producto) VALUES (?,?,?,?,?)");
            $upd = $this->db->prepare("UPDATE inventario SET Stock_Actual=Stock_Actual-? WHERE ID_Producto=?");
            $mov = $this->db->prepare("INSERT INTO movimiento_inventario (Tipo,Cantidad,Referencia,ID_Producto,ID_Usuario) VALUES ('salida',?,?,?,?)");
            $gnm = $this->db->prepare("SELECT Nombre FROM producto WHERE ID_Producto=?");

            $ticket_items = [];
            foreach ($items as $item) {
                $st = $this->db->prepare("SELECT Stock_Actual FROM inventario WHERE ID_Producto=?");
                $st->execute([$item['id']]);
                if ((int)$st->fetchColumn() < $item['cantidad'])
                    throw new \Exception("Stock insuficiente para producto ID {$item['id']}");

                $sub = round($item['precio'] * $item['cantidad'], 2);
                $det->execute([$item['cantidad'], $item['precio'], $sub, $venta_id, $item['id']]);
                $upd->execute([$item['cantidad'], $item['id']]);
                $mov->execute([$item['cantidad'], $numero, $item['id'], $_SESSION['user_id']]);

                $gnm->execute([$item['id']]);
                $ticket_items[] = [
                    'nombre'   => $gnm->fetchColumn(),
                    'cantidad' => (int)$item['cantidad'],
                    'precio'   => (float)$item['precio'],
                    'subtotal' => $sub,
                ];
            }

            // Cliente y factura
            $nit = '0'; $razon = 'CONSUMIDOR FINAL'; $cli_nombre = 'Consumidor Final'; $cli_ci = '';
            if ($cliente_id) {
                $cli = $this->db->prepare("SELECT Nombre,Apellido,CI,NIT,Razon_Social FROM cliente WHERE ID_Cliente=?");
                $cli->execute([$cliente_id]);
                $c         = $cli->fetch();
                $nit       = $c['NIT']         ?? '0';
                $razon     = $c['Razon_Social'] ?? 'CONSUMIDOR FINAL';
                $cli_nombre = trim(($c['Nombre'] ?? '') . ' ' . ($c['Apellido'] ?? ''));
                $cli_ci    = $c['CI'] ?? '';
            }

            $fcount  = $this->db->query("SELECT COUNT(*)+1 FROM factura")->fetchColumn();
            $num_fac = 'FAC-' . str_pad($fcount, 6, '0', STR_PAD_LEFT);
            $this->db->prepare("INSERT INTO factura (Numero_Factura,NIT,Razon_Social,Monto_Total,ID_Venta) VALUES (?,?,?,?,?)")
                     ->execute([$num_fac, $nit, $razon, $total, $venta_id]);

            // Método de pago
            $mp = $this->db->prepare("SELECT Nombre FROM metodo_pago WHERE ID_Metodo=?");
            $mp->execute([$metodo_id]);
            $metodo_nombre = $mp->fetchColumn();

            // Config negocio
            $cfg = [];
            try {
                $rows = $this->db->query("SELECT clave,valor FROM configuracion")->fetchAll();
                foreach ($rows as $r) $cfg[$r['clave']] = $r['valor'];
            } catch (\Exception $e) {}

            $this->db->commit();

            Response::success([
                // Datos de venta
                'venta_id'       => $venta_id,
                'numero'         => $numero,
                'numero_factura' => $num_fac,
                'fecha'          => date('d/m/Y'),
                'hora'           => date('H:i'),
                // Totales
                'subtotal'       => $subtotal,
                'descuento'      => $descuento,
                'iva'            => $iva,
                'iva_pct'        => (IVA_RATE * 100) . '%',
                'total'          => $total,
                // Pago y cajero
                'metodo'         => $metodo_nombre,
                'cajero'         => $_SESSION['user_name'] ?? '',
                // Cliente
                'cliente_nombre' => $cli_nombre,
                'cliente_nit'    => $nit,
                'cliente_ci'     => $cli_ci,
                'razon_social'   => $razon,
                // Items para ticket
                'items'          => $ticket_items,
                // Datos del negocio
                'negocio'        => $cfg,
            ], 'Venta registrada');

        } catch (\Exception $e) {
            $this->db->rollBack();
            Response::error($e->getMessage());
        }
    }
}
