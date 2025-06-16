<?php
    include("session.php");
    include 'db.php';
    
    $cod_empresa = $_SESSION['cod_empresa'];
     
    function obtenerTotalHorasTurnos($cadenaTurnos) {
        // Desglosar la cadena de turnos en un array
        $turnosArray = explode(',', $cadenaTurnos);
    
        $totalHoras = 0;
    
        // Recorrer cada turno en la cadena
        foreach ($turnosArray as $cod_turno) {
            // Eliminar posibles espacios en blanco
            $cod_turno = trim($cod_turno);
    
            // Consultar en la base de datos el turno correspondiente
            $sql = "SELECT hora_inicio, hora_fin FROM turnos WHERE cod_turno = ? AND cod_empresa = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("si", $cod_turno,$cod_empresa);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Si se encuentra el turno, calcular las horas
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
    
                // Convertir horas a formato datetime
                $hora_inicio = new DateTime($row['hora_inicio']);
                $hora_fin = new DateTime($row['hora_fin']);
    
                // Calcular la diferencia en horas
                $interval = $hora_inicio->diff($hora_fin);
                $horasTurno = $interval->h + ($interval->i / 60); // Incluyendo minutos como fracciÛn de hora
    
                // Sumar las horas del turno al total
                $totalHoras += $horasTurno;
            }
        }
    
        // Devolver el total de horas
        return $totalHoras;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $llave = $_POST['llave'];
        $descripcion = $_POST['descripcion'];
        $valor = $_POST['valor'];
        $fec_ini = $_POST['fec_ini'];
        $fec_fin = $_POST['fec_fin'];
        $estado = $_POST['estado'];
        
        $totalHoras = obtenerTotalHorasTurnos($valor);
        
        if ($llave == 'GTAC1' || $llave == 'GTAC3' ||  $llave == 'GTAC2'){
            if ($fec_ini == "" || $fec_fin == "" || $fec_ini <= $fec_fin){
                echo "<script>
                        alert('Por favor verifique las fechas');
                        window.location.href = 'form_registrar_parametro.php';
                      </script>";
                exit;
            }
        }    
        
        if ($llave == 'GTAC1'){
            if ($totalHoras > 48){
                echo "<script>
                        alert('La suma total de horas semanales no puede exceder 48, por favor verifique...!');
                        window.location.href = 'form_registrar_parametro.php';
                      </script>";
                exit;
            }
            // Validar que el valor tenga exactamente 7 turnos
            /*$turnos = explode('-', $valor);
            if (count($turnos) != 7) {
                echo "<script>
                        alert('El valor no tiene exactamente 7 turnos, por favor verifique...!');
                        window.location.href = 'form_registrar_parametro.php';
                      </script>";
                exit;
            }*/
            /*}else if ($llave == 'GTAC3'){
                // Validar que el valor tenga exactamente 10 turnos
                $turnos = explode('-', $valor);
                if (count($turnos) != 10) {
                    echo "<script>
                            alert('El valor no tiene exactamente 10 turnos, por favor verifique...!');
                            window.location.href = 'form_registrar_parametro.php';
                          </script>";
                    exit;
                }*/
        }else if ($llave == 'GTAC2'){
                if ($totalHoras > 96){
                    echo "<script>
                            alert('La suma total de horas quincenales no puede exceder 96, por favor verifique...!');
                            window.location.href = 'form_registrar_parametro.php';
                          </script>";
                    exit;
                }
                // Validar que el valor tenga exactamente 15 turnos
                /*$turnos = explode('-', $valor);
                if (count($turnos) != 15) {
                    echo "<script>
                            alert('El valor no tiene exactamente 15 turnos, por favor verifique...!');
                            window.location.href = 'form_registrar_parametro.php';
                          </script>";
                    exit;
                }*/
        }/*else if ($llave == 'GTMES'){
                // Validar que el valor tenga exactamente 7 o 10 o 15 turnos
                $turnos = explode('-', $valor);
                if (!in_array(count($turnos), [7, 10, 15])) {
                    echo "<script>
                            alert('El valor no tiene exactamente 7 o 10 o 15 turnos, por favor verifique...!');
                            window.location.href = 'form_registrar_parametro.php';
                          </script>";
                    exit;
                }
            }*/
    
        $sql = "INSERT INTO parametros (llave,descripcion, valor, fec_ini, fec_fin,estado,cod_empresa) VALUES ('$llave','$descripcion', '$valor', '$fec_ini', '$fec_fin','$estado','$cod_empresa')";
    
        if ($conn->query($sql) === TRUE) {
            
            //echo "<div class='alert alert-success'>Nuevo registro creado exitosamente</div>";
            
            // Insertar registro de auditorÌa
            $tabla_afectada = 'parametros';
            $campo_afectado = 'Todos';
            $dato_viejo = null;
            $dato_nuevo = json_encode(array(
                'codigo' => $codigo,
                'descripcion' => $descripcion,
                'valor' => $valor,
                'estado' => $estado,
                'fec_ini' => $fec_ini,
                'fec_fin' => $fec_fin
            ));
            $tipo_cambio = 'INSERT';
    
            $sql_auditoria = "INSERT INTO auditoria (tabla_afectada, campo_afectado, dato_viejo, dato_nuevo, tipo_cambio, usuario) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_auditoria = $conn->prepare($sql_auditoria);
            $stmt_auditoria->bind_param("ssssss", $tabla_afectada, $campo_afectado, $dato_viejo, $dato_nuevo, $tipo_cambio, $usuario);
    
            if ($stmt_auditoria->execute() !== TRUE) {
                echo "<div class='alert alert-danger'>Error al registrar la auditorÌa: " . $stmt_auditoria->error . "</div>";
            }else{
                echo "<script>
                        alert('°Registro exitoso!');
                        window.location.href = 'form_consultar_parametros.php';
                      </script>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
        $stmt_auditoria->close();
        $conn->close();
    }
    include("session.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Par…÷metros</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Crear Par&aacute;metro</h2>
        <form method="post" action="form_registrar_parametro.php">
            <div class="form-group">
                <label>Llave:</label>
                <input type="text" name="llave" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Valor:</label>
                <input type="text" name="valor" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Descripci&oacute;n:</label>
                <input type="text" name="descripcion" class="form-control" required>
            </div>
             <div class="form-group">
                <label>Estado</label>
                <select class="form-control" name="estado" required>
                    <option value="0">Inactivo</option>
                    <option value="1">Activo</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha Inicio:</label>
                <input type="date" name="fec_ini" class="form-control">
            </div>
            <div class="form-group">
                <label>Fecha Fin:</label>
                <input type="date" name="fec_fin" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
