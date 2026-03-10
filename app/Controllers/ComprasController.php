<?php
// app/Controllers/ComprasController.php
class ComprasController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador','Almacén']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $proveedores = $this->db->query("
            SELECT p.*, pa.Nombre as pais FROM proveedor p
            LEFT JOIN pais pa ON p.ID_Pais=pa.ID_Pais
            WHERE p.activo=1 ORDER BY p.Nombre
        ")->fetchAll();

        $productos = $this->db->query("
            SELECT p.ID_Producto, p.SKU, p.Nombre, p.Precio_Costo,
                   i.Stock_Actual, t.Nombre as tipo, ma.Nombre as marca
            FROM producto p
            JOIN inventario i ON p.ID_Producto=i.ID_Producto
            JOIN tipo_producto t ON p.ID_Tipo=t.ID_Tipo
            JOIN modelo mo ON p.ID_Modelo=mo.ID_Modelo
            JOIN marca ma ON mo.ID_Marca=ma.ID_Marca
            WHERE p.activo=1 ORDER BY p.Nombre
        ")->fetchAll();

        $historial = $this->db->query("
            SELECT cp.Numero_Orden, cp.Fecha, cp.Total, cp.Estado,
                   pr.Nombre as proveedor
            FROM compra_proveedor cp
            JOIN proveedor pr ON cp.ID_Proveedor=pr.ID_Proveedor
            ORDER BY cp.Fecha DESC LIMIT 20
        ")->fetchAll();

        include ROOT . '/app/Views/compras/index.php';
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Método no permitido', 405);
        }
        $data        = json_decode(file_get_contents('php://input'), true);
        $items       = $data['items']       ?? [];
        $proveedor   = (int)($data['proveedor_id'] ?? 0);
        $descuento   = (float)($data['descuento'] ?? 0);

        if (!$proveedor)  Response::error('Selecciona un proveedor');
        if (empty($items)) Response::error('La orden está vacía');

        try {
            $this->db->beginTransaction();

            $subtotal = 0;
            foreach ($items as $i) $subtotal += $i['costo'] * $i['cantidad'];
            $total = $subtotal - $descuento;

            $count  = $this->db->query("SELECT COUNT(*)+1 FROM compra_proveedor")->fetchColumn();
            $numero = 'OC-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            $emp = $this->db->prepare("SELECT ID_Empleado FROM empleado WHERE ID_Usuario=? LIMIT 1");
            $emp->execute([$_SESSION['user_id']]);
            $emp_id = $emp->fetchColumn() ?: null;

            $stmt = $this->db->prepare("
                INSERT INTO compra_proveedor (Numero_Orden,Subtotal,Descuento,Total,ID_Proveedor,ID_Empleado)
                VALUES (?,?,?,?,?,?)
            ");
            $stmt->execute([$numero, $subtotal, $descuento, $total, $proveedor, $emp_id]);
            $compra_id = $this->db->lastInsertId();

            $det = $this->db->prepare("
                INSERT INTO detalle_compra (Cantidad,Precio_Compra,Subtotal,ID_Compra,ID_Producto)
                VALUES (?,?,?,?,?)
            ");
            $upd = $this->db->prepare("
                UPDATE inventario SET Stock_Actual=Stock_Actual+? WHERE ID_Producto=?
            ");
            $upd_costo = $this->db->prepare("
                UPDATE producto SET Precio_Costo=? WHERE ID_Producto=?
            ");
            $mov = $this->db->prepare("
                INSERT INTO movimiento_inventario (Tipo,Cantidad,Referencia,ID_Producto,ID_Usuario)
                VALUES ('entrada',?,?,?,?)
            ");

            foreach ($items as $item) {
                $det->execute([$item['cantidad'], $item['costo'], $item['costo']*$item['cantidad'], $compra_id, $item['id']]);
                $upd->execute([$item['cantidad'], $item['id']]);
                $upd_costo->execute([$item['costo'], $item['id']]);
                $mov->execute([$item['cantidad'], $numero, $item['id'], $_SESSION['user_id']]);
            }

            $this->db->commit();
            Response::success(['numero' => $numero, 'total' => $total], 'Orden registrada y stock actualizado');

        } catch (Exception $e) {
            $this->db->rollBack();
            Response::error($e->getMessage());
        }
    }
}
