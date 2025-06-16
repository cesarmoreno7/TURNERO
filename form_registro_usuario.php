<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $codigo_usu = $_POST['codigo_usu'];
    $clave_usu = $_POST['clave_usu'];
    $confirmar_clave_usu = $_POST['confirmar_clave_usu'];
    $empleado_id = $_POST['empleado_id'];
    $estado_usu = $_POST['estado_usu'];
    $tipo_usu = $_POST['tipo_usu'];
    $mail_usu = $_POST['mail_usu'];

    // Leer los códigos de empresa permitidas desde el archivo empresas.ini
    $config = parse_ini_file('empresas.ini', true);
    $cod_empresa = $config['EMPRESAS']['hospsjdr'];

    // Verificar si las contraseñas coinciden
    if ($clave_usu === $confirmar_clave_usu) {
        // Verificar si el código de usuario y el empleado_id ya existen
        $sql_check = "SELECT codigo_usu, empleado_id FROM usuarios WHERE codigo_usu = ? AND empleado_id = ? AND cod_empresa = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param('sii', $codigo_usu, $empleado_id, $cod_empresa);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Mostrar alerta si el usuario y empleado ya existen
            echo "<script>alert('El código de usuario y el empleado ya existen. Por favor, elija otro Empleado.'); window.history.back();</script>";
        } else {
            // Encriptar la contraseña con bcrypt
            $clave_usu_bcrypt = password_hash($clave_usu, PASSWORD_BCRYPT);

            // Insertar datos en la tabla
            $sql = "INSERT INTO usuarios (codigo_usu, clave_usu, empleado_id, estado_usu, tipo_usu, mail_usu, cod_empresa) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssissss', $codigo_usu, $clave_usu_bcrypt, $empleado_id, $estado_usu, $tipo_usu, $mail_usu, $cod_empresa);

            if ($stmt->execute()) {
                // Redirigir si el registro fue exitoso
                echo "<script>
                        alert('¡Registro exitoso!');
                        window.location.href = 'form_consulta_usuario.php';
                      </script>";
            } else {
                // Mostrar alerta en caso de error al ejecutar la consulta
                echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
            }

            $stmt->close();
        }

        $stmt_check->close();
    } else {
        // Mostrar alerta si las contraseñas no coinciden
        echo "<script>alert('Las contraseñas no coinciden. Por favor, inténtelo de nuevo.'); window.history.back();</script>";
    }

    $conn->close();
}

include("session.php");

// Obtener lista de empleados 
$sql_empleados = "SELECT e.empleado_id, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo 
                FROM empleados e INNER JOIN empleados_servicio es 
                ON (e.empleado_id = es.empleado_id)
                WHERE e.cod_empresa = ".$cod_empresa."
                AND es.servicio_id = ".$_SESSION['servicio_id'];
$result_empleados = $conn->query($sql_empleados);

?>

<?php
    echo "<strong>Bienvenido:</strong> ".$_SESSION['user_name']." --- "."<strong> Servicio: </strong>".$_SESSION['nom_serv'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Registrar Usuario</h2>
    <form action="form_registro_usuario.php" method="post">
        <div class="form-group">
            <label for="codigo_usu">Código de Usuario</label>
            <input type="text" class="form-control" id="codigo_usu" name="codigo_usu" required>
        </div>
        <div class="form-group">
            <label for="clave_usu">Clave</label>
            <input type="password" class="form-control" id="clave_usu" name="clave_usu" required>
            <div class="input-group-append">
                <span class="input-group-text" id="togglePassword">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label for="confirmar_clave_usu">Confirmar la Clave</label>
            <input type="password" class="form-control" id="confirmar_clave_usu" name="confirmar_clave_usu" required>
            <div class="input-group-append">
                <span class="input-group-text" id="togglePassword1">
                    <i class="fas fa-eye" id="eyeIcon1"></i>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label for="empleado_id">Empleado</label>
            <select class="form-control" id="empleado_id" name="empleado_id" required>
                <option value="">Seleccione empleado</option>
                <?php if ($result_empleados->num_rows > 0): ?>
                    <?php while($row = $result_empleados->fetch_assoc()): ?>
                        <option value="<?php echo $row['empleado_id']; ?>"><?php echo $row['nombre_completo']; ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="estado_usu">Estado</label>
            <select class="form-control" id="estado_usu" name="estado_usu" required>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_usu">Tipo de Usuario</label>
            <select class="form-control" id="tipo_usu" name="tipo_usu" required>
                <option value="Admin">Admin</option>
                <option value="EmpleadoAdmin">EmpleadoAdmin</option>
                <option value="Empleado">Empleado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="mail_usu">Correo Electrónico</label>
            <input type="email" class="form-control" id="mail_usu" name="mail_usu" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
        <a href="form_consulta_usuario.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
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

    // Funcionalidad para mostrar/ocultar la repetición de clave
    document.getElementById('togglePassword1').addEventListener('click', function () {
        var passwordInput = document.getElementById('confirmar_clave_usu');
        var eyeIcon = document.getElementById('eyeIcon1');
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
