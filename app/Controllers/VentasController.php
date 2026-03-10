<?php
// app/Controllers/VentasController.php  ← ARCHIVO NUEVO
class VentasController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador','Cajero']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        $st = $this->db->prepare("
            SELECT v.ID_Venta, v.Numero, v.Fecha, v.Subtotal, v.Descuento,
                   v.IVA, v.Total, v.Estado,
                   COALESCE(CONCAT(c.Nombre,' ',c.Apellido), 'Consumidor Final') AS cliente,
                   mp.Nombre AS metodo,
                   f.Numero_Factura, f.ID_Factura,
                   CONCAT(u.nombre,' ',u.apellido) AS cajero
            FROM venta v
            LEFT JOIN cliente c      ON v.ID_Cliente  = c.ID_Cliente
            LEFT JOIN metodo_pago mp ON v.ID_Metodo   = mp.ID_Metodo
            LEFT JOIN factura f      ON v.ID_Venta    = f.ID_Venta
            LEFT JOIN empleado e     ON v.ID_Empleado = e.ID_Empleado
            LEFT JOIN usuarios u     ON e.ID_Usuario  = u.id
            WHERE DATE(v.Fecha) BETWEEN ? AND ?
            ORDER BY v.Fecha DESC
        ");
        $st->execute([$desde, $hasta]);
        $ventas = $st->fetchAll();

        $totales = [
            'count'       => count($ventas),
            'sum'         => array_sum(array_column($ventas, 'Total')),
            'completadas' => count(array_filter($ventas, fn($v) => $v['Estado'] === 'completada')),
            'anuladas'    => count(array_filter($ventas, fn($v) => $v['Estado'] === 'anulada')),
        ];

        include ROOT . '/app/Views/ventas/index.php';
    }

    // GET /ventas/detalle?id=X  →  JSON con items para reimprimir ticket
    public function detalle(): void {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) Response::error('ID inválido');

        $st = $this->db->prepare("
            SELECT v.ID_Venta, v.Numero, v.Fecha, v.Subtotal, v.Descuento, v.IVA, v.Total, v.Estado,
                   COALESCE(CONCAT(c.Nombre,' ',c.Apellido),'Consumidor Final') AS cliente_nombre,
                   c.NIT AS cliente_nit, c.CI AS cliente_ci,
                   COALESCE(c.Razon_Social,'CONSUMIDOR FINAL') AS razon_social,
                   mp.Nombre AS metodo,
                   f.Numero_Factura, f.ID_Factura,
                   CONCAT(u.nombre,' ',u.apellido) AS cajero
            FROM venta v
            LEFT JOIN cliente c      ON v.ID_Cliente  = c.ID_Cliente
            LEFT JOIN metodo_pago mp ON v.ID_Metodo   = mp.ID_Metodo
            LEFT JOIN factura f      ON v.ID_Venta    = f.ID_Venta
            LEFT JOIN empleado e     ON v.ID_Empleado = e.ID_Empleado
            LEFT JOIN usuarios u     ON e.ID_Usuario  = u.id
            WHERE v.ID_Venta = ?
        ");
        $st->execute([$id]);
        $v = $st->fetch();
        if (!$v) Response::error('Venta no encontrada', 404);

        $its = $this->db->prepare("
            SELECT dv.Cantidad, dv.Precio_Unitario AS precio, dv.Subtotal, p.Nombre, p.SKU
            FROM detalle_venta dv JOIN producto p ON dv.ID_Producto=p.ID_Producto
            WHERE dv.ID_Venta=?
        ");
        $its->execute([$id]);
        $v['items'] = $its->fetchAll();

        // Formato fecha para ticket
        $v['fecha'] = date('d/m/Y', strtotime($v['Fecha']));
        $v['hora']  = date('H:i',  strtotime($v['Fecha']));
        $v['iva_pct'] = (IVA_RATE * 100) . '%';

        // Config negocio
        $cfg = [];
        try {
            foreach ($this->db->query("SELECT clave,valor FROM configuracion")->fetchAll() as $r)
                $cfg[$r['clave']] = $r['valor'];
        } catch (\Exception $e) {}
        $v['negocio'] = $cfg;

        Response::success($v);
    }

    // POST /ventas/anular  (solo Admin)
    public function anular(): void {
        Auth::requireRole(['Administrador']);
        $id     = (int)($_POST['id']     ?? 0);
        $motivo = trim($_POST['motivo']  ?? 'Sin motivo');
        if (!$id) Response::error('ID inválido');

        $st = $this->db->prepare("SELECT Estado, Numero FROM venta WHERE ID_Venta=?");
        $st->execute([$id]);
        $venta = $st->fetch();
        if (!$venta)                          Response::error('Venta no encontrada');
        if ($venta['Estado'] === 'anulada')   Response::error('La venta ya estaba anulada');

        try {
            $this->db->beginTransaction();

            $its = $this->db->prepare("SELECT ID_Producto, Cantidad FROM detalle_venta WHERE ID_Venta=?");
            $its->execute([$id]);
            $upd = $this->db->prepare("UPDATE inventario SET Stock_Actual=Stock_Actual+? WHERE ID_Producto=?");
            $mov = $this->db->prepare("INSERT INTO movimiento_inventario (Tipo,Cantidad,Referencia,Notas,ID_Producto,ID_Usuario) VALUES ('entrada',?,?,?,?,?)");

            foreach ($its->fetchAll() as $item) {
                $upd->execute([$item['Cantidad'], $item['ID_Producto']]);
                $mov->execute([$item['Cantidad'], 'ANULACION-'.$venta['Numero'], $motivo, $item['ID_Producto'], $_SESSION['user_id']]);
            }

            $this->db->prepare("UPDATE venta SET Estado='anulada' WHERE ID_Venta=?")->execute([$id]);
            $this->db->commit();
            Response::success(null, 'Venta anulada y stock restaurado');
        } catch (\Exception $e) {
            $this->db->rollBack();
            Response::error($e->getMessage());
        }
    }
}
