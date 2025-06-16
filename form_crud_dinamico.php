<?php
    include("session.php");
    include 'db.php';
    include 'obtener_valor_llave.php';
    include 'obtener_estado_llave.php';
    
    $CC_estado = obtenerEstadoPorLlave('CCOSTO');
    $CC_valor = obtenerValorPorLlave('CCOSTO');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Dinámico</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>CRUD Dinámico con Búsqueda</h2>

    <!-- Formulario para seleccionar la tabla -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="tabla">Seleccionar Tabla</label>
            <select class="form-control" id="tabla" name="tabla">
                <option value="pais">Paises</option>
                <option value="departamento">Departamentos</option>
                <option value="municipio">Municipios</option>
                <option value="vereda">Veredas</option>
                <option value="empresas">Empresas</option>
                <option value="tipo_contrato">Tipos de Contrato</option>
                <option value="tipo_identificacion">Tipos de Identificación</option>
                <option value="cargo">Cargos</option>
                <option value="rol">Roles</option>
                <option value="">-------------------------</option>
                <option value="servicio">Servicios</option>
                <?php
                    if ($CC_estado == 1 && $CC_valor == "S"){
                        echo '<option value="centro_costo">Centros de Costos</option>';
                    }
                ?>
                <option value="grupo">Grupos</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Consultar</button>
    </form>
    <br>

    <?php
        $tabla = isset($_POST['tabla']) ? $_POST['tabla'] : '';
        $buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

        if (!empty($tabla)) {
            // Obtener columnas de la tabla seleccionada
            $query = "DESCRIBE $tabla";
            $result = $conn->query($query);
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row;
            }

            // Consultar claves foráneas
            $foreignKeys = [];
            $fk_query = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = '$tabla' AND REFERENCED_TABLE_NAME IS NOT NULL";
            $fk_result = $conn->query($fk_query);
            while ($row = $fk_result->fetch_assoc()) {
                $foreignKeys[$row['COLUMN_NAME']] = [
                    'referenced_table' => $row['REFERENCED_TABLE_NAME'],
                    'referenced_column' => $row['REFERENCED_COLUMN_NAME']
                ];
            }

            // Mostrar formulario de búsqueda después de seleccionar la tabla
            echo '
            <form method="POST" action="">
                <input type="hidden" name="tabla" value="' . $tabla . '">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <input type="text" class="form-control mb-2" id="buscar" name="buscar" placeholder="Buscar...">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2">Buscar</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#crudModal" onclick="crearRegistro(\'' . $tabla . '\', ' . json_encode($columns) . ', ' . json_encode($foreignKeys) . ')">Crear Nuevo Registro</button>
                    </div>
                </div>
            </form>';

            // Construir la consulta con el criterio de búsqueda
            $query = "SELECT * FROM $tabla";
            if (!empty($buscar)) {
                $query .= " WHERE ";
                $conditions = [];
                foreach ($columns as $column) {
                    $conditions[] = "{$column['Field']} LIKE '%$buscar%'";
                }
                $query .= implode(' OR ', $conditions);
            }

            $result = $conn->query($query);

            // Generar la tabla dinámica
            echo "<table class='table table-striped mt-4'>";
            echo "<thead><tr>";
            foreach ($columns as $column) {
                echo "<th>{$column['Field']}</th>";
            }
            echo "<th>Acciones</th>";
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<td>{$row[$column['Field']]}</td>";
                }
                echo "<td>
                        <a href='#' class='btn btn-warning' data-toggle='modal' data-target='#crudModal' onclick='editarRegistro(\"$tabla\", ".json_encode($row).", ".json_encode($columns).", ".json_encode($foreignKeys).")'>Actualizar</a>                    
                    </td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }

        $conn->close();
    ?>

    <!-- Modal para Crear/Actualizar Registros -->
    <div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="crudModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crudModalLabel">Crear/Actualizar Registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="crudForm" method="POST" action="">
                    <div class="modal-body">
                        <!-- Campos del formulario generados dinámicamente por PHP -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    async function editarRegistro(tabla, data, columns, foreignKeys) {
        document.getElementById('crudForm').action = 'crud_action.php?action=update&tabla=' + tabla;
        let formBody = '';
        for (let column of columns) {
            const field = column['Field'];
            const value = data[field] || '';
            if (foreignKeys.hasOwnProperty(field)) {
                const fkInfo = foreignKeys[field];
                const referencedTable = fkInfo['referenced_table'];
                const referencedColumn = fkInfo['referenced_column'];
                const options = await fetch('get_foreign_key_options.php?table=' + referencedTable + '&column=' + referencedColumn)
                    .then(response => response.json());
                let selectOptions = '';
                options.forEach(option => {
                    const selected = option == value ? 'selected' : '';
                    selectOptions += `<option value="${option}" ${selected}>${option}</option>`;
                });
                formBody += `
                    <div class="form-group">
                        <label for="${field}">${field}</label>
                        <select class="form-control" id="${field}" name="${field}">
                            ${selectOptions}
                        </select>
                    </div>`;
            } else {
                formBody += `
                    <div class="form-group">
                        <label for="${field}">${field}</label>
                        <input type="text" class="form-control" id="${field}" name="${field}" value="${value}">
                    </div>`;
            }
        }
        document.querySelector('.modal-body').innerHTML = formBody;
        document.getElementById('crudModalLabel').textContent = 'Actualizar Registro';
    }

    async function crearRegistro(tabla, columns, foreignKeys) {
        document.getElementById('crudForm').action = 'crud_action.php?action=create&tabla=' + tabla;
        let formBody = '';
        for (let column of columns) {
            const field = column['Field'];
            if (foreignKeys.hasOwnProperty(field)) {
                const fkInfo = foreignKeys[field];
                const referencedTable = fkInfo['referenced_table'];
                const referencedColumn = fkInfo['referenced_column'];
                const options = await fetch('get_foreign_key_options.php?table=' + referencedTable + '&column=' + referencedColumn)
                    .then(response => response.json());
                let selectOptions = '';
                options.forEach(option => {
                    selectOptions += `<option value="${option}">${option}</option>`;
                });
                formBody += `
                    <div class="form-group">
                        <label for="${field}">${field}</label>
                        <select class="form-control" id="${field}" name="${field}">
                            ${selectOptions}
                        </select>
                    </div>`;
            } else {
                formBody += `
                    <div class="form-group">
                        <label for="${field}">${field}</label>
                        <input type="text" class="form-control" id="${field}" name="${field}">
                    </div>`;
            }
        }
        document.querySelector('.modal-body').innerHTML = formBody;
        document.getElementById('crudModalLabel').textContent = 'Crear Nuevo Registro';
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
