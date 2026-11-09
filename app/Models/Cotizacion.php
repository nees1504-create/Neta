// app/models/Cotizacion.php
class Cotizacion {
    private $db;
    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=neta_db', 'user', 'pass');
    }
    public function guardarSolicitud($datos) {
        $stmt = $this->db->prepare("INSERT INTO solicitudes_cotizacion (nombre, email, paquete_id, fecha, estado) VALUES (:nombre, :email, :paquete_id, :fecha, 'pendiente')");
        $stmt->execute($datos);
    }
}
