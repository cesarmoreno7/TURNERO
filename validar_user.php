<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $codigo_usu = $_POST['codigo_usu'];
    $sql = "SELECT codigo_usu, cod_empresa FROM usuarios WHERE codigo_usu = ?";
    $stmt_check = $conn->prepare($sql);
    $stmt_check->bind_param('s', $codigo_usu);
    $stmt_check->execute();
    $stmt_check->store_result();
   
    if ($stmt_check->num_rows === 1) {
        $stmt_check->bind_result($codigo_usu, $cod_empresa);
        $stmt_check->fetch();
        
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
        
        $_SESSION['cod_empresa'] = $cod_empresa;
        $_SESSION['codigo_usu'] = $codigo_usu;
        //echo "HOLA";
        header('Location: form_pwd.php');
    } else {
        header('Location: form_user.php?error=1');
    }
    
    $stmt_check->close();
}
?>
