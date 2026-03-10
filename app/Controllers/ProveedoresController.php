<?php
// app/Controllers/ProveedoresController.php  ← ARCHIVO NUEVO
class ProveedoresController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador','Almacén']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $proveedores = $this->db->query("
            SELECT p.*, pa.Nombre AS pais_nombre,
                   (SELECT COUNT(*) FROM compra_proveedor cp WHERE cp.ID_Proveedor=p.ID_Proveedor) AS total_compras,
                   (SELECT COALESCE(SUM(cp.Total),0) FROM compra_proveedor cp
                    WHERE cp.ID_Proveedor=p.ID_Proveedor AND cp.Estado='recibida') AS monto_total
            FROM proveedor p
            LEFT JOIN pais pa ON p.ID_Pais=pa.ID_Pais
            WHERE p.activo=1
            ORDER BY p.Nombre
        ")->fetchAll();

        $paises = $this->db->query("SELECT * FROM pais ORDER BY Nombre")->fetchAll();
        include ROOT . '/app/Views/proveedores/index.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Response::error('Método no permitido', 405);

        $id     = (int)($_POST['id']              ?? 0);
        $nombre = trim($_POST['nombre']           ?? '');
        $tel    = trim($_POST['telefono']         ?? '');
        $email  = trim($_POST['email']            ?? '');
        $dir    = trim($_POST['direccion']        ?? '');
        $nit    = trim($_POST['nit']              ?? '');
        $cond   = trim($_POST['condicion_pago']   ?? '');
        $pais   = (int)($_POST['pais_id']         ?? 0) ?: null;

        if (!$nombre) Response::error('El nombre del proveedor es requerido');

        if ($id) {
            $this->db->prepare("UPDATE proveedor SET Nombre=?,Telefono=?,Email=?,Direccion=?,NIT=?,Condicion_Pago=?,ID_Pais=? WHERE ID_Proveedor=?")
                     ->execute([$nombre, $tel, $email, $dir, $nit, $cond, $pais, $id]);
            Response::success(['id' => $id], 'Proveedor actualizado');
        } else {
            $this->db->prepare("INSERT INTO proveedor (Nombre,Telefono,Email,Direccion,NIT,Condicion_Pago,ID_Pais) VALUES (?,?,?,?,?,?,?)")
                     ->execute([$nombre, $tel, $email, $dir, $nit, $cond, $pais]);
            Response::success(['id' => (int)$this->db->lastInsertId()], 'Proveedor creado');
        }
    }

    public function eliminar(): void {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) Response::error('ID inválido');
        $this->db->prepare("UPDATE proveedor SET activo=0 WHERE ID_Proveedor=?")->execute([$id]);
        Response::success(null, 'Proveedor eliminado');
    }

    public function historial(): void {
        $id = (int)($_GET['id'] ?? 0);
        $st = $this->db->prepare("
            SELECT Numero_Orden, DATE_FORMAT(Fecha,'%d/%m/%Y') AS fecha, Total, Estado
            FROM compra_proveedor WHERE ID_Proveedor=? ORDER BY Fecha DESC LIMIT 15
        ");
        $st->execute([$id]);
        Response::success($st->fetchAll());
    }
}
