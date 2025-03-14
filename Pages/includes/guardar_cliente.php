<script>
$(document).ready(function() {
    // Handle "Guardar Informacion de Cliente" button click
    $('#guardarCliente').on('click', function() {
        // Collect form data
        var clientData = {
            nombresProforma: $('#nombresProforma').val(),
            apellidosProforma: $('#apellidosProforma').val(),
            celularProforma: $('#celularProforma').val(),
            emailProforma: $('#emailProforma').val(),
            direccionProforma: $('#direccionProforma').val(),
            infoProforma: $('#infoProforma').val()
        };

        // Validate fields
        if (
            !clientData.nombresProforma ||
            !clientData.apellidosProforma ||
            !clientData.celularProforma ||
            !clientData.emailProforma ||
            !clientData.direccionProforma
        ) {
            console.log('Por favor, complete todos los campos requeridos.'); // Changed to console.log
            return; // Stop the function if any required field is empty
        }

        // Send data via AJAX
        $.ajax({
            url: 'includes/a.php', // Path to the PHP function
            type: 'POST',
            data: clientData,
            success: function(response) {
                return;
            },
            error: function(xhr, status, error) {
                return;
            }
        });
    });
});
</script>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['nombresProforma'];
    $lastname = $_POST['apellidosProforma'];
    $phone = $_POST['celularProforma'];
    $email = $_POST['emailProforma'];
    $address = $_POST['direccionProforma'];
    $info = $_POST['infoProforma'];

    try {
        // Database connection
        include '../../Queries/db_connect.php';

        // Check if a user with the same name and lastname or phone already exists
        $sqlCheck = "SELECT id FROM clientes WHERE (nombres = :name AND apellidos = :lastname) OR telf = :phone";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            'name' => $name,
            'lastname' => $lastname,
            'phone' => $phone
        ]);

        if ($stmtCheck->rowCount() > 0) {
            // User exists, update the record
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            $userId = $row['id'];

            $sqlUpdate = "UPDATE clientes SET nombres = :name, apellidos = :lastname, telf = :phone, email = :email, direccion = :address, info = :info WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                'name' => $name,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'info' => $info,
                'id' => $userId
            ]);

            echo "Cliente actualizado correctamente.";
        } else {
            // User does not exist, insert a new record
            $sqlInsert = "INSERT INTO clientes (nombres, apellidos, telf, email, direccion, info) VALUES (:name, :lastname, :phone, :email, :address, :info)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                'name' => $name,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'info' => $info
            ]);

            echo "Cliente registrado correctamente.";
        }
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>
