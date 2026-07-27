<?php
session_start();
require_once('../conexion.php'); // Ajusta la ruta a tu conexion.php

// 1. Recibimos el filtro si existe
$filtro_nivel = isset($_GET['filtro_nivel']) ? $_GET['filtro_nivel'] : '';

// 2. Buscamos todos los niveles para llenar los dos select (el de filtro y el de promoción)
$sql_niveles = "SELECT * FROM niveles";
$q_niveles_filtro = mysqli_query($conexion, $sql_niveles);
$q_niveles_promocion = mysqli_query($conexion, $sql_niveles); 

// 3. Construimos la consulta de estudiantes dinámicamente según el filtro
$sql_estudiantes = "SELECT e.id_estudiante, e.id_nivel, p.nombre, p.apellido, p.cedula, n.nivel_academico as nivel_actual
                    FROM estudiantes e
                    INNER JOIN personas p ON e.id_persona = p.id_persona
                    INNER JOIN niveles n ON e.id_nivel = n.id_nivel";

// Si el administrador seleccionó un nivel en el filtro, agregamos la condición WHERE
if ($filtro_nivel != '') {
    $filtro_seguro = mysqli_real_escape_string($conexion, $filtro_nivel);
    $sql_estudiantes .= " WHERE e.id_nivel = '$filtro_seguro'";
}

$sql_estudiantes .= " ORDER BY n.id_nivel ASC, p.nombre ASC";
$q_estudiantes = mysqli_query($conexion, $sql_estudiantes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Promover Estudiantes</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <!-- Agrega aquí tus links a CSS -->
</head>
<body style="background:#0b0f19; color:white; font-family:sans-serif; padding: 2rem;">

    <h2>Promoción de Nivel Académico 🎓</h2>
    <p>Selecciona los estudiantes que aprobaron y elige a qué nivel serán promovidos.</p>

    <!-- ==========================================
         SECCIÓN 1: FILTRO DE BÚSQUEDA 
         ========================================== -->
    <div style="margin-bottom: 2rem; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
        <form method="GET" action="">
            <label style="font-weight: bold; font-size: 1.1rem; color: #94a3b8;">🔍 Filtrar lista por nivel actual:</label>
            <br><br>
            <select name="filtro_nivel" onchange="this.form.submit()" style="padding: 10px; border-radius: 5px; width: 300px; background: #1e293b; color: white; border: 1px solid #334155;">
                <option value="">Mostrar todos los estudiantes</option>
                <?php while($nivel = mysqli_fetch_assoc($q_niveles_filtro)): ?>
                    <option value="<?php echo $nivel['id_nivel']; ?>" <?php if($filtro_nivel == $nivel['id_nivel']) echo 'selected'; ?>>
                        <?php echo $nivel['nivel_academico']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <!-- ==========================================
         SECCIÓN 2: FORMULARIO DE PROMOCIÓN 
         ========================================== -->
    <form action="procesar_promocion.php" method="POST">
        
        <div style="margin-bottom: 2rem; background: #1e293b; padding: 1.5rem; border-radius: 8px;">
            <label style="font-weight: bold; font-size: 1.2rem;">¿A qué nivel van a ser promovidos los seleccionados?</label>
            <br><br>
            <select name="id_nivel_nuevo" required style="padding: 10px; border-radius: 5px; width: 300px;">
                <option value="" disabled selected>Selecciona el nuevo nivel...</option>
                <?php while($nivel = mysqli_fetch_assoc($q_niveles_promocion)): ?>
                    <option value="<?php echo $nivel['id_nivel']; ?>">
                        <?php echo $nivel['nivel_academico']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background: #3b82f6;">
                    <th style="padding: 10px;">Promover</th>
                    <th style="padding: 10px;">Cédula</th>
                    <th style="padding: 10px;">Estudiante</th>
                    <th style="padding: 10px;">Nivel Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($q_estudiantes) > 0) {
                    while($est = mysqli_fetch_assoc($q_estudiantes)): 
                ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 10px;">
                        <input type="checkbox" name="estudiantes[]" value="<?php echo $est['id_estudiante']; ?>" style="width:20px; height:20px; cursor:pointer;">
                    </td>
                    <td style="padding: 10px;"><?php echo $est['cedula']; ?></td>
                    <td style="padding: 10px;"><?php echo $est['nombre'] . " " . $est['apellido']; ?></td>
                    <td style="padding: 10px;"><?php echo $est['nivel_actual']; ?></td>
                </tr>
                <?php 
                    endwhile; 
                } else {
                    echo "<tr><td colspan='4' style='padding: 15px; text-align: center; color: #94a3b8;'>No hay estudiantes en este nivel.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <br>
        <button type="submit" style="background: #10b981; color: white; border: none; padding: 15px 30px; font-size: 1.2rem; border-radius: 5px; font-weight: bold; cursor: pointer;">
            Promover Estudiantes Seleccionados
        </button>

        <a href="index.php" style="color: #3b82f6; text-decoration: underline; margin-left: 10px;">← Volver al Panel Maestro</a>
    </form>
</body>
</html>