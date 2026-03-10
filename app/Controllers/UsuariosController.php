<?php
// app/Controllers/UsuariosController.php  ← ARCHIVO NUEVO
class UsuariosController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $usuarios = $this->db->query("
            SELECT u.id, u.nombre, u.apellido, u.email, u.activo,
                   r.nombre AS rol, r.id AS rol_id,
                   e.Cargo, e.ID_Empleado
            FROM usuarios u
            JOIN roles r ON u.rol_id = r.id
            LEFT JOIN empleado e ON e.ID_Usuario = u.id
            ORDER BY u.nombre
        ")->fetchAll();

        $roles = $this->db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
        include ROOT . '/app/Views/usuarios/index.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') Response::error('Método no permitido', 405);

        $id       = (int)($_POST['id']       ?? 0);
        $nombre   = trim($_POST['nombre']    ?? '');
        $apellido = trim($_POST['apellido']  ?? '');
        $email    = trim($_POST['email']     ?? '');
        $rol_id   = (int)($_POST['rol_id']   ?? 0);
        $cargo    = trim($_POST['cargo']     ?? '');
        $password = $_POST['password']       ?? '';

        if (!$nombre || !$apellido || !$email || !$rol_id)
            Response::error('Nombre, apellido, email y rol son requeridos');

        $dup = $this->db->prepare("SELECT id FROM usuarios WHERE email=? AND id!=?");
        $dup->execute([$email, $id]);
        if ($dup->fetch()) Response::error('Ese email ya está registrado');

        try {
            $this->db->beginTransaction();

            if ($id) {
                // Editar
                if ($password && strlen($password) >= 6) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->prepare("UPDATE usuarios SET nombre=?,apellido=?,email=?,rol_id=?,password_hash=? WHERE id=?")
                             ->execute([$nombre, $apellido, $email, $rol_id, $hash, $id]);
                } else {
                    $this->db->prepare("UPDATE usuarios SET nombre=?,apellido=?,email=?,rol_id=? WHERE id=?")
                             ->execute([$nombre, $apellido, $email, $rol_id, $id]);
                }
                $emp = $this->db->prepare("SELECT ID_Empleado FROM empleado WHERE ID_Usuario=?");
                $emp->execute([$id]);
                if ($emp->fetch())
                    $this->db->prepare("UPDATE empleado SET Nombre=?,Apellido=?,Cargo=? WHERE ID_Usuario=?")
                             ->execute([$nombre, $apellido, $cargo, $id]);
                $this->db->commit();
                Response::success(['id' => $id], 'Usuario actualizado');
            } else {
                // Nuevo
                if (!$password || strlen($password) < 6) Response::error('La contraseña debe tener al menos 6 caracteres');
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $this->db->prepare("INSERT INTO usuarios (nombre,apellido,email,password_hash,rol_id) VALUES (?,?,?,?,?)")
                         ->execute([$nombre, $apellido, $email, $hash, $rol_id]);
                $uid = (int)$this->db->lastInsertId();
                $this->db->prepare("INSERT INTO empleado (Nombre,Apellido,Cargo,Fecha_Contratacion,ID_Usuario) VALUES (?,?,?,CURDATE(),?)")
                         ->execute([$nombre, $apellido, $cargo ?: 'Sin cargo', $uid]);
                $this->db->commit();
                Response::success(['id' => $uid], 'Usuario creado exitosamente');
            }
        } catch (\Exception $e) {
            $this->db->rollBack();
            Response::error($e->getMessage());
        }
    }

    public function toggleActivo(): void {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) Response::error('ID inválido');
        if ($id === (int)$_SESSION['user_id']) Response::error('No puedes desactivarte a ti mismo');

        $st = $this->db->prepare("SELECT activo FROM usuarios WHERE id=?");
        $st->execute([$id]);
        $u = $st->fetch();
        if (!$u) Response::error('Usuario no encontrado');

        $nuevo = $u['activo'] ? 0 : 1;
        $this->db->prepare("UPDATE usuarios SET activo=? WHERE id=?")->execute([$nuevo, $id]);
        Response::success(['activo' => $nuevo], $nuevo ? 'Usuario activado' : 'Usuario desactivado');
    }
}
