    <?php
    include '../../session-start.php';
    require_once '../../conexion.php';

    // Capturar los filtros enviados desde el formulario.
    $busqueda = "";
    $filtro_nivel = "";
    $filtro_periodo = "";

    if (isset($_GET['buscar'])) {
        $busqueda = trim($_GET['buscar']);
    }

    if (isset($_GET['nivel'])) {
        $filtro_nivel = intval($_GET['nivel']);
    }

    if (isset($_GET['periodo'])) {
        $filtro_periodo = intval($_GET['periodo']);
    }

    // Consultar los niveles académicos disponibles.
    $sql_niveles = "
        SELECT id_nivel, nivel_academico
        FROM niveles
        ORDER BY id_nivel ASC
    ";

    $resultado_niveles = mysqli_query($conexion, $sql_niveles);

    // Consultar los períodos académicos disponibles.
    $sql_periodos = "
        SELECT id_periodo, nombre_periodo
        FROM periodos_academicos
        ORDER BY id_periodo DESC
    ";

    $resultado_periodos = mysqli_query($conexion, $sql_periodos);

    // Preparar la consulta principal.
    $sql = "
    SELECT
        estudiantes.id_estudiante,
        personas.cedula,
        personas.nombre,
        personas.apellido,
        estudiantes.email,
        personas.telefono,
        niveles.nivel_academico,
        estudiantes.id_nivel,

        (
            SELECT periodos_academicos.nombre_periodo
            FROM inscripciones
            INNER JOIN periodos_academicos
                ON inscripciones.id_periodo = periodos_academicos.id_periodo
            WHERE inscripciones.id_estudiante = estudiantes.id_estudiante
            AND inscripciones.estado = 1
            ORDER BY inscripciones.fecha_inscripcion DESC,
                inscripciones.id_inscripcion DESC
            LIMIT 1
        ) AS nombre_periodo

        FROM estudiantes
        INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
        INNER JOIN niveles
        ON estudiantes.id_nivel = niveles.id_nivel
        WHERE 1 = 1
    ";

    // Aplicar búsqueda por nombre, apellido, cédula o correo.
    if ($busqueda !== "") {

        $busqueda_segura = mysqli_real_escape_string($conexion, $busqueda);

        $sql .= "
        AND (
            personas.nombre LIKE '%$busqueda_segura%'
            OR personas.apellido LIKE '%$busqueda_segura%'
            OR personas.cedula LIKE '%$busqueda_segura%'
            OR estudiantes.email LIKE '%$busqueda_segura%'
        )
    ";
    }

    // Aplicar filtro por nivel académico.
    if ($filtro_nivel > 0) {
        $sql .= "
        AND estudiantes.id_nivel = $filtro_nivel
    ";
    }

    // Aplicar filtro por período académico.
    // Se consideran las inscripciones activas del estudiante.
    if ($filtro_periodo > 0) {
        $sql .= "
        AND EXISTS (
            SELECT 1
            FROM inscripciones
            WHERE inscripciones.id_estudiante = estudiantes.id_estudiante
            AND inscripciones.id_periodo = $filtro_periodo
            AND inscripciones.estado = 1
        )
    ";
    }

    // Ordenar alfabéticamente por apellido y nombre.
    $sql .= "
    ORDER BY personas.apellido ASC, personas.nombre ASC
";

    // Ejecutar la consulta. a ver si hubo algun error
    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }
    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lista de Estudiantes - EFB</title>
        <link rel="icon" type="image/png" href="/IDCsistema/images/EFB.png">
        <link rel="stylesheet" href="/IDCsistema/css/mystyle.css">
        <link rel="stylesheet" href="/IDCsistema/css/movil.css">
    </head>

    <body>

        <!-- Menú lateral unificado -->
        <?php include '../sidebaradmin.php'; ?>

        <!-- Contenido Principal -->
        <main class="main-content">

            <!-- Formulario de búsqueda y filtros -->
            <div class="reportes-card">

                <form method="GET" class="reportes-filter-form">

                    <!-- Buscar estudiantes por sus datos personales -->
                    <div class="reportes-filter-group">
                        <label for="buscar">Buscar estudiante:</label>

                        <input
                            type="text"
                            name="buscar"
                            id="buscar"
                            class="reportes-select"
                            placeholder="Nombre, apellido, cédula o correo..."
                            value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>

                    <!-- Filtrar por nivel académico -->
                    <div class="reportes-filter-group">
                        <label for="nivel">Nivel académico:</label>

                        <select name="nivel" id="nivel" class="reportes-select">
                            <option value="">Todos los niveles</option>

                            <?php while ($nivel = mysqli_fetch_assoc($resultado_niveles)) { ?>
                                <option
                                    value="<?php echo $nivel['id_nivel']; ?>"
                                    <?php if ($filtro_nivel == $nivel['id_nivel']) {
                                        echo "selected";
                                    } ?>>
                                    <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <!-- Filtrar por período académico -->
                    <div class="reportes-filter-group">
                        <label for="periodo">Período académico:</label>

                        <select name="periodo" id="periodo" class="reportes-select">
                            <option value="">Todos los períodos</option>

                            <?php while ($periodo = mysqli_fetch_assoc($resultado_periodos)) { ?>
                                <option
                                    value="<?php echo $periodo['id_periodo']; ?>"
                                    <?php if ($filtro_periodo == $periodo['id_periodo']) {
                                        echo "selected";
                                    } ?>>
                                    <?php echo htmlspecialchars($periodo['nombre_periodo']); ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <!-- Botón para aplicar los filtros -->
                    <button type="submit" class="reportes-btn-search">
                        Buscar
                    </button>

                    <!-- Limpiar los filtros -->
                    <a href="listar.php"
                        class="estudiantes-limpiar-filtros"
                        style="color: #00a6ff !important; text-decoration: none !important; font-weight: bold;">
                        Limpiar filtros
                    </a>

                </form>

            </div>

            <div class="estudiantes-table-wrapper">

                <table class="estudiantes-table">


                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Nivel Académico</th>
                            <th>Período Académico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (mysqli_num_rows($resultado) > 0) { ?>

                            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                                <tr>
                                    <td><?php echo htmlspecialchars($fila['id_estudiante']); ?></td>
                                    <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>
                                    <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>
                                    <td><?php echo htmlspecialchars($fila['email']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>

                                    <td>
                                        <?php
                                        if (!empty($fila['nombre_periodo'])) {
                                            echo htmlspecialchars($fila['nombre_periodo']);
                                        } else {
                                            echo "Sin período activo";
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <a href="editar.php?id=<?php echo $fila['id_estudiante']; ?>" class="estudiantes-btn-action estudiantes-btn-edit">
                                            Editar
                                        </a>

                                        <a href="certificados_estudiante.php?id_estudiante=<?php echo $fila['id_estudiante']; ?>" class="estudiantes-btn-action estudiantes-btn-edit">
                                            Certificados
                                        </a>

                                        <a href="eliminar.php?id=<?php echo $fila['id_estudiante']; ?>" class="estudiantes-btn-action estudiantes-btn-delete" onclick="return confirm('¿Está seguro de eliminar este estudiante?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px; color: #b0b0b0;">
                                    <strong>No se encontraron estudiantes.</strong>
                                    <br>
                                    Intenta modificar la búsqueda o los filtros seleccionados.
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