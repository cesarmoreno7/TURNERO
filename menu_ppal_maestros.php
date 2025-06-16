<?php
    include("session.php");
    include 'obtener_valor_llave.php';
    include 'obtener_estado_llave.php';
    
    $cod_empresa = $_SESSION['cod_empresa'];
    
    $CC_estado = obtenerEstadoPorLlave('CCOSTO',$cod_empresa);
    $CC_valor = obtenerValorPorLlave('CCOSTO',$cod_empresa);
    
    $rlogo_estado = obtenerEstadoPorLlave('RLOGO',$cod_empresa);
    $rlogo_valor = obtenerValorPorLlave('RLOGO',$cod_empresa);
    
    if ($rlogo_estado == 1){
        $nombre_logo = $rlogo_valor;
    }else{
        $nombre_logo = 'logo.jpg';
    }
    
    
    $rfondo_estado = obtenerEstadoPorLlave('RFONDO',$cod_empresa);
    $rfondo_valor = obtenerValorPorLlave('RFONDO',$cod_empresa);
    
    if ($rfondo_estado == 1){
        $nombre_fondo = $rfondo_valor;
    }else{
        $nombre_fondo = 'hospital.jpg';
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Men&uacute; Principal</title>
    <!-- Incluir el CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /*background: url('images/<?php //echo $nombre_fondo; ?>') no-repeat center center fixed;*/
            background-size: cover;
            background-color: #d4edda; /* Verde claro */
        }
        .dashboard-container {
            margin-top: 50px;
            background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco semitransparente para mejorar la legibilidad */
            padding: 20px;
            border-radius: 8px;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .card-custom {
            margin-bottom: 30px;
        }
         /* Estilo para mostrar el submen¨² hacia la derecha */
        .dropdown-submenu {
            position: relative;
        }
        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
        }
          /* Logo en la barra de navegación */
        .navbar-brand img {
            height: 50px; /* Ajustar el tamaño del logo */
            width: auto;
        }
    </style>
</head>
<body>
    <?php
     if ($_SESSION['user_type'] === 'Admin'){
        echo "<strong>Bienvenido:</strong> ".$_SESSION['user_name'];
    }else{
        echo "<strong>Bienvenido:</strong> ".$_SESSION['user_name'];
        echo "<br>";
        echo "<strong> Empresa: </strong>".$_SESSION['nom_empresa'];
        echo "<br>";
        echo "<strong>Servicio: </strong>".$_SESSION['nom_serv'];
    }
    ?>
    <center>
        <a class="navbar-brand h2" href="#" style="color: #00BFFF; font-weight: bold;">
            RUSTER
        </a>
    </center>
    <!-- Barra de navegaci¨®n -->
    <!--<nav class="navbar navbar-expand-lg navbar-light bg-light" style="background-color: #00BFFF;">-->
     <nav class="navbar navbar-expand-lg" style="background-color: #87CEEB;">   
         <a class="navbar-brand" href="#">
            <!-- Espacio para el logo -->
            <img src="images/<?php echo $nombre_logo; ?>" alt="Logo"> <!-- Cambia 'logo.png' por la ruta de tu logo -->
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link dropdown-toggle" href="#" id="dropdownMaestros" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Maestros
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMaestros">
                        <div class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Principales</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="form_consulta_pais.php">Paises</a>
                                <a class="dropdown-item" href="form_consulta_depto.php">Departamentos</a>
                                <a class="dropdown-item" href="form_consulta_mpio.php">Municipios</a>
                                <a class="dropdown-item" href="form_consulta_vereda.php">Veredas</a>
                                <?php if ($_SESSION['user_type'] === 'Admin'){  ?>
                                    <a class="dropdown-item" href="form_consulta_empresa.php">Empresas</a>
                                <?php } ?>
                                <a class="dropdown-item" href="form_consulta_tipo_contr.php">Tipos de Contrato</a>
                                <a class="dropdown-item" href="form_consulta_tipo_id.php">Tipos de Identificaci&oacute;n</a>
                                <a class="dropdown-item" href="form_consulta_cargo.php">Cargos</a>
                                <a class="dropdown-item" href="form_consulta_rol.php">Roles</a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="form_consulta_servicio.php">Servicios</a>
                        <?php
                            if ($CC_estado == 1 &&  $CC_valor == "S"){
                                echo '<a class="dropdown-item" href="form_consulta_ccostos.php">Centros de Costos</a>';
                            }
                        ?>
                        <a class="dropdown-item" href="form_consulta_grupo.php">Grupos</a>
                        <a class="dropdown-item" href="form_consulta_empleados.php">Empleados</a>
                        <!--<a class="dropdown-item" href="form_consulta_empleados_servicio.php">EmpleadosxServicio</a>-->
                         <?php //if ($_SESSION['user_type'] === 'Admin'){ ?>
                            <a class="dropdown-item" href="form_consulta_personal.php">Ingresos</a>
                         <?php //} ?>
                        <a class="dropdown-item" href="form_consultar_parametros.php">Parametros</a>
                        <a class="dropdown-item" href="form_consulta_usuario.php">Usuarios</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link dropdown-toggle" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Generar Turnos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <?php
                            if ($CC_estado == 1 &&  $CC_valor == "S"){
                                echo '<a class="dropdown-item" href="form_generar_turnos_emp.php">Modo Manual</a>';
                            } else {
                                echo '<a class="dropdown-item" href="form_generar_turnos_emp_serv_gru.php">Modo Manual</a>';
                            }
                        ?>
                        <?php
                            if ($CC_estado == 1 &&  $CC_valor == "S"){
                                echo '<a class="dropdown-item" href="form_generar_turnos_aut.php">Modo Automatico</a>';
                            } else {
                                echo '<a class="dropdown-item" href="form_generar_turnos_aut_serv_gru.php">Modo Automatico</a>';
                            }
                        ?>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link dropdown-toggle" href="#" id="dropdownAdminMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Administraci&oacute;n Turnos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownAdminMenuLink">
                        <a class="dropdown-item" href="form_consulta_turnos_base.php">Turnos</a>
                        <a class="dropdown-item" href="form_create_turnos_def.php">Grabar Turnos Definitivos</a>
                        <a class="dropdown-item" href="form_activar_turnos_def.php">Activar Turnos Definitivos</a>
                        <a class="dropdown-item" href="form_inactivar_turnos_def.php">Inactivar Turnos Definitivos</a>
                        <a class="dropdown-item" href="form_aprobar_rechazar_intercambios.php">Intercambios</a>
                        <!--<a class="dropdown-item" href="form_consulta_reemplazos_def.php">Reemplazos Def.</a>-->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link dropdown-toggle" href="#" id="dropdownAdminMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Consultas
                    </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownAdminMenuLink">
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <?php
                                if ($CC_estado == 1 &&  $CC_valor == "S"){
                                    echo '<a class="dropdown-item" href="form_consulta_generar_turnos_emp.php">Modo Manual</a>';
                                } else {
                                    echo '<a class="dropdown-item" href="form_consulta_generar_turnos_emp_serv_gru.php">Modo Manual</a>';
                                }
                            ?>
                            <?php
                                if ($CC_estado == 1 &&  $CC_valor == "S"){
                                    echo '<a class="dropdown-item" href="form_consulta_generar_turnos_aut.php">Modo Automatico</a>';
                                } else {
                                    echo '<a class="dropdown-item" href="form_consulta_generar_turnos_aut_serv_gru.php">Modo Automatico</a>';
                                }
                            ?>
                        </div>
                        <a class="dropdown-item" href="form_consulta_turnos_def.php">Turnos Def.</a>
                        <a class="dropdown-item" href="form_consulta_turnos_def_grafico.php">Picos de Trabajo</a>
                        <a class="dropdown-item" href="form_horas_lab_por_turno_grafico.php">Horas Laboradas por turno</a>
                        <a class="dropdown-item" href="form_consulta_horas_turnos_def_grafico.php">Detalle Horas</a>
                        <a class="dropdown-item" href="form_emp_carga_laboral_grafico.php">Carga Laboral</a>
                        <a class="dropdown-item" href="form_consulta_intercambios_def.php">Intercambios Def.</a>
                        <!--<a class="dropdown-item" href="form_consulta_reemplazos_def.php">Reemplazos Def.</a>-->
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link dropdown-toggle" href="#" id="dropdownAdminMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Estadisticas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownAdminMenuLink">
                        <a class="dropdown-item" href="form_consulta_estadisticas.php">Horas y Novedades</a>
                        <a class="dropdown-item" href="form_consulta_estadisticas2.php">Turnos</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </nav>
   
    <!-- Incluir el JavaScript de Bootstrap y sus dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Agregar JS para habilitar el submen¨² -->
    <script>
        // Habilitar los submen¨²s
        $('.dropdown-submenu a.dropdown-toggle').on("click", function(e) {
            $(this).next('.dropdown-menu').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    </script>
</body>
</html>
