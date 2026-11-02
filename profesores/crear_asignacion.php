<?php
include '../session-start.php';
require_once("../conexion.php");

// 1. LÓGICA DE SEGURIDAD
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_profesor = $_SESSION['usuario'] ?? 'Profesor';
$mensaje = "";

// ==========================================
// 2. OBTENER EL PERÍODO ACTIVO ACTUAL
// ==========================================
$query_pa = "SELECT id_periodo FROM periodos_academicos WHERE estado = 1 LIMIT 1";
$res_pa = mysqli_query($conexion, $query_pa);
$id_periodo_activo = 0;
if ($res_pa && $row_pa = mysqli_fetch_assoc($res_pa)) {
    $id_periodo_activo = intval($row_pa['id_periodo']);
}

// ==========================================
// 3. OBTENER EL ID DEL PROFESOR ACTUAL
// ==========================================
$query_id_prof = "SELECT id_profesor FROM profesores WHERE id_usuario = '$id_usuario' LIMIT 1";
$res_id_prof = mysqli_query($conexion, $query_id_prof);
$id_profesor = 0;
if ($res_id_prof && $row_prof = mysqli_fetch_assoc($res_id_prof)) {
    $id_profesor = intval($row_prof['id_profesor']);
}

// ==========================================
// 4. CONSULTA DE NIVELES FILTRADOS POR PERÍODO Y PROFESOR
// ==========================================
$query_niveles = "SELECT n.id_nivel, n.nivel_academico 
                  FROM niveles n 
                  INNER JOIN profesor_nivel pn ON n.id_nivel = pn.id_nivel AND pn.id_periodo = $id_periodo_activo
                  WHERE pn.id_profesor = $id_profesor
                  ORDER BY n.id_nivel ASC";

$result_niveles = mysqli_query($conexion, $query_niveles);

$niveles_permitidos = [];
if ($result_niveles) {
    while ($row = mysqli_fetch_assoc($result_niveles)) {
        $niveles_permitidos[] = $row;
    }
}
$ids_validos = array_column($niveles_permitidos, 'id_nivel');

// ==========================================
// 5. PROCESAMIENTO DEL FORMULARIO
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_nivel = mysqli_real_escape_string($conexion, $_POST['id_nivel']);
    $titulo_tarea = mysqli_real_escape_string($conexion, $_POST['titulo_tarea']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $tema = mysqli_real_escape_string($conexion, $_POST['tema']);
    $fecha_limite = mysqli_real_escape_string($conexion, $_POST['fecha_limite']);

    // Validar permisos del nivel para el período actual
    if (!in_array($id_nivel, $ids_validos)) {
        $mensaje = "<div class='alert-error'>❌ No tienes permiso para publicar en este nivel académico para el período actual.</div>";
    } elseif (!empty($id_nivel) && !empty($titulo_tarea) && !empty($fecha_limite)) {
        
        // INSERT vinculando nivel, profesor y período activo
        $query_insertar = "INSERT INTO asignacion (id_nivel, id_profesor, titulo_tarea, descripcion, tema, fecha_limite, id_periodo) 
                           VALUES ('$id_nivel', '$id_profesor', '$titulo_tarea', '$descripcion', '$tema', '$fecha_limite', $id_periodo_activo)";
        
        if (mysqli_query($conexion, $query_insertar)) {
            $mensaje = "<div class='alert-success'>✓ Asignación publicada exitosamente en el aula virtual para el período actual.</div>";
        } else {
            $mensaje = "<div class='alert-error'>Error al guardar: " . mysqli_error($conexion) . "</div>";
        }
    } else {
        $mensaje = "<div class='alert-warning'>⚠️ Por favor, rellene todos los campos obligatorios.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Profesor - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="../css/movil.css">
</head>
<body>

    <?php include 'sidebarprof.php'; ?> 

    <main class="main-content">
        <div class="info-card">
            <h3>Publicar Nueva Asignación</h3>
            
            <?php echo $mensaje; ?>

            <form action="crear_asignacion.php" method="POST">
                
                <div class="form-group">
                    <label for="id_nivel">Nivel Académico Destinatario</label>
                    <select name="id_nivel" id="id_nivel" class="form-control" required>
                        <?php if (count($niveles_permitidos) > 1): ?>
                            <option value="">-- Seleccione el nivel --</option>
                        <?php endif; ?>

                        <?php foreach ($niveles_permitidos as $nivel): ?>
                            <option value="<?php echo $nivel['id_nivel']; ?>" <?php echo (count($niveles_permitidos) === 1) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                            </option>
                        <?php endforeach; ?>

                        <?php if (empty($niveles_permitidos)): ?>
                            <option value="">⚠️ No tienes ningún nivel asignado en este período</option>
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

                <button type="submit" class="btn-action btn-submit-full" <?php echo empty($niveles_permitidos) ? 'disabled' : ''; ?>>
                    Publicar en el Aula
                </button>
            </form>
        </div>
    </main>

    <?php include '../script-seguridad.php'; ?>

</body> 
</html>