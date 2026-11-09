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



// app/models/Reserva.php (Método adicional)
    public function cambiarEstado($id_reserva, $cliente_id, $nuevo_estado) {
        $stmt = $this->db->prepare("UPDATE reservas SET estado = :estado WHERE id = :id AND cliente_id = :cliente_id");
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id_reserva, ':cliente_id' => $cliente_id]);
    }
