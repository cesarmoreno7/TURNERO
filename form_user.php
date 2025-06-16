<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: auto;
            padding-top: 100px;
        }
        .input-group-text {
            cursor: pointer;
        }
    </style>
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
    <div class="login-container">
        <h2 class="text-center">TURNERO</h2>
        <br>
        <form action="validar_user.php" method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="codigo_usu">C&oacute;digo de usuario</label>
                <input type="text" class="form-control" id="codigo_usu" name="codigo_usu" placeholder="Ingrese C&oacute;digo de usuario" required>
                <div class="invalid-feedback">Por favor, ingresa tu c&oacute;digo de usuario.</div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Siguiente</button>
            <br>
            <!--<a class="nav-link" href="enviar_mail.php">Enviar Mail de Prueba</a>-->  
            <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger mt-3">Usuario incorrecto</div>';
                }
            ?>
        </form>
    </div>
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Funcionalidad para mostrar/ocultar la clave
        document.getElementById('togglePassword').addEventListener('click', function () {
            var passwordInput = document.getElementById('clave_usu');
            var eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
   <!-- Incluir FontAwesome para los iconos -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
