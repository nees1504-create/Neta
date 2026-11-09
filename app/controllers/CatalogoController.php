// app/controllers/CatalogoController.php
require_once 'app/models/Paquete.php';

class CatalogoController {
    public function index() {
        $modelo = new Paquete();
        $paquetes = $modelo->obtenerActivos();
        require_once 'app/views/catalogo/index.php';
    }
}

// app/models/Paquete.php
class Paquete {
    private $db;
    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=neta_db', 'user', 'pass');
    }
    public function obtenerActivos() {
        $stmt = $this->db->prepare("SELECT * FROM paquetes WHERE estado = 'activo'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<!-- app/views/catalogo/index.php -->
<h2>Catálogo de Paquetes</h2>
<?php foreach ($paquetes as $paquete): ?>
    <div class="paquete">
        <h3><?= htmlspecialchars($paquete['nombre']) ?></h3>
        <p>Precio: <?= htmlspecialchars($paquete['precio']) ?> BOB</p>
    </div>
<?php endforeach; ?>
