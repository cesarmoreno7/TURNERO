<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'prodsoft_turnero', 'turnero2024', 'prodsoft_turnero');
if ($conn->connect_error) {
    die("Conexi¨®n fallida: " . $conn->connect_error);
}

// Procesar los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $servicio_id = 2;
    $centro_costo_id = 3;
    $grupo_id = 4;

    $empleado_id = $_POST['empleado_id'];
    $horas_contratadas = $_POST['horas_contratadas'];
    //$horas_a_laborar_mes = $_POST['horas_a_laborar_mes'];
    $horas_a_laborar_mes = 240;
    $turno_dia = $_POST['turno_dia'];
    
    $valid = true;
    $errores = [];
    
    /*foreach ($turno_dia as $empleado_id => $turnos) {
        foreach ($turnos as $dia => $turno_id) {
            if (!empty($turno_id)) {
                $fecha = $dia == 'next_month_1' ? date('Y-m-d', strtotime('first day of +1 month')) : date('Y-m-') . str_pad($dia, 2, '0', STR_PAD_LEFT);
                
                // Validar que no haya horarios duplicados
                $sql = "SELECT * FROM detalle_turno WHERE empleado_id = ? AND fecha = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    die("Error al preparar la consulta: " . $conn->error);
                }
                $stmt->bind_param("is", $empleado_id, $fecha);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $valid = false;
                    $errores[] = "El empleado $empleado_id ya tiene un horario registrado para la fecha $fecha.";
                }
                $stmt->close();
            }
        }
    }*/
    
    if ($valid) {
        foreach ($turno_dia as $empleado_id => $turnos) {
            $horas_contratadas_emp = $horas_contratadas[$empleado_id];
            $horas_a_laborar_mes_emp = $horas_a_laborar_mes[$empleado_id];
            $total_hd = 0;
            $total_hn = 0;
            $total_hed = 0;
            $total_hen = 0;
            $total_reduccion_horas = 0;
            $horas_a_laborar_mes_emp = 240; //por ahora se le asigna 240
            
            foreach ($turnos as $dia => $turno_id) {
                if (!empty($turno_id)) {
                    $fecha = $dia == 'next_month_1' ? date('Y-m-d', strtotime('first day of +1 month')) : date('Y-m-') . str_pad($dia, 2, '0', STR_PAD_LEFT);
                    
                    // Obtener los valores de horas del turno seleccionado
                    $turnos_sql = "SELECT t.tipo_turno, ht.horas_diurnas_tot, ht.horas_nocturnas_tot, ht.thed, ht.thenoc 
                                   FROM horas_turnos ht INNER JOIN turnos t on(t.cod_turno = ht.cod_turno) WHERE ht.id_turno = ?";
                    $stmt = $conn->prepare($turnos_sql);
                    if ($stmt === false) {
                        die("Error al preparar la consulta: " . $conn->error);
                    }
                    $stmt->bind_param("i", $turno_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $turno_data = $result->fetch_assoc();
                    $stmt->close();
                    
                    $hd = $turno_data['horas_diurnas_tot'];
                    $hn = $turno_data['horas_nocturnas_tot'];
                    $hed = $turno_data['thed'];
                    $hen = $turno_data['thenoc'];
                    
                    $total_hd += $hd;
                    $total_hn += $hn;
                    $total_hed += $hed;
                    $total_hen += $hen;

                    if ($turno_data['tipo_turno'] == -1) {
                        $total_reduccion_horas += 8;
                    }
                    
                    // Comprobar si ya existe un registro para esta fecha
                    $check_sql = "SELECT * FROM detalle_turno WHERE empleado_id = ? AND fecha = ?";
                    $stmt = $conn->prepare($check_sql);
                    if ($stmt === false) {
                        die("Error al preparar la consulta: " . $conn->error);
                    }
                    $stmt->bind_param("is", $empleado_id, $fecha);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // Actualizar el registro existente
                        $sql = "UPDATE detalle_turno SET turno_id = ?, servicio_id = ?, centro_costo_id = ?, grupo_id = ?, hd = ?, hn = ?, hed = ?, hen = ?, horas_contratadas = ?, horas_laborar_mes = ? 
                                WHERE empleado_id = ? AND fecha = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            die("Error al preparar la consulta: " . $conn->error);
                        }
                        $stmt->bind_param("issiisiiiiss", $turno_id, $servicio_id, $centro_costo_id, $grupo_id, $hd, $hn, $hed, $hen, $horas_contratadas_emp, $horas_a_laborar_mes_emp, $empleado_id, $fecha);
                    } else {
                        // Insertar un nuevo registro
                        $sql = "INSERT INTO detalle_turno (empleado_id, fecha, turno_id, servicio_id, centro_costo_id, grupo_id, hd, hn, hed, hen, horas_contratadas, horas_laborar_mes) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            die("Error al preparar la consulta: " . $conn->error);
                        }
                        $stmt->bind_param("issiisiiiisi", $empleado_id, $fecha, $turno_id, $servicio_id, $centro_costo_id, $grupo_id, $hd, $hn, $hed, $hen, $horas_contratadas_emp, $horas_a_laborar_mes_emp);
                    }

                    // Ejecutar la consulta
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // Actualizar los campos hd, hn, hed, hen en la base de datos
            $update_sql = "UPDATE detalle_turno SET horas_laborar_mes = horas_laborar_mes - ? WHERE empleado_id = ? AND encabezado_id = LAST_INSERT_ID()";
            $stmt = $conn->prepare($update_sql);
            if ($stmt === false) {
                die("Error al preparar el Update: " . $conn->error);
            }
            $stmt->bind_param("iiiisi", $total_reduccion_horas, $empleado_id);
            
            $stmt->execute();
            $stmt->close();
        }

        echo "Los turnos han sido guardados exitosamente.";
    } else {
        foreach ($errores as $error) {
            echo "<p>$error</p>";
        }
    }
}

$conn->close();
?>
