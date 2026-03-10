<?php
// app/Controllers/CatalogoController.php
class CatalogoController {
    private PDO $db;

    public function __construct() {
        Auth::requireRole(['Administrador']);
        $this->db = Database::connect();
    }

    public function index(): void {
        $paises  = $this->db->query("SELECT * FROM pais ORDER BY Nombre")->fetchAll();
        $marcas  = $this->db->query("SELECT ma.*,pa.Nombre as pais FROM marca ma JOIN pais pa ON ma.ID_Pais=pa.ID_Pais ORDER BY ma.Nombre")->fetchAll();
        $modelos = $this->db->query("SELECT mo.*,ma.Nombre as marca FROM modelo mo JOIN marca ma ON mo.ID_Marca=ma.ID_Marca ORDER BY mo.Nombre")->fetchAll();
        $tipos   = $this->db->query("SELECT * FROM tipo_producto ORDER BY Nombre")->fetchAll();
        include ROOT . '/app/Views/catalogo/index.php';
    }

    public function guardarPais(): void {
        $nombre = clean($_POST['nombre'] ?? '');
        if (!$nombre) { Response::error('Nombre requerido'); }
        $this->db->prepare("INSERT INTO pais (Nombre) VALUES (?)")->execute([$nombre]);
        Response::success(['id'=>$this->db->lastInsertId()],'País creado');
    }

    public function guardarMarca(): void {
        $nombre  = clean($_POST['nombre']   ?? '');
        $pais_id = (int)($_POST['pais_id']  ?? 0);
        if (!$nombre || !$pais_id) { Response::error('Datos incompletos'); }
        $this->db->prepare("INSERT INTO marca (Nombre,ID_Pais) VALUES (?,?)")->execute([$nombre,$pais_id]);
        Response::success(['id'=>$this->db->lastInsertId()],'Marca creada');
    }

    public function guardarModelo(): void {
        $nombre   = clean($_POST['nombre']    ?? '');
        $marca_id = (int)($_POST['marca_id']  ?? 0);
        if (!$nombre || !$marca_id) { Response::error('Datos incompletos'); }
        $this->db->prepare("INSERT INTO modelo (Nombre,ID_Marca) VALUES (?,?)")->execute([$nombre,$marca_id]);
        Response::success(['id'=>$this->db->lastInsertId()],'Modelo creado');
    }

    public function guardarTipo(): void {
        $nombre = clean($_POST['nombre'] ?? '');
        if (!$nombre) { Response::error('Nombre requerido'); }
        $this->db->prepare("INSERT INTO tipo_producto (Nombre) VALUES (?)")->execute([$nombre]);
        Response::success(['id'=>$this->db->lastInsertId()],'Tipo creado');
    }
}
