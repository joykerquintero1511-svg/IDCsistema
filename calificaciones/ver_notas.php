<?php

require_once '../conexion.php';

session_start();

if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin')
) {
    header("Location: ../login.php");
    exit();
}

// Consulta para llenar el select de niveles
$sql = "
    SELECT id_nivel, nivel_academico
    FROM niveles
    ORDER BY nivel_academico ASC
";

$resultado = mysqli_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Calificaciones | EFB</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../images/EFB.png">

    <style>
        /* =========================================
           ESTILO CARACTERÍSTICO Y MODERNO
        ========================================= */
        body {
            background-color: #04071d;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .contenedor-principal {
            max-width: 1100px;
            margin: 0 auto;
            background: #c0c0c1ac;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .boton-volver {
            display: inline-block;
            background: #f1f5f9;
            color: #0b2545;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            transition: 0.3s;
            margin-bottom: 20px;
        }
        .boton-volver:hover {
            background: #e2e8f0;
        }
        .cabecera {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .cabecera img {
            width: 100px; /* Tamaño del logo */
            margin-bottom: 15px;
        }
        .cabecera h1 {
            color: #0b2545; /* Azul principal */
            margin: 0;
            font-size: 28px;
        }
        .panel-filtros {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
        }
        .grid-filtros {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        .grupo-form {
            display: flex;
            flex-direction: column;
        }
        .grupo-form label {
            font-weight: 600;
            font-size: 14px;
            color: #475569;
            margin-bottom: 6px;
        }
        .grupo-form select, .grupo-form input {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border 0.3s;
        }
        .grupo-form select:focus, .grupo-form input:focus {
            border-color: #0b2545;
        }
        .botones-filtro {
            display: flex;
            gap: 10px;
        }
        .btn-buscar {
            background: #0b2545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            flex-grow: 1;
        }
        .btn-buscar:hover {
            background: #071a30;
        }
        .btn-limpiar {
            background: #ffffff;
            color: #475569;
            padding: 10px 20px;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-limpiar:hover {
            background: #f1f5f9;
        }
        .tabla-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th {
            background: #0b2545;
            color: white;
            padding: 15px;
            text-align: left;
            font-size: 14px;
            white-space: nowrap;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            color: #334155;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .btn-editar {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
            background: #eff6ff;
            padding: 5px 10px;
            border-radius: 4px;
            transition: 0.3s;
        }
        .btn-editar:hover {
            background: #dbeafe;
        }
        .badge-info {
            background: #e0e7ff;
            color: #3730a3;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 14px;
        }
        .panel-exportar {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .btn-exportar {
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-exportar:hover {
            transform: translateY(-2px);
        }
        .btn-print { background: #0b2545; }
        .btn-pdf { background: #dc2626; }
        .btn-excel { background: #16a34a; }
    </style>
</head>

<body>

    <div class="contenedor-principal">
        
        <a href="index.php" class="boton-volver">← Volver al Inicio</a>

        <div class="cabecera">
            <!-- INYECCIÓN DEL LOGO -->
            <img src="../images/EFB.png" alt="Logo Escuela de Formación Bíblica">
            <h1>Panel de Calificaciones</h1>
        </div>

        <form method="GET" class="panel-filtros">
            <div class="grid-filtros">
                
                <div class="grupo-form">
                    <label>Nivel Académico:</label>
                    <select name="id_nivel" required onchange="this.form.submit();">
                        <option value="">Seleccione un nivel</option>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                            <option value="<?php echo $fila['id_nivel']; ?>"
                                <?php if (isset($_GET['id_nivel']) && $_GET['id_nivel'] == $fila['id_nivel']){ echo "selected"; } ?>>
                                <?php echo htmlspecialchars($fila['nivel_academico']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="grupo-form">
                    <label>Evaluación:</label>
                    <select name="evaluacion">
                        <option value="">Seleccione una evaluación</option>
                        <?php
                        if (isset($_GET['id_nivel']) && $_GET['id_nivel'] != "") {
                            $id_nivel_seleccionado = $_GET['id_nivel'];
                            $sql_evaluaciones = "SELECT DISTINCT evaluacion FROM calificaciones WHERE id_nivel = '$id_nivel_seleccionado' AND evaluacion != '' ORDER BY evaluacion ASC";
                            $resultado_evaluaciones = mysqli_query($conexion, $sql_evaluaciones);

                            while ($fila_evaluacion = mysqli_fetch_assoc($resultado_evaluaciones)) {
                                $evaluacion_seleccionada = "";
                                if (isset($_GET['evaluacion']) && $_GET['evaluacion'] == $fila_evaluacion['evaluacion']) {
                                    $evaluacion_seleccionada = "selected";
                                }
                                echo '<option value="' . htmlspecialchars($fila_evaluacion['evaluacion']) . '" ' . $evaluacion_seleccionada . '>';
                                echo htmlspecialchars(ucwords(strtolower($fila_evaluacion['evaluacion'])));
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="grupo-form">
                    <label>Buscar estudiante:</label>
                    <input type="text" name="buscar" placeholder="Ej. Juan Pérez" value="<?php if(isset($_GET['buscar'])){ echo htmlspecialchars($_GET['buscar']); } ?>">
                </div>

                <div class="botones-filtro">
                    <button type="submit" class="btn-buscar">Buscar</button>
                    <a href="ver_notas.php" class="btn-limpiar">Limpiar</a>
                </div>

            </div>
        </form>

        <?php
        // Este bloque solo se ejecuta cuando el usuario pulsa Buscar
        if (isset($_GET['id_nivel']) && isset($_GET['evaluacion']) && $_GET['evaluacion'] != "") {

            $id_nivel = $_GET['id_nivel'];
            $evaluacion = trim($_GET['evaluacion']);
            $buscar = "";

            if (isset($_GET['buscar'])) {
                $buscar = trim($_GET['buscar']);
            }

            $sql_calificaciones = "
                SELECT
                    calificaciones.id_calificacion,
                    personas.nombre,
                    personas.apellido,
                    calificaciones.descripcion_nota_1,
                    calificaciones.descripcion_nota_2,
                    calificaciones.nota_1,
                    calificaciones.nota_2,
                    calificaciones.nota_final,
                    calificaciones.observacion
                FROM calificaciones
                INNER JOIN estudiantes ON calificaciones.id_estudiante = estudiantes.id_estudiante
                INNER JOIN personas ON estudiantes.id_persona = personas.id_persona
                WHERE calificaciones.id_nivel = '$id_nivel'
            ";

            if ($evaluacion !== "") {
                $sql_calificaciones .= " AND calificaciones.evaluacion = '$evaluacion'";
            }
            if ($buscar != "") {
                $sql_calificaciones .= " AND (personas.nombre LIKE '%$buscar%' OR personas.apellido LIKE '%$buscar%')";
            }
            
            $sql_calificaciones .= " ORDER BY personas.apellido ASC";
        
            $resultado_calificaciones = mysqli_query($conexion,$sql_calificaciones);

            if (!$resultado_calificaciones) {
                die("Error al consultar las calificaciones: " . mysqli_error($conexion));
            }
        ?>

        <?php if (mysqli_num_rows($resultado_calificaciones) > 0) { ?>

            <h3 style="color: #0b2545; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Resultados de Búsqueda</h3>

            <?php if ($evaluacion != "") { ?>
                <span class="badge-info">Evaluación: <?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?></span>
            <?php } ?>

            <?php
            $fila_encabezado = mysqli_fetch_assoc($resultado_calificaciones);

            $nombre_actividad_1 = "Nota 1";
            $nombre_actividad_2 = "Nota 2";

            if ($fila_encabezado['descripcion_nota_1'] != "") {
                $nombre_actividad_1 = ucwords(strtolower($fila_encabezado['descripcion_nota_1']));
            }
            if ($fila_encabezado['descripcion_nota_2'] != "") {
                $nombre_actividad_2 = ucwords(strtolower($fila_encabezado['descripcion_nota_2']));
            }

            mysqli_data_seek($resultado_calificaciones, 0);
            ?>

            <div class="tabla-responsive">
                <table>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th><?php echo htmlspecialchars($nombre_actividad_1); ?></th>
                        <th><?php echo htmlspecialchars($nombre_actividad_2); ?></th>
                        <th>Nota Final</th>
                        <th>Observación</th>
                        <th>Acción</th>
                    </tr>

                    <?php while ($fila_calificacion = mysqli_fetch_assoc($resultado_calificaciones)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucwords(strtolower($fila_calificacion['nombre']))); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(strtolower($fila_calificacion['apellido']))); ?></td>
                            <td><?php echo htmlspecialchars($fila_calificacion['nota_1']); ?></td>
                            <td>
                                <?php
                                if ($fila_calificacion['nota_2'] === null) {
                                    echo "<span style='color: #94a3b8; font-style: italic;'>No registrada</span>";
                                } else {
                                    echo htmlspecialchars($fila_calificacion['nota_2']);
                                }
                                ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($fila_calificacion['nota_final']); ?></strong></td>
                            <td>
                                <?php
                                if ($fila_calificacion['observacion'] === null || $fila_calificacion['observacion'] === "") {
                                    echo "<span style='color: #94a3b8; font-style: italic;'>Sin observación</span>";
                                } else {
                                    echo htmlspecialchars(ucwords(strtolower($fila_calificacion['observacion'])));
                                }
                                ?>
                            </td>
                            <td>
                                <a href="editar_notas.php?id_calificacion=<?php echo $fila_calificacion['id_calificacion']; ?>" class="btn-editar">Editar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <div class="panel-exportar">
                <a href="imprimir_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" target="_blank" class="btn-exportar btn-print">🖨 Imprimir</a>
                
                <a href="pdf_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" target="_blank" class="btn-exportar btn-pdf">📄 Exportar PDF</a>
                
                <a href="excel_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" class="btn-exportar btn-excel">📊 Exportar Excel</a>
            </div>

        <?php } else { ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; border-left: 4px solid #dc2626;">
                <p style="margin: 0;">No se encontraron calificaciones para ese nivel y evaluación.</p>
            </div>
        <?php } ?>

        <?php } ?>

    </div>

</body>

</html>