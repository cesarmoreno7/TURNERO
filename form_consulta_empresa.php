<?php
    include 'menu_ppal_maestros.php';
?>
<?php
    include 'db.php';
    $conn->set_charset("utf8"); //Para grantizar las tildes
    
    $cod_empresa =  $_SESSION['cod_empresa'];
    
    // Búsqueda
    /*$search_query = '';
    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
    }*/
    
    // Consulta de empresas con búsqueda
    $query = "SELECT e.*, d.nom_departamento, m.nom_municipio, p.nom_pais 
              FROM empresas e
              JOIN departamento d ON e.cod_departamento = d.cod_departamento
              JOIN municipio m ON e.cod_municipio = m.cod_municipio
              JOIN pais p ON e.cod_pais = p.cod_pais
              WHERE cod_empresa = $cod_empresa";
              /*e.nom_empresa LIKE '%$search_query%' 
              OR e.nit_empresa LIKE '%$search_query%' 
              OR e.mail_empresa LIKE '%$search_query%'";*/
    
    $empresas_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Lista de Empresas</h2>

    <!-- Formulario de búsqueda -->
    <!--<form method="GET" action="form_consulta_empresa.php" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, NIT o correo" value="<?= $search_query ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>-->

    <a href="create_empresa.php" class="btn btn-success">Crear Nueva Empresa</a>

    <!-- Tabla de empresas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>NIT</th>
                <th>Nombre Empresa</th>
                <th>Tipo Empresa</th>
                <th>Dirección</th>
                <th>Teléfono 1</th>
                <th>Teléfono 2</th>
                <th>Correo</th>
                <th>País</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($empresas_result) > 0): ?>
                <?php while ($empresa = mysqli_fetch_assoc($empresas_result)): ?>
                    <tr>
                        <td><?= $empresa['cod_empresa'] ?></td>
                        <td><?= $empresa['nit_empresa'] ?></td>
                        <td><?= $empresa['nom_empresa'] ?></td>
                        <td><?= $empresa['tipo_empresa'] ?></td>
                        <td><?= $empresa['dir_empresa'] ?></td>
                        <td><?= $empresa['tel1_empresa'] ?></td>
                        <td><?= $empresa['tel2_empresa'] ?></td>
                        <td><?= $empresa['mail_empresa'] ?></td>
                        <td><?= $empresa['nom_pais'] ?></td>
                        <td><?= $empresa['nom_departamento'] ?></td>
                        <td><?= $empresa['nom_municipio'] ?></td>
                        <td>
                            <a href="update_empresa.php?cod_empresa=<?= $empresa['cod_empresa'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11">No se encontraron resultados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
</div>
</body>
</html>
