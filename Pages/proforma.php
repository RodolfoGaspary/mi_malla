<!DOCTYPE html>
<html>
<head>
    <title>Proformar</title>
    <?php include 'includes/bootstrap_meta.php';?>
    <link rel="stylesheet" href="../CSS/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'includes/header.php';?>
<div class="container-fluid">
    <div class="col-md-2">
        <h2>Buscar Cliente</h2>
        <input class="form-control" type="text" id="search_cliente" placeholder="Nombre o Apellido...">
    </div>
    <div id="results"></div>
    <form class="row g-3" method="POST" action="">
        <h2>Informacion del Cliente</h2>
        <div class="col-md-6">
            <label for="nombresProforma" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="nombresProforma" name="nombresProforma" placeholder="Nombres" required>
        </div>
        <div class="col-md-6">
            <label for="apellidosProforma" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidosProforma" name="apellidosProforma" placeholder="Apellidos" required>
        </div>
        <div class="col-md-4">
            <label for="celularProforma" class="form-label">Celular</label>
            <input type="text" class="form-control" id="celularProforma" name="celularProforma" placeholder="Celular" required>
        </div>
        <div class="col-md-8">
            <label for="emailProforma" class="form-label">E-mail</label>
            <input type="text" class="form-control" id="emailProforma" name="emailProforma" placeholder="Correo Electronico" required>
        </div>
        <div class="col-12">
            <label for="direccionProforma" class="form-label">Direccion</label>
            <input type="text" class="form-control" id="direccionProforma" name="direccionProforma" placeholder="Calle/Avenida/Jiron 123 apartamento 123, Distrito " required>
        </div>
        <div class="mb-3">
            <label for="infoProforma" class="form-label">Informacion Adicional</label>
            <textarea class="form-control" id="infoProforma" name="infoProforma" rows="3"></textarea>
        </div>

        <div class="col-12">
        <button type="button" class="btn btn-primary" id="guardarCliente" name="guardarCliente">Guardar Informacion de Cliente</button>
        </div>
        <h2>Areas</h2>
        <div class="container-fluid">
            <div id="rowContainer">
                <!-- Initial Row -->
                <ul class="conteiner-fluid list-group list-group-horizontal mb-auto flex-fill row d-flex justify-content-center">
                    <li class="list-group-item col-md-4 col-sm-3">
                        <label for="Tipo" class="form-label">Tipo</label>
                        <input id="Tipo" type="text" class="form-control" name="Tipo[]" placeholder="Tipo" required>
                    </li>
                    <li class="list-group-item col-md-4 col-sm-3">
                        <label for="Ubicacion" class="form-label">Ubicacion</label>
                        <input id="Ubicacion" type="text" class="form-control" name="Ubicacion[]" placeholder="Ubicacion" required>
                    </li>
                    <li class="list-group-item col">
                        <label for="Ancho" class="form-label">Ancho</label>
                        <input id="Ancho" type="text" class="form-control" name="Ancho[]" placeholder="Ancho" required>
                    </li>
                    <li class="list-group-item col">
                        <label for="Altura" class="form-label">Altura</label>
                        <input id="Altura" type="text" class="form-control" name="Altura[]" placeholder="Altura" required>
                    </li>

                    <li class="list-group-item col-md-auto col-sm-1 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-agregar">➕</button>
                    </li>

                </ul>
                
            </div>

            <div class="col-12 d-flex justify-content-center mt-3">
                <button type="submit" class="btn btn-primary" name="save">Generar Proforma</but>
            </div>
        </div>
    </form>
    </div>   
    <?php include('includes/guardar_cliente.php') ?>
    <script>
        $(document).ready(function() {
            // When the user types in the search bar
            $('#search_cliente').on('input', function() {
                var query = $(this).val();
                if (query.length >= 2) { // Start searching after 2 characters
                    $.ajax({
                        url: 'includes/search_user.php',
                        type: 'GET',
                        data: { query: query },
                        success: function(response) {
                            $('#results').html(response);
                        }
                    });
                } else {
                    $('#results').html('');
                }
            });

            // When a user is selected from the results
            $(document).on('click', '.result-item', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'includes/get_user.php',
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        var user = JSON.parse(response);
                        $('#nombresProforma').val(user.nombres);
                        $('#apellidosProforma').val(user.apellidos);
                        $('#celularProforma').val(user.telf);
                        $('#emailProforma').val(user.email);
                        $('#direccionProforma').val(user.direccion);
                        $('#infoProforma').val(user.info);
                        
                        // Clear the search input and results
                        $('#search_cliente').val(''); // Clear the search input
                        $('#results').html(''); // Clear the results container
                    }
                });
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add Row
            $(document).on('click', '.btn-agregar', function() {
                var newRow = `
                    <ul class=" added-row conteiner-fluid list-group list-group-horizontal mb-auto flex-fill row d-flex justify-content-center">
                        <li class="list-group-item col-md-4 col-sm-3">
                            <label for="Tipo" class="form-label">Tipo</label>
                            <input type="text" class="form-control" name="Tipo[]" placeholder="Tipo" required>
                        </li>
                        <li class="list-group-item col-md-4 col-sm-3">
                            <label for="Ubicacion" class="form-label">Ubicacion</label>
                            <input type="text" class="form-control" name="Ubicacion[]" placeholder="Ubicacion" required>
                        </li>
                        <li class="list-group-item col">
                            <label for="Ancho" class="form-label">Ancho</label>
                            <input type="text" class="form-control" name="Ancho[]" placeholder="Ancho" required>
                        </li>
                        <li class="list-group-item col">
                            <label for="Altura" class="form-label">Altura</label>
                            <input type="text" class="form-control" name="Altura[]" placeholder="Altura" required>
                        </li>
                        <li class="list-group-item col-md-auto col-sm-1 d-flex align-items-center justify-content-center">
                            <button type="button" class="btn btn-eliminar">➖</button>
                        </li>
                    </ul>
                `;
                $('#rowContainer').append(newRow);
            });

            // Remove Row
            $(document).on('click', '.btn-eliminar', function() {
                // Only remove rows with the 'added-row' class
                if ($(this).closest('ul').hasClass('added-row')) {
                    $(this).closest('ul').remove();
                }
            });
        });
    </script>
    <?php include 'includes/bootstrap_end.php';?>