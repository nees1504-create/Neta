<!-- app/views/catalogo/index.php -->
<h2>Catálogo de Paquetes</h2>
<?php foreach ($paquetes as $paquete): ?>
    <div class="paquete">
        <h3><?= htmlspecialchars($paquete['nombre']) ?></h3>
        <p>Precio: <?= htmlspecialchars($paquete['precio']) ?> BOB</p>
    </div>
<?php endforeach; ?>
