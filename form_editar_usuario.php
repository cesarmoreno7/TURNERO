<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_usu = $_POST['codigo_usu'];
    $clave_usu = $_POST['clave_usu'];
    $confirmar_clave_usu = $_POST['confirmar_clave_usu'];
    
    // Verificar si las contraseè´–as coinciden
    if ($clave_usu === $confirmar_clave_usu) {
        $estado_usu = $_POST['estado_usu'];
        $tipo_usu = $_POST['tipo_usu'];
        $mail_usu = $_POST['mail_usu'];

        // Encriptar la contraseè´–a con bcrypt
        $clave_usu_bcrypt = password_hash($clave_usu, PASSWORD_BCRYPT);
        
        // Actualizar los datos del usuario en la base de datos
        $sql = "UPDATE usuarios SET clave_usu=?, estado_usu=?, tipo_usu=?, mail_usu=? WHERE codigo_usu=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $clave_usu_bcrypt, $estado_usu, $tipo_usu, $mail_usu, $codigo_usu);

        if ($stmt->execute()) {
            //echo "Registro modificado exitosamente";
            header('Location: form_consulta_usuario.php');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Las contraseè´–as no coinciden. Por favor, intè´±ntelo de nuevo.";
        header('Location: form_editar_usuario.php?codigo_usu='.$codigo_usu);
    }
    $conn->close();
    exit();
}

// Obtener el usuario basado en el cè´—digo
if (isset($_GET['codigo_usu'])) {
    $codigo_usu = $_GET['codigo_usu'];
    $sql = "SELECT * FROM usuarios WHERE codigo_usu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $codigo_usu);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
}
include("session.php");
?>

<?php
    echo "<strong>Bienvenido:</strong> ".$_SESSION['user_name']." --- "."<strong> Servicio: </strong>".$_SESSION['nom_serv'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Editar Usuario</h2>
    <form action="form_editar_usuario.php" method="POST">
        <input type="hidden" name="codigo_usu" value="<?= $usuario['codigo_usu'] ?>">
        
        <div class="form-group col-md-4">
            <label for="clave_usu">Clave</label>
            <input type="password" class="form-control" id="clave_usu" name="clave_usu" required>
            <div class="input-group-append">
                <span class="input-group-text" id="togglePassword">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>
        
        <div class="form-group col-md-4">
            <label for="confirmar_clave_usu">Confirmar Clave</label>
            <input type="password" class="form-control" id="confirmar_clave_usu" name="confirmar_clave_usu" required>
            <div class="input-group-append">
                <span class="input-group-text" id="togglePassword1">
                    <i class="fas fa-eye" id="eyeIcon1"></i>
                </span>
            </div>
        </div>
        
        <div class="form-group col-md-4">
            <label for="estado_usu">Estado</label>
            <select class="form-control" id="estado_usu" name="estado_usu" required>
                <option value="Activo" <?= $usuario['estado_usu'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                <option value="Inactivo" <?= $usuario['estado_usu'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        
        <div class="form-group col-md-4">
            <label for="tipo_usu">Tipo de Usuario</label>
            <select class="form-control" id="tipo_usu" name="tipo_usu" required>
                <option value="Admin" <?= $usuario['tipo_usu'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="Empleado" <?= $usuario['tipo_usu'] == 'Empleado' ? 'selected' : '' ?>>Empleado</option>
                <option value="EmpleadoAdmin" <?= $usuario['tipo_usu'] == 'EmpleadoAdmin' ? 'selected' : '' ?>>EmpleadoAdmin</option>
            </select>
        </div>
        
        <div class="form-group col-md-4">
            <label for="mail_usu">Correo</label>
            <input type="email" class="form-control" id="mail_usu" name="mail_usu" value="<?= $usuario['mail_usu'] ?>" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar</button>
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
    
    // Funcionalidad para mostrar/ocultar la repeticiè´—n de clave
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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
