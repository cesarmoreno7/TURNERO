<?php
     include("session.php");
     include("db.php");
     include 'obtener_valor_llave.php';
     include 'obtener_estado_llave.php';
     
     $cod_empresa = $_SESSION['cod_empresa'];
     
    $rlogo_estado = obtenerEstadoPorLlave('RLOGO',$cod_empresa);
    $rlogo_valor = obtenerValorPorLlave('RLOGO',$cod_empresa);
    
    if ($rlogo_estado == 1){
        $nombre_logo = $rlogo_valor;
    }else{
        $nombre_logo = 'logo.jpg';
    }
    
    if ($rfondo_estado == 1){
        $nombre_fondo = $rfondo_valor;
    }else{
        $nombre_fondo = 'hospital.jpg';
    }
    
    $empleado_id = $_SESSION['empleado_id'];
     
     // Consulta de intercambios
    $sql = "SELECT COUNT(*) as total FROM intercambio_turnos WHERE estado = 'Pendiente' and empleado_id_solicitante = $empleado_id AND cod_empresa = $cod_empresa";
    $resultado = $conn->query($sql);
    
    // Obtener el resultado de la consulta
    $fila = $resultado->fetch_assoc();
    $filas = $fila['total'];
    
     // Consulta de reemplazos
    $sql_reemplazo = "SELECT COUNT(*) as total_reemplazos FROM reemplazo WHERE estado = 'Pendiente' and id_empleado_reemplazado = $empleado_id AND cod_empresa = $cod_empresa";
    $resultado_reemplazo = $conn->query($sql_reemplazo);
    
    // Obtener el resultado de la consulta
    $fila_reemplazo = $resultado_reemplazo->fetch_assoc();
    $filas_reemplazos = $fila_reemplazo['total_reemplazos'];
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
        .navbar-nav .nav-link {
            color: black !important; /* Color negro con !important para evitar que sea sobrescrito */
        }
    </style>
</head>
<body>
    <?php
        echo "<strong> Bienvenido: </strong> ".$_SESSION['user_name'];
        echo "<br>";
        echo "<strong> Empresa: </strong>".$_SESSION['nom_empresa'];
        echo "<br>";
        echo "<strong> Servicio: </strong>".$_SESSION['nom_serv'];
    ?>
    <center>
        <a class="navbar-brand h2" href="#" style="color: #00BFFF; font-weight: bold;">
            RUSTER
        </a>
    </center>
    <!-- Barra de navegaci贸n -->
    <nav class="navbar navbar-expand-lg" style="background-color: #87CEEB;">   
        <a class="navbar-brand" href="#">
            <!-- Espacio para el logo -->
            <img src="images/<?php echo $nombre_logo; ?>" alt="Logo"/>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link" href="form_consulta_turnos_emp.php">Mis Turnos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link" href="form_intercambios_empleado.php">Mis Intercambios</a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link dropdown-toggle font-weight-bold" class="nav-link" href="form_reemplazos_empleado.php">Mis Reemplazos</a>
                </li>-->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card card-custom">
                <div class="card-body">
                    <?php if ($filas > 0): ?>
                        <div class="alert alert-warning">
                            <strong>Notificaci&oacute;n:</strong> Tienes solicitudes de intercambio pendientes por revisión
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom">
                <div class="card-body">
                    <?php if ($filas_reemplazos > 0): ?>
                        <div class="alert alert-warning">
                            <strong>Notificaci&oacute;n:</strong> Tienes solicitudes de reemplazos pendientes por revisión
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Incluir el JavaScript de Bootstrap y sus dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


