// app/controllers/CotizacionController.php
require_once 'app/models/Cotizacion.php';

class CotizacionController {
    public function solicitar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $modelo = new Cotizacion();
            $datos = [
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'paquete_id' => $_POST['paquete_id'],
                'fecha_tentativa' => $_POST['fecha']
            ];
            $modelo->guardarSolicitud($datos);
            header("Location: /cotizacion/exito");
        } else {
            require_once 'app/views/cotizacion/solicitar.php';
        }
    }
}




// app/controllers/CotizacionController.php (Método adicional)
    public function gestionar($id) {
        $modelo = new Cotizacion();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $margen = $_POST['margen'];
            $total = $modelo->calcularTotal($_POST['servicios'], $margen);
            $modelo->actualizarTotal($id, $total);
            header("Location: /cotizaciones/lista");
        }
    }
