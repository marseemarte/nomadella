<?php
include './sidebar.php';
include './conexion.php'; // archivo de conexión PDO a tu base 'nomadella'

// Obtener paquetes
$paquetes = $pdo->query("SELECT * FROM paquetes_turisticos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Paquetes Turísticos - Nomadella</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
</head>

<body>
    <div class="main-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Paquetes Turísticos</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Paquetes Turísticos</h2>
            <a href="nuevo_paquete.php" class="btn btn-outline-primary">+ Nuevo Paquete</a>
        </div>

        <?php foreach ($paquetes as $paquete): ?>
            <?php
            $id_paquete = $paquete['id_paquete'];

            // ALOJAMIENTOS
            $stmt = $pdo->prepare("SELECT a.nombre, a.direccion, a.ciudad, a.categoria FROM alojamientos a JOIN paquete_alojamientos pa ON a.id_alojamiento = pa.id_alojamiento WHERE pa.id_paquete = ?");
            $stmt->execute([$id_paquete]);
            $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // AUTOS
            $stmt = $pdo->prepare("SELECT aa.proveedor, aa.tipo_vehiculo, aa.precio_por_dia FROM alquiler_autos aa JOIN paquete_autos pa ON aa.id_alquiler = pa.id_alquiler WHERE pa.id_paquete = ?");
            $stmt->execute([$id_paquete]);
            $autos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // VUELOS
            $stmt = $pdo->prepare("SELECT v.aerolinea, v.origen, v.destino, v.precio_base FROM vuelos v JOIN paquete_vuelos pv ON v.id_vuelo = pv.id_vuelo WHERE pv.id_paquete = ?");
            $stmt->execute([$id_paquete]);
            $vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // SERVICIOS ADICIONALES
            $stmt = $pdo->prepare("SELECT s.nombre, s.descripcion, s.precio FROM servicios_adicionales s JOIN paquete_servicios ps ON s.id_servicio = ps.id_servicio WHERE ps.id_paquete = ?");
            $stmt->execute([$id_paquete]);
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ETIQUETAS
            $stmt = $pdo->prepare("SELECT e.nombre FROM etiquetas e JOIN paquete_etiquetas pe ON e.id_etiqueta = pe.id_etiqueta WHERE pe.id_paquete = ?");
            $stmt->execute([$id_paquete]);
            $etiquetas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            ?>

            <div class="card-paquete">
                <div class="row">
                    <div class="col-md-6">
                        <h4 style="color:#750D37"><?= htmlspecialchars($paquete['nombre']) ?></h4>
                        <p><?= htmlspecialchars($paquete['descripcion']) ?></p>

                        <h6><i class="bi bi-houses"></i> Alojamiento:</h6>
                        <?php foreach ($alojamientos as $a): ?>
                            <p> <?= htmlspecialchars($a['nombre']) ?> - <?= htmlspecialchars($a['ciudad']) ?> (<?= htmlspecialchars($a['categoria']) ?>)</p>
                        <?php endforeach; ?>

                        <h6><i class="bi bi-car-front"></i> Alquiler de Auto:</h6>
                        <?php foreach ($autos as $a): ?>
                            <p> <?= htmlspecialchars($a['proveedor']) ?> - <?= htmlspecialchars($a['tipo_vehiculo']) ?> - $<?= number_format($a['precio_por_dia'], 2) ?>/día</p>
                        <?php endforeach; ?>

                        <h6><i class="bi bi-airplane"></i> Vuelos:</h6>
                        <?php foreach ($vuelos as $v): ?>
                            <p> <?= htmlspecialchars($v['aerolinea']) ?> (<?= htmlspecialchars($v['origen']) ?> → <?= htmlspecialchars($v['destino']) ?>) - $<?= number_format($v['precio_base'], 2) ?></p>
                        <?php endforeach; ?>

                        <h6><i class="bi bi-plus-circle"></i> Servicios Adicionales:</h6>
                        <?php foreach ($servicios as $s): ?>
                            <p> <?= htmlspecialchars($s['nombre']) ?> - <?= htmlspecialchars($s['descripcion']) ?> - $<?= number_format($s['precio'], 2) ?></p>
                        <?php endforeach; ?>

                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div>
                            <h6>Etiquetas:</h6>
                            <div class="d-flex flex-wrap">
                                <?php foreach ($etiquetas as $et): ?>
                                    <span class="etiqueta"><?= htmlspecialchars($et) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div>
                            <div>
                            <p>Precio: <b>$<?= number_format($paquete['precio_base'], 2) ?></b></p>
                            <p>Destino: <?= htmlspecialchars($paquete['destino']) ?></p>
                            <p>Fecha: <?= date("d/m/Y", strtotime($paquete['fecha_inicio'])) ?> - <?= date("d/m/Y", strtotime($paquete['fecha_fin'])) ?></p>    
                            </div>
                            <div>
                                <p>Estado: 
                                    <?php if ($paquete['activo'] == '1'): ?>
                                        <span class="text-success fw-bold">Activo</span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold">Inactivo</span>
                                    <?php endif; ?>
                                </p> 
                            </div>
                            
                            <a href="editar_paquete.php?id=<?= $id_paquete ?>" class="btn btn-editar"><i class="bi bi-pencil-square"></i> EDITAR</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

</body>

</html>