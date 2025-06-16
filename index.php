<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gesti&oacute;n de Turnos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /*background: url('images/hospital.jpg') no-repeat center center fixed;*/
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
    </style>
</head>
<body>

    <div class="container dashboard-container">
        <div class="dashboard-header">
            <h1>Bienvenido al Sistema para Gesti&oacute;n de Turnos</h1>
             <center>
                <a class="navbar-brand h3" href="#" style="color: #00BFFF; font-weight: bold;">
                    Ruster
                </a>
            </center>
            <p>Gestione f&aacute;cilmente los turnos de sus empleados</p>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Crear y administrar tableros de turnos</h5>
                        <p class="card-text">Configure y administre los turnos de sus empleados de manera sencilla.</p>
                        <!--<a href="crear_turnos.php" class="btn btn-primary">Ir a Crear Turnos</a>-->
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Generar y administrar turnos manuales y autom&aacute;ticos</h5>
                        <p class="card-text">Genere los turnos de sus empleados tanto de forma manual como automatica y con posibilidad de editarlos.</p>
                        <!--<a href="ver_turnos.php" class="btn btn-primary">Ir a Ver Turnos</a>-->
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Realizar intercambios de turnos y reemplazos de personal</h5>
                        <p class="card-text">Realice intercambios de turnos entre sus empleados y reemplazos de manera efectiva.</p>
                        <!--<a href="ver_turnos.php" class="btn btn-primary">Ir a Ver Turnos</a>-->
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Crear y administrar usuarios</h5>
                        <p class="card-text">Registre y administre todos los usuarios que pueden tener acceso al sistema.</p>
                        <!--<a href="admin_usuarios.php" class="btn btn-primary">Ir a Administrar Usuarios</a>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="form_user.php" class="btn btn-primary">Ir a Login</a>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
