<?php
// config/database.php — Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'techstore');
define('DB_USER', 'root');
define('DB_PASS', '');       // En XAMPP por defecto está vacío
define('DB_CHARSET', 'utf8mb4');

// config/app.php se incluye desde aquí también
define('APP_NAME',    'TechStore POS');
define('APP_VERSION', '2.0');
define('APP_URL',     'http://localhost/techstore');
define('IVA_RATE',    0.13);   // 13% IVA Bolivia
define('CURRENCY',    'Bs');
