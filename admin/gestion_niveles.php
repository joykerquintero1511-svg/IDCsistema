<?php
session_start();
require_once('../conexion.php');

$mensaje = "";
$tipo_alerta = "";

// 1. PROCESAR EDICIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'editar') {$id_nivel = isset($_POST['id_nivel']) ? intval($_POST['id_nivel']) : 0;
    
    // Recibimos la sección (Ej: 4B) y la limpiamos
    $seccion_input = isset($_POST['seccion_nivel']) ? strtoupper(trim($_POST['seccion_nivel'])) : '';
    $seccion_input = preg_replace('/[^A-Z0-9]/', '',$seccion_input);
    
    // Extraemos el código base numérico (Ej: De "4B" toma "4")
    $coincidencias = array();
    preg_match('/\d+/', $seccion_input,$coincidencias);
    $codigo_nivel = isset($coincidencias[0]) ? $coincidencias[0] :$seccion_input;

    $nombre_materia = isset($_POST['nombre_nivel']) ? trim($_POST['nombre_nivel']) : '';
    
    // Concatenamos para guardar en el campo 'nivel_academico'
    $nivel_academico = $seccion_input . ' - ' .$nombre_materia;

    if (!empty($id_nivel) && !empty($seccion_input) && !empty($nombre_materia)) {
        // Actualizamos usando solo las columnas existentes en tu BD
        $sql_update = "UPDATE niveles SET codigo_nivel = ?, nivel_academico = ? WHERE id_nivel = ?";
        $stmt = mysqli_prepare($conexion,$sql_update);
        mysqli_stmt_bind_param($stmt, "ssi", $codigo_nivel, $nivel_academico,$id_nivel);

        if (mysqli_stmt_execute($stmt)) {$mensaje = "¡Nivel actualizado con éxito!";
            $tipo_alerta = "success";
        } else {
            $mensaje = "Error al actualizar: " . mysqli_error($conexion);$tipo_alerta = "error";
        }
        mysqli_stmt_close($stmt);
    } else {
        $mensaje = "Por favor completa todos los campos del formulario.";
        $tipo_alerta = "warning";
    }
}

// 2. MENSAJE AL ELIMINAR
if (isset($_GET['msg']) && $_GET['msg'] === 'eliminado') {$mensaje = "¡Nivel eliminado correctamente!";
    $tipo_alerta = "success";
}

// 3. CONSULTA A LA BASE DE DATOS
$sql = "SELECT * FROM niveles ORDER BY id_nivel DESC";
$resultado = mysqli_query($conexion,$sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Niveles - EFB</title>
    <link rel="icon" type="image/png" href="../IDCsistema/images/EFB.png">
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
    <style>
        .gestion-container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 10px;
        }

        .top-bar-niveles {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 1.5rem;
        }

        .btn-crear-nivel {
            background: #0976fc;
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(9, 118, 252, 0.3);
            transition: all 0.2s ease;
        }

        .btn-crear-nivel:hover {
            background: #1d82ff;
        }

        .tabla-niveles-wrapper {
            background: #151e2f;
            border: 1px solid #1e293b;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .tabla-niveles {
            width: 100%;
            border-collapse: collapse;
            color: #fff;
            text-align: left;
            font-size: 0.95rem;
        }

        .tabla-niveles th {
            background: #1e293b;
            color: #94a3b8;
            padding: 14px 16px;
            font-weight: 600;
            border-bottom: 1px solid #334155;
        }

        .tabla-niveles td {
            padding: 14px 16px;
            border-bottom: 1px solid #1e293b;
            vertical-align: middle;
        }

        .acciones-cell {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-accion-editar {
            background: #f59e0b;
            border: none;
            color: white;
            padding: 7px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-accion-eliminar {
            background: #ef4444;
            color: white;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .modal-bg {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        }

        .modal-card {
            background: #151e2f;
            border: 1px solid #1e293b;
            border-radius: 12px;
            padding: 24px;
            width: 90%;
            max-width: 450px;
            color: #fff;
        }
    </style>
</head>
<body>

    <?php include 'sidebaradmin.php'; ?>

    <main class="main-content">
        <div class="gestion-container">
            
            <!-- ENCABEZADO -->
            <div class="top-bar-niveles">
                <div>
                    <h1 style="margin: 0; font-size: 1.8rem; color: #fff;">Gestión de Niveles Académicos</h1>
                    <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 0.95rem;">Administra, edita o elimina los niveles del sistema</p>
                </div>
                <a href="crear_nivel.php" class="btn-crear-nivel">+ Nuevo Nivel</a>
            </div>

            <!-- ALERTA DE MENSAJE -->
            <?php if(!empty($mensaje)): ?>
                <div style="padding: 0.9rem; margin-bottom: 1.5rem; border-radius: 6px; font-size: 0.9rem; text-align: center; background: <?php echo ($tipo_alerta == 'success') ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)'; ?>; color: <?php echo ($tipo_alerta == 'success') ? '#4ade80' : '#f87171'; ?>; border: 1px solid <?php echo ($tipo_alerta == 'success') ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)'; ?>;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- TABLA -->
            <div class="tabla-niveles-wrapper">
                <table class="tabla-niveles">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Código Base</th>
                            <th style="width: 45%;">Nivel Académico</th>
                            <th style="width: 15%;">Periodo</th>
                            <th style="width: 25%; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($resultado)): 
                                // Descomponemos el string '4A - Historia de la Iglesia'
                                $partes = explode(' - ', $row['nivel_academico']);$seccion_val = isset($partes[0]) ?$partes[0] : $row['codigo_nivel'];$nombre_val = isset($partes[1]) ?$partes[1] : '';
                            ?>
                                <tr>
                                    <td style="font-weight: bold; color: #38bdf8;"><?php echo htmlspecialchars($row['codigo_nivel']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nivel_academico']); ?></td>
                                    <td style="color: #94a3b8;"><?php echo htmlspecialchars($row['periodo_academico']); ?></td>
                                    <td>
                                        <div class="acciones-cell">
                                            <button type="button" class="btn-accion-editar" 
                                                data-id="<?php echo $row['id_nivel']; ?>"
                                                data-seccion="<?php echo htmlspecialchars($seccion_val, ENT_QUOTES); ?>"
                                                data-nombre="<?php echo htmlspecialchars($nombre_val, ENT_QUOTES); ?>"
                                                onclick="prepararEdicion(this)">
                                                ✏️ Editar
                                            </button>
                                            
                                            <a href="eliminar_nivel.php?id=<?php echo $row['id_nivel']; ?>" class="btn-accion-eliminar" onclick="return confirm('¿Estás seguro de eliminar este nivel?');">
                                                🗑️ Eliminar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="padding: 24px; text-align: center; color: #94a3b8;">No hay niveles registrados actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <!-- VENTANA MODAL PARA EDITAR -->
    <div id="modalEditar" class="modal-bg">
        <div class="modal-card">
            <h2 style="margin-top: 0; font-size: 1.25rem;">Editar Nivel Académico</h2>
            <form action="gestion_niveles.php" method="POST">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" id="edit_id_nivel" name="id_nivel">

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: #94a3b8;">Sección (Ej. 4B, 3A)</label>
                    <input type="text" id="edit_seccion_nivel" name="seccion_nivel" required style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #334155; color: #fff; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: #94a3b8;">Nombre de la Materia / Nivel</label>
                    <input type="text" id="edit_nombre_nivel" name="nombre_nivel" required style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #334155; color: #fff; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; color: #94a3b8;">Nivel Académico (Resultado final)</label>
                    <input type="text" id="edit_nivel_academico" readonly style="width: 100%; padding: 10px; border-radius: 6px; background: rgba(255, 255, 255, 0.05); border: 1px solid #334155; color: #38bdf8; font-weight: bold; cursor: not-allowed; box-sizing: border-box;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="cerrarModalEditar()" style="background: #475569; border: none; color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="background: #0976fc; border: none; color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../script-seguridad.php'; ?>

    <!-- JS ROBUSTO USANDO DATA-ATTRIBUTES (Evita errores de sintaxis en el onclick) -->
    <script>
    const modal = document.getElementById('modalEditar');
    const inputEditSeccion = document.getElementById('edit_seccion_nivel');
    const inputEditNombre = document.getElementById('edit_nombre_nivel');
    const inputEditResultado = document.getElementById('edit_nivel_academico');

    function prepararEdicion(btn) {
        document.getElementById('edit_id_nivel').value = btn.getAttribute('data-id');
        inputEditSeccion.value = btn.getAttribute('data-seccion');
        inputEditNombre.value = btn.getAttribute('data-nombre');
        actualizarResultadoEdit();
        modal.style.display = 'flex';
    }

    function cerrarModalEditar() {
        modal.style.display = 'none';
    }

    function actualizarResultadoEdit() {
        const sec = inputEditSeccion.value.trim();
        const nom = inputEditNombre.value.trim();
        inputEditResultado.value = (sec && nom) ? `${sec} - ${nom}` : (sec || nom || '');
    }

    inputEditSeccion.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        actualizarResultadoEdit();
    });

    inputEditNombre.addEventListener('input', actualizarResultadoEdit);
    </script>
</body>
</html>