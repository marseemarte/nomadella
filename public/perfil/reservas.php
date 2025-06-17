<?php
session_start();
include '../conexion.php';
include '../header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

// Consulta de reservas del usuario
$sql = "
    SELECT 
        o.id_orden,
        o.fecha_orden,
        o.total,
        o.estado,
        o.medio_pago,
        p.nombre AS paquete,
        p.fecha_inicio,
        p.fecha_fin
    FROM ordenes o
    JOIN orden_items oi ON o.id_orden = oi.id_orden
    JOIN paquetes_turisticos p ON oi.id_producto = p.id_paquete
    WHERE o.id_usuario = ?
      AND oi.tipo_producto = 'paquete_turistico'
    ORDER BY o.fecha_orden DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$reservas = $stmt->get_result();
?>

<div class="container my-5">
    <h2 class="text-center mb-4" style="color: #a63e62;">Mis Reservas</h2>

    <?php if ($reservas->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($r = $reservas->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= htmlspecialchars($r['paquete']) ?></h5>
                            <p class="mb-1"><strong>Fecha del viaje:</strong> <?= date('d/m/Y', strtotime($r['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($r['fecha_fin'])) ?></p>
                            <p class="mb-1"><strong>Total:</strong> $<?= number_format($r['total'], 2) ?> USD</p>
                            <p class="mb-1"><strong>Estado:</strong> 
                                <span class="badge bg-<?= $r['estado'] === 'Confirmada' ? 'success' : ($r['estado'] === 'Pendiente' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($r['estado']) ?>
                                </span>
                            </p>
                            <p class="mb-0"><strong>Pago:</strong> <?= $r['medio_pago'] ?? 'No especificado' ?></p>
                        </div>
                        <div class="card-footer text-muted small">
                            Reservado el <?= date('d/m/Y H:i', strtotime($r['fecha_orden'])) ?>
                        </div>
                         <a href="perfil/edit_reservas.php?id=<?= $r['id_orden'] ?>" class="btn btn-outline-primary btn-sm mt-3">
    Editar / Cancelar
</a>
                        
                    </div>
                    
                </div>
            <?php endwhile; ?>
           
        </div>
    <?php else: ?>
        <p class="text-muted text-center">No tenés reservas aún.</p>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
