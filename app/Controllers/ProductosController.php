<?php
// app/Controllers/ProductosController.php
class ProductosController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador','Almacén']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $productos = $this->db->query("
            SELECT p.*, t.Nombre as tipo, ma.Nombre as marca,
                   mo.Nombre as modelo, i.Stock_Actual, i.Stock_Minimo
            FROM producto p
            JOIN tipo_producto t ON p.ID_Tipo=t.ID_Tipo
            JOIN modelo mo ON p.ID_Modelo=mo.ID_Modelo
            JOIN marca ma ON mo.ID_Marca=ma.ID_Marca
            LEFT JOIN inventario i ON p.ID_Producto=i.ID_Producto
            WHERE p.activo=1 ORDER BY p.Nombre
        ")->fetchAll();

        $tipos   = $this->db->query("SELECT * FROM tipo_producto ORDER BY Nombre")->fetchAll();
        $modelos = $this->db->query("SELECT mo.*,ma.Nombre as marca FROM modelo mo JOIN marca ma ON mo.ID_Marca=ma.ID_Marca ORDER BY mo.Nombre")->fetchAll();

        include ROOT . '/app/Views/productos/index.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Response::error('Método no permitido',405); }
        $d  = $_POST;
        $id = (int)($d['id'] ?? 0);

        $fields = [
            'SKU'            => clean($d['sku']       ?? ''),
            'Nombre'         => clean($d['nombre']    ?? ''),
            'Descripcion'    => clean($d['descripcion']?? ''),
            'Voltaje'        => clean($d['voltaje']   ?? ''),
            'Potencia_Watts' => clean($d['potencia']  ?? ''),
            'Garantia_Meses' => (int)($d['garantia']  ?? 12),
            'Precio_Costo'   => (float)($d['costo']   ?? 0),
            'Precio_Venta'   => (float)($d['precio']  ?? 0),
            'ID_Tipo'        => (int)($d['tipo_id']   ?? 0),
            'ID_Modelo'      => (int)($d['modelo_id'] ?? 0),
        ];

        if (!$fields['Nombre'])    { Response::error('El nombre es requerido'); }
        if (!$fields['ID_Tipo'])   { Response::error('Selecciona el tipo'); }
        if (!$fields['ID_Modelo']) { Response::error('Selecciona el modelo'); }

        if ($id) {
            $sql  = "UPDATE producto SET SKU=?,Nombre=?,Descripcion=?,Voltaje=?,Potencia_Watts=?,Garantia_Meses=?,Precio_Costo=?,Precio_Venta=?,ID_Tipo=?,ID_Modelo=? WHERE ID_Producto=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([...array_values($fields), $id]);
            Response::success(['id'=>$id],'Producto actualizado');
        } else {
            $sql  = "INSERT INTO producto (SKU,Nombre,Descripcion,Voltaje,Potencia_Watts,Garantia_Meses,Precio_Costo,Precio_Venta,ID_Tipo,ID_Modelo) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($fields));
            $pid  = (int)$this->db->lastInsertId();
            // Crear inventario
            $stock = (int)($d['stock_inicial'] ?? 0);
            $this->db->prepare("INSERT INTO inventario (Stock_Actual,Stock_Minimo,ID_Producto) VALUES (?,?,?)")
                     ->execute([$stock, (int)($d['stock_minimo']??5), $pid]);
            if ($stock > 0) {
                $this->db->prepare("INSERT INTO movimiento_inventario (Tipo,Cantidad,Referencia,ID_Producto,ID_Usuario) VALUES ('entrada',?,'STOCK-INICIAL',?,?)")
                         ->execute([$stock,$pid,$_SESSION['user_id']]);
            }
            Response::success(['id'=>$pid],'Producto creado');
        }
    }

    public function eliminar(): void {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { Response::error('ID inválido'); }
        $this->db->prepare("UPDATE producto SET activo=0 WHERE ID_Producto=?")->execute([$id]);
        Response::success(null,'Producto eliminado');
    }
}
