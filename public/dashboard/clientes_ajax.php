<?php
$mysqli = new mysqli("localhost", "root", "", "nomadella");
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
$where = $search ? "WHERE nombre LIKE '%$search%' OR email LIKE '%$search%' OR telefono LIKE '%$search%'" : "";
$sql = "SELECT * FROM usuarios $where ORDER BY fecha_registro DESC LIMIT 10";
$result = $mysqli->query($sql);
if ($result && $result->num_rows > 0):
    foreach ($result as $i => $row): ?>
        <tr data-id="<?= $row['id_usuario'] ?>">
            <td><?= $row['id_usuario'] ?></td>
            <td data-id="<?= $row['id_usuario'] ?>"><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
            <td>
                <?php if (isset($row['estado']) && $row['estado'] === 'activo'): ?>
                    <span class="badge bg-success">Activo</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Inactivo</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="editar_usuario.php?id=<?= $row['id_usuario'] ?>" class="btn btn-sm btn-primary me-1">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
                <?php if ($row['estado']): ?>
                    <button type="button" class="btn btn-sm btn-danger btn-desactivar" data-id="<?= $row['id_usuario'] ?>" data-nombre="<?= htmlspecialchars($row['nombre']) ?>">
                        <i class="bi bi-person-x"></i> Desactivar
                    </button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach;
else: ?>
    <tr>
        <td colspan="5" class="text-center">No se encontraron usuarios.</td>
    </tr>
<?php endif;
$mysqli->close();
?>