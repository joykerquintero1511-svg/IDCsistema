<?php
include '../session-start.php';
require_once("../conexion.php");
// ... resto de tu código





// 1. LOGICA DE SEGURIDAD (Intacta)
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

$nombre_profesor = $_SESSION['usuario'] ?? 'Profesor';
$mensaje = "";

// 2. PROCESAMIENTO DEL FORMULARIO (Intacto)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_nivel = mysqli_real_escape_string($conexion, $_POST['id_nivel']);
    $titulo_tarea = mysqli_real_escape_string($conexion, $_POST['titulo_tarea']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $tema = mysqli_real_escape_string($conexion, $_POST['tema']);
    $fecha_limite = mysqli_real_escape_string($conexion, $_POST['fecha_limite']);

    if (!empty($id_nivel) && !empty($titulo_tarea) && !empty($fecha_limite)) {
        $query_insertar = "INSERT INTO asignacion (id_nivel, titulo_tarea, descripcion, tema, fecha_limite) 
                           VALUES ('$id_nivel', '$titulo_tarea', '$descripcion', '$tema', '$fecha_limite')";
        
        if (mysqli_query($conexion, $query_insertar)) {
            $mensaje = "<div class='alert-success'>✓ Asignación publicada exitosamente en el aula virtual.</div>";
        } else {
            $mensaje = "<div class='alert-error'>Error al guardar: " . mysqli_error($conexion) . "</div>";
        }
    } else {
        $mensaje = "<div class='alert-warning'>⚠️ Por favor, rellene todos los campos obligatorios.</div>";
    }
}

// 3. CONSULTA DINÁMICA DE NIVELES (Intacta)
$query_niveles = "SELECT id_nivel, nivel_academico FROM niveles ORDER BY id_nivel ASC";
$result_niveles = mysqli_query($conexion, $query_niveles);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Profesor - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <link rel="stylesheet" href="../css/mystyle.css">
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <img src="../images/EFB.png" alt="Logo">
                <h2>Profesor</h2>
            </div>
            <ul class="menu-links">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="crear_asignacion.php" class="active">Nueva Asignación</a></li>
            </ul>
        </div>
    </aside>

    <main class="main-content">
        <div class="info-card">
            <h3>Publicar Nueva Asignación</h3>
            
            <?php echo $mensaje; ?>

            <form action="crear_asignacion.php" method="POST">
                
                <div class="form-group">
                    <label for="id_nivel">Nivel Académico Destinatario </label>
                    <select name="id_nivel" id="id_nivel" class="form-control" required>
                        <option value="">-- Seleccione el nivel --</option>
                        <?php if ($result_niveles): ?>
                            <?php while($nivel = mysqli_fetch_assoc($result_niveles)): ?>
                                <option value="<?php echo $nivel['id_nivel']; ?>">
                                    <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tema">Tema Doctrinal o Unidad</label>
                    <input type="text" name="tema" id="tema" class="form-control" placeholder="Ej: Bibliología, Cristología, Teología...">
                </div>

                <div class="form-group">
                    <label for="titulo_tarea">Título de la Asignación *</label>
                    <input type="text" name="titulo_tarea" id="titulo_tarea" class="form-control" placeholder="Ej: Mapa conceptual sobre los Evangelios" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Instrucciones o Pautas de Entrega</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Escriba aquí los detalles del trabajo, preguntas o lecturas obligatorias..."></textarea>
                </div>

                <div class="form-group">
                    <label for="fecha_limite">Fecha Límite de Entrega *</label>
                    <input type="date" name="fecha_limite" id="fecha_limite" class="form-control" required>
                </div>

                <button type="submit" class="btn-action btn-submit-full">Publicar en el Aula</button>
            </form>
        </div>
    </main>

</body>
</html>