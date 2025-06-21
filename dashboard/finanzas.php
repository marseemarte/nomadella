<?php
// filepath: c:\xampp\htdocs\nomadella\dashboard\finanzas.php
include 'conexion.php';
include 'verificar_admin.php';

// Total de ingresos solo de ordenes confirmadas
$total_ingresos_query = "
    SELECT COALESCE(SUM(total), 0) AS total 
    FROM ordenes 
    WHERE LOWER(estado) = 'confirmada'
";
$total_ingresos_result = $conn->query($total_ingresos_query);
$total_ingresos = $total_ingresos_result->fetch_assoc()['total'];

// Como no tenemos egresos, dejamos en 0 (se puede agregar después)
//$saldo = $total_ingresos - $total_egresos;

// Movimientos financieros: solo ingresos (por ahora)
$movimientos_query = "
    SELECT 
        id_orden, 
        fecha_orden, 
        total, 
        estado, 
        medio_pago,
        id_usuario
    FROM ordenes 
    WHERE LOWER(estado) = 'confirmada'
    ORDER BY fecha_orden DESC
    LIMIT 50
";
$movimientos = $conn->query($movimientos_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finanzas | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/nomadella/css/apartados.css">
    <style>
        .main-content { margin-left: 260px; padding: 40px 30px 30px 30px; min-height: 100vh; background: #FFF6F8; }
        .card-kpi { background: #fff; border: 1px solid #6CE0B6; border-radius: 18px; box-shadow: 0 4px 24px 0 rgba(117, 13, 55, 0.08); padding: 24px 18px 18px 18px; }
        .card-header { background: #5CC7ED; color: #fff; font-weight: bold; border-radius: 10px 10px 0 0; }
        .table thead { background-color: #750D37; color: #FFF6F8; }
        .badge-ingreso { background: #3AB789; }
        .badge-egreso { background: #b84e6f; }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Finanzas</li>
        </ol>
    </nav>

    <h2 class="mb-4">Finanzas</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-kpi shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Ingresos</h6>
                    <h3 class="text-success">$<?= number_format($total_ingresos, 2) ?></h3>
                    <small class="text-muted">Reservas confirmadas</small>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-4">
            <div class="card card-kpi shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Saldo</h6>
                    <h3 class="<?= $saldo >= 0 ? 'text-success' : 'text-danger' ?>">$<?= number_format($saldo, 2) ?></h3>
                    <small class="text-muted">Ingresos - Egresos</small>
                </div>
            </div>
        </div> -->
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <i class="bi bi-cash-coin"></i> Movimientos Financieros
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Detalle</th>
                            <th>Cliente</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($movimientos && $movimientos->num_rows > 0): ?>
                            <?php while ($m = $movimientos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($m['fecha_orden'])) ?></td>
                                    <td><span class="badge badge-ingreso">Ingreso</span></td>
                                    <td><?= "Reserva #" . htmlspecialchars($m['id_orden']) . " - " . htmlspecialchars($m['medio_pago']) ?></td>
                                    <td>
                                        <a href="clientes.php?highlight=<?= $m['id_usuario'] ?>" class="btn btn-outline-primary btn-sm">
                                            Cliente #<?= $m['id_usuario'] ?>
                                        </a>
                                    </td>
                                    <td class="text-success">$<?= number_format($m['total'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No hay movimientos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>