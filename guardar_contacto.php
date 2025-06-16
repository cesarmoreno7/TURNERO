<?php
    // Datos de conexión a la base de datos
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes

    // Verificar si se envió el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $nombre = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $celular = trim($_POST["celular"]);
        $mensaje = trim($_POST["message"]);

        // Validar que los campos no estén vacíos
        if (!empty($nombre) && !empty($email) && !empty($celular) && !empty($mensaje)) {
            // Preparar la consulta SQL
            $stmt = $conn->prepare("INSERT INTO contactos (nombre, email, celular, mensaje) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $celular, $mensaje);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "Contacto guardado exitosamente.";
            } else {
                echo "Error al guardar el contacto: " . $stmt->error;
            }

            // Cerrar la consulta
            $stmt->close();
        } else {
            echo "Todos los campos son obligatorios.";
        }
    }

    // Cerrar conexión
    $conn->close();
?>
