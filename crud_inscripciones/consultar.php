<?php
require_once('../conexion.php');

// Verificar si llegó el ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se especificó qué inscripción consultar.");
}

$id = $_GET['id'];
if (!is_numeric($id)) {
    die("Error: ID inválido.");
}
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
    <title>Consultar Inscripción</title>
    <link rel="stylesheet" href="../css/vendor.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Tus estilos específicos para tarjeta y botones */
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .campo {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 180px;
            color: #fff;
        }
        .valor {
            color: #e0e0e0;
        }
        .btn-accion {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            margin-right: 8px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn-volver {
            font-size: 1.2rem;
            background:#6f42c1;
            color: white;
        }
        .btn-volver:hover {
            background: #5a6268;
        }
        .btn-editar {
            font-size: 1.2rem;
            background: #007bff;
            color: white;
        }
        .btn-editar:hover {
            background: #0056b3;
        }
    </style>
</head>
<body class="s-pagewrap ss-home">

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
                <li><a href="index.php" class="active">Inscripciones</a></li>
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

<main class="s-content">
    <section class="container" style="padding: 4rem 2rem;">
        <div class="row">
            <div class="column xl-12">
                <h1>🔍 Detalle de Inscripción</h1>
                <div class="card">
                    <div class="campo">
                        <span class="label">ID:</span>
                        <span class="valor"><?php echo $fila['id_inscripcion']; ?></span>
                    </div>
                    <div class="campo">
                        <span class="label">ID Estudiante:</span>
                        <span class="valor"><?php echo $fila['id_estudiante']; ?></span>
                    </div>
                    <div class="campo">
                        <span class="label">Nivel Académico:</span>
                        <span class="valor"><?php echo htmlspecialchars($fila['nivel_academico']); ?></span>
                    </div>
                    <div class="campo">
                        <span class="label">Fecha Inscripción:</span>
                        <span class="valor"><?php echo date('d/m/Y', strtotime($fila['fecha_inscripcion'])); ?></span>
                    </div>
                    <div class="campo">
                        <span class="label">Periodo:</span>
                        <span class="valor"><?php echo $fila['periodo']; ?></span>
                    </div>
                    <div class="campo">
                        <span class="label">Estado:</span>
                        <span class="valor"><?php echo $fila['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></span>
                    </div>
                </div>
                <a href="listar.php" class="btn-accion btn-volver">← Volver al listado</a>
                <a href="editar.php?id=<?php echo $fila['id_inscripcion']; ?>" class="btn-accion btn-editar">Editar</a>
            </div>
        </div>
    </section>
</main>

<footer style="text-align: center; padding: 3rem 0; background: #0c0c0c; color: rgba(255,255,255,0.4); margin-top: 3rem;">
    <p>© <?php echo date('Y'); ?> Escuela de Formación Bíblica. Todos los derechos reservados.</p>
</footer>

<script src="../js/plugins.js"></script>
<script src="../js/main.js"></script>
</body>
</html>