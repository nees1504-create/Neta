// app/models/Reserva.php
class Reserva {
    private $db;
    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=neta_db', 'user', 'pass');
    }
    public function validarDisponibilidad($paquete_id, $fecha, $cantidad) {
        // Lógica simplificada de validación de cupos
        return true; 
    }
    public function registrar($datos) {
        $stmt = $this->db->prepare("INSERT INTO reservas (cliente_id, paquete_id, fecha_viaje, cantidad, estado) VALUES (:cliente_id, :paquete_id, :fecha_viaje, :cantidad, 'pendiente')");
        $stmt->execute($datos);
    }
}
