<?php
include 'conexion.php';
include 'header.php';

$id_paquete = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM paquetes_turisticos WHERE id_paquete = $id_paquete AND activo = 1";
$result = $conexion->query($sql);
$paquete = $result->fetch_assoc();

if (!$paquete) {
    echo "<h2>Paquete no encontrado.</h2>";
    exit;
}

$aloj = $conexion->query("SELECT a.* FROM alojamientos a 
    INNER JOIN paquete_alojamientos pa ON pa.id_alojamiento = a.id_alojamiento 
    WHERE pa.id_paquete = $id_paquete")->fetch_assoc();

$vuelo = $conexion->query("SELECT v.* FROM vuelos v 
    INNER JOIN paquete_vuelos pv ON pv.id_vuelo = v.id_vuelo 
    WHERE pv.id_paquete = $id_paquete")->fetch_assoc();

$auto = $conexion->query("SELECT alq.* FROM alquiler_autos alq 
    INNER JOIN paquete_autos pa ON pa.id_alquiler = alq.id_alquiler 
    WHERE pa.id_paquete = $id_paquete")->fetch_assoc();

$servicios = $conexion->query("SELECT s.* FROM servicios_adicionales s 
    INNER JOIN paquete_servicios ps ON ps.id_servicio = s.id_servicio 
    WHERE ps.id_paquete = $id_paquete");

$etiquetas = $conexion->query("SELECT e.nombre FROM etiquetas e 
    INNER JOIN paquete_etiquetas pe ON pe.id_etiqueta = e.id_etiqueta 
    WHERE pe.id_paquete = $id_paquete");

$comentarios = $conexion->query("SELECT c.*, u.nombre FROM comentarios_paquetes c 
    LEFT JOIN usuarios u ON u.id_usuario = c.id_usuario 
    WHERE c.id_paquete = $id_paquete ORDER BY c.fecha DESC");
?>

<body>
<style>
    body {
        background: #f8f8f4;
        margin: 0;
        font-family: 'Montserrat', Arial, sans-serif;
    }
    .detalle-main {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .badge {
        font-size: 0.9rem;
        white-space: normal;
        margin-bottom: 0.3rem;
        display: inline-block;
    }
    h1 {
        font-weight: 700;
        font-size: 2.5rem;
    }
    h4 {
        font-size: 1.8rem;
    }
    .detalle-main p {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .detalle-main {
            padding: 15px;
        }
        h1 {
            font-size: 1.8rem;
        }
        h4 {
            font-size: 1.4rem;
        }
        .detalle-main p {
            font-size: 1rem;
        }
        #cantidad {
            width: 60% !important;
            max-width: 100% !important;
        }
        .btn {
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
        }
    }
</style>

<main class="detalle-main container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12 text-center">
            <h2 class="mb-4 text-dark">Detalles del Paquete Turístico</h2>
            <h1 class="text-primary"><?= htmlspecialchars($paquete['nombre']) ?></h1>

            <div class="mb-3">
                <?php while($et = $etiquetas->fetch_assoc()): ?>
                    <span class="badge rounded-pill text-white" style="background:linear-gradient(90deg, #741d41 60%, #b84e6f 100%)">
                        <?= htmlspecialchars($et['nombre']) ?>
                    </span>
                <?php endwhile; ?>
            </div>

            <h4 class="text-danger fw-bold">Total: $<span id="precioTotal"><?= number_format($paquete['precio_base'],2) ?></span> USD</h4>

            <p class="mt-3"><i class="bi bi-geo-alt-fill"></i> <b>Destino:</b> <?= htmlspecialchars($paquete['destino']) ?></p>
            <p><i class="bi bi-info-circle"></i> <b>Tipo:</b> <?= htmlspecialchars($paquete['tipo_paquete']) ?></p>
            <p><i class="bi bi-calendar-event"></i> <b>Fechas:</b> <?= date('d/m/Y', strtotime($paquete['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($paquete['fecha_fin'])) ?></p>
            <p><i class="bi bi-people-fill"></i> <b>Cupo disponible:</b> <?= $paquete['cupo_disponible'] ?></p>

            <?php if(isset($_SESSION['usuario_id'])): ?>
                <div class="d-flex justify-content-center align-items-center gap-2 mt-3">
                    <input type="number" id="cantidad" value="1" min="1" max="<?= $paquete['cupo_disponible'] ?>" class="form-control text-center" style="max-width: 80px;">
                    <button class="btn btn-primary" onclick="agregarAlCarrito(<?= $paquete['id_paquete'] ?>)">
                        <i class="bi bi-cart-plus"></i> Agregar al carrito
                    </button>
                </div>
            <?php else: ?>
                <div class="mt-3">
                    <a href="login.php" class="btn btn-outline-primary">Inicia sesión para reservar</a>
                </div>
            <?php endif; ?>

            <div class="mt-5 text-start">
                <h3>Descripción</h3>
                <p><?= nl2br(htmlspecialchars($paquete['descripcion'])) ?></p>
            </div>

            <div class="mt-4">
                <a href="index.php" class="btn btn-secondary">← Volver a paquetes</a>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h3 class="mb-3 text-center">Información Adicional</h3>
        <div class="accordion" id="infoAcordeon">

            <!-- Alojamiento -->
            <?php if($aloj): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAloj">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAloj" aria-expanded="false" aria-controls="collapseAloj">
                        Alojamiento Incluido
                    </button>
                </h2>
                <div id="collapseAloj" class="accordion-collapse collapse" aria-labelledby="headingAloj" data-bs-parent="#infoAcordeon">
                    <div class="accordion-body">
                        <ul>
                            <li><b><?= htmlspecialchars($aloj['nombre']) ?></b> (<?= htmlspecialchars($aloj['categoria']) ?>) - <?= htmlspecialchars($aloj['ciudad']) ?></li>
                            <li><?= htmlspecialchars($aloj['direccion']) ?></li>
                            <li><?= htmlspecialchars($aloj['descripcion']) ?></li>
                            <li><i class="bi bi-telephone"></i> <?= htmlspecialchars($aloj['telefono']) ?> | <?= htmlspecialchars($aloj['email_contacto']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Vuelo -->
            <?php if($vuelo): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingVuelo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVuelo" aria-expanded="false" aria-controls="collapseVuelo">
                        Vuelo Incluido
                    </button>
                </h2>
                <div id="collapseVuelo" class="accordion-collapse collapse" aria-labelledby="headingVuelo" data-bs-parent="#infoAcordeon">
                    <div class="accordion-body">
                        <ul>
                            <li><b><?= htmlspecialchars($vuelo['aerolinea']) ?></b> - Vuelo <?= htmlspecialchars($vuelo['codigo_vuelo']) ?></li>
                            <li>Origen: <?= htmlspecialchars($vuelo['origen']) ?> | Destino: <?= htmlspecialchars($vuelo['destino']) ?></li>
                            <li>Salida: <?= date('d/m/Y H:i', strtotime($vuelo['fecha_salida'])) ?></li>
                            <li>Llegada: <?= date('d/m/Y H:i', strtotime($vuelo['fecha_llegada'])) ?></li>
                            <li>Precio Base: $<?= number_format($vuelo['precio_base'], 2) ?> USD</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Auto -->
            <?php if($auto): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAuto">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAuto" aria-expanded="false" aria-controls="collapseAuto">
                        Alquiler de Auto Incluido
                    </button>
                </h2>
                <div id="collapseAuto" class="accordion-collapse collapse" aria-labelledby="headingAuto" data-bs-parent="#infoAcordeon">
                    <div class="accordion-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="autoIncluido" checked>
                            <label class="form-check-label" for="autoIncluido">
                                Incluir este alquiler de auto
                            </label>
                        </div>
                        <ul>
                            <li><b><?= htmlspecialchars($auto['proveedor']) ?></b> - <?= htmlspecialchars($auto['tipo_vehiculo']) ?></li>
                            <li>Retiro: <?= htmlspecialchars($auto['ubicacion_retiro']) ?></li>
                            <li>Entrega: <?= htmlspecialchars($auto['ubicacion_entrega']) ?></li>
                            <li>Precio por día: $<?= number_format($auto['precio_por_dia'], 2) ?> USD</li>
                            <li><?= htmlspecialchars($auto['condiciones']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Servicios adicionales -->
            <?php if($servicios && $servicios->num_rows): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingServicios">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseServicios" aria-expanded="false" aria-controls="collapseServicios">
                        Servicios Adicionales
                    </button>
                </h2>
                <div id="collapseServicios" class="accordion-collapse collapse" aria-labelledby="headingServicios" data-bs-parent="#infoAcordeon">
                    <div class="accordion-body">
                        <ul>
                            <?php while($srv = $servicios->fetch_assoc()): ?>
                            <li>
                                <div class="form-check">
                                    <input class="form-check-input servicio-adicional" type="checkbox" value="<?= $srv['id_servicio'] ?>" id="servicio<?= $srv['id_servicio'] ?>" checked>
                                    <label class="form-check-label" for="servicio<?= $srv['id_servicio'] ?>">
                                        <b><?= htmlspecialchars($srv['nombre']) ?></b>: <?= htmlspecialchars($srv['descripcion']) ?> (<?= htmlspecialchars($srv['tipo']) ?>, $<?= number_format($srv['precio'],2) ?> USD)
                                    </label>
                                </div>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
const precioBase = <?= $paquete['precio_base'] ?>;
const precioAuto = <?= ($auto) ? $auto['precio_por_dia'] : 0 ?>;
const preciosServicios = {};
<?php 
$servicios = $conexion->query("SELECT s.* FROM servicios_adicionales s 
    INNER JOIN paquete_servicios ps ON ps.id_servicio = s.id_servicio 
    WHERE ps.id_paquete = $id_paquete");
while($srv = $servicios->fetch_assoc()): ?>
    preciosServicios[<?= $srv['id_servicio'] ?>] = <?= $srv['precio'] ?>;
<?php endwhile; ?>

function actualizarPrecio() {
    let total = precioBase;
    const incluirAuto = document.getElementById('autoIncluido');
    if(incluirAuto && incluirAuto.checked) {
        total += parseFloat(precioAuto);
    }

    document.querySelectorAll('.servicio-adicional:checked').forEach(item => {
        total += parseFloat(preciosServicios[item.value]);
    });

    document.getElementById('precioTotal').innerText = total.toFixed(2);
}

if(document.getElementById('autoIncluido')) {
    document.getElementById('autoIncluido').addEventListener('change', actualizarPrecio);
}
document.querySelectorAll('.servicio-adicional').forEach(item => {
    item.addEventListener('change', actualizarPrecio);
});

document.addEventListener('DOMContentLoaded', () => {
    actualizarPrecio();
});

function agregarAlCarrito(id_paquete) {
    const cantidad = document.getElementById('cantidad').value;
    const incluirAuto = document.getElementById('autoIncluido') ? document.getElementById('autoIncluido').checked : false;

    let serviciosSeleccionados = [];
    document.querySelectorAll('.servicio-adicional:checked').forEach(function(item) {
        serviciosSeleccionados.push(item.value);
    });

    fetch('carrito/agregar_item.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'tipo_producto=paquete_turistico' +
              '&id_producto=' + id_paquete +
              '&cantidad=' + cantidad +
              '&incluir_auto=' + (incluirAuto ? 1 : 0) +
              '&servicios=' + encodeURIComponent(JSON.stringify(serviciosSeleccionados))
    })
    .then(res => res.text())
    .then(txt => {
        
    });
}
</script>