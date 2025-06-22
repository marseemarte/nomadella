<?php
include 'conexion.php';
include 'verificar_admin.php';
// KPIs
$totalVentas = $pdo->query("SELECT COALESCE(SUM(total), 0) AS total FROM ordenes")->fetch()['total'];
$reservasHoy = $pdo->query("SELECT COUNT(*) AS total FROM ordenes WHERE DATE(fecha_orden) = CURDATE()")->fetch()['total'];
$clientes = $pdo->query("SELECT COUNT(*) AS total FROM usuarios")->fetch()['total'];
$nuevos = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE DATE(fecha_registro) = CURDATE()")->fetch()['total'];
$paquetes = $pdo->query("SELECT COUNT(*) AS total FROM paquetes_turisticos WHERE activo = 1")->fetch()['total'];

// NUEVO: Total de reservas activas
$reservaActivas = $pdo->query("SELECT COUNT(*) AS total FROM ordenes WHERE estado = 'activa' OR estado = 'confirmada'")->fetch()['total'];

$ventas = $pdo->query("
  SELECT DAYNAME(fecha_orden) AS dia, SUM(total) AS total
  FROM ordenes
  WHERE fecha_orden >= CURDATE() - INTERVAL 6 DAY
  GROUP BY dia
  ORDER BY FIELD(dia, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
")->fetchAll(PDO::FETCH_ASSOC);

$destinos = $pdo->query("
  SELECT pt.destino, COUNT(*) AS reservas
  FROM orden_items oi
  JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
  
  GROUP BY pt.destino
  ORDER BY reservas DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$reservas = $pdo->query("
  SELECT CONCAT(u.nombre, ' ', u.apellido) AS cliente, pt.destino, o.fecha_orden AS fecha, oi.subtotal AS precio
  FROM ordenes o
  JOIN usuarios u ON u.id_usuario = o.id_usuario
  JOIN orden_items oi ON oi.id_orden = o.id_orden
  LEFT JOIN paquetes_turisticos pt ON pt.id_paquete = oi.id_producto
  
  ORDER BY o.fecha_orden DESC
  LIMIT 4
")->fetchAll(PDO::FETCH_ASSOC);

$comentarios = $pdo->query("
  SELECT u.nombre, c.texto
  FROM comentarios_paquetes c
  JOIN usuarios u ON u.id_usuario = c.id_usuario
  ORDER BY c.fecha DESC
  LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración - Agencia de Turismo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="/nomadella/css/dashboard.css">

</head>
<body>

  <?php include 'sidebar.php'; ?>
  <div class="content">
    <h2 class="mb-4">Dashboard - Visión General</h2>

    <div class="row g-4">
      <div class="col-md-3">
        <div class="card card-kpi shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Ventas Totales</h6>
            <h3>$<?= number_format($totalVentas, 2) ?></h3>
            <small class="c-success">+3.5% semanal</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-kpi shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Reservas Activas</h6>
            <h3><?= $reservaActivas ?></h3>
            <small class="c-success"><?= $reservasHoy ?> hoy</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-kpi shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Clientes Registrados</h6>
            <h3><?= $clientes ?></h3>
            <small class="c-success">+<?= $nuevos ?> nuevos</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-kpi shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Paquetes Activos</h6>
            <h3><?= $paquetes ?></h3>
            <small class="c-success">Actualizados hoy</small>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-4">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header">Gráfico de Ventas (Últimos 7 días)</div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="salesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-header">Top Destinos Reservados</div>
          <ul class="list-group list-group-flush">
            <?php foreach ($destinos as $d): ?>
              <li class="list-group-item"><?= htmlspecialchars($d['destino']) ?> -<i style="color: gray;"> <?= $d['reservas'] ?> reservas</i> </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <div >
      <div >
        <div class="card shadow-sm">
          <div class="card-header">Últimas Reservas</div>
          <div class="card-body">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Cliente</th>
                  <th>Destino</th>
                  <th>Fecha</th>
                  <th>Monto</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservas as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['cliente']) ?></td>
                    <td><?= htmlspecialchars($r['destino']) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                    <td>$<?= number_format($r['precio'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header">Comentarios Recientes</div>
          <ul class="list-group list-group-flush">
            <?php foreach ($comentarios as $c): ?>
              <li class="list-group-item"><strong><?= htmlspecialchars($c['nombre']) ?></strong>: "<?= htmlspecialchars($c['texto']) ?>"</li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div> -->
    </div>

  </div>

  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode(array_column($ventas, 'dia')) ?>,
        datasets: [{
          label: 'Ventas ($)',
          data: <?= json_encode(array_map('floatval', array_column($ventas, 'total'))) ?>,
          borderColor: 'rgba(13, 110, 253, 1)',
          backgroundColor: 'rgba(13, 110, 253, 0.2)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' }
        }
      }
    });
  </script>

</body>
</html>

