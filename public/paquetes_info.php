<?php
include 'conexion.php';
include 'header.php';
// Obtener el id del paquete por GET
$id_paquete = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta principal del paquete
$sql = "SELECT * FROM paquetes_turisticos WHERE id_paquete = $id_paquete AND activo = 1";
$result = $conexion->query($sql);
$paquete = $result->fetch_assoc();

if (!$paquete) {
    echo "<h2>Paquete no encontrado.</h2>";
    exit;
}

// Alojamiento
$aloj = $conexion->query("SELECT a.* FROM alojamientos a 
    INNER JOIN paquete_alojamientos pa ON pa.id_alojamiento = a.id_alojamiento 
    WHERE pa.id_paquete = $id_paquete")->fetch_assoc();

// Vuelo
$vuelo = $conexion->query("SELECT v.* FROM vuelos v 
    INNER JOIN paquete_vuelos pv ON pv.id_vuelo = v.id_vuelo 
    WHERE pv.id_paquete = $id_paquete")->fetch_assoc();

// Auto
$auto = $conexion->query("SELECT alq.* FROM alquiler_autos alq 
    INNER JOIN paquete_autos pa ON pa.id_alquiler = alq.id_alquiler 
    WHERE pa.id_paquete = $id_paquete")->fetch_assoc();

// Servicios adicionales
$servicios = $conexion->query("SELECT s.* FROM servicios_adicionales s 
    INNER JOIN paquete_servicios ps ON ps.id_servicio = s.id_servicio 
    WHERE ps.id_paquete = $id_paquete");

// Etiquetas
$etiquetas = $conexion->query("SELECT e.nombre FROM etiquetas e 
    INNER JOIN paquete_etiquetas pe ON pe.id_etiqueta = e.id_etiqueta 
    WHERE pe.id_paquete = $id_paquete");

// Comentarios
$comentarios = $conexion->query("SELECT c.*, u.nombre FROM comentarios_paquetes c 
    LEFT JOIN usuarios u ON u.id_usuario = c.id_usuario 
    WHERE c.id_paquete = $id_paquete ORDER BY c.fecha DESC");
?>



<body>
    
    <main class="detalle-main">
        <div class="detalle-header">
            <img class="detalle-img" src="<?= htmlspecialchars($paquete['imagen_destacada']) ?>" alt="Imagen paquete">
            <div class="detalle-info">
                <h1><?= htmlspecialchars($paquete['nombre']) ?></h1>
                <div class="detalle-etiquetas">
                    <?php while($et = $etiquetas->fetch_assoc()): ?>
                        <span><?= htmlspecialchars($et['nombre']) ?></span>
                    <?php endwhile; ?>
                </div>
                <div class="detalle-precio">$<?= number_format($paquete['precio_base'],2) ?> USD</div>
                <div><b>Destino:</b> <?= htmlspecialchars($paquete['destino']) ?></div>
                <div><b>Tipo:</b> <?= htmlspecialchars($paquete['tipo_paquete']) ?></div>
                <div><b>Fechas:</b> <?= date('d/m/Y', strtotime($paquete['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($paquete['fecha_fin'])) ?></div>
                <div><b>Cupo disponible:</b> <?= $paquete['cupo_disponible'] ?></div>
            </div>
        </div>
        <div class="detalle-section">
            <h3>Descripción</h3>
            <p><?= nl2br(htmlspecialchars($paquete['descripcion'])) ?></p>
        </div>
        <?php if($aloj): ?>
        <div class="detalle-section">
            <h3>Alojamiento incluido</h3>
            <ul class="detalle-list">
                <li><b><?= htmlspecialchars($aloj['nombre']) ?></b> (<?= htmlspecialchars($aloj['categoria']) ?>) - <?= htmlspecialchars($aloj['ciudad']) ?></li>
                <li><?= htmlspecialchars($aloj['direccion']) ?></li>
                <li><?= htmlspecialchars($aloj['descripcion']) ?></li>
            </ul>
        </div>
        <?php endif; ?>
        <?php if($vuelo): ?>
        <div class="detalle-section">
            <h3>Vuelo</h3>
            <ul class="detalle-list">
                <li><b><?= htmlspecialchars($vuelo['aerolinea']) ?></b> - Vuelo <?= htmlspecialchars($vuelo['codigo_vuelo']) ?></li>
                <li>Origen: <?= htmlspecialchars($vuelo['origen']) ?> | Destino: <?= htmlspecialchars($vuelo['destino']) ?></li>
                <li>Salida: <?= date('d/m/Y H:i', strtotime($vuelo['fecha_salida'])) ?> | Llegada: <?= date('d/m/Y H:i', strtotime($vuelo['fecha_llegada'])) ?></li>
            </ul>
        </div>
        <?php endif; ?>
        <?php if($auto): ?>
        <div class="detalle-section">
            <h3>Alquiler de auto</h3>
            <ul class="detalle-list">
                <li><b><?= htmlspecialchars($auto['proveedor']) ?></b> - <?= htmlspecialchars($auto['tipo_vehiculo']) ?></li>
                <li>Retiro: <?= htmlspecialchars($auto['ubicacion_retiro']) ?> | Entrega: <?= htmlspecialchars($auto['ubicacion_entrega']) ?></li>
                <li>Precio por día: $<?= number_format($auto['precio_por_dia'],2) ?> USD</li>
                <li><?= htmlspecialchars($auto['condiciones']) ?></li>
            </ul>
        </div>
        <?php endif; ?>
        <?php if($servicios && $servicios->num_rows): ?>
        <div class="detalle-section">
            <h3>Servicios adicionales</h3>
            <ul class="detalle-list">
                <?php while($srv = $servicios->fetch_assoc()): ?>
                    <li><b><?= htmlspecialchars($srv['nombre']) ?></b>: <?= htmlspecialchars($srv['descripcion']) ?> (<?= htmlspecialchars($srv['tipo']) ?>, $<?= number_format($srv['precio'],2) ?> USD)</li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php endif; ?>
        <div class="detalle-section">
            <h3>Comentarios de viajeros</h3>
            <?php if($comentarios->num_rows): ?>
                <?php while($com = $comentarios->fetch_assoc()): ?>
                    <div class="comentario">
                        <div class="comentario-nombre"><?= htmlspecialchars($com['nombre'] ?: 'Anónimo') ?></div>
                        <div class="comentario-punt"><?= str_repeat('★', intval($com['puntuacion'])) . str_repeat('☆', 5-intval($com['puntuacion'])) ?></div>
                        <div><?= htmlspecialchars($com['texto']) ?></div>
                        <div style="font-size:0.9em;color:#888;"><?= date('d/m/Y', strtotime($com['fecha'])) ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay comentarios aún.</p>
            <?php endif; ?>
        </div>
        <div style="margin-top:32px;">
            <a href="index.php" class="paquete-btn" style="text-decoration:none;">← Volver a paquetes</a>
        </div>
    </main>
    <?php
    include 'footer.php';
    ?>
</body>
</html>