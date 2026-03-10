<?php
// ================================================================
//  ARCHIVO 1: app/Helpers/Auth.php  ← REEMPLAZAR
//  Agregar 'ventas', 'proveedores', 'usuarios' a los permisos
// ================================================================

class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public static function login(array $user): void {
        self::start();
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['rol'];
        $_SESSION['rol_id']     = $user['rol_id'];
        $_SESSION['logged_in']  = true;
    }

    public static function logout(): void {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function check(): bool {
        self::start();
        return !empty($_SESSION['logged_in']);
    }

    public static function require(): void {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/public/login');
            exit;
        }
    }

    public static function requireRole(array $roles): void {
        self::require();
        if (!in_array($_SESSION['user_role'], $roles)) {
            header('Location: ' . APP_URL . '/public/dashboard?error=sin_permiso');
            exit;
        }
    }

    public static function user(): array {
        self::start();
        return [
            'id'     => $_SESSION['user_id']    ?? null,
            'name'   => $_SESSION['user_name']  ?? 'Invitado',
            'email'  => $_SESSION['user_email'] ?? '',
            'role'   => $_SESSION['user_role']  ?? '',
            'rol_id' => $_SESSION['rol_id']     ?? null,
        ];
    }

    public static function role(): string {
        self::start();
        return $_SESSION['user_role'] ?? '';
    }

    public static function can(string $permission): bool {
        $perms = [
            'Administrador' => [
                'pos','ventas','compras','inventario','clientes',
                'productos','catalogo','reportes','usuarios','proveedores'
            ],
            'Cajero'  => ['pos','ventas','clientes'],
            'Almacén' => ['compras','inventario','productos','proveedores'],
        ];
        return in_array($permission, $perms[self::role()] ?? []);
    }
}
