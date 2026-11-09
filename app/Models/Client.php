<?php
namespace App\Models;
use App\Core\Database;
class Client {
 public function all(string $q=''): array { $db=Database::connection(); if($q!==''){ $s=$db->prepare('SELECT * FROM clients WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY id DESC'); $like="%$q%"; $s->execute([$like,$like,$like]); return $s->fetchAll(); } return $db->query('SELECT * FROM clients ORDER BY id DESC')->fetchAll(); }
 public function save(array $d): void { $db=Database::connection(); if(!empty($d['id'])){$s=$db->prepare('UPDATE clients SET name=?,email=?,phone=?,country=?,preferences=? WHERE id=?');$s->execute([$d['name'],$d['email'],$d['phone'],$d['country'],$d['preferences'],$d['id']]);}else{$s=$db->prepare('INSERT INTO clients(name,email,phone,country,preferences) VALUES(?,?,?,?,?)');$s->execute([$d['name'],$d['email'],$d['phone'],$d['country'],$d['preferences']]);} }
 public function delete(int $id): void { $s=Database::connection()->prepare('DELETE FROM clients WHERE id=?');$s->execute([$id]); }
}




#------------------------------------------
// app/models/Cliente.php
class Cliente {
    private $db;
    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=neta_db', 'user', 'pass');
    }
    public function buscarPorCI($ci) {
        $stmt = $this->db->prepare("SELECT id FROM clientes WHERE ci = :ci");
        $stmt->execute([':ci' => $ci]);
        return $stmt->fetch();
    }
    public function guardar($datos) {
        $stmt = $this->db->prepare("INSERT INTO clientes (nombre, ci, telefono, email) VALUES (:nombre, :ci, :telefono, :email)");
        $stmt->execute($datos);
    }
}
