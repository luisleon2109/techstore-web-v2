<?php
// app/Controllers/ReportesController.php
class ReportesController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador']);
        $this->db = Database::connect();
    }

    public function index(): void {
        include ROOT . '/app/Views/reportes/index.php';
    }

    public function ventas(): void {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        $ventas = $this->db->prepare("
            SELECT v.Numero, v.Fecha, v.Total, v.Estado,
                   CONCAT(c.Nombre,' ',c.Apellido) as cliente,
                   mp.Nombre as metodo, f.Numero_Factura
            FROM venta v
            LEFT JOIN cliente c ON v.ID_Cliente=c.ID_Cliente
            LEFT JOIN metodo_pago mp ON v.ID_Metodo=mp.ID_Metodo
            LEFT JOIN factura f ON v.ID_Venta=f.ID_Venta
            WHERE DATE(v.Fecha) BETWEEN ? AND ? AND v.Estado='completada'
            ORDER BY v.Fecha DESC
        ");
        $ventas->execute([$desde, $hasta]);
        $data = $ventas->fetchAll();

        // Solo procesamos PDF si se solicita el formato
        if (($_GET['formato'] ?? '') === 'pdf') {
            $this->exportarPDFVentas($data, $desde, $hasta);
            return;
        }

        // Datos para la vista previa en pantalla
        $total = array_sum(array_column($data,'Total'));
        Response::success(['ventas' => $data, 'total' => $total, 'desde'=>$desde, 'hasta'=>$hasta]);
    }

    public function inventario(): void {
        $data = $this->db->query("
            SELECT p.SKU, p.Nombre, t.Nombre as tipo, ma.Nombre as marca,
                   p.Precio_Costo, p.Precio_Venta,
                   i.Stock_Actual, i.Stock_Minimo,
                   (p.Precio_Venta * i.Stock_Actual) as valor_inventario
            FROM producto p
            JOIN inventario i ON p.ID_Producto=i.ID_Producto
            JOIN tipo_producto t ON p.ID_Tipo=t.ID_Tipo
            JOIN modelo mo ON p.ID_Modelo=mo.ID_Modelo
            JOIN marca ma ON mo.ID_Marca=ma.ID_Marca
            WHERE p.activo=1 ORDER BY i.Stock_Actual ASC
        ")->fetchAll();

        Response::success($data);
    }

    private function exportarPDFVentas(array $data, string $desde, string $hasta): void {
        require_once ROOT . '/vendor/fpdf/fpdf.php';

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        
        // --- ENCABEZADO CORPORATIVO ---
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(26, 43, 95); // Azul oscuro
        $pdf->Cell(0, 12, utf8_decode('TECHSTORE POS - REPORTE DE VENTAS'), 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 6, utf8_decode("Periodo: " . date('d/m/Y', strtotime($desde)) . " al " . date('d/m/Y', strtotime($hasta))), 0, 1, 'C');
        $pdf->Ln(8);

        // --- CABECERA DE LA TABLA ---
        $pdf->SetFillColor(26, 43, 95);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetFont('Arial', 'B', 10);
        
        $w = [35, 45, 80, 45, 40, 32]; 
        // Usamos utf8_decode para el símbolo de grado y tildes
        $header = [utf8_decode('N° Venta'), 'Fecha / Hora', 'Cliente', utf8_decode('Método Pago'), 'Factura', 'Total'];
        
        for($i=0; $i<count($header); $i++) {
            $pdf->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();

        // --- DATOS ---
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFont('Arial', '', 10);
        $total_general = 0;
        $fill = false;

        foreach ($data as $row) {
            $pdf->SetFillColor(248, 249, 252);
            
            $pdf->Cell($w[0], 8, $row['Numero'], 'LRB', 0, 'C', $fill);
            $pdf->Cell($w[1], 8, date('d/m/Y H:i', strtotime($row['Fecha'])), 'RB', 0, 'C', $fill);
            
            // Limpieza de nombres de clientes
            $nombre = utf8_decode($row['cliente'] ?? 'Consumidor Final');
            $pdf->Cell($w[2], 8, " " . substr($nombre, 0, 40), 'RB', 0, 'L', $fill);
            
            $pdf->Cell($w[3], 8, utf8_decode($row['metodo']), 'RB', 0, 'C', $fill);
            $pdf->Cell($w[4], 8, $row['Numero_Factura'] ?? '---', 'RB', 0, 'C', $fill);
            
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell($w[5], 8, 'Bs ' . number_format($row['Total'], 2), 'RB', 0, 'R', $fill);
            $pdf->SetFont('Arial', '', 10);
            
            $pdf->Ln();
            $total_general += $row['Total'];
            $fill = !$fill; // Efecto cebra
        }

        // --- RESUMEN FINAL ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(255, 193, 7); // Amarillo TechStore
        $pdf->SetTextColor(0, 0, 0);
        
        $ancho_etiqueta = array_sum(array_slice($w, 0, 5));
        $pdf->Cell($ancho_etiqueta, 12, 'TOTAL GENERAL RECAUDADO  ', 1, 0, 'R', true);
        $pdf->Cell($w[5], 12, 'Bs ' . number_format($total_general, 2), 1, 1, 'R', true);

        // Pie de página con número de página
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 10, utf8_decode('Página ') . $pdf->PageNo(), 0, 0, 'C');

        $pdf->Output('I', "Reporte_Ventas_" . date('Ymd') . ".pdf");
        exit;
    }
}