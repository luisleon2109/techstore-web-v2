<?php
// app/Controllers/DashboardController.php
class DashboardController {
    public function index(): void {
        Auth::require();
        $db = Database::connect();

        $stats = [
            'ventas_hoy'      => $db->query("SELECT COALESCE(SUM(Total),0) FROM venta WHERE DATE(Fecha)=CURDATE() AND Estado='completada'")->fetchColumn(),
            'ventas_mes'      => $db->query("SELECT COALESCE(SUM(Total),0) FROM venta WHERE MONTH(Fecha)=MONTH(NOW()) AND YEAR(Fecha)=YEAR(NOW()) AND Estado='completada'")->fetchColumn(),
            'num_ventas_hoy'  => $db->query("SELECT COUNT(*) FROM venta WHERE DATE(Fecha)=CURDATE() AND Estado='completada'")->fetchColumn(),
            'productos_total' => $db->query("SELECT COUNT(*) FROM producto WHERE activo=1")->fetchColumn(),
            'stock_bajo'      => $db->query("SELECT COUNT(*) FROM inventario i JOIN producto p ON i.ID_Producto=p.ID_Producto WHERE i.Stock_Actual<=i.Stock_Minimo AND i.Stock_Actual>0 AND p.activo=1")->fetchColumn(),
            'agotados'        => $db->query("SELECT COUNT(*) FROM inventario WHERE Stock_Actual=0")->fetchColumn(),
            'clientes'        => $db->query("SELECT COUNT(*) FROM cliente WHERE activo=1")->fetchColumn(),
            'compras_mes'     => $db->query("SELECT COALESCE(SUM(Total),0) FROM compra_proveedor WHERE MONTH(Fecha)=MONTH(NOW()) AND YEAR(Fecha)=YEAR(NOW()) AND Estado='recibida'")->fetchColumn(),
        ];

        // Últimas 5 ventas
        $ultimas_ventas = $db->query("
            SELECT v.Numero, v.Fecha, v.Total, v.Estado,
                   CONCAT(c.Nombre,' ',c.Apellido) as cliente, mp.Nombre as metodo
            FROM venta v
            LEFT JOIN cliente c ON v.ID_Cliente=c.ID_Cliente
            LEFT JOIN metodo_pago mp ON v.ID_Metodo=mp.ID_Metodo
            ORDER BY v.Fecha DESC LIMIT 5
        ")->fetchAll();

        // Alertas stock bajo
        $alertas = $db->query("
            SELECT p.Nombre, p.SKU, i.Stock_Actual, i.Stock_Minimo
            FROM inventario i JOIN producto p ON i.ID_Producto=p.ID_Producto
            WHERE i.Stock_Actual<=i.Stock_Minimo AND p.activo=1
            ORDER BY i.Stock_Actual ASC LIMIT 6
        ")->fetchAll();

        include ROOT . '/app/Views/dashboard.php';
    }
}
