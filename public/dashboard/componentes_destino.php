<?php
// filepath: c:\xampp\htdocs\nomadella\dashboard\componentes_destino.php

// Requiere: $id_destino y $destino definidos antes de incluir este archivo

// Traer proveedores por id_destino
$alojamientos = [];
$vuelos = [];
$autos = [];
$servicios = [];

if ($id_destino) {
    // Alojamientos
    $res = $conn->query("SELECT id_alojamiento, nombre FROM alojamientos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $alojamientos[] = $row;

    // Vuelos
    $res = $conn->query("SELECT id_vuelo, aerolinea FROM vuelos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $vuelos[] = $row;

    // Autos
    $res = $conn->query("SELECT id_alquiler, proveedor FROM alquiler_autos WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $autos[] = $row;

    // Servicios adicionales
    $res = $conn->query("SELECT id_servicio, nombre FROM servicios_adicionales WHERE id_destino = $id_destino");
    while ($row = $res->fetch_assoc()) $servicios[] = $row;
}
?>

<!-- Bloque HTML de asociación de componentes (puedes parametrizar el botón final) -->
<h5 class="mb-3">Destino: <span class="text-success"><?= htmlspecialchars($destino) ?></span></h5>
<!-- Alojamientos -->
<div class="mb-4 bloque-componente bloque-alojamiento">
    <input type="hidden" name="omitir_alojamiento" id="omitir_alojamiento" value="0">
    <label class="form-label">Alojamientos en destino</label>
    <?php if (empty($alojamientos)): ?>
        <div class="alert alert-warning d-flex align-items-center justify-content-between">
            <div>
                No hay alojamientos para este destino.
                <button type="button" class="btn-modal-proveedor btn btn-sm btn-primary ms-2"
                    data-bs-toggle="modal" data-bs-target="#modalProveedor" data-tipo="alojamiento">
                    <i class="bi bi-plus-circle"></i> Agregar alojamiento en <?= htmlspecialchars($destino) ?>
                </button>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="alojamiento">
                Omitir por ahora
            </button>
        </div>
    <?php else: ?>
        <select name="alojamientos[]" class="form-select" multiple required>
            <?php foreach ($alojamientos as $row): ?>
                <option value="<?= $row['id_alojamiento'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
    <?php endif; ?>
</div>

<div class="mb-4 bloque-componente bloque-vuelo">
    <input type="hidden" name="omitir_vuelo" id="omitir_vuelo" value="0">
    <label class="form-label">vuelos en destino</label>
    <?php if (empty($vuelos)): ?>
        <div class="alert alert-warning d-flex align-items-center justify-content-between">
            <div>
                No hay vuelos para este destino.
                <button type="button" class="btn-modal-proveedor btn btn-sm btn-primary ms-2"
                    data-bs-toggle="modal" data-bs-target="#modalProveedor" data-tipo="vuelo">
                    <i class="bi bi-plus-circle"></i> Agregar vuelo en <?= htmlspecialchars($destino) ?>
                </button>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="vuelo">
                Omitir por ahora
            </button>
        </div>
    <?php else: ?>
        <select name="vuelos[]" class="form-select" multiple required>
            <?php foreach ($vuelos as $row): ?>
                <option value="<?= $row['id_vuelo'] ?>"><?= htmlspecialchars($row['aerolinea']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
    <?php endif; ?>
</div>

<!-- Autos -->
<div class="mb-4 bloque-componente bloque-auto">
    <input type="hidden" name="omitir_auto" id="omitir_auto" value="0">
    <label class="form-label">Autos disponibles</label>
    <?php if (empty($autos)): ?>
        <div class="alert alert-warning d-flex align-items-center justify-content-between">
            <div>
                No hay autos para este destino.
                <button type="button" class="btn-modal-proveedor btn btn-sm btn-primary ms-2"
                    data-bs-toggle="modal" data-bs-target="#modalProveedor" data-tipo="auto">
                    <i class="bi bi-plus-circle"></i> Agregar auto en <?= htmlspecialchars($destino) ?>
                    </button>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="auto">
                Omitir por ahora
            </button>
        </div>
    <?php else: ?>
        <select name="autos[]" class="form-select" multiple required>
            <?php foreach ($autos as $row): ?>
                <option value="<?= $row['id_alquiler'] ?>"><?= htmlspecialchars($row['proveedor']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
    <?php endif; ?>
</div>
<!-- Servicios -->
<div class="mb-4 bloque-componente bloque-servicio">
    <input type="hidden" name="omitir_servicio" id="omitir_servicio" value="0">
    <label class="form-label">Servicios adicionales</label>
    <?php if (empty($servicios)): ?>
        <div class="alert alert-warning d-flex align-items-center justify-content-between">
            <div>
                No hay servicios para este destino.
                <button type="button" class="btn-modal-proveedor btn btn-sm btn-primary ms-2"
                    data-bs-toggle="modal" data-bs-target="#modalProveedor" data-tipo="servicio">
                    <i class="bi bi-plus-circle"></i> Agregar servicio en <?= htmlspecialchars($destino) ?>
                </button>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm ms-3 btn-omitir" data-omitir="servicio">
                Omitir por ahora
            </button>
        </div>
    <?php else: ?>
        <select name="servicios[]" class="form-select" multiple required>
            <?php foreach ($servicios as $row): ?>
                <option value="<?= $row['id_servicio'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-end">
<a href="paquetes.php" class="btn btn-secondary px-4 me-2">
    <i class="bi bi-x-circle"></i> Cancelar
    </a>
    <button type="submit" class="btn btn-success px-4">
        <i class="bi bi-check-circle"></i> Guardar destino
                
    </button>
</div>
</div>


<!-- Modal y JS pueden ir en un archivo aparte o incluirse aquí -->