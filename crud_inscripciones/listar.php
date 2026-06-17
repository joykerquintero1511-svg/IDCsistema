<?php
require_once('../conexion.php');

 $conexion = $conexion ?? null; /* Significa:

“si $conexion no existe, créala como null”

 Para PHP → no cambia nada
 Para VS Code → deja de marcar error,xq se tildaba
 $conexion(linea 56) x la extension intelephense q pensaba no
 estaba declarada la variable igual corria el sistema
 pero no m gustaba cm se veia.y tambien xq se usa
 require_once,la variable viene de otro archivo y
 el codigo funciona bien.
*/


$sql = "SELECT * FROM inscripciones ORDER BY id_inscripcion DESC";
$resultado = ejecutarConsulta($conexion, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Listado de Inscripciones</title>
    <link rel="stylesheet" href="../css/vendor.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .tabla-inscripciones {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }
        .tabla-inscripciones th,
        .tabla-inscripciones td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
        }
        .tabla-inscripciones th {
            background-color: rgba(0, 0, 0, 0.4);
            color: #ffffff;
        }
        .tabla-inscripciones tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
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
        .btn-ver {
            background: #28a745;
            color: white;
        }
        .btn-ver:hover {
            background: #218838;
        }
        .btn-editar {
            background: #007bff;
            color: white;
        }
        .btn-editar:hover {
            background: #6f42c1;
        }
        .btn-nuevo {
            background: #4a148c;
            color: white;
            padding: 10px 20px;
            display: inline-block;
            margin-bottom: 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-nuevo:hover {
            background: #5a32a3;
        }
        .estado-activo {
            color: #28a745;
        }
        .estado-inactivo {
            color: #dc3545;
        }
        h1 {
            color: #ffffff;
        }
        .subtitulo {
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 2rem;
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
    <h1>Listado de Inscripciones</h1>
    
   <div style="display: inline-block;">
    <a href="registrar.php" class="btn-nuevo">Nueva Inscripción</a>
</div>

    <table class="tabla-inscripciones">
        <thead>
            <tr>
                <th>ID Inscripcion</th>
                <th>ID Estudiante</th>
                <th>Nivel Académico</th>
                <th>Fecha Inscripción</th>
                <th>Periodo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo $fila['id_inscripcion']; ?></td>
                <td><?php echo $fila['id_estudiante']; ?></td>
                <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($fila['fecha_inscripcion'])); ?></td>
                <td><?php echo $fila['periodo']; ?></td>
                <td>
                    <?php if ($fila['estado'] == 1): ?>
                        <span class="estado-activo">✓ Activo</span>
                    <?php else: ?>
                        <span class="estado-inactivo">✗ Inactivo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="consultar.php?id=<?php echo $fila['id_inscripcion']; ?>" class="btn-accion btn-ver">Ver</a>
                    <a href="editar.php?id=<?php echo $fila['id_inscripcion']; ?>" class="btn-accion btn-editar">Editar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <footer style="text-align: center; padding: 3rem 0; background: #0c0c0c; color: rgba(255,255,255,0.4); margin-top: 3rem;">
    <p>© <?php echo date('Y'); ?> Escuela de Formación Bíblica. Todos los derechos reservados.</p>
</footer>
    <script src="../js/plugins.js"></script>
    <script src="../js/main.js"></script>
</body>
 </html>