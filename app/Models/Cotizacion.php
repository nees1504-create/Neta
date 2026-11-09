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




// app/models/Cotizacion.php (Método adicional)
    public function calcularTotal($servicios, $margen) {
        $costo_base = array_sum(array_column($servicios, 'precio'));
        return $costo_base + ($costo_base * ($margen / 100));
    }
    public function actualizarTotal($id, $total) {
        $stmt = $this->db->prepare("UPDATE cotizaciones SET total = :total, estado = 'enviada' WHERE id = :id");
        $stmt->execute([':total' => $total, ':id' => $id]);
    }
