<?php

require_once '../../conexion.php';
$filtro_estado = "";
$filtro_nivel = "";
$filtro_anio = "";
$filtro_mes = "";

$condiciones= [];


if (isset($_GET['estado'])) {
    $filtro_estado = $_GET['estado'];
}

if (isset($_GET['nivel'])) {
    $filtro_nivel = $_GET['nivel'];
}

if (isset($_GET['anio'])) {
    $filtro_anio = $_GET['anio'];
}

if (isset($_GET['mes'])) {
    $filtro_mes = $_GET['mes'];
}


//en $consulta PHP está guardando un texto SQL dentro de una variable,es otra forma usando sql
// Buscar estudiantes inscritos
$consulta = "
    SELECT
        inscripciones.id_inscripcion,
        personas.cedula,
        personas.nombre,
        personas.apellido,
        inscripciones.nivel_academico,
        inscripciones.periodo,
        inscripciones.estado,
        estudiantes.id_estudiante
    FROM inscripciones
    INNER JOIN estudiantes 
        ON inscripciones.id_estudiante = estudiantes.id_estudiante
    INNER JOIN personas 
        ON estudiantes.id_persona = personas.id_persona
";
$consulta_niveles = "
    SELECT DISTINCT nivel_academico
    FROM inscripciones
    ORDER BY nivel_academico ASC
";
$consulta_anios = "
    SELECT DISTINCT YEAR(fecha_inscripcion) AS anio
    FROM inscripciones
    ORDER BY anio DESC
";
$consulta_meses = "
    SELECT DISTINCT MONTH(fecha_inscripcion) AS mes
    FROM inscripciones
    ORDER BY mes ASC
";

$resultado_niveles = $conexion->query($consulta_niveles);
$resultado_anios = $conexion->query($consulta_anios);
$resultado_meses = $conexion->query($consulta_meses);

if ($filtro_estado !== "") {
   $condiciones[] = "inscripciones.estado = $filtro_estado";
}

if ($filtro_nivel !== "") {
   $condiciones[] = "inscripciones.nivel_academico = '$filtro_nivel'";
}

if ($filtro_anio !== "") {
   $condiciones[] = "YEAR(inscripciones.fecha_inscripcion) = $filtro_anio";
}

if ($filtro_mes !== "") {
   $condiciones[] = "MONTH(inscripciones.fecha_inscripcion) = $filtro_mes";
}

// Aqui dice Si hay al menos un filtro guardado, entonces agrega WHERE.
if(count($condiciones) > 0) { //COUNT: revisa si hay filtros guardados
    $consulta .= " WHERE ";
    $consulta .= implode (" AND ", $condiciones); // IMPLODE: une los filtros  con AND
}

$consulta .= " ORDER BY personas.apellido ASC ";

$resultado = $conexion->query($consulta);

if (!$resultado) {
    die('Error en la consulta: ' . $conexion->error);
}

?>
   <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estudiantes</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #1a1a1a;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: #111111;
            color: #ffffff;
            padding: 30px 20px;
        }

        .brand {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 40px;
            color: #ffffff;
        }

        .brand span {
            color: #0070f3;
        }

        .menu-item {
            display: block;
            color: #a0a0a0;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: #222222;
            color: #ffffff;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            background-color: #fafdff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.75rem;
            color: #111111;
        }

        .btn-logout {
            border: 1px solid #eaeaea;
            color: #666666;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-logout:hover {
            background-color: #000000;
            color: #ffffff;
        }

        .card {
            background: #ffffff;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 25px;
        }

        .card p {
            color: #666666;
            margin-bottom: 20px;
        }

        .resumen-card{
    background: #f8fbff;
    border: 1px solid #d9e8ff;
    border-left: 6px solid #0070f3;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    }

    .resumen-card h3{
    color: #555;
    font-size: 16px;
    margin-bottom: 10px;
    }

        .resumen-card h1{
        color: #0070f3;
        font-size: 40px;
        font-weight: bold;
    }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }

        th {
            background-color: #111111;
            color: #ffffff;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eaeaea;
        }

        tr:hover {
            background-color: #f3f6fb;
        }
        .filtro-form {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 20px;
}

.filtro-form label {
    font-weight: bold;
}

.filtro-form select {
    padding: 8px;
    border: 1px solid #dcdcdc;
    border-radius: 6px;
}

.filtro-form button {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    background-color: #111111;
    color: white;
    cursor: pointer;
}
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="brand">IDC<span>sistema</span></div>
        <a href="../../admin_panel.php" class="menu-item">Consola Central</a>
        <a href="../estudiantes/listar.php" class="menu-item">Gestión de Estudiantes</a>
        <a href="reporte_estudiantes.php" class="menu-item active">Reporte de Estudiantes</a>
        <a href="../../logout.php" class="menu-item">Cerrar Sesión</a>
    </div>

    <div class="main-content">

        <div class="header">
            <h1>Reporte de Estudiantes</h1>
            <a href="../../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>

 <div class="card">

    <form method="GET" class="filtro-form">

        <label for="estado">Filtrar por estado:</label>

    <select name="estado" id="estado">
        <option value="">Todos</option>
        <option value="1" <?php if($filtro_estado === "1"){ echo "selected"; } ?>>Activos</option>
        <option value="0" <?php if($filtro_estado === "0"){ echo "selected"; } ?>>Inactivos</option>
    </select>

    <label for="nivel">Nivel académico:</label>

<select name="nivel" id="nivel">

    <option value="">Todos</option>

    <?php while($nivel = $resultado_niveles->fetch_assoc()) { ?>

        <option
            value="<?php echo $nivel['nivel_academico']; ?>"

            <?php
            if($filtro_nivel == $nivel['nivel_academico']){
                echo "selected";
            }
            ?>

        >
            <?php echo $nivel['nivel_academico']; ?>
        </option>

    <?php } ?>

</select>

<label for="anio">Año de inscripción:</label>

<select name="anio" id="anio">

    <option value="">Todos</option>

    <?php while($anio = $resultado_anios->fetch_assoc()) { ?>

        <option
            value="<?php echo $anio['anio']; ?>"

            <?php
            if($filtro_anio == $anio['anio']){
                echo "selected";
            }
            ?>

        >
            <?php echo $anio['anio']; ?>
        </option>

    <?php } ?>

</select>

    <button type="submit">Buscar</button>

</form>
            <div class="resumen-card">

    <h3>Total de estudiantes encontrados</h3>

    <h1>

        <?php echo $resultado->num_rows; ?>

    </h1>

    </div>

            <table>
                <tr>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nivel</th>
                    <th>Periodo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

                <?php while($fila = $resultado->fetch_assoc()) { ?>

                    <tr>
                        <td><?php echo htmlspecialchars($fila['cedula']); ?></td>
                        <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>
                        <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>
                        <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                        <td><?php echo htmlspecialchars($fila['periodo']); ?></td>

                        
                        
                     <td>
                            <?php
                            if($fila['estado'] == 1){
                                echo "<span style='color: green; font-weight: bold;'> Activo</span>";
                            }else{
                                echo "<span style='color: red; font-weight: bold;'> Inactivo</span>";
                            }
                            ?>
                        </td>
                        <td>
                         <a href="detalle_estudiante.php?id_inscripcion=<?php echo $fila['id_inscripcion']; ?>">
                             Ver detalles
                        </a>
                             
                        </td>
                    </tr>

                <?php } ?>

            </table>
        </div>

    </div>

</body>
</html>