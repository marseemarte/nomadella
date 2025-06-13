<div class="mb-4">
        <input type="text" class="form-control w-50" id="buscador" placeholder="Buscar proveedor...">
        <div id="resultados" class="position-absolute bg-white border mt-1 w-50 z-3 rounded"></div>
    </div>

    <ul class="nav nav-tabs mb-4" id="tabsProveedor" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="aloj-tab" data-bs-toggle="tab" href="#alojamiento">Alojamientos</a></li>
        <li class="nav-item"><a class="nav-link" id="vuel-tab" data-bs-toggle="tab" href="#vuelos">Vuelos</a></li>
        <li class="nav-item"><a class="nav-link" id="auto-tab" data-bs-toggle="tab" href="#autos">Alquiler de Autos</a></li>
        <li class="nav-item"><a class="nav-link" id="serv-tab" data-bs-toggle="tab" href="#servicios">Servicios Adicionales</a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane fade show active" id="alojamiento">
            <div class="row g-4">
                <?php while ($a = $alojamientos->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title"><?= htmlspecialchars($a['nombre']) ?></h5>
                        <p><?= nl2br($a['descripcion']) ?></p>
                        <p><i class="bi bi-telephone"></i> <?= $a['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $a['email'] ?></p>
                        <span class="badge badge-azul"><?= ucfirst($a['tipo']) ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="tab-pane fade" id="vuelos">
            <div class="row g-4">
                <?php while ($v = $vuelos->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title"><?= htmlspecialchars($v['nombre']) ?></h5>
                        <p><?= nl2br($v['descripcion']) ?></p>
                        <p><i class="bi bi-telephone"></i> <?= $v['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $v['email'] ?></p>
                        <span class="badge badge-azul"><?= ucfirst($v['tipo']) ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="tab-pane fade" id="autos">
            <div class="row g-4">
                <?php while ($au = $autos->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title"><?= htmlspecialchars($au['nombre']) ?></h5>
                        <p><?= nl2br($au['descripcion']) ?></p>
                        <p><i class="bi bi-telephone"></i> <?= $au['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $au['email'] ?></p>
                        <span class="badge badge-azul"><?= ucfirst($au['tipo']) ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="tab-pane fade" id="servicios">
            <div class="row g-4">
                <?php while ($s = $servicios->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5 class="card-title"><?= htmlspecialchars($s['nombre']) ?></h5>
                        <p><?= nl2br($s['descripcion']) ?></p>
                        <p><i class="bi bi-telephone"></i> <?= $s['telefono'] ?> <br> <i class="bi bi-envelope"></i> <?= $s['email'] ?></p>
                        <span class="badge badge-azul"><?= ucfirst($s['tipo']) ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>