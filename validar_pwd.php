<?php

    include("session.php");
    include 'db.php';
    include 'obtener_valor_llave.php';
    include 'obtener_estado_llave.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        function verificar_contraseña($contraseña_ingresada, $hash_almacenado) {
            //Desencriptar el hash
            
            // Verificar la contraseña
            return password_verify($contraseña_ingresada, $hash_almacenado);
        }
    
        $codigo_usu = $_POST['codigo_usu'];
        $clave_usu = $_POST['clave_usu'];
    
        
        // Obtener el usuario correspondiente
        $sql = "SELECT u.tipo_usu,u.codigo_usu,u.empleado_id, e.nombre, e.apellido, emp.nom_empresa 
                FROM usuarios u JOIN empleados e ON u.empleado_id = e.empleado_id
                                JOIN empresas emp ON u.cod_empresa = emp.cod_empresa
                WHERE u.codigo_usu = ? AND u.clave_usu = ? AND u.estado_usu = 'Activo'";
        
        //echo $sql;
        
        $stmt_check = $conn->prepare($sql);
        $stmt_check->bind_param('ss', $codigo_usu, $clave_usu);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        $user = $result->fetch_assoc();
        
        $cod_empresa =  $user['cod_empresa'];
            
        // Leer los códigos de empresa permitidos desde el archivo empresas.ini
        $config = parse_ini_file('empresas.ini', true);
        $cod_empresa1 = $config['EMPRESAS']['hospsjdr'];
        $cod_empresa3 = $config['EMPRESAS']['emppruebas'];
        $cod_empresa4 = $config['EMPRESAS']['empvigilancia'];
        
        if ($cod_empresa == $cod_empresa1){
            $_SESSION['cod_empresa'] = $cod_empresa1;
        }else if($cod_empresa == $cod_empresa3){
            $_SESSION['cod_empresa'] = $cod_empresa3;
        }else if($cod_empresa == $cod_empresa4){
            $_SESSION['cod_empresa'] = $cod_empresa4;
        }
        
        if ($result->num_rows === 1 && $user['tipo_usu'] === 'Admin'){
            $_SESSION['user_id'] = $user['codigo_usu'];
            $_SESSION['user_type'] = $user['tipo_usu'];
            $_SESSION['empleado_id'] = $user['empleado_id'];
            $_SESSION['mail_usu'] = $user['mail_usu'];
            $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
            $_SESSION['nom_empresa'] = $user['nom_empresa'];
            header('Location: menu_ppal_admin.php');
        }else{
    
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                //$hash_almacenado = $user['clave_usu'];
                
                //echo $hash_almacenado;
        
                // Verificar la contraseña ingresada
                //if (verificar_contraseña('$clave_usu', $hash_almacenado)) {
                    $sql = "
                        SELECT u.codigo_usu, u.tipo_usu, u.empleado_id, e.nombre, e.apellido, es.servicio_id, es.centro_costo_id, es.grupo_id, serv.nombre as nom_serv, u.cod_empresa, emp.tipo_empresa, emp.nom_empresa
                        FROM usuarios u
                        JOIN empleados e ON u.empleado_id = e.empleado_id
                        JOIN empleados_servicio es ON e.empleado_id = es.empleado_id
                        JOIN servicio serv ON es.servicio_id = serv.servicio_id
                        JOIN empresas emp ON u.cod_empresa = emp.cod_empresa
                        WHERE u.codigo_usu = ? AND u.clave_usu = ? AND u.estado_usu = 'Activo' AND es.estado = 'Activo' LIMIT 1
                    ";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ss', $codigo_usu, $clave_usu);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    //echo $result->num_rows;
        
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        $_SESSION['user_id'] = $user['codigo_usu'];
                        $_SESSION['user_type'] = $user['tipo_usu'];
                        $_SESSION['empleado_id'] = $user['empleado_id'];
                        $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
                        $_SESSION['servicio_id'] = $user['servicio_id'];
                        $_SESSION['centro_costo_id'] = $user['centro_costo_id'];
                        $_SESSION['grupo_id'] = $user['grupo_id'];
                        $_SESSION['nom_serv'] = $user['nom_serv'];
                        $_SESSION['nom_empresa'] = $user['nom_empresa'];
                        $_SESSION['tipo_empresa'] = $user['tipo_empresa'];
                        $_SESSION['mail_usu'] = $user['mail_usu'];
                        
                        $empleado_id = "";
                        
                        $empleado_id = $user['empleado_id'];
                        
                        $MCT_estado = obtenerEstadoPorLlave('ACT',$cod_empresa);
                        $MCT_valor = obtenerValorPorLlave('ACT',$cod_empresa);
                        
                        //echo $MCT_valor;
                        
                        //$rdo = strpos((string)$MCT_valor, (string)$empleado_id);
                        
                        //echo $rdo;*/
                        
                       //echo $empleado_id."--".$rdo."--".$MCT_estado;
        
                        // Verificar si el empleado_id está en la cadena MCT_valor
                        if ($user['tipo_usu'] == 'EmpleadoAdmin' && strpos((string)$MCT_valor, (string)$empleado_id) >= 0 && $MCT_estado == 1) {
                            header('Location: menu_ppal_admin.php');
                        }else if ($user['tipo_usu'] == 'Empleado' && strpos((string)$MCT_valor, (string)$empleado_id) == '' && $MCT_estado == 1) {
                            header('Location: menu_ppal_emp.php');
                        }
                        
                        exit();
                    /*} else {
                        header('Location: form_pwd.php?error=1');
                        exit();
                    }*/
                } else {
                    //echo "Clave incorrecta";
                    header('Location: form_pwd.php?error=1');
                }
            } else {
                //echo "Clave no encontrada";
                header('Location: form_pwd.php?error=1');
            }
        }
        $stmt_check->close();
        $conn->close();
    }
?>
