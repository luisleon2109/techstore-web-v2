<?php
// app/Controllers/InventarioController.php
class InventarioController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador','Almacén']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $productos = $this->db->query("
            SELECT p.ID_Producto, p.SKU, p.Nombre, p.Precio_Venta, p.Precio_Costo,
                   i.Stock_Actual, i.Stock_Minimo, i.ID_Inventario,
                   t.Nombre as tipo, ma.Nombre as marca, mo.Nombre as modelo
            FROM producto p
            JOIN inventario i ON p.ID_Producto=i.ID_Producto
            JOIN tipo_producto t ON p.ID_Tipo=t.ID_Tipo
            JOIN modelo mo ON p.ID_Modelo=mo.ID_Modelo
            JOIN marca ma ON mo.ID_Marca=ma.ID_Marca
            WHERE p.activo=1 ORDER BY i.Stock_Actual ASC
        ")->fetchAll();

        $stats = [
            'total'     => count($productos),
            'en_stock'  => count(array_filter($productos, fn($p)=>$p['Stock_Actual']>$p['Stock_Minimo'])),
            'stock_bajo'=> count(array_filter($productos, fn($p)=>$p['Stock_Actual']>0&&$p['Stock_Actual']<=$p['Stock_Minimo'])),
            'agotados'  => count(array_filter($productos, fn($p)=>$p['Stock_Actual']==0)),
        ];

        include ROOT . '/app/Views/inventario/index.php';
    }

    public function ajuste(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Response::error('Método no permitido',405); }
        
        $id       = (int)($_POST['producto_id'] ?? 0);
        $cantidad = (int)($_POST['cantidad']    ?? 0);
        $notas    = htmlspecialchars(trim($_POST['notas'] ?? '')); // Corrección de clean()

        if (!$id) { Response::error('Producto inválido'); }

        $inv = $this->db->prepare("SELECT Stock_Actual FROM inventario WHERE ID_Producto=?");
        $inv->execute([$id]);
        $stock = (int)$inv->fetchColumn();
        $nuevo = $stock + $cantidad;

        if ($nuevo < 0) { Response::error('El stock resultante no puede ser negativo'); }

        $this->db->prepare("UPDATE inventario SET Stock_Actual=? WHERE ID_Producto=?")->execute([$nuevo,$id]);
        
        $this->db->prepare("
            INSERT INTO movimiento_inventario (Tipo, Cantidad, Referencia, Notas, ID_Producto, ID_Usuario)
            VALUES ('ajuste', ?, 'AJUSTE-MANUAL', ?, ?, ?)
        ")->execute([abs($cantidad), $notas, $id, $_SESSION['user_id']]);

        Response::success(['nuevo_stock'=>$nuevo], 'Inventario actualizado correctamente');
    }
}