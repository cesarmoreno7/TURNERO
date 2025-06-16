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
                $horasTurno = $interval->h + ($interval->i / 60); // Incluyendo minutos como fracción de hora
    
                // Sumar las horas del turno al total
                $totalHoras += $horasTurno;
            }
        }
    
        // Devolver el total de horas
        return $totalHoras;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $codigo = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        //$llave = $_POST['llave'];
        $valor = $_POST['valor'];
        $estado = $_POST['estado'];
        $fec_ini = $_POST['fec_ini'];
        $fec_fin = $_POST['fec_fin'];
        $usuario = $_SESSION['codigo_usu'];
        
        // Obtener los valores antiguos antes de la actualización
        $sql_select = "SELECT descripcion, valor, estado, fec_ini, fec_fin FROM parametros WHERE codigo = ? AND  cod_empresa = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param('ii', $codigo, $cod_empresa);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $old_data = $result->fetch_assoc();
        $stmt_select->close();
        
        if ($old_data) {
            
            if ($llave == 'GTAC1' || $llave == 'GTAC3' ||  $llave == 'GTAC2'){
                if ($fec_ini == "" || $fec_fin == "" || $fec_ini <= $fec_fin){
                    echo "<script>
                            alert('Por favor verifique las fechas');
                            window.location.href = 'form_editar_parametro.php?codigo=' . $codigo . '&error=1';
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
        
            $sql = "UPDATE parametros SET descripcion = ?, valor = ?, estado = ?, fec_ini = ?, fec_fin = ? WHERE codigo = ? AND  cod_empresa = ?";
            $stmt_update  = $conn->prepare($sql);
            $stmt_update ->bind_param('sssssii', $descripcion, $valor, $estado, $fec_ini, $fec_fin, $codigo, $cod_empresa);
            
            //echo $sql;
        
            if ($stmt_update ->execute()) {
               
                 // Insertar registro de auditoría
                $tabla_afectada = 'parametros';
                $campo_afectado = 'Todos';
                $dato_viejo = json_encode($old_data);
                $dato_nuevo = json_encode(array(
                    'descripcion' => $descripcion,
                    'valor' => $valor,
                    'estado' => $estado,
                    'fec_ini' => $fec_ini,
                    'fec_fin' => $fec_fin
                ));
                $tipo_cambio = 'UPDATE';
    
                $sql_auditoria = "INSERT INTO auditoria (tabla_afectada, campo_afectado, dato_viejo, dato_nuevo, tipo_cambio, usuario) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_auditoria = $conn->prepare($sql_auditoria);
                $stmt_auditoria->bind_param("ssssss", $tabla_afectada, $campo_afectado, $dato_viejo, $dato_nuevo, $tipo_cambio, $usuario);
    
                if ($stmt_auditoria->execute() !== TRUE) {
                    echo "<div class='alert alert-danger'>Error al registrar la auditoría: " . $stmt_auditoria->error . "</div>";
                }
                
                echo "<script>
                        alert('¡Registro exitoso!');
                        window.location.href = 'form_consultar_parametros.php';
                      </script>";
                
                //header('Location: form_consultar_parametros.php?msg=success');
                    
            } else {
                header('Location: form_editar_parametro.php?codigo=' . $codigo . '&error=1');
            }
            
            $stmt_update->close();
            $stmt_auditoria->close();
        
        } else {
            echo "<div class='alert alert-danger'>Error: No se encontró el registro con el código proporcionado.</div>";
        }
    }
?>
