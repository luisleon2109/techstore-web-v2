<?php
// app/Controllers/AuthController.php
require_once ROOT . '/app/Models/UsuarioModel.php';

class AuthController {
    private UsuarioModel $model;

    public function __construct() {
        $this->model = new UsuarioModel();
    }

    public function index(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->login();
            return;
        }

        if (Auth::check()) {
            header('Location: ' . APP_URL . '/public/dashboard');
            exit;
        }
        include ROOT . '/app/Views/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $user = $this->model->findByEmail($email);
        
        // CORRECCIÓN: Aceptamos contraseñas encriptadas Y contraseñas en texto plano
        $isPasswordValid = false;
        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                $isPasswordValid = true; // Entra si está encriptada
            } elseif ($password === $user['password_hash']) {
                $isPasswordValid = true; // Entra si es texto normal (como admin123)
            }
        }
        
        if (!$isPasswordValid) {
            $error = 'Credenciales incorrectas. Intenta de nuevo.';
            include ROOT . '/app/Views/auth/login.php';
            return;
        }
        
        if (!$user['activo']) {
            $error = 'Tu cuenta está desactivada. Contacta al administrador.';
            include ROOT . '/app/Views/auth/login.php';
            return;
        }
        
        $this->model->updateLastLogin($user['id']);
        Auth::login($user);
        
        header('Location: ' . APP_URL . '/public/dashboard');
        exit;
    }

    public function logout(): void {
        Auth::logout();
        header('Location: ' . APP_URL . '/public/login');
        exit;
    }
}