<?php
session_start();
include("../conexion.php");

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
    <style>
        /* ESTÉTICA GENERAL E INTEGRACIÓN DE FONT-FAMILY DEL INDEX */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        
        body { 
            background-color: #0b0b0b; 
            color: #e0e0e0; 
            display: flex; 
            min-height: 100vh; 
        }

        /* BARRA LATERAL DEL INDEX (Fija a la izquierda) */
        .sidebar { 
            width: 260px; 
            background-color: #111111; 
            border-right: 1px solid rgba(255, 255, 255, 0.05); 
            padding: 2.5rem 1.5rem; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between;
            height: 100vh;
            position: fixed; 
            top: 0;
            left: 0;
        }

        /* INTEGRACIÓN DEL LOGO IDÉNTICA AL INICIO */
        .sidebar-brand { 
            display: flex; 
            align-items: center; 
            gap: 0.8rem; 
            margin-bottom: 2.5rem; 
        }
        
        .sidebar-brand img { 
            max-width: 35px; 
            height: auto; 
        }
        
        .sidebar-brand h2 { 
            font-size: 1.2rem; 
            font-weight: bold; 
            color: #fff; 
            letter-spacing: 0.5px; 
        }

        /* MENÚ DE ENLACES */
        .menu-links { 
            list-style: none; 
            display: flex; 
            flex-direction: column; 
            gap: 0.5rem; 
        }
        
        .menu-links a { 
            color: #a0a0a0; 
            text-decoration: none; 
            padding: 0.8rem 1.2rem; 
            border-radius: 8px; 
            display: block; 
            font-size: 0.95rem; 
            font-weight: 500; 
            transition: all 0.3s;
        }
        
        .menu-links a:hover, .menu-links a.active { 
            background: rgba(36, 82, 133, 0.15); 
            color: #3a7bc8; 
            font-weight: bold; 
        }

        .btn-logout { 
            color: #ff5555; 
            text-decoration: none; 
            padding: 0.8rem 1.2rem; 
            font-weight: bold; 
            border-radius: 8px; 
            transition: background 0.3s;
            text-align: center;
        }
        
        .btn-logout:hover { 
            background: rgba(255, 85, 85, 0.1); 
        }

        /* CONTENIDO PRINCIPAL (Centrado y respetando el espacio del sidebar) */
        .main-content { 
            margin-left: 260px; 
            flex-grow: 1; 
            padding: 40px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            width: calc(100% - 260px);
        }

        /* CARD INTEGRADA PARA EL FORMULARIO */
        .info-card { 
            background-color: #121212; 
            border: 1px solid #1c1c1c; 
            border-radius: 8px; 
            padding: 30px; 
            width: 100%; 
            max-width: 650px; 
        }
        
        .info-card h3 { 
            font-size: 1.1rem; 
            font-weight: 700; 
            color: #ffffff; 
            margin-bottom: 25px; 
            border-bottom: 1px solid #1c1c1c; 
            padding-bottom: 12px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }

        /* CAMPOS DEL FORMULARIO OSCURO DE NUEVA ASIGNACIÓN */
        .form-group { 
            margin-bottom: 20px; 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
        }
        
        .form-group label { 
            font-size: 0.85rem; 
            color: #888888; 
            font-weight: 500; 
        }
        
        .form-control { 
            background-color: #161b22; 
            border: 1px solid #30363d; 
            border-radius: 6px; 
            padding: 11px 14px; 
            color: #ffffff; 
            font-size: 0.95rem; 
            transition: border-color 0.2s; 
            width: 100%; 
        }
        
        .form-control:focus { 
            outline: none; 
            border-color: #58a6ff; 
        }
        
        select.form-control { 
            cursor: pointer; 
            color-scheme: dark; 
        }
        
        textarea.form-control { 
            resize: vertical; 
            min-height: 110px; 
        }

        .btn-submit { 
            background-color: #238636; 
            color: #ffffff; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 6px; 
            font-weight: 600; 
            font-size: 0.95rem; 
            cursor: pointer; 
            transition: background 0.2s; 
            margin-top: 10px; 
            width: 100%; 
        }
        
        .btn-submit:hover { 
            background-color: #2ea043; 
        }

        /* ALERTAS DE NOTIFICACIÓN */
        .alert-success { color: #2ea043; background: #161b22; padding: 12px; border: 1px solid #2ea043; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error { color: #f85149; background: #161b22; padding: 12px; border: 1px solid #f85149; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-warning { color: #ff9e2c; background: #161b22; padding: 12px; border: 1px solid #ff9e2c; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }

        /* Blanquear el icono del calendario nativo en inputs de fecha */
        input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
}
    </style>
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
                <li><a href="#">Calificar Tareas</a></li>
                <li><a href="#">Control de Asistencia</a></li>
            </ul>
        </div>
        <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <div class="info-card">
            <h3>Publicar Nueva Asignación</h3>
            
            <?php echo $mensaje; ?>

            <form action="crear_asignacion.php" method="POST">
                
                <div class="form-group">
                    <label for="id_nivel">Nivel Académico Destinatario *</label>
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

                <button type="submit" class="btn-submit">Publicar en el Aula</button>
            </form>
        </div>
    </main>

</body>
</html>