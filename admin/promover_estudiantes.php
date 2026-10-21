<?php
session_start();
require_once('../conexion.php'); 

// 1. Recibimos el filtro si existe
$filtro_nivel = isset($_GET['filtro_nivel']) ? $_GET['filtro_nivel'] : '';

// 2. Buscamos todos los niveles para llenar los dos select
$sql_niveles = "SELECT * FROM niveles ORDER BY id_nivel ASC";
$q_niveles_filtro = mysqli_query($conexion, $sql_niveles);
$q_niveles_promocion = mysqli_query($conexion, $sql_niveles); 

// 3. Solo consultamos si se seleccionó un nivel en el filtro
$q_estudiantes = false;
if (!empty($filtro_nivel)) {
    $filtro_seguro = mysqli_real_escape_string($conexion, $filtro_nivel);
    $sql_estudiantes = "SELECT e.id_estudiante, e.id_nivel, p.nombre, p.apellido, p.cedula, n.nivel_academico as nivel_actual
                        FROM estudiantes e
                        INNER JOIN personas p ON e.id_persona = p.id_persona
                        INNER JOIN niveles n ON e.id_nivel = n.id_nivel
                        WHERE e.id_nivel = '$filtro_seguro'
                        ORDER BY p.nombre ASC";
    $q_estudiantes = mysqli_query($conexion, $sql_estudiantes);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promover Estudiantes - EFB</title>
    <link rel="icon" type="image/png" href="/IDCsistema/images/EFB.png">
    <link rel="stylesheet" href="/IDCsistema/css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>
<body>

    <!-- Menú lateral unificado -->
    <?php include 'sidebaradmin.php'; ?>

    <!-- Contenedor Principal (Aplica el margen correcto) -->
    <main class="main-content">
        
        <div class="promocion-header">
            <h1>Promoción de Nivel Académico 🎓</h1>
            <p>Selecciona los estudiantes que aprobaron y elige a qué nivel serán promovidos.</p>
        </div>

        <!-- SECCIÓN 1: FILTRO DE BÚSQUEDA -->
        <div class="promocion-card">
            <form method="GET" action="">
                <label>🔍 Filtrar lista por nivel actual </label>
                <select name="filtro_nivel" onchange="this.form.submit()" class="promocion-select" required>
                    <option value="">-- Selecciona el nivel actual a promover --</option>
                    <?php while($nivel = mysqli_fetch_assoc($q_niveles_filtro)): ?>
                        <option value="<?php echo $nivel['id_nivel']; ?>" <?php if($filtro_nivel == $nivel['id_nivel']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <!-- SECCIÓN 2: FORMULARIO DE PROMOCIÓN -->
        <form action="procesar_promocion.php" method="POST" id="form-promocion">
            
            <div class="promocion-card">
                <label>¿A qué nivel van a ser promovidos los seleccionados?</label>
                <select name="id_nivel_nuevo" required class="promocion-select">
                    <option value="" disabled selected>Selecciona el nuevo nivel...</option>
                    <?php while($nivel = mysqli_fetch_assoc($q_niveles_promocion)): ?>
                        <option value="<?php echo $nivel['id_nivel']; ?>">
                            <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="promocion-table-wrapper">
                <table class="promocion-table">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 110px;">
                                <label style="cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <input type="checkbox" id="check-todos" checked class="checkbox-custom">
                                    <span>Todos</span>
                                </label>
                            </th>
                            <th>Cédula</th>
                            <th>Estudiante</th>
                            <th>Nivel Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtro_nivel)): ?>
                            <tr>
                                <td colspan="4" style="padding: 2.5rem; text-align: center; color: rgba(255,255,255,0.6); font-size: 1rem;">
                                    📌 <strong>Por favor, selecciona un nivel actual en el filtro superior</strong> para visualizar y promover a sus estudiantes.
                                </td>
                            </tr>
                        <?php elseif ($q_estudiantes && mysqli_num_rows($q_estudiantes) > 0): ?>
                            <?php while($est = mysqli_fetch_assoc($q_estudiantes)): ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="estudiantes[]" value="<?php echo $est['id_estudiante']; ?>" class="checkbox-custom check-estudiante" checked>
                                    </td>
                                    <td><?php echo htmlspecialchars($est['cedula']); ?></td>
                                    <td><?php echo htmlspecialchars(ucwords(strtolower($est['nombre'] . " " . $est['apellido']))); ?></td>
                                    <td><?php echo htmlspecialchars($est['nivel_actual']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="padding: 2rem; text-align: center; color: rgba(255,255,255,0.5);">
                                    No hay estudiantes registrados en este nivel.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="promocion-actions">
                <button type="submit" class="promocion-btn-submit">
                    Promover Estudiantes Seleccionados
                </button>
                <a href="index.php" class="promocion-link-back">← Volver al Panel Maestro</a>
            </div>
        </form>

    </main>

    <!-- SCRIPT DE SELECCIÓN MASIVA (TODOS / DESSELECCIONAR) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkTodos = document.getElementById('check-todos');
        const checksEstudiantes = document.querySelectorAll('.check-estudiante');
        const formPromocion = document.getElementById('form-promocion');

        if (checkTodos && checksEstudiantes.length > 0) {
            // Evento para marcar/desmarcar todos desde el checkbox principal
            checkTodos.addEventListener('change', function() {
                checksEstudiantes.forEach(chk => {
                    chk.checked = this.checked;
                });
            });

            // Si se desmarca individualmente a un estudiante, se desmarca el checkbox "Todos"
            checksEstudiantes.forEach(chk => {
                chk.addEventListener('change', function() {
                    const todosMarcados = Array.from(checksEstudiantes).every(c => c.checked);
                    checkTodos.checked = todosMarcados;
                });
            });
        }

        // Validación para evitar enviar el formulario sin seleccionar al menos a un estudiante
        if (formPromocion) {
            formPromocion.addEventListener('submit', function(e) {
                const seleccionados = document.querySelectorAll('.check-estudiante:checked');
                if (seleccionados.length === 0) {
                    e.preventDefault();
                    alert('⚠️ Debes seleccionar al menos un estudiante para promover.');
                }
            });
        }
    });
    </script>
</body>
</html>