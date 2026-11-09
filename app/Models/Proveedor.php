// app/models/Proveedor.php
class Proveedor {
    private $db;
    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=neta_db', 'user', 'pass');
    }
    public function crear($datos) {
        $stmt = $this->db->prepare("INSERT INTO proveedores (nombre_empresa, nombre_contacto) VALUES (:empresa, :contacto)");
        $stmt->execute($datos);
    }
}
