<?php
namespace App\Controllers;
use App\Core\Controller; use App\Core\Database;
class AuthController extends Controller { public function login(): void { if(!empty($_SESSION['user'])) \redirect(($_SESSION['user']['role']??'')==='Cliente'?'client-area':'dashboard'); $error=''; if($_SERVER['REQUEST_METHOD']==='POST'){ \csrf_check(); $email=trim($_POST['email']??'');$pass=$_POST['password']??''; try{$s=Database::connection()->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email=? AND status=1 LIMIT 1');$s->execute([$email]);$u=$s->fetch(); if($u && password_verify($pass,$u['password_hash'])){session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];\redirect(($u['role']==='Cliente')?'client-area':'dashboard');} $error='Correo o contraseña incorrectos.';}catch(\PDOException $ex){$error='No se pudo conectar a la base de datos. Importa database/neta_sgi.sql y revisa config/config.php.';}} $this->render('auth/login',['error'=>$error]); } }


/**
 * CU-14 Registrar cliente (Agente de ventas): clientes captados por teléfono, mensajería u oficina.
 * Flujo del documento: validar datos → ¿existe correo o teléfono? → cargar ficha existente / crear registro.
 */
class AgentClientController extends Controller {
    private const CHANNELS = ['Teléfono', 'Mensajería', 'Oficina'];

    public function form(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { $this->store(); return; }
        $this->render('clients/register', ['channels' => self::CHANNELS]);
    }

    private function store(): void {
        \csrf_check();
        $db = Database::connection();
        try {
            $name = trim($_POST['name'] ?? ''); $email = strtolower(trim($_POST['email'] ?? '')); $phone = trim($_POST['phone'] ?? '');
            $country = trim($_POST['country'] ?? '') ?: 'Bolivia'; $pref = trim($_POST['preferences'] ?? '');
            $channel = in_array($_POST['channel'] ?? '', self::CHANNELS, true) ? $_POST['channel'] : 'Oficina';
            if ($name === '') throw new \RuntimeException('El nombre completo es obligatorio.');
            if ($email === '' && $phone === '') throw new \RuntimeException('Indica al menos un correo o un teléfono de contacto.');
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new \RuntimeException('El formato del correo electrónico no es válido.');
            if ($phone !== '' && !preg_match('/^\+?[0-9 ()\-]{6,20}$/', $phone)) throw new \RuntimeException('El teléfono solo puede contener números, espacios, + y guiones (6 a 20 caracteres).');

            // ¿Existe correo o teléfono? → se carga la ficha existente en lugar de duplicarla.
            $s = $db->prepare("SELECT id,name FROM clients WHERE (? <> '' AND email=?) OR (? <> '' AND phone=?) LIMIT 1");
            $s->execute([$email, $email, $phone, $phone]);
            if ($existing = $s->fetch()) {
                \flash('success', 'El cliente «' . $existing['name'] . '» ya estaba registrado. Se muestra su ficha existente.');
                \redirect('clients&q=' . urlencode($email !== '' ? $email : $phone));
            }
            $db->beginTransaction();
            $s = $db->prepare('INSERT INTO clients(name,email,phone,country,preferences) VALUES(?,?,?,?,?)');
            $s->execute([$name, $email !== '' ? $email : null, $phone !== '' ? $phone : null, $country, mb_substr($pref, 0, 255)]);
            $id = (int)$db->lastInsertId();
            $s = $db->prepare('INSERT INTO client_origin(client_id,channel,registered_by) VALUES(?,?,?)');
            $s->execute([$id, $channel, $_SESSION['user']['id']]);
            $db->commit();
            \flash('success', 'Cliente registrado correctamente (canal: ' . $channel . ').');
            \redirect('clients&q=' . urlencode($email !== '' ? $email : $phone));
        } catch (\RuntimeException $e) { if ($db->inTransaction()) $db->rollBack(); \flash('error', $e->getMessage()); }
        catch (\PDOException $e) { if ($db->inTransaction()) $db->rollBack(); \flash('error', 'No se pudo registrar al cliente. Verifica que el correo no esté duplicado.'); }
        \redirect('client-new');
    }
}
