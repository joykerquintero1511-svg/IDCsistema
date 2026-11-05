    <?php
    include '../../session-start.php';

    require_once '../../conexion.php';


    $filtro_nivel = "";
    $filtro_anio = "";
    $filtro_mes = "";
    $filtro_periodo = "";

    // Capturar el período seleccionado en el formulario.
    if (isset($_GET['periodo']) && $_GET['periodo'] !== "") {
        $filtro_periodo = intval($_GET['periodo']);
    }
    $condiciones = [];

    // Recuperar los filtros actuales.
    if (isset($_GET['nivel'])) {
        $filtro_nivel = $_GET['nivel'];
    }

    if (isset($_GET['anio'])) {
        $filtro_anio = $_GET['anio'];
    }

    if (isset($_GET['mes'])) {
        $filtro_mes = $_GET['mes'];
    }

    // Consultar las inscripciones y sus períodos académicos.
    $consulta = "
    SELECT
        inscripciones.id_inscripcion,
        personas.cedula,
        personas.nombre,
        personas.apellido,
        niveles.nivel_academico,
        periodos_academicos.nombre_periodo,
        estudiantes.id_estudiante
    FROM inscripciones
    INNER JOIN estudiantes
        ON inscripciones.id_estudiante = estudiantes.id_estudiante
    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
    INNER JOIN niveles
        ON inscripciones.id_nivel = niveles.id_nivel
    LEFT JOIN periodos_academicos
    ON inscripciones.id_periodo = periodos_academicos.id_periodo
    WHERE inscripciones.estado = 1
";

    // Consultar los niveles disponibles.
    $consulta_niveles = "
    SELECT id_nivel, nivel_academico
    FROM niveles
    ORDER BY id_nivel ASC
";

    // Consultar los períodos académicos disponibles.
    $consulta_periodos = "
    SELECT id_periodo, nombre_periodo
    FROM periodos_academicos
    ORDER BY id_periodo DESC
";

    // Consultar los años de inscripción.
    $consulta_anios = "
    SELECT DISTINCT YEAR(fecha_inscripcion) AS anio
    FROM inscripciones
    ORDER BY anio DESC
";

    // Consultar los meses de inscripción.
    $consulta_meses = "
    SELECT DISTINCT MONTH(fecha_inscripcion) AS mes
    FROM inscripciones
    ORDER BY mes ASC
";

    $resultado_niveles = $conexion->query($consulta_niveles);
    $resultado_periodos = $conexion->query($consulta_periodos);
    $resultado_anios = $conexion->query($consulta_anios);
    $resultado_meses = $conexion->query($consulta_meses);

    // Aplicar el filtro de nivel académico.
    if ($filtro_nivel !== "") {
        $condiciones[] = "inscripciones.id_nivel = " . intval($filtro_nivel);
    }

    // Aplicar el filtro de año.
    if ($filtro_anio !== "") {
        $condiciones[] = "YEAR(inscripciones.fecha_inscripcion) = " . intval($filtro_anio);
    }

    // Aplicar el filtro de mes.
    if ($filtro_mes !== "") {
        $condiciones[] = "MONTH(inscripciones.fecha_inscripcion) = " . intval($filtro_mes);
    }

    // Aplicar el filtro de período académico.
    if ($filtro_periodo !== "") {
        $condiciones[] = "inscripciones.id_periodo = " . $filtro_periodo;
    }

    // Incorporar los filtros seleccionados a la consulta.
    if (count($condiciones) > 0) {
        $consulta .= " AND ";
        $consulta .= implode(" AND ", $condiciones);
    }

    // Ordenar los estudiantes por apellido y nombre.
    $consulta .= " ORDER BY personas.apellido ASC, personas.nombre ASC, inscripciones.fecha_inscripcion DESC ";

    $resultado = $conexion->query($consulta);

    if (!$resultado) {
        die('Error en la consulta: ' . $conexion->error);
    }


    if (!$resultado) {
        die('Error en la consulta: ' . $conexion->error);
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reporte de Estudiantes - EFB</title>
        <link rel="icon" type="image/png" href="../../images/EFB.png">
        <link rel="stylesheet" href="../../css/mystyle.css">
        <link rel="stylesheet" href="/IDCsistema/css/movil.css">
    </head>

    <body>


        <main class="main-content-reporte">


           <div style="margin-bottom: 2rem; background-color: transparent; padding: 15px; border-radius: 8px; text-align: left;">

            <a href="../estudiantes/listar.php" style="display: inline-block;
            background: #1d1d1d74;
            color: #2563eb;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            transition: 0.3s;
            margin-bottom: 20px;"> Volver</a>

            <img src="../../images/EFB.png" alt="Logo Escuela de Formación Bíblica" style="
            width: 100px; margin-bottom: 15px; align-items: center; display: block; margin-left: auto; margin-right: auto;">
                <h1 style=" color: #fff bold; text-align: center; ">Reporte de Estudiantes</h1>
            </div>

            <div class="reportes-card">
                <form method="GET" class="reportes-filter-form">

                    <div class="reportes-filter-group">
                        <label for="nivel">Nivel académico:</label>
                        <select name="nivel" id="nivel" class="reportes-select">
                            <option value="">Todos</option>
                            <?php while ($nivel = $resultado_niveles->fetch_assoc()) { ?>
                                <option value="<?php echo $nivel['id_nivel']; ?>" <?php if ($filtro_nivel == $nivel['id_nivel']) {
                                                                                        echo "selected";
                                                                                    } ?>>
                                    <?php echo $nivel['nivel_academico']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="reportes-filter-group">
                        <label for="periodo">Período académico:</label>
                        <select name="periodo" id="periodo" class="reportes-select">
                            <option value="">Todos</option>

                            <?php while ($periodo = $resultado_periodos->fetch_assoc()) { ?>
                                <option value="<?php echo $periodo['id_periodo']; ?>"
                                    <?php if ($filtro_periodo == $periodo['id_periodo']) {
                                        echo "selected";
                                    } ?>>
                                    <?php echo htmlspecialchars($periodo['nombre_periodo']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="reportes-filter-group">
                        <label for="anio">Año de inscripción:</label>
                        <select name="anio" id="anio" class="reportes-select">
                            <option value="">Todos</option>
                            <?php while ($anio = $resultado_anios->fetch_assoc()) { ?>
                                <option value="<?php echo $anio['anio']; ?>" <?php if ($filtro_anio == $anio['anio']) {
                                                                                    echo "selected";
                                                                                } ?>>
                                    <?php echo $anio['anio']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="reportes-filter-group">
                        <label for="mes">Mes de inscripción:</label>
                        <select name="mes" id="mes" class="reportes-select">
                            <option value="">Todos</option>
                            <?php
                            $nombre_meses = [
                                1 => "Enero",
                                2 => "Febrero",
                                3 => "Marzo",
                                4 => "Abril",
                                5 => "Mayo",
                                6 => "Junio",
                                7 => "Julio",
                                8 => "Agosto",
                                9 => "Septiembre",
                                10 => "Octubre",
                                11 => "Noviembre",
                                12 => "Diciembre"
                            ];
                            while ($mes = $resultado_meses->fetch_assoc()) { ?>
                                <option value="<?php echo $mes['mes']; ?>" <?php if ($filtro_mes == $mes['mes']) {
                                                                                echo "selected";
                                                                            } ?>>
                                    <?php echo $nombre_meses[$mes['mes']]; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="reportes-btn-search">Buscar</button>

                </form>
            </div>

            <div class="reportes-stat-card">
                <h3>Total de estudiantes encontrados</h3>
                <h1><?php echo $resultado->num_rows; ?></h1>
            </div>

            <div class="reportes-table-wrapper">
                <table class="reportes-table">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Nivel</th>
                            <th>Período académico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['cedula']); ?></td>
                                <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>
                                <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>
                                <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                                <td>
                                    <?php
                                    if (!empty($fila['nombre_periodo'])) {
                                        echo htmlspecialchars($fila['nombre_periodo']);
                                    } else {
                                        echo "Sin período";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="reportes-link-action" href="detalle_estudiante.php?id_inscripcion=<?php echo $fila['id_inscripcion']; ?>&nivel=<?php echo urlencode($filtro_nivel); ?>&anio=<?php echo $filtro_anio; ?>&mes=<?php echo $filtro_mes; ?>">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </main>

        <?php include '../../script-seguridad.php'; ?>

    </body>

    </html>