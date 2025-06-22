<?php
include 'conexion.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$estado = isset($_GET['estado']) ? $conn->real_escape_string($_GET['estado']) : '';
$offset = ($page - 1) * $limit;

$where = "WHERE 1";
if (!empty($search)) {
    $where .= " AND (u.nombre LIKE '%$search%' OR u.apellido LIKE '%$search%' OR o.id_orden LIKE '%$search%')";
}
if (!empty($estado)) {
    $where .= " AND o.estado = '$estado'";
}

$totalQuery = $conn->query("SELECT COUNT(*) as total
    FROM ordenes o
    JOIN usuarios u ON u.id_usuario = o.id_usuario
    $where");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT o.id_orden, o.fecha_orden, o.estado, o.total, u.nombre, u.apellido
        FROM ordenes o
        JOIN usuarios u ON u.id_usuario = o.id_usuario
        $where
        ORDER BY o.fecha_orden DESC
        LIMIT $limit OFFSET $offset";

$reservas = $conn->query($sql);

$html = '';
if ($reservas && $reservas->num_rows > 0) {
    while ($r = $reservas->fetch_assoc()) {
        $id_orden = $r['id_orden'];

        // badge de estado
        $estado_lower = strtolower($r['estado']);
        $badge = 'bg-secondary';
        if ($estado_lower == 'confirmada') $badge = 'bg-success';
        elseif ($estado_lower == 'pendiente') $badge = 'bg-warning';
        elseif ($estado_lower == 'cancelada') $badge = 'bg-danger';



        $html .= '<tr>';
        $html .= '<td>' . $id_orden . '</td>';
        $html .= '<td>' . htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($r['fecha_orden'])) . '</td>';
        $html .= '<td><span class="badge ' . $badge . '">' . ucfirst($estado_lower) . '</span></td>';
        $html .= '<td>$' . number_format($r['total'], 2) . '</td>';

$html .= '<td>
                    <button class="btn btn-sm btn-info btn-ver-detalle" data-id="' . $id_orden . '" type="button">Ver detalles</button>
                    <button class="btn btn-sm btn-danger" onclick="confirmarCancelacion(' . $id_orden . ')" type="button">Cancelar</button>
                    <a class="btn btn-sm btn-primary" href="editar_reserva.php?id=' . $id_orden . '">Editar</a>
                </td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>';
}

$response = [
    'html' => $html,
    'start' => $offset + 1,
    'end' => min($offset + $limit, $totalRows),
    'total' => $totalRows,
    'pages' => $totalPages
];

header('Content-Type: application/json');
echo json_encode($response);
