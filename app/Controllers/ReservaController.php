// app/controllers/ReservaController.php
require_once 'app/models/Reserva.php';

class ReservaController {
    public function crear() {
        session_start();
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /login");
            exit;
        }
        $modelo = new Reserva();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'cliente_id' => $_SESSION['id_usuario'],
                'paquete_id' => $_POST['paquete_id'],
                'fecha_viaje' => $_POST['fecha'],
                'cantidad' => $_POST['cantidad']
            ];
            if ($modelo->validarDisponibilidad($datos['paquete_id'], $datos['fecha_viaje'], $datos['cantidad'])) {
                $modelo->registrar($datos);
                header("Location: /reservas/exito");
            } else {
                echo "No hay disponibilidad.";
            }
        }
    }
}



// app/controllers/ReservaController.php (Método adicional)
    public function cancelar($id) {
        session_start();
        $modelo = new Reserva();
        // Verificar que la reserva pertenezca al usuario
        $modelo->cambiarEstado($id, $_SESSION['id_usuario'], 'cancelada');
        header("Location: /reservas/mis_reservas");
    }
