<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/csrf.php';
// Procesa el envío antes de imprimir nada: define $proformaMensaje y $proformaId.
require __DIR__ . '/includes/guardar_proforma.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Proformar · Mi Malla</title>
    <?php include 'includes/bootstrap_meta.php'; ?>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container-fluid" style="padding-top: 5rem;">

    <?= $proformaMensaje ?>

    <div class="col-md-4">
        <label for="search_cliente" class="form-label"><h2>Buscar Cliente</h2></label>
        <input class="form-control" type="text" id="search_cliente"
               placeholder="Nombre o apellido..." autocomplete="off">
    </div>
    <div id="results" class="mt-2"></div>

    <form class="row g-3 mt-2" method="POST" action="">
        <?= csrf_field() ?>

        <h2>Información del Cliente</h2>
        <div class="col-md-6">
            <label for="nombresProforma" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="nombresProforma" name="nombresProforma" required>
        </div>
        <div class="col-md-6">
            <label for="apellidosProforma" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidosProforma" name="apellidosProforma" required>
        </div>
        <div class="col-md-4">
            <label for="celularProforma" class="form-label">Celular</label>
            <input type="tel" class="form-control" id="celularProforma" name="celularProforma" required>
        </div>
        <div class="col-md-8">
            <label for="emailProforma" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="emailProforma" name="emailProforma" required>
        </div>
        <div class="col-12">
            <label for="direccionProforma" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccionProforma" name="direccionProforma"
                   placeholder="Calle/Avenida/Jirón 123, distrito" required>
        </div>
        <div class="col-12">
            <label for="infoProforma" class="form-label">Información adicional</label>
            <textarea class="form-control" id="infoProforma" name="infoProforma" rows="3"></textarea>
        </div>

        <div class="col-12">
            <button type="button" class="btn btn-outline-primary" id="guardarCliente">
                Guardar información de cliente
            </button>
            <span id="clienteEstado" class="ms-2 small"></span>
        </div>

        <h2 class="mt-4">Áreas</h2>
        <div class="container-fluid">
            <div id="rowContainer">
                <ul class="list-group list-group-horizontal row d-flex justify-content-center">
                    <li class="list-group-item col-md-4 col-sm-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" class="form-control" name="Tipo[]" required>
                    </li>
                    <li class="list-group-item col-md-4 col-sm-3">
                        <label class="form-label">Ubicación</label>
                        <input type="text" class="form-control" name="Ubicacion[]" required>
                    </li>
                    <li class="list-group-item col">
                        <label class="form-label">Ancho (m)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="Ancho[]" required>
                    </li>
                    <li class="list-group-item col">
                        <label class="form-label">Altura (m)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="Altura[]" required>
                    </li>
                    <li class="list-group-item col-md-auto col-sm-1 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-agregar" aria-label="Agregar área">➕</button>
                    </li>
                </ul>
            </div>

            <div class="col-12 d-flex justify-content-center mt-3">
                <button type="submit" class="btn btn-primary" name="save">Generar Proforma</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
<script>
$(function () {
    const csrf = <?= json_encode(csrf_token()) ?>;

    // ---- Autocompletado de clientes ----
    $('#search_cliente').on('input', function () {
        const query = $(this).val();
        if (query.length < 2) { $('#results').empty(); return; }

        $.ajax({
            url: 'includes/search_user.php',
            type: 'GET',
            data: { query: query },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: html => $('#results').html(html),
            error: () => $('#results').html('<p class="text-danger mb-0">Error en la búsqueda.</p>')
        });
    });

    $(document).on('click', '.result-item', function () {
        $.ajax({
            url: 'includes/get_user.php',
            type: 'GET',
            data: { id: $(this).data('id') },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (user) {
                $('#nombresProforma').val(user.nombres);
                $('#apellidosProforma').val(user.apellidos);
                $('#celularProforma').val(user.telf);
                $('#emailProforma').val(user.email);
                $('#direccionProforma').val(user.direccion);
                $('#infoProforma').val(user.info);
                $('#search_cliente').val('');
                $('#results').empty();
            },
            error: () => $('#results').html('<p class="text-danger mb-0">No se pudo cargar el cliente.</p>')
        });
    });

    // ---- Guardar cliente sin generar proforma ----
    $('#guardarCliente').on('click', function () {
        const data = {
            csrf_token:         csrf,
            nombresProforma:    $('#nombresProforma').val(),
            apellidosProforma:  $('#apellidosProforma').val(),
            celularProforma:    $('#celularProforma').val(),
            emailProforma:      $('#emailProforma').val(),
            direccionProforma:  $('#direccionProforma').val(),
            infoProforma:       $('#infoProforma').val()
        };

        const estado = $('#clienteEstado');

        if (!data.nombresProforma || !data.apellidosProforma || !data.celularProforma
            || !data.emailProforma || !data.direccionProforma) {
            estado.removeClass('text-success').addClass('text-danger')
                  .text('Completa los campos obligatorios.');
            return;
        }

        estado.removeClass('text-danger text-success').text('Guardando...');

        $.ajax({
            url: 'includes/guardar_cliente.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: res => estado.removeClass('text-danger').addClass('text-success')
                                  .text('Cliente ' + res.accion + '.'),
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.error) || 'No se pudo guardar.';
                estado.removeClass('text-success').addClass('text-danger').text(msg);
            }
        });
    });

    // ---- Filas de área ----
    $(document).on('click', '.btn-agregar', function () {
        $('#rowContainer').append(`
            <ul class="added-row list-group list-group-horizontal row d-flex justify-content-center">
                <li class="list-group-item col-md-4 col-sm-3">
                    <label class="form-label">Tipo</label>
                    <input type="text" class="form-control" name="Tipo[]" required>
                </li>
                <li class="list-group-item col-md-4 col-sm-3">
                    <label class="form-label">Ubicación</label>
                    <input type="text" class="form-control" name="Ubicacion[]" required>
                </li>
                <li class="list-group-item col">
                    <label class="form-label">Ancho (m)</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" name="Ancho[]" required>
                </li>
                <li class="list-group-item col">
                    <label class="form-label">Altura (m)</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" name="Altura[]" required>
                </li>
                <li class="list-group-item col-md-auto col-sm-1 d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-eliminar" aria-label="Quitar área">➖</button>
                </li>
            </ul>`);
    });

    $(document).on('click', '.btn-eliminar', function () {
        $(this).closest('ul.added-row').remove();
    });
});
</script>
<?php include 'includes/bootstrap_end.php'; ?>
