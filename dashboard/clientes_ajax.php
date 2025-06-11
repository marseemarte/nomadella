<?php
$mysqli = new mysqli("localhost", "root", "", "nomadella");
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
$where = $search ? "WHERE nombre LIKE '%$search%' OR email LIKE '%$search%' OR telefono LIKE '%$search%'" : "";
$sql = "SELECT * FROM usuarios $where ORDER BY fecha_registro DESC LIMIT 10";
$result = $mysqli->query($sql);
if ($result && $result->num_rows > 0):
    foreach ($result as $i => $row): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
        </tr>
    <?php endforeach;
else: ?>
    <tr><td colspan="5" class="text-center">No se encontraron usuarios.</td></tr>
<?php endif;
$mysqli->close();
?>