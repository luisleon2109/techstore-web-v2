<?php
// public/index.php — Front Controller  ← REEMPLAZAR
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/database.php';
require_once ROOT . '/app/Helpers/Database.php';
require_once ROOT . '/app/Helpers/Auth.php';
require_once ROOT . '/app/Helpers/Response.php';

Auth::start();

// ── Función helper limpieza (usada en controllers) ──
if (!function_exists('clean')) {
    function clean(string $v): string { return htmlspecialchars(strip_tags(trim($v))); }
}

// ── Parsear ruta ──
$uri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base  = dirname($_SERVER['SCRIPT_NAME']);
$route = trim(str_replace([$base, '/index.php'], '', $uri), '/');
$parts = explode('/', $route);

$module = $parts[0] ?: 'login';
$action = $parts[1] ?? 'index';
$id     = $parts[2] ?? null;

// ── Router ──
$routes = [
    ''            => ['AuthController',       'index'],
    'login'       => ['AuthController',       $action],
    'auth'        => ['AuthController',       $action],
    'logout'      => ['AuthController',       'logout'],
    'dashboard'   => ['DashboardController',  'index'],
    'pos'         => ['PosController',        $action],
    'ventas'      => ['VentasController',     $action],   // ← NUEVO
    'compras'     => ['ComprasController',    $action],
    'inventario'  => ['InventarioController', $action],
    'clientes'    => ['ClientesController',   $action],
    'productos'   => ['ProductosController',  $action],
    'catalogo'    => ['CatalogoController',   $action],
    'reportes'    => ['ReportesController',   $action],
    'usuarios'    => ['UsuariosController',   $action],   // ← NUEVO
    'proveedores' => ['ProveedoresController',$action],   // ← NUEVO
];

$controllerName = $routes[$module][0] ?? null;
$method         = $routes[$module][1] ?? 'index';

if (!$controllerName) {
    http_response_code(404);
    echo "<div style='text-align:center;padding:60px;font-family:sans-serif;'>";
    echo "<h2>Error 404</h2><p>La ruta <b>'{$module}'</b> no existe.</p>";
    echo "<a href='" . APP_URL . "/public/login'>← Volver al inicio</a></div>";
    exit;
}

$controllerFile = ROOT . '/app/Controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "<div style='text-align:center;padding:60px;font-family:sans-serif;'>";
    echo "<h2>Módulo no disponible</h2><p>El controlador <b>'{$controllerName}'</b> no fue encontrado.</p>";
    echo "<a href='" . APP_URL . "/public/login'>← Volver al inicio</a></div>";
    exit;
}

require_once $controllerFile;
$controller = new $controllerName();
if (!method_exists($controller, $method)) $method = 'index';
$controller->$method($id);
