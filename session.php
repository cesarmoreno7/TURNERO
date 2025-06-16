<?php
     //include("sesion.php");
    session_start();
    if (!isset($_SESSION['codigo_usu'])) {
        header('Location: index.php');
        exit();
    }
    $cod_empresa = $_SESSION['cod_empresa'];
    $codigo_usu = $_SESSION['codigo_usu'];
?>
