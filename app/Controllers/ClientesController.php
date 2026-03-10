<?php
// app/Controllers/ClientesController.php
class ClientesController {
    private PDO $db;

    public function __construct() {
        Auth::require();
        $this->db = Database::connect();
    }

    public function index(): void {
        $clientes = $this->db->query("
            SELECT c.*,
                   (SELECT COUNT(*) FROM venta v WHERE v.ID_Cliente=c.ID_Cliente AND v.Estado='completada') as total_ventas,
                   (SELECT COALESCE(SUM(v.Total),0) FROM venta v WHERE v.ID_Cliente=c.ID_Cliente AND v.Estado='completada') as monto_total
            FROM cliente c WHERE c.activo=1 ORDER BY c.Nombre
        ")->fetchAll();
        include ROOT . '/app/Views/clientes/index.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { Response::error('Método no permitido',405); }
        $d = $_POST;
        $id = (int)($d['id'] ?? 0);

        $fields = [
            'Nombre'       => clean($d['nombre']   ?? ''),
            'Apellido'     => clean($d['apellido']  ?? ''),
            'CI'           => clean($d['ci']        ?? ''),
            'Telefono'     => clean($d['telefono']  ?? ''),
            'Email'        => clean($d['email']     ?? ''),
            'NIT'          => clean($d['nit']       ?? ''),
            'Razon_Social' => clean($d['razon']     ?? ''),
        ];
        if (!$fields['Nombre']) { Response::error('El nombre es requerido'); }

        if ($id) {
            $sql = "UPDATE cliente SET Nombre=?,Apellido=?,CI=?,Telefono=?,Email=?,NIT=?,Razon_Social=? WHERE ID_Cliente=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([...array_values($fields), $id]);
            Response::success(['id' => $id], 'Cliente actualizado');
        } else {
            $sql = "INSERT INTO cliente (Nombre,Apellido,CI,Telefono,Email,NIT,Razon_Social) VALUES (?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($fields));
            Response::success(['id' => $this->db->lastInsertId()], 'Cliente creado');
        }
    }

    public function eliminar(): void {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { Response::error('ID inválido'); }
        $this->db->prepare("UPDATE cliente SET activo=0 WHERE ID_Cliente=?")->execute([$id]);
        Response::success(null, 'Cliente eliminado');
    }

    public function historial(): void {
        $id = (int)($_GET['id'] ?? 0);
        $ventas = $this->db->prepare("
            SELECT v.Numero, v.Fecha, v.Total, v.Estado, mp.Nombre as metodo,
                   f.Numero_Factura
            FROM venta v
            LEFT JOIN metodo_pago mp ON v.ID_Metodo=mp.ID_Metodo
            LEFT JOIN factura f ON v.ID_Venta=f.ID_Venta
            WHERE v.ID_Cliente=? ORDER BY v.Fecha DESC LIMIT 20
        ");
        $ventas->execute([$id]);
        Response::success($ventas->fetchAll());
    }
}
