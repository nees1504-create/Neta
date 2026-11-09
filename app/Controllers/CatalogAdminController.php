<?php
namespace App\Controllers;
use App\Core\Controller; use App\Core\Database; use App\Models\Package; use App\Models\Provider; use App\Models\PackageService;

/** CU-21 Gestionar catálogo y proveedores (Administrador): destinos/paquetes, servicios incluidos y proveedores. */
class CatalogAdminController extends Controller {
    private const CATEGORIES = ['Tour nacional', 'Paquete internacional', 'Boletos aéreos', 'Crucero', 'Seguro de viaje'];

    private function guard(): void { $this->requireAdmin(); }

    /** Paquetes/destinos y sus servicios incluidos. */
    public function index(): void {
        $this->guard();
        $pkgModel = new Package(); $selected = (int)($_GET['package'] ?? 0);
        $this->render('catalog_admin/index', [
            'packages' => $pkgModel->all(), 'categories' => self::CATEGORIES, 'selected' => $selected,
            'services' => $selected ? (new PackageService())->forPackage($selected) : [], 'providers' => (new Provider())->active(),
        ]);
    }

    public function packageSave(): void {
        $this->guard(); \csrf_check();
        $d = [];
        foreach (['id', 'name', 'destination', 'category', 'duration_days', 'price', 'currency', 'stock', 'description', 'status'] as $k) $d[$k] = trim($_POST[$k] ?? '');
        if ($d['name'] === '' || $d['destination'] === '' || !is_numeric($d['price']) || (float)$d['price'] < 0 || (int)$d['duration_days'] < 1 || (int)$d['stock'] < 0
            || !in_array($d['currency'], ['BOB', 'USD', 'EUR'], true) || !in_array($d['status'], ['Activo', 'Inactivo'], true) || !in_array($d['category'], self::CATEGORIES, true)) {
            \flash('error', 'Revisa los datos: nombre, destino, categoría, precio, duración, cupos, moneda y estado deben ser válidos.');
            \redirect('catalog-admin');
        }
        try { (new Package())->save($d); \flash('success', $d['id'] !== '' ? 'Paquete actualizado.' : 'Paquete creado.'); }
        catch (\Throwable $e) { \flash('error', 'No se pudo guardar el paquete.'); }
        \redirect('catalog-admin');
    }

    public function packageStatus(): void {
        $this->guard(); \csrf_check();
        $s = Database::connection()->prepare("UPDATE packages SET status=IF(status='Activo','Inactivo','Activo') WHERE id=?");
        $s->execute([(int)($_POST['id'] ?? 0)]);
        \flash('success', 'Estado del paquete actualizado.'); \redirect('catalog-admin');
    }

    public function serviceSave(): void {
        $this->guard(); \csrf_check();
        $packageId = (int)($_POST['package_id'] ?? 0); $name = trim($_POST['name'] ?? '');
        if (!$packageId || $name === '') { \flash('error', 'Indica el paquete y el nombre del servicio.'); \redirect('catalog-admin&package=' . $packageId); }
        try {
            (new PackageService())->save(['id' => (int)($_POST['id'] ?? 0), 'package_id' => $packageId, 'provider_id' => (int)($_POST['provider_id'] ?? 0), 'name' => $name,
                'service_type' => trim($_POST['service_type'] ?? ''), 'description' => trim($_POST['description'] ?? ''), 'included' => isset($_POST['included']) ? 1 : 0]);
            \flash('success', 'Servicio guardado.');
        } catch (\PDOException $e) { \flash('error', 'No se pudo guardar el servicio. Verifica que no esté repetido en este paquete.'); }
        \redirect('catalog-admin&package=' . $packageId);
    }

    public function serviceDelete(): void {
        $this->guard(); \csrf_check();
        (new PackageService())->delete((int)($_POST['id'] ?? 0));
        \flash('success', 'Servicio eliminado.'); \redirect('catalog-admin&package=' . (int)($_POST['package_id'] ?? 0));
    }

    /** Proveedores: alta, edición y activación/desactivación. */
    public function providers(): void {
        $this->guard(); $m = new Provider(); $edit = isset($_GET['edit']) ? $m->find((int)$_GET['edit']) : null; $history = isset($_GET['history']) ? $m->services((int)$_GET['history']) : [];
        $this->render('catalog_admin/providers', ['providers' => $m->all(trim($_GET['q'] ?? '')), 'edit' => $edit, 'history' => $history, 'historyId' => (int)($_GET['history'] ?? 0), 'counts' => $m->countServices()]);
    }

    public function providerSave(): void {
        $this->guard(); \csrf_check();
        $d = ['id' => (int)($_POST['id'] ?? 0), 'name' => trim($_POST['name'] ?? ''), 'contact_name' => trim($_POST['contact_name'] ?? '') ?: null, 'phone' => trim($_POST['phone'] ?? '') ?: null,
              'email' => trim($_POST['email'] ?? '') ?: null, 'service_type' => trim($_POST['service_type'] ?? '') ?: null, 'status' => ($_POST['status'] ?? '') === 'Inactivo' ? 'Inactivo' : 'Activo'];
        if ($d['name'] === '') { \flash('error', 'El nombre de la empresa es obligatorio.'); \redirect('providers'); }
        if ($d['email'] !== null && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) { \flash('error', 'El correo del proveedor no es válido.'); \redirect('providers'); }
        try { (new Provider())->save($d); \flash('success', $d['id'] ? 'Proveedor actualizado.' : 'Proveedor registrado.'); }
        catch (\PDOException $e) { \flash('error', 'No se pudo guardar. Verifica que no exista otro proveedor con el mismo nombre.'); }
        \redirect('providers');
    }

    public function providerStatus(): void {
        $this->guard(); \csrf_check();
        $m = new Provider(); $p = $m->find((int)($_POST['id'] ?? 0));
        if ($p) { $m->setStatus((int)$p['id'], $p['status'] === 'Activo' ? 'Inactivo' : 'Activo'); \flash('success', 'Estado del proveedor actualizado.'); }
        \redirect('providers');
    }
}
