<?php
session_start();

// Validar que sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$mensaje = "";
$clase_alerta = "";

// ==========================================
// 0. OBTENER EL PERÍODO ACTIVO ACTUAL
// ==========================================
$query_periodo_activo = "SELECT id_periodo, nombre_periodo FROM periodos_academicos WHERE estado = 1 LIMIT 1";
$res_pa = mysqli_query($conexion, $query_periodo_activo);
$id_periodo_activo = 0;
$nombre_periodo_activo = "Ninguno";

if ($res_pa && $row_pa = mysqli_fetch_assoc($res_pa)) {
    $id_periodo_activo = intval($row_pa['id_periodo']);
    $nombre_periodo_activo = $row_pa['nombre_periodo'];
} else {
    // Si no hay período activo, detenemos la acción para evitar registros huérfanos
    die("<div style='background: #7f1d1d; color: #fee2e2; padding: 20px; font-family: sans-serif; text-align: center;'>
            <h3>Error Crítico de Configuración</h3>
            <p>No hay ningún período con estado <strong>'Activo'</strong> en el sistema. Por favor, ve al módulo de períodos y activa uno antes de asignar profesores.</p>
            <a href='periodos.php' style='color: #38bdf8;'>Ir a Gestión de Períodos</a>
         </div>");
}

// ==========================================
// 1. PROCESAR ACCIONES (ASIGNAR O DESASIGNAR)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    // ASIGNAR NIVEL
    if ($accion === 'asignar') {
        $id_profesor = intval($_POST['id_profesor']);
        $id_nivel    = intval($_POST['id_nivel']);

        if ($id_profesor > 0 && $id_nivel > 0) {
            
            // 1. VERIFICAR SI EL NIVEL YA ESTÁ ASIGNADO A OTRO PROFESOR EN ESTE PERÍODO
            $check_nivel_ocupado = "SELECT p.nombre, p.apellido 
                                    FROM profesor_nivel pn 
                                    INNER JOIN profesores p ON pn.id_profesor = p.id_profesor 
                                    WHERE pn.id_nivel = $id_nivel AND pn.id_periodo = $id_periodo_activo LIMIT 1";
            $res_ocupado = mysqli_query($conexion, $check_nivel_ocupado);

            if (mysqli_num_rows($res_ocupado) > 0) {
                $row_occ = mysqli_fetch_assoc($res_ocupado);
                $nombre_ocupante = $row_occ['nombre'] . ' ' . $row_occ['apellido'];
                
                $mensaje = "⚠️ Este nivel ya se encuentra asignado al docente <strong>{$nombre_ocupante}</strong> en este período.";
                $clase_alerta = "background: #854d0e; color: #fef08a; border: 1px solid #eab308;";
            } else {
                // 2. INSERTAR SI ESTÁ LIBRE
                $sql_insert = "INSERT INTO profesor_nivel (id_profesor, id_nivel, id_periodo) VALUES ($id_profesor, $id_nivel, $id_periodo_activo)";
                if (mysqli_query($conexion, $sql_insert)) {
                    $mensaje = "¡Nivel asignado con éxito al docente para el período actual ($nombre_periodo_activo)!";
                    $clase_alerta = "background: #065f46; color: #d1fae5; border: 1px solid #10b981;";
                } else {
                    $mensaje = "Error al asignar el nivel: " . mysqli_error($conexion);
                    $clase_alerta = "background: #7f1d1d; color: #fee2e2; border: 1px solid #ef4444;";
                }
            }
        }
    }

    // DESASIGNAR NIVEL (Se elimina la asignación correspondiente al período activo)
    if ($accion === 'desasignar') {
        $id_profesor = intval($_POST['id_profesor']);
        $id_nivel    = intval($_POST['id_nivel']);

        $sql_delete = "DELETE FROM profesor_nivel WHERE id_profesor = $id_profesor AND id_nivel = $id_nivel AND id_periodo = $id_periodo_activo";
        if (mysqli_query($conexion, $sql_delete)) {
            $mensaje = "¡Nivel desasignado correctamente del docente para este período!";
            $clase_alerta = "background: #065f46; color: #d1fae5; border: 1px solid #10b981;";
        } else {
            $mensaje = "Error al quitar la asignación: " . mysqli_error($conexion);
            $clase_alerta = "background: #7f1d1d; color: #fee2e2; border: 1px solid #ef4444;";
        }
    }
}

// ==========================================
// 2. OBTENER PROFESORES Y SUS NIVELES ASIGNADOS (FILTRADO POR PERÍODO ACTIVO)
// ==========================================
$query_profs = "SELECT p.id_profesor, p.nombre, p.apellido, p.id_usuario,
                GROUP_CONCAT(n.id_nivel SEPARATOR '||') AS ids_niveles,
                GROUP_CONCAT(n.nivel_academico SEPARATOR '||') AS nombres_niveles
                FROM profesores p
                LEFT JOIN profesor_nivel pn ON p.id_profesor = pn.id_profesor AND pn.id_periodo = $id_periodo_activo
                LEFT JOIN niveles n ON pn.id_nivel = n.id_nivel
                GROUP BY p.id_profesor
                ORDER BY p.nombre ASC";
$res_profs = mysqli_query($conexion, $query_profs);

// ==========================================
// 3. OBTENER USUARIOS PARA CRUZAR CORREOS
// ==========================================
$query_usuarios = "SELECT id_usuario, usuario, email FROM usuarios";
$res_usuarios = mysqli_query($conexion, $query_usuarios);
$usuarios_list = [];
if ($res_usuarios) {
    while($u = mysqli_fetch_assoc($res_usuarios)) {
        $usuarios_list[] = $u;
    }
}

// ==========================================
// 4. OBTENER NIVELES DISPONIBLES
// ==========================================
$query_niveles = "SELECT id_nivel, nivel_academico FROM niveles ORDER BY id_nivel ASC";
$res_niveles = mysqli_query($conexion, $query_niveles);
$niveles_array = [];
if ($res_niveles) {
    while($row_n = mysqli_fetch_assoc($res_niveles)) {
        $niveles_array[] = $row_n;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Niveles a Profesores - Escuela de Formación Bíblica</title>
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
    <link rel="icon" type="image/png" href="../images/EFB.png">
</head>
<body style="background: #0b0f19; color: white; font-family: sans-serif; margin: 0; padding: 20px;">

    <div style="max-width: 950px; margin: 40px auto; background: #1e293b; padding: 30px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="../images/EFB.png" alt="Logo EFB" style="width: 70px; height: auto; margin-bottom: 10px;">
            <h2 style="color: #38bdf8; margin: 0;">Gestión de Asignación: Profesores y Niveles</h2>
            <p style="color: #94a3b8; font-size: 14px;">Asocia a los docentes con uno o varios niveles académicos en el sistema.</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div style="padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; <?php echo $clase_alerta; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO DE ASIGNACIÓN -->
        <form action="asignar_nivel_profesor.php" method="POST" style="background: #0f172a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.05);">
            <input type="hidden" name="accion" value="asignar">
            <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
                
                <div style="flex: 1; min-width: 250px;">
                    <label style="display: block; color: #cbd5e1; margin-bottom: 8px; font-weight: bold;">Seleccionar Profesor:</label>
                    <select name="id_profesor" required style="width: 100%; padding: 10px; background: #1e293b; color: white; border: 1px solid #475569; border-radius: 5px;">
                        <option value="">-- Seleccione un profesor --</option>
                        <?php 
                        if ($res_profs) {
                            mysqli_data_seek($res_profs, 0);
                            while($p = mysqli_fetch_assoc($res_profs)): 
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
                        + Asignar Nivel
                    </button>
                </div>

            </div>
        </form>

 <!-- Indicador Visual Dinámico del Período Activo -->
<div style="background: linear-gradient(135deg, #0f172a, #1e293b); border: 1px solid #38bdf8; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
    <div>
        <span style="color: #94a3b8; font-size: 0.85rem; display: block; text-transform: uppercase; letter-spacing: 1px;">Período Académico en Curso</span>
        <strong style="color: #38bdf8; font-size: 1.1rem;">⭐ <?php echo htmlspecialchars($nombre_periodo_activo); ?></strong>
    </div>
    <span style="background: #065f46; color: #d1fae5; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; border: 1px solid #10b981;">
        Modo Activo / Escritura
    </span>
</div>

        <!-- TABLA DE DOCENTES Y NIVELES ASIGNADOS -->
        <h3 style="color: #f1f5f9; border-bottom: 2px solid #334155; padding-bottom: 8px; margin-top: 20px;">Listado Actual de Docentes y sus Niveles</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left;">
                <thead>
                    <tr style="background: #0f172a; color: #94a3b8; border-bottom: 1px solid #334155;">
                        <th style="padding: 12px;">Profesor</th>
                        <th style="padding: 12px;">Correo</th>
                        <th style="padding: 12px;">Niveles Asignados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($res_profs) {
                        mysqli_data_seek($res_profs, 0);
                        if (mysqli_num_rows($res_profs) > 0):
                            while($row = mysqli_fetch_assoc($res_profs)): 
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

                                $array_ids = !empty($row['ids_niveles']) ? explode('||', $row['ids_niveles']) : [];
                                $array_nombres = !empty($row['nombres_niveles']) ? explode('||', $row['nombres_niveles']) : [];
                    ?>
                        <tr style="border-bottom: 1px solid #1e293b; color: #e2e8f0;">
                            <td style="padding: 12px; font-weight: 500;"><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                            <td style="padding: 12px; color: #94a3b8;"><?php echo $correo_tabla; ?></td>
                            <td style="padding: 12px;">
                                <?php if (!empty($array_nombres)): ?>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php for ($i = 0; $i < count($array_nombres); $i++): ?>
                                            <div style="background: #0369a1; color: #e0f2fe; padding: 4px 10px; border-radius: 4px; font-size: 13px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
                                                <span><?php echo htmlspecialchars($array_nombres[$i]); ?></span>
                                                
                                                <!-- FORMULARIO DE DESASIGNACIÓN INDIVIDUAL -->
                                                <form action="asignar_nivel_profesor.php" method="POST" style="display: inline; margin: 0; padding: 0;">
                                                    <input type="hidden" name="accion" value="desasignar">
                                                    <input type="hidden" name="id_profesor" value="<?php echo $row['id_profesor']; ?>">
                                                    <input type="hidden" name="id_nivel" value="<?php echo $array_ids[$i]; ?>">
                                                    <button type="submit" onclick="return confirm('¿Deseas desasignar el nivel \'<?php echo htmlspecialchars($array_nombres[$i]); ?>\' de este docente?');" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1;" title="Quitar asignación">
                                                        ✕
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="background: #78350f; color: #fef3c7; padding: 4px 10px; border-radius: 4px; font-size: 13px;">
                                        Sin niveles asignados
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