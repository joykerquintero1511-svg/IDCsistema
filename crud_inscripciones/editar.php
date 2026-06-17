<?php
require_once('../conexion.php');

// Verificar si llegó el ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se especificó qué inscripción editar.");
}

$id = $_GET['id'];

// Validar que el ID sea un número
if (!is_numeric($id)) {
    die("Error: ID inválido.");
}

// Procesar el formulario cuando se envía (ACTUALIZAR)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nivel_academico = $_POST['nivel_academico'];
    $fecha_inscripcion = $_POST['fecha_inscripcion'];
    $periodo = $_POST['periodo'];
    $estado = $_POST['estado'];

    $sql = "UPDATE inscripciones SET 
            nivel_academico = '$nivel_academico',
            fecha_inscripcion = '$fecha_inscripcion',
            periodo = '$periodo',
            estado = '$estado'
            WHERE id_inscripcion = $id";
    
    if (mysqli_query($conexion, $sql)) {
        header("Location: listar.php");
        exit;
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}

// Cargar los datos actuales de la inscripción (para mostrar en el formulario)
$sql = "SELECT * FROM inscripciones WHERE id_inscripcion = $id";
$resultado = ejecutarConsulta($conexion, $sql);
$fila = mysqli_fetch_assoc($resultado);

if (!$fila) {
    die("Inscripción no encontrada");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Inscripción</title>
    <link rel="stylesheet" href="../css/vendor.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .formulario-inscripcion {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .formulario-inscripcion label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }
        .formulario-inscripcion input,
        .formulario-inscripcion select {
            width: 100%;
            padding: 10px;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #fff;
        }
        .formulario-inscripcion button {
            background: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        .formulario-inscripcion button:hover {
            background: #0056b3;
        }
        .btn-cancelar {
            display: inline-block;
            text-align: center;
            width: 100%;
            margin-top: 1rem;
            padding: 12px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .btn-cancelar:hover {
            background: #5a6268;
        }
        h1 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 2rem;
        }
        .formulario-inscripcion select option {
    background: #1e1e1e;
    color: #ffffff;
}
    </style>
</head>
<body class="s-pagewrap ss-home">

<!-- HEADER (menú del proyecto) -->
<header class="s-header">
    <div class="container s-header__content">
        <div class="s-header__block">
            <div class="header-logo">
                <a class="logo" href="../index.php">
                    <img src="../images/EFB.png" alt="logoEFB">
                </a>
            </div>
            <a class="header-menu-toggle" href="#0"><span>Menú</span></a>
        </div>
        <nav class="header-nav">
            <ul class="header-nav__links">
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="index.php">Inscripciones</a></li>
                <li><a href="registrar.php">Nueva Inscripción</a></li>
            </ul>
            <div class="header-contact">
                <a href="../logout.php" class="header-contact__num btn" style="background: #dc3545; color: white;">
                    Cerrar Sesión
                </a>
            </div>
        </nav>
    </div>
</header>

<!-- CONTENIDO PRINCIPAL -->
<main class="s-content">
    <section class="container" style="padding: 4rem 2rem;">
        <div class="row">
            <div class="column xl-12">
                <h1>Editar Inscripción</h1>
                <form method="POST" class="formulario-inscripcion">
                    <label>Nivel Académico:</label>
                  <select name="nivel_academico" required>
                    <?php
                     $sql_niveles = "SELECT nivel_academico FROM niveles ORDER BY nivel_academico";
                    $result_niveles = ejecutarConsulta($conexion, $sql_niveles);
                    while($nivel = mysqli_fetch_assoc($result_niveles)):
                    ?>
        <option value="<?php echo $nivel['nivel_academico']; ?>" 
            <?php echo ($nivel['nivel_academico'] == $fila['nivel_academico']) ? 'selected' : ''; ?>>
            <?php echo $nivel['nivel_academico']; ?>
        </option>
    <?php endwhile; ?>
</select>

                    <label>Fecha Inscripción:</label>
                    <input type="date" name="fecha_inscripcion" value="<?php echo $fila['fecha_inscripcion']; ?>" required>

                    <label>Periodo:</label>
                    <input type="text" name="periodo" value="<?php echo $fila['periodo']; ?>" required>

                    <label>Estado:</label>
                    <select name="estado">
                        <option value="1" <?php echo ($fila['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($fila['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                    </select>

                    <button type="submit"> Actualizar</button>
                    <a href="listar.php" class="btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer style="text-align: center; padding: 3rem 0; background: #0c0c0c; color: rgba(255,255,255,0.4); margin-top: 3rem;">
    <p>© <?php echo date('Y'); ?> Escuela de Formación Bíblica. Todos los derechos reservados.</p>
</footer>

<script src="../js/plugins.js"></script>
<script src="../js/main.js"></script>
</body>
</html>