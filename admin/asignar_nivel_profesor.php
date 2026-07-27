<?php
session_start();

// 1. Validar que sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$mensaje = "";
$clase_alerta = "";

// 2. Procesar la asignación cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_profesor = intval($_POST['id_profesor']);
    $id_nivel    = intval($_POST['id_nivel']);

    $sql_update = "UPDATE profesores SET id_nivel = $id_nivel WHERE id_profesor = $id_profesor";
    
    if (mysqli_query($conexion, $sql_update)) {
        $mensaje = "¡Profesor asignado al nivel correctamente!";
        $clase_alerta = "background: #065f46; color: #d1fae5; border: 1px solid #10b981;";
    } else {
        $mensaje = "Error al actualizar la asignación: " . mysqli_error($conexion);
        $clase_alerta = "background: #7f1d1d; color: #fee2e2; border: 1px solid #ef4444;";
    }
}

// 3. Obtener listado de profesores y niveles
$query_profs = "SELECT p.id_profesor, p.nombre, p.apellido, p.id_usuario, p.id_nivel, 
                n.nivel_academico AS nivel_actual
                FROM profesores p
                LEFT JOIN niveles n ON p.id_nivel = n.id_nivel";
$res_profs = mysqli_query($conexion, $query_profs);

// Obtener todos los usuarios para hacer un cruce inteligente en PHP
$query_usuarios = "SELECT id_usuario, usuario, email FROM usuarios";
$res_usuarios = mysqli_query($conexion, $query_usuarios);
$usuarios_list = [];
while($u = mysqli_fetch_assoc($res_usuarios)) {
    $usuarios_list[] = $u;
}

// 4. Obtener listado de niveles disponibles
$query_niveles = "SELECT id_nivel, nivel_academico FROM niveles";
$res_niveles = mysqli_query($conexion, $query_niveles);
$niveles_array = [];
while($row_n = mysqli_fetch_assoc($res_niveles)) {
    $niveles_array[] = $row_n;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Niveles a Profesores - Escuela de Formación Bíblica</title>
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="icon" type="image/png" href="../images/EFB.png">
</head>
<body style="background: #0b0f19; color: white; font-family: sans-serif; margin: 0; padding: 20px;">

    <div style="max-width: 900px; margin: 40px auto; background: #1e293b; padding: 30px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="../images/EFB.png" alt="Logo EFB" style="width: 70px; height: auto; margin-bottom: 10px;">
            <h2 style="color: #38bdf8; margin: 0;">Gestión de Asignación: Profesores y Niveles</h2>
            <p style="color: #94a3b8; font-size: 14px;">Asocia a los docentes con el nivel académico correspondiente en el sistema.</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div style="padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; <?php echo $clase_alerta; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="asignar_nivel_profesor.php" method="POST" style="background: #0f172a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.05);">
            <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
                
                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; color: #cbd5e1; margin-bottom: 8px; font-weight: bold;">Seleccionar Profesor:</label>
                    <select name="id_profesor" required style="width: 100%; padding: 10px; background: #1e293b; color: white; border: 1px solid #475569; border-radius: 5px;">
                        <option value="">-- Seleccione un profesor --</option>
                        <?php 
                        if ($res_profs) {
                            mysqli_data_seek($res_profs, 0);
                            while($p = mysqli_fetch_assoc($res_profs)): 
                                // Buscar correo de forma inteligente
                                $correo_mostrar = 'Sin correo';
                                foreach($usuarios_list as $u) {
                                    if (!empty($p['id_usuario']) && $p['id_usuario'] == $u['id_usuario']) {
                                        $correo_mostrar = $u['email'];
                                        break;
                                    }
                                    if (strcasecmp(trim($p['nombre']), trim($u['usuario'])) == 0 || 
                                        stripos($u['email'], strtolower($p['nombre'])) !== false) {
                                        $correo_mostrar = $u['email'];
                                    }
                                }
                        ?>
                            <option value="<?php echo $p['id_profesor']; ?>">
                                <?php echo $p['nombre'] . " " . $p['apellido'] . " (" . $correo_mostrar . ")"; ?>
                            </option>
                        <?php 
                            endwhile;
                        } 
                        ?>
                    </select>
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; color: #cbd5e1; margin-bottom: 8px; font-weight: bold;">Asignar Nivel:</label>
                    <select name="id_nivel" required style="width: 100%; padding: 10px; background: #1e293b; color: white; border: 1px solid #475569; border-radius: 5px;">
                        <option value="">-- Seleccione un nivel --</option>
                        <?php foreach($niveles_array as $n): ?>
                            <option value="<?php echo $n['id_nivel']; ?>">
                                <?php echo $n['nivel_academico']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 11px 20px; font-weight: bold; border-radius: 5px; cursor: pointer;">
                        Guardar Asignación
                    </button>
                </div>

            </div>
        </form>

        <h3 style="color: #f1f5f9; border-bottom: 2px solid #334155; padding-bottom: 8px; margin-top: 20px;">Listado Actual de Docentes</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left;">
                <thead>
                    <tr style="background: #0f172a; color: #94a3b8; border-bottom: 1px solid #334155;">
                        <th style="padding: 12px;">Profesor</th>
                        <th style="padding: 12px;">Correo</th>
                        <th style="padding: 12px;">Nivel Asignado Actual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($res_profs) {
                        mysqli_data_seek($res_profs, 0);
                        if (mysqli_num_rows($res_profs) > 0):
                            while($row = mysqli_fetch_assoc($res_profs)): 
                                // Cruzar correo para la tabla también
                                $correo_tabla = 'N/A';
                                foreach($usuarios_list as $u) {
                                    if (!empty($row['id_usuario']) && $row['id_usuario'] == $u['id_usuario']) {
                                        $correo_tabla = $u['email'];
                                        break;
                                    }
                                    if (strcasecmp(trim($row['nombre']), trim($u['usuario'])) == 0 || 
                                        stripos($u['email'], strtolower($row['nombre'])) !== false) {
                                        $correo_tabla = $u['email'];
                                    }
                                }
                    ?>
                        <tr style="border-bottom: 1px solid #1e293b; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 500;"><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                            <td style="padding: 12px; color: #94a3b8;"><?php echo $correo_tabla; ?></td>
                            <td style="padding: 12px;">
                                <?php if (!empty($row['nivel_actual'])): ?>
                                    <span style="background: #0369a1; color: #e0f2fe; padding: 4px 10px; border-radius: 4px; font-size: 13px; font-weight: bold;">
                                        <?php echo $row['nivel_actual']; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="background: #78350f; color: #fef3c7; padding: 4px 10px; border-radius: 4px; font-size: 13px;">
                                        Sin nivel asignado
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                            endwhile;
                        else:
                    ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 20px; color: #94a3b8;">No hay profesores registrados en el sistema.</td>
                        </tr>
                    <?php 
                        endif;
                    } 
                    ?>
                </tbody>
            </table>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="background: #475569; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                ← Volver al Panel de Administración
            </a>
        </div>

    </div>

</body>
</html>