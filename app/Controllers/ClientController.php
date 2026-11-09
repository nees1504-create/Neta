<?php
namespace App\Controllers;
use App\Core\Controller; use App\Models\Client;
class ClientController extends Controller { public function index(): void { $m=new Client(); $this->render('clients/index',['clients'=>$m->all(trim($_GET['q']??''))]); } public function save(): void { \csrf_check(); try{(new Client())->save(['id'=>$_POST['id']??null,'name'=>trim($_POST['name']??''),'email'=>trim($_POST['email']??''),'phone'=>trim($_POST['phone']??''),'country'=>trim($_POST['country']??''),'preferences'=>trim($_POST['preferences']??'')]);\flash('success','Cliente guardado correctamente.');}catch(\Throwable $e){\flash('error','No se pudo guardar. Verifica los datos y que el correo no esté duplicado.');}\redirect('clients');} public function delete(): void { \csrf_check(); $this->requireAdmin(); try{(new Client())->delete((int)($_POST['id']??0));\flash('success','Cliente eliminado.');}catch(\Throwable $e){\flash('error','No se puede eliminar: el cliente puede tener reservas asociadas.');}\redirect('clients');} }




#---------------------------
// app/controllers/ClienteController.php
require_once 'app/models/Cliente.php';

class ClienteController {
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $modelo = new Cliente();
            if ($modelo->buscarPorCI($_POST['ci'])) {
                echo "El cliente ya existe.";
            } else {
                $datos = [
                    'nombre' => $_POST['nombre'],
                    'ci' => $_POST['ci'],
                    'telefono' => $_POST['telefono'],
                    'email' => $_POST['email']
                ];
                $modelo->guardar($datos);
                header("Location: /clientes/lista");
            }
        } else {
            require_once 'app/views/cliente/formulario.php';
        }
    }
}
