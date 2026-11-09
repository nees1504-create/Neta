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
