<!-- // app/controllers/CatalogoController.php -->
require_once 'app/models/Paquete.php';

class CatalogoController {
    public function index() {
        $modelo = new Paquete();
        $paquetes = $modelo->obtenerActivos();
        require_once 'app/views/catalogo/index.php';
    }
}

// app/controllers/CatalogoController.php (Método adicional)
    public function buscar() {
        $modelo = new Destino();
        $criterios = [
            'pais' => $_GET['pais'] ?? '',
            'precio_max' => $_GET['precio_max'] ?? null
        ];
        $resultados = $modelo->filtrar($criterios);
        require_once 'app/views/catalogo/buscar.php';
    }
