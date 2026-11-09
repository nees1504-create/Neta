// app/models/Destino.php
class Destino {
    private $db;
    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=neta_db', 'user', 'pass');
    }
    public function filtrar($criterios) {
        $sql = "SELECT * FROM destinos WHERE estado = 'activo'";
        $params = [];
        if (!empty($criterios['pais'])) {
            $sql .= " AND pais = :pais";
            $params[':pais'] = $criterios['pais'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
