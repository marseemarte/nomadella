document.querySelectorAll('.btn-omitir').forEach(btn => {
    btn.addEventListener('click', function () {
        const tipo = this.getAttribute('data-omitir');
        document.getElementById('omitir_' + tipo).value = '1';
        this.closest('.bloque-componente').querySelector('select')?.setAttribute('disabled', 'disabled');
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-success');
        this.textContent = 'Omitido';
        this.disabled = true;
    });
});

let tipoActual = 'alojamiento';

document.querySelectorAll('[data-bs-target="#modalProveedor"]').forEach(btn => {
    btn.addEventListener('click', function () {
        tipoActual = this.getAttribute('data-tipo');
        document.querySelector('#modalProveedor select[name="tipo"]').value = tipoActual;
        document.getElementById('modal_id_destino').value = "<?= $id_destino ?>";
    });
});

document.getElementById('formProveedorModal').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;
    const nombre = form.nombre.value.trim();
    const tipo = form.tipo.value;
    const id_destino = form.id_destino.value;

    if (!nombre || !tipo || !id_destino) return;

    // AJAX para guardar en la base
    fetch('alta_destino.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            nombre: nombre,
            tipo: tipo,
            id_destino: id_destino
        })
    })
        .then(res => res.json())
        .then(data => {
            let bloque = document.querySelector('.bloque-' + tipo);
            let selectName = '';
            if (tipo === 'alojamiento') selectName = 'alojamientos[]';
            if (tipo === 'vuelo') selectName = 'vuelos[]';
            if (tipo === 'auto') selectName = 'autos[]';
            if (tipo === 'servicio') selectName = 'servicios[]';

            let select = bloque.querySelector('select[name="' + selectName + '"]');
            if (!select) {
                // Si no existe el select, crea uno y reemplaza la alerta
                let selectHtml = `<select name="${selectName}" class="form-select" multiple required></select>
                        <small class="text-muted">Puede seleccionar varios (Ctrl + click)</small>`;
                let alertDiv = bloque.querySelector('.alert');
                if (alertDiv) {
                    alertDiv.insertAdjacentHTML('afterend', selectHtml);
                    alertDiv.remove();
                } else {
                    bloque.insertAdjacentHTML('beforeend', selectHtml);
                }
                select = bloque.querySelector('select[name="' + selectName + '"]');
            }

            if (select) {
                let option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.nombre;
                option.selected = true;
                select.appendChild(option);
                select.removeAttribute('disabled');
            }

            // Cierra el modal y limpia el form
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalProveedor'));
            modal.hide();
            form.reset();
        })
        .catch(() => {
            alert('Error al guardar el proveedor');
        });
});