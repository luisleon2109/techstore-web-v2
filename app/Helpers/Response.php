<?php
// app/Helpers/Response.php — Helper para respuestas JSON de la API
class Response {
    public static function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'OK'): void {
        self::json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    public static function error(string $message, int $code = 400): void {
        self::json(['success' => false, 'message' => $message], $code);
    }
}

// ─── Formato de moneda ───
function fmt(float $amount): string {
    return 'Bs ' . number_format($amount, 2, '.', ',');
}

// ─── Sanitizar entrada ───
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)));
}
