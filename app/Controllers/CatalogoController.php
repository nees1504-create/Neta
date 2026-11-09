<!-- // app/controllers/CatalogoController.php -->
require_once 'app/models/Paquete.php';

class CatalogoController {
    public function index() {
        $modelo = new Paquete();
        $paquetes = $modelo->obtenerActivos();
        require_once 'app/views/catalogo/index.php';
    }
}
