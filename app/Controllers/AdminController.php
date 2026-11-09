// app/controllers/AdminController.php
require_once 'app/models/Destino.php';
require_once 'app/models/Proveedor.php';

class AdminController {
    public function gestionarDestinos() {
        $modelo = new Destino();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'pais' => $_POST['pais'],
                'precio' => $_POST['precio']
            ];
            $modelo->crear($datos);
            header("Location: /admin/destinos");
        }
    }
    
    public function gestionarProveedores() {
        $modelo = new Proveedor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'empresa' => $_POST['empresa'],
                'contacto' => $_POST['contacto']
            ];
            $modelo->crear($datos);
            header("Location: /admin/proveedores");
        }
    }
}
