<?php
// app/Models/UsuarioModel.php
class UsuarioModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre as rol
            FROM usuarios u JOIN roles r ON u.rol_id=r.id
            WHERE u.email=? AND u.activo=1 LIMIT 1
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function updateLastLogin(int $id): void {
        $this->db->prepare("UPDATE usuarios SET ultimo_acceso=NOW() WHERE id=?")->execute([$id]);
    }

    public function getAll(): array {
        return $this->db->query("
            SELECT u.id, u.nombre, u.apellido, u.email, u.activo, u.ultimo_acceso,
                   r.nombre as rol
            FROM usuarios u JOIN roles r ON u.rol_id=r.id
            ORDER BY u.nombre
        ")->fetchAll();
    }
}
