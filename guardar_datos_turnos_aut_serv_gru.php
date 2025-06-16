<?php

    include 'db.php';
    
    include 'obtener_valor_llave.php';
    include 'obtener_estado_llave.php';
    
    $JDIUR_valor  = obtenerValorPorLlave('JDIUR');
    $JDIUR_estado = obtenerEstadoPorLlave('JDIUR');
 
    if ($JDIUR_estado == 1) {
         
        $dato = $JDIUR_valor;
        
        // Usamos explode para dividir la cadena por el carácter ":"
        $partes = explode(":", $dato);
        
        // Asignamos los valores a variables
        $valor1 = $partes[0]; // 06
        $valor2 = $partes[1]; // 21
        
    }
    
    function esFestivo($fecha) {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM festivos WHERE fecha = ?");
        $stmt->bind_param('s', $fecha);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }
    
    function calcularHoras($inicio, $fin, $fecha) {
        
        $diurna_inicio = strtotime($fecha .' '.$valor1.':00:00');
        $diurna_fin = strtotime($fecha .' '.$valor2.':00:00');
        $inicio_ts = strtotime($fecha . ' ' . $inicio);
        $fin_ts = strtotime($fecha . ' ' . $fin);
        $jornada_diurna_inicio = strtotime($fecha .' '.$valor1.':00:00');
        $jornada_diurna_fin = strtotime($fecha .' '.$valor2.':00:00');
    
        $total_horas = ($fin_ts - $inicio_ts) / 3600;
        
        if ($total_horas < 0){
            $total_horas = $total_horas * (-1);
        }
    
        $horas_diurnas_tot = 0;
        $horas_nocturnas_tot = 0;
    
        if ($fin_ts < $inicio_ts) {
            $fin_ts += 86400;
        }
    
        $thed = 0;
        $thenoc = 0;
        
        $horas_diurnas_ordinario_ls = 0;
        $horas_nocturnas_ordinario_ls = 0;
        $horas_festivas_diurnas_ls = 0;
        $horas_festivas_nocturnas_ls = 0;
        $horas_diurnas_ordinarias_sd = 0;
        $horas_nocturnas_ordinarias_sd = 0;
        $horas_festivas_diurnas_sd = 0;
        $horas_festivas_nocturnas_sd = 0;
        $horas_diurnas_ordinarias_dlf = 0;
        $horas_nocturnas_ordinarias_dlf = 0;
        $horas_festivas_diurnas_dlf = 0;
        $horas_festivas_nocturnas_dlf = 0;
        $horas_diurnas_ordinarias_dlo_lfmo = 0;
        $horas_nocturnas_ordinarias_dlo_lfmo = 0;
        $horas_festivas_diurnas_dlo_lfmo = 0;
        $horas_festivas_nocturnas_dlo_lfmo = 0;
    
        $dia_semana = date('N', strtotime($fecha));
        $es_festivo = esFestivo($fecha);
        
        while ($inicio_ts < $fin_ts) {
            $es_diurno = ($inicio_ts >= $diurna_inicio && $inicio_ts < $diurna_fin);
            
            if ($es_diurno) {
                $horas_diurnas_tot++;
                if ($inicio_ts < $jornada_diurna_inicio || $inicio_ts >= $jornada_diurna_fin || $dia_semana >= 6 || $es_festivo) {
                    $thed++;
                }
            } else {
                $horas_nocturnas_tot++;
                if ($inicio_ts < $jornada_diurna_inicio || $inicio_ts >= $jornada_diurna_fin || $dia_semana >= 6 || $es_festivo) {
                    $thenoc++;
                }
            }
    
            if ($dia_semana >= 1 && $dia_semana <= 5) {
                if ($es_diurno) {
                    if ($es_festivo) {
                        $horas_festivas_diurnas_ls++;
                    } else {
                        $horas_diurnas_ordinario_ls++;
                    }
                } else {
                    if ($es_festivo) {
                        $horas_festivas_nocturnas_ls++;
                    } else {
                        $horas_nocturnas_ordinario_ls++;
                    }
                }
            } elseif ($dia_semana == 6) {
                if ($es_diurno) {
                    $horas_diurnas_ordinarias_sd++;
                } else {
                    $horas_nocturnas_ordinarias_sd++;
                }
            } elseif ($dia_semana == 7) {
                if ($es_diurno) {
                    $horas_festivas_diurnas_sd++;
                } else {
                    $horas_festivas_nocturnas_sd++;
                }
            }
    
            $inicio_ts += 3600;
            
            if (date('Y-m-d', $inicio_ts) !== $fecha) {
                $fecha = date('Y-m-d', $inicio_ts);
                $dia_semana = date('N', strtotime($fecha));
                $es_festivo = esFestivo($fecha);
                $diurna_inicio = strtotime($fecha . ' 06:00:00');
                $diurna_fin = strtotime($fecha . ' 21:00:00');
                $jornada_diurna_inicio = strtotime($fecha . ' 08:00:00');
                $jornada_diurna_fin = strtotime($fecha . ' 18:00:00');
            }
        }
    
        if ($dia_semana == 7) {
            $fechaSiguiente = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
            $dia_siguiente = date('N', strtotime($fechaSiguiente));
            $es_festivo_siguiente = esFestivo($fechaSiguiente);
    
            if ($dia_siguiente == 1 && $es_festivo_siguiente) {
                $horas_diurnas_ordinarias_dlf = $horas_diurnas_tot;
                $horas_nocturnas_ordinarias_dlf = $horas_nocturnas_tot;
            } elseif ($dia_siguiente == 1 && !$es_festivo_siguiente) {
                $horas_diurnas_ordinarias_dlo_lfmo = $horas_diurnas_tot;
                $horas_nocturnas_ordinarias_dlo_lfmo = $horas_nocturnas_tot;
                $horas_festivas_diurnas_dlo_lfmo = $horas_festivas_diurnas_sd;
                $horas_festivas_nocturnas_dlo_lfmo = $horas_festivas_nocturnas_sd;
            }
        } elseif ($dia_semana == 1 && esFestivo($fecha)) {
            $fechaSiguiente = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
            if (date('N', strtotime($fechaSiguiente)) == 2) {
                $horas_diurnas_ordinarias_dlo_lfmo = $horas_diurnas_tot;
                $horas_nocturnas_ordinarias_dlo_lfmo = $horas_nocturnas_tot;
                $horas_festivas_diurnas_dlo_lfmo = $horas_festivas_diurnas_ls;
                $horas_festivas_nocturnas_dlo_lfmo = $horas_festivas_nocturnas_ls;
            }
        }
    
        return [
            'total_horas' => $total_horas,
            'horas_diurnas_tot' => $horas_diurnas_tot,
            'horas_nocturnas_tot' => $horas_nocturnas_tot,
            'horas_diurnas_ordinario_ls' => $horas_diurnas_ordinario_ls,
            'horas_nocturnas_ordinario_ls' => $horas_nocturnas_ordinario_ls,
            'horas_festivas_diurnas_ls' => $horas_festivas_diurnas_ls,
            'horas_festivas_nocturnas_ls' => $horas_festivas_nocturnas_ls,
            'horas_diurnas_ordinarias_sd' => $horas_diurnas_ordinarias_sd,
            'horas_nocturnas_ordinarias_sd' => $horas_nocturnas_ordinarias_sd,
            'horas_festivas_diurnas_sd' => $horas_festivas_diurnas_sd,
            'horas_festivas_nocturnas_sd' => $horas_festivas_nocturnas_sd,
            'horas_diurnas_ordinarias_dlf' => $horas_diurnas_ordinarias_dlf,
            'horas_nocturnas_ordinarias_dlf' => $horas_nocturnas_ordinarias_dlf,
            'horas_festivas_diurnas_dlf' => $horas_festivas_diurnas_dlf,
            'horas_festivas_nocturnas_dlf' => $horas_festivas_nocturnas_dlf,
            'horas_diurnas_ordinarias_dlo_lfmo' => $horas_diurnas_ordinarias_dlo_lfmo,
            'horas_nocturnas_ordinarias_dlo_lfmo' => $horas_nocturnas_ordinarias_dlo_lfmo,
            'horas_festivas_diurnas_dlo_lfmo' => $horas_festivas_diurnas_dlo_lfmo,
            'horas_festivas_nocturnas_dlo_lfmo' => $horas_festivas_nocturnas_dlo_lfmo,
            'thed' => $thed,
            'thenoc' => $thenoc
        ];
    }

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
         // Obteniendo los datos del formulario
         $unidad_funcional = $_POST['unidad_funcional'];
         $ano = $_POST['ano'];
         $mes = $_POST['mes'];
         $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
         $dias_habiles = $_POST['dias_habiles'];
         $numero_festivos = $_POST['numero_festivos'];
         $horas_laborar = $_POST['horas_laborar'];
        
         $servicio_id = $_POST['servicio_id'];
         $centro_costo_id = $_POST['centro_costo_id'];
         $grupo_id = $_POST['grupo_id'];
     
         $empleado_id = $_POST['empleado_id'];
         $horas_contratadas = $_POST['horas_contratadas'];
         //$horas_contratadas_mes = 240;
         $horas_a_laborar_mes = $_POST['horas_a_laborar_mes'];
         $turno_dia = $_POST['turno_dia'];
         
         $cod_empresa =  $_POST['cod_empresa'];
         $codigo_usu  =  $_POST['codigo_usu'];
         
         $valid = true;
         $errores = [];
         $empleados_sin_descanso = [];
    
         // Comprobar si ya existe un registro en encabezado_turno
        /* $check_encabezado_sql = "SELECT * FROM encabezado_turno WHERE unidad_funcional = '$unidad_funcional' AND servicio_id = '$servicio_id' AND centro_costo_id = '$centro_costo_id' AND grupo_id = '$grupo_id' AND ano = '$ano' AND mes = '$mes'
         AND codigo_usu = '$codigo_usu' AND cod_empresa = $cod_empresa";
         $result = $conn->query($check_encabezado_sql);
         
         if ($result->num_rows == 0) {
             $sql = "INSERT INTO encabezado_turno (unidad_funcional, servicio_id, centro_costo_id, grupo_id, ano, mes, dias_habiles, festivos_domingos, horas_laborar_mes, codigo_usu, cod_empresa)
                             VALUES ('$unidad_funcional', '$servicio_id', '$centro_costo_id', '$grupo_id', '$ano', '$mes', '$dias_habiles', '$numero_festivos', '$horas_laborar','$codigo_usu','$cod_empresa')";
             $conn->query($sql);               
             //echo $sql;
            // $encabezado_id = $conn->insert_id;
         } */
         
         
        if (isset($_POST['actualizar_turnos'])) {
             
                if ($valid) {
                    foreach ($turno_dia as $empleado_id => $turnos) {
                         $horas_contratadas_emp = round($horas_contratadas[$empleado_id]);
                         $horas_a_laborar_mes_emp = $horas_a_laborar_mes[$empleado_id];
                         $total_hd = 0;
                         $total_hn = 0;
                         $total_hed = 0;
                         $total_hen = 0;
                         $total_reduccion_horas = 0;
                         $tiene_descanso_fin_semana = false;
                         
                        foreach ($turnos as $dia => $turno_cod) {
                            if (!empty($turno_id)) {
                                
                                 $fecha = $dia == 'next_month_1' ? date('Y-m-d', strtotime('first day of +1 month')) : $ano."-".$mes."-". str_pad($dia, 2, '0', STR_PAD_LEFT);
                                 
                                 // Calcular horas diurnas, nocturnas, extras diurnas y extras nocturnas
                                $turno_sql = "SELECT hora_inicio, hora_fin, tipo_turno FROM turnos WHERE cod_turno = '$turno_cod' AND cod_empresa = $cod_empresa";
                                $turno_result = $conn->query($turno_sql);
                                $turno_data = $turno_result->fetch_assoc();
                                
                                $hora_inicio = $turno_data['hora_inicio'];
                                $hora_fin    = $turno_data['hora_fin'];
                                $tipo_turno  = $turno_data['tipo_turno'];
                                
                                $horas = calcularHoras($hora_inicio, $hora_fin, $fecha);
                                $hd = $horas['horas_diurnas_tot'];
                                $hn = $horas['horas_nocturnas_tot'];
                                $hed = $horas['thed'];
                                $hen = $horas['thenoc'];
                                
                                if ($tipo_turno == -1) {
                                    $total_reduccion_horas += 8;
                                }
                                
                                // Comprobar si ya existe un registro para esta fecha
                                $check_sql = "SELECT * FROM detalle_turno WHERE empleado_id = '$empleado_id' AND fecha = '$fecha' AND cod_empresa = $cod_empresa";
                                $result = $conn->query($check_sql);
                                
                                if ($result->num_rows > 0) {
                                    
                                    $horas_laborar_turno = ($hd + $hn) - $total_reduccion_horas;
                                    
                                    // Actualizar el registro existente en detalle_turno
                                    $sql = "UPDATE detalle_turno SET turno_id = '$turno_id', hd = '$hd', hn = '$hn', hed = '$hed', hen = '$hen', horas_laborar_turno = $horas_laborar_turno
                                            WHERE empleado_id = $empleado_id AND fecha = '$fecha' AND turno_cod = '$turno_cod' AND cod_empresa = $cod_empresa";
                                    $conn->query($sql);    
                                    
                                     // Actualizar el registro existente en detalle_turno_def
                                    $sql_det_turno_def = "UPDATE detalle_turno_def SET turno_id = '$turno_cod', hd = '$hd', hn = '$hn', hed = '$hed', hen = '$hen', horas_laborar_turno = $horas_laborar_turno
                                            WHERE empleado_id = $empleado_id AND fecha = '$fecha' AND cod_empresa = $cod_empresa";
                                    $conn->query($sql_det_turno_def);  
                                    
                                    //ACTUALIZAR LA TABLA: horas_turnos
                                    $sql_horas_turnos = "UPDATE horas_turnos 
                                    SET cod_turno = '$turno_cod',
                                        fecha = '$fecha',
                                        total_horas = '{$horas['total_horas']}',
                                        horas_diurnas_tot = '{$horas['horas_diurnas_tot']}',
                                        horas_nocturnas_tot = '{$horas['horas_nocturnas_tot']}',
                                        horas_diurnas_ordinario_ls = '{$horas['horas_diurnas_ordinario_ls']}',
                                        horas_nocturnas_ordinario_ls = '{$horas['horas_nocturnas_ordinario_ls']}',
                                        horas_festivas_diurnas_ls = '{$horas['horas_festivas_diurnas_ls']}',
                                        horas_festivas_nocturnas_ls = '{$horas['horas_festivas_nocturnas_ls']}',
                                        horas_diurnas_ordinarias_sd = '{$horas['horas_diurnas_ordinarias_sd']}',
                                        horas_nocturnas_ordinarias_sd = '{$horas['horas_nocturnas_ordinarias_sd']}',
                                        horas_festivas_diurnas_sd = '{$horas['horas_festivas_diurnas_sd']}',
                                        horas_festivas_nocturnas_sd = '{$horas['horas_festivas_nocturnas_sd']}',
                                        horas_diurnas_ordinarias_dlf = '{$horas['horas_diurnas_ordinarias_dlf']}',
                                        horas_nocturnas_ordinarias_dlf = '{$horas['horas_nocturnas_ordinarias_dlf']}',
                                        horas_festivas_diurnas_dlf = '{$horas['horas_festivas_diurnas_dlf']}',
                                        horas_festivas_nocturnas_dlf = '{$horas['horas_festivas_nocturnas_dlf']}',
                                        horas_diurnas_ordinarias_dlo_lfmo = '{$horas['horas_diurnas_ordinarias_dlo_lfmo']}',
                                        horas_nocturnas_ordinarias_dlo_lfmo = '{$horas['horas_nocturnas_ordinarias_dlo_lfmo']}',
                                        horas_festivas_diurnas_dlo_lfmo = '{$horas['horas_festivas_diurnas_dlo_lfmo']}',
                                        horas_festivas_nocturnas_dlo_lfmo = '{$horas['horas_festivas_nocturnas_dlo_lfmo']}',
                                        thed = '{$horas['thed']}',
                                        thenoc = '{$horas['thenoc']}',
                                        cod_empresa = $cod_empresa,
                                        empleado_id = $empleado_id
                                    WHERE empleado_id = $empleado_id AND fecha = '$fecha' AND cod_empresa = $cod_empresa";
                                    $conn->query($sql_horas_turnos);  
                                    
                                    //ACTUALIZAR LA TABLA: horas_turnos_def
                                    $sql_horas_turnos_def = "UPDATE horas_turnos_def
                                    SET cod_turno = '$turno_cod',
                                        fecha = '$fecha',
                                        total_horas = '{$horas['total_horas']}',
                                        horas_diurnas_tot = '{$horas['horas_diurnas_tot']}',
                                        horas_nocturnas_tot = '{$horas['horas_nocturnas_tot']}',
                                        horas_diurnas_ordinario_ls = '{$horas['horas_diurnas_ordinario_ls']}',
                                        horas_nocturnas_ordinario_ls = '{$horas['horas_nocturnas_ordinario_ls']}',
                                        horas_festivas_diurnas_ls = '{$horas['horas_festivas_diurnas_ls']}',
                                        horas_festivas_nocturnas_ls = '{$horas['horas_festivas_nocturnas_ls']}',
                                        horas_diurnas_ordinarias_sd = '{$horas['horas_diurnas_ordinarias_sd']}',
                                        horas_nocturnas_ordinarias_sd = '{$horas['horas_nocturnas_ordinarias_sd']}',
                                        horas_festivas_diurnas_sd = '{$horas['horas_festivas_diurnas_sd']}',
                                        horas_festivas_nocturnas_sd = '{$horas['horas_festivas_nocturnas_sd']}',
                                        horas_diurnas_ordinarias_dlf = '{$horas['horas_diurnas_ordinarias_dlf']}',
                                        horas_nocturnas_ordinarias_dlf = '{$horas['horas_nocturnas_ordinarias_dlf']}',
                                        horas_festivas_diurnas_dlf = '{$horas['horas_festivas_diurnas_dlf']}',
                                        horas_festivas_nocturnas_dlf = '{$horas['horas_festivas_nocturnas_dlf']}',
                                        horas_diurnas_ordinarias_dlo_lfmo = '{$horas['horas_diurnas_ordinarias_dlo_lfmo']}',
                                        horas_nocturnas_ordinarias_dlo_lfmo = '{$horas['horas_nocturnas_ordinarias_dlo_lfmo']}',
                                        horas_festivas_diurnas_dlo_lfmo = '{$horas['horas_festivas_diurnas_dlo_lfmo']}',
                                        horas_festivas_nocturnas_dlo_lfmo = '{$horas['horas_festivas_nocturnas_dlo_lfmo']}',
                                        thed = '{$horas['thed']}',
                                        thenoc = '{$horas['thenoc']}',
                                        cod_empresa = $cod_empresa,
                                        empleado_id = $empleado_id
                                    WHERE empleado_id = $empleado_id AND fecha = '$fecha' AND cod_empresa = $cod_empresa";
                                    $conn->query($sql_horas_turnos_def); 
                                    
                                } 
                               
                            }
                        }
                 
                        echo "<script>
                            alert('¡Actualización de turnos exitosa!');
                            window.location.href = 'form_generar_turnos_aut.php';
                             </script>";
                    }
                }
                 
             //}
        } elseif (isset($_POST['generar_automatico'])) {
            
            $PTS_valor  = obtenerValorPorLlave('PTS');
            $PTS_estado = obtenerEstadoPorLlave('PTS');
            
            if ($PTS_valor === 'S' && $PTS_estado == 1) {
                
                // Validar la cantidad de registros con valor "GTAC1" y estado = 1
                $check_gtac_sql = "SELECT COUNT(*) as count FROM parametros WHERE llave like '%GTAC1%' AND estado = 1 AND cod_empresa = $cod_empresa";
                $result_gtac = $conn->query($check_gtac_sql);
                $row_gtac = $result_gtac->fetch_assoc();
                
                if ($row_gtac['count'] > 1) {
                    die("Error: Existen múltiples registros que contienen el valor 'GTAC1' y estado = 1. Solo debe existir un registro.");
                }
                
                $valorParametro = "SELECT valor FROM parametros WHERE llave = 'GTAC1' AND estado = 1 AND cod_empresa = $cod_empresa";
                $result = $conn->query($valorParametro);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $valores_GTAC = $row['valor'];
                }
    
                /*echo "Contenido del array \$valores_GTAC1:<br>";
                 print_r($valores_GTAC1);
                 echo "<br><br>";*/
                
                // Distribuir datos del array de forma semanal para cada empleado
                $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $_POST['ano']);
                
                if ($valid) {
    
                     // Consulta para obtener el maximo encabezado_id
                     $sql = "SELECT MAX(encabezado_id) AS max_encabezado_id FROM encabezado_turno WHERE cod_empresa = $cod_empresa";
                     $result = $conn->query($sql);
                     if ($result->num_rows > 0) {
                         $row = $result->fetch_assoc();
                         $max_encabezado_id = $row['max_encabezado_id'];
                     }  
                     
                    $batch_inserts = []; // Array para acumular los inserts
                    $batch_hours = [];   // Array para acumular las inserciones en horas_turnos
                     
                    /* echo "Contenido del array \$empleado_id:<br>";
                     print_r($empleado_id);
                     echo "<br><br>";*/
                    
                    //Se recorren los empleados
                    foreach ($_POST['empleado_id'] as $index => $empleado_id) {
                        
                        $horas_contratadas_emp = round($horas_contratadas[$empleado_id]);
                        $total_reduccion_horas = 0;
                        $indice_turno = 0;
                        
                        //Se recorren los días del mes
                        for ($dia = 1; $dia <= $dias_mes; $dia++) {
                            
                            $caracter = $valores_GTAC[$indice_turno];
                            $turno_actual = $caracter;
                            
                            // Calcular horas diurnas, nocturnas, horas extras diurnas y horas extras nocturnas
                            $turno_sql = "SELECT hora_inicio, hora_fin, tipo_turno FROM turnos WHERE cod_turno = '$turno_actual' AND cod_empresa = $cod_empresa";
                            $turno_result = $conn->query($turno_sql);
                            $turno_data = $turno_result->fetch_assoc();
                            
                            $hora_inicio = $turno_data['hora_inicio'];
                            $hora_fin    = $turno_data['hora_fin'];
                            $tipo_turno  = $turno_data['tipo_turno'];
                            
                            $fecha = $ano."-".$mes."-". str_pad($dia, 2, '0', STR_PAD_LEFT);
                            
                            $horas = calcularHoras($hora_inicio, $hora_fin, $fecha);
                            $hd = $horas['horas_diurnas_tot'];
                            $hn = $horas['horas_nocturnas_tot'];
                            $hed = $horas['thed'];
                            $hen = $horas['thenoc'];
                            
                            if ($tipo_turno == -1) {
                                $total_reduccion_horas += 8;
                            }
                           
                            //Aquí se debe validar que no exista el registro para el empleado y la fecha que se está procesando
                            $check_sql = "SELECT * FROM detalle_turno WHERE empleado_id = $empleado_id AND fecha = '$fecha' AND cod_empresa = $cod_empresa";
                            $result = $conn->query($check_sql);
                            
                            if ($result->num_rows == 0) {
                                
                                // Acumular valores en el array para la consulta batch
                                $horas_laborar_turno = ($hd + $hn) - $total_reduccion_horas;
                                $batch_inserts[] = "($max_encabezado_id, $empleado_id, '$fecha', '$turno_actual', $servicio_id, $centro_costo_id, $grupo_id, $hd, $hn, $hed, $hen, $horas_contratadas_emp, $horas_laborar_turno, '$codigo_usu', $cod_empresa)";
                                
                                /*echo "Contenido del array \$batch_inserts:<br>";
                                print_r($batch_inserts);
                                echo "<br><br>";*/
                                
                                // Acumular datos para la tabla horas_turnos
                                $batch_hours[] = "('$turno_actual', '$fecha', '{$horas['total_horas']}', '{$horas['horas_diurnas_tot']}', '{$horas['horas_nocturnas_tot']}', 
                                '{$horas['horas_diurnas_ordinario_ls']}', '{$horas['horas_nocturnas_ordinario_ls']}', '{$horas['horas_festivas_diurnas_ls']}', '{$horas['horas_festivas_nocturnas_ls']}', 
                                '{$horas['horas_diurnas_ordinarias_sd']}', '{$horas['horas_nocturnas_ordinarias_sd']}', '{$horas['horas_festivas_diurnas_sd']}', '{$horas['horas_festivas_nocturnas_sd']}', 
                                '{$horas['horas_diurnas_ordinarias_dlf']}', '{$horas['horas_nocturnas_ordinarias_dlf']}', '{$horas['horas_festivas_diurnas_dlf']}', '{$horas['horas_festivas_nocturnas_dlf']}', 
                                '{$horas['horas_diurnas_ordinarias_dlo_lfmo']}', '{$horas['horas_nocturnas_ordinarias_dlo_lfmo']}', '{$horas['horas_festivas_diurnas_dlo_lfmo']}', '{$horas['horas_festivas_nocturnas_dlo_lfmo']}', 
                                '{$horas['thed']}', '{$horas['thenoc']}', $cod_empresa, $empleado_id)";
                                
                                if (($dias_mes == 28 || $dias_mes == 29 || $dias_mes == 30 || $dias_mes == 31) && ($dia == $dias_mes)){
                                    
                                    $total_reduccion_horas = 0;
                                    $mes = $_POST['mes'];
                                    $mes++;
                                    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
                                    $dia = 1;
        
                                    $fecha = $ano."-".$mes."-". str_pad($dia, 2, '0', STR_PAD_LEFT);
        
                                    $indice_turno = $indice_turno + 2;
                                    $caracter = $valores_GTAC[$indice_turno];
                                    $turno_actual = $caracter;
                                    //$turno_cod = obtener_turno_cod($turno_actual,$empleado_id);  
                                    
                                    // Calcular horas diurnas, nocturnas, horas extras diurnas y horas extras nocturnas
                                    $turno_sql = "SELECT hora_inicio, hora_fin, tipo_turno FROM turnos WHERE cod_turno = '$turno_actual' AND cod_empresa = $cod_empresa";
                                    $turno_result = $conn->query($turno_sql);
                                    $turno_data = $turno_result->fetch_assoc();
                                    
                                    $hora_inicio = $turno_data['hora_inicio'];
                                    $hora_fin    = $turno_data['hora_fin'];
                                    $tipo_turno  = $turno_data['tipo_turno'];
                                
                                    $fecha = $ano."-".$mes."-". str_pad($dia, 2, '0', STR_PAD_LEFT);
                                    
                                    $horas = calcularHoras($hora_inicio, $hora_fin, $fecha);
                                    $hd = $horas['horas_diurnas_tot'];
                                    $hn = $horas['horas_nocturnas_tot'];
                                    $hed = $horas['thed'];
                                    $hen = $horas['thenoc'];
                                    
                                    if ($tipo_turno == -1) {
                                        $total_reduccion_horas += 8;
                                    }
                                    
                                     // Acumular valores en el array para la consulta batch
                                    $horas_laborar_turno = ($hd + $hn) - $total_reduccion_horas;
                                    $batch_inserts[] = "($max_encabezado_id, $empleado_id, '$fecha', '$turno_actual', $servicio_id, $centro_costo_id, $grupo_id, $hd, $hn, $hed, $hen, $horas_contratadas_emp, $horas_laborar_turno, '$codigo_usu', $cod_empresa)";
                                    
                                    /*echo "Contenido del array \$batch_inserts:<br>";
                                    print_r($batch_inserts);
                                    echo "<br><br>";*/
                                    
                                    // Acumular datos para la tabla horas_turnos
                                    $batch_hours[] = "('$turno_actual', '$fecha', '{$horas['total_horas']}', '{$horas['horas_diurnas_tot']}', '{$horas['horas_nocturnas_tot']}', 
                                    '{$horas['horas_diurnas_ordinario_ls']}', '{$horas['horas_nocturnas_ordinario_ls']}', '{$horas['horas_festivas_diurnas_ls']}', '{$horas['horas_festivas_nocturnas_ls']}', 
                                    '{$horas['horas_diurnas_ordinarias_sd']}', '{$horas['horas_nocturnas_ordinarias_sd']}', '{$horas['horas_festivas_diurnas_sd']}', '{$horas['horas_festivas_nocturnas_sd']}', 
                                    '{$horas['horas_diurnas_ordinarias_dlf']}', '{$horas['horas_nocturnas_ordinarias_dlf']}', '{$horas['horas_festivas_diurnas_dlf']}', '{$horas['horas_festivas_nocturnas_dlf']}', 
                                    '{$horas['horas_diurnas_ordinarias_dlo_lfmo']}', '{$horas['horas_nocturnas_ordinarias_dlo_lfmo']}', '{$horas['horas_festivas_diurnas_dlo_lfmo']}', '{$horas['horas_festivas_nocturnas_dlo_lfmo']}', 
                                    '{$horas['thed']}', '{$horas['thenoc']}', $cod_empresa, $empleado_id)";
                                
                                }
                            }
                            
                            if ($indice_turno < 12){
                                $indice_turno = $indice_turno + 2;
                            }else{
                                $indice_turno = 0;
                            }
                            
                        }  
                    }
                    
                    // Ejecutar todos los INSERT de detalle_turno en una sola consulta
                    if (!empty($batch_inserts)) {
                        /*echo "Contenido del array \$batch_inserts:<br>";
                                print_r($batch_inserts);
                                echo "<br><br>";*/
                        $insert_sql = "INSERT INTO detalle_turno (encabezado_id, empleado_id, fecha, turno_id, servicio_id, centro_costo_id, grupo_id, hd, hn, hed, hen, horas_contratadas, horas_laborar_turno, codigo_usu, cod_empresa) VALUES " . implode(', ', $batch_inserts);
                        //echo $insert_sql;
                        $conn->query($insert_sql);
                    }
    
                    // Ejecutar todos los INSERT de horas_turnos en una sola consulta
                    if (!empty($batch_hours)) {
                        $insert_hours_sql = "INSERT INTO horas_turnos (cod_turno, fecha, total_horas, horas_diurnas_tot, horas_nocturnas_tot, horas_diurnas_ordinario_ls, horas_nocturnas_ordinario_ls, horas_festivas_diurnas_ls, horas_festivas_nocturnas_ls, horas_diurnas_ordinarias_sd, horas_nocturnas_ordinarias_sd, horas_festivas_diurnas_sd, horas_festivas_nocturnas_sd, horas_diurnas_ordinarias_dlf, horas_nocturnas_ordinarias_dlf, horas_festivas_diurnas_dlf, horas_festivas_nocturnas_dlf, horas_diurnas_ordinarias_dlo_lfmo, horas_nocturnas_ordinarias_dlo_lfmo, horas_festivas_diurnas_dlo_lfmo, horas_festivas_nocturnas_dlo_lfmo, thed, thenoc, cod_empresa, empleado_id) VALUES " . implode(', ', $batch_hours);
                        $conn->query($insert_hours_sql);
                    }
                    
                    echo "<script>
                    alert('¡Genreación de turnos exitosa!');
                    window.location.href = 'form_generar_turnos_aut_serv_gru.php';
                     </script>";
                }
               
            } 
        }
    }
    
   
    
    $conn->close();
    
    

?>
