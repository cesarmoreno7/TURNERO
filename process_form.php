<?php
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "prodsoft_turnero";
    $password = "turnero2024";
    $dbname = "prodsoft_turnero";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obteniendo los datos del formulario
    $unidad_funcional = $_POST['unidad_funcional'];
    $servicio_id = $_POST['servicio'];
    $centro_costo_id = $_POST['centro_costo'];
    $grupo_id = $_POST['grupo'];
    $ano = $_POST['ano'];
    $mes = $_POST['mes'];
    $dias_habiles = $_POST['dias_habiles'];
    $numero_festivos = $_POST['numero_festivos'];
    $horas_laborar = $_POST['horas_laborar'];

    // Aquí puedes insertar los datos en la base de datos o hacer cualquier procesamiento adicional
    // Ejemplo de inserción:
    $sql = "INSERT INTO encabezado_turno (unidad_funcional, servicio, centro_costo, grupo, ano, mes, dias_habiles, numero_festivos, horas_laborar)
    VALUES ('$unidad_funcional', '$servicio', '$centro_costo', '$grupo', '$ano', '$mes', '$dias_habiles', '$numero_festivos', '$horas_laborar')";

    if ($conn->query($sql) === TRUE) {
        echo "Datos guardados correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
?>
