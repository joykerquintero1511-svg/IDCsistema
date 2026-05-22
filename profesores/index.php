<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesores | IDCsistema</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, sans-serif; }
        body { background-color: #f8f9fa; color: #1a1a1a; display: flex; min-height: 100vh; }
        
        /* Sidebar Lateral Minimalista */
        .sidebar { width: 260px; background-color: #111111; color: #ffffff; padding: 30px 20px; flex-shrink: 0; }
        .brand { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 40px; color: #ffffff; }
        .brand span { color: #0070f3; } /* El plus azul */
        .menu-item { display: block; color: #a0a0a0; text-decoration: none; padding: 12px 15px; margin-bottom: 8px; border-radius: 6px; font-size: 0.95rem; transition: all 0.2s; }
        .menu-item:hover, .menu-item.active { background-color: #222222; color: #ffffff; }
        .menu-item.active { border-left: 4px solid #0070f3; }

        /* Contenedor Principal */
        .main-content { flex-grow: 1; padding: 40px; background-color: #fafdff; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eaeaea; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 1.75rem; font-weight: 700; color: #111111; }
        
        /* Botón de Logout elegante */
        .btn-logout { background-color: transparent; border: 1px solid #eaeaea; color: #666666; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
        .btn-logout:hover { background-color: #000000; color: #ffffff; border-color: #000000; }

        /* Tarjetas y Contenido */
        .welcome-card { background: #ffffff; border: 1px solid #eaeaea; border-radius: 8px; padding: 30px; margin-bottom: 30px; }
        .welcome-card p { color: #666666; margin-top: 8px; font-size: 1rem; }
        .badge { display: inline-block; background-color: #e6f0ff; color: #0070f3; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-left: 10px; }

        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card { background: #ffffff; border: 1px solid #eaeaea; border-radius: 8px; padding: 24px; transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card h3 { font-size: 1.1rem; font-weight: 600; color: #111111; margin-bottom: 10px; }
        .card p { color: #666666; font-size: 0.9rem; line-height: 1.5; }
        
        /* Enlace de acción en azul como acento */
        .card-link { display: inline-block; margin-top: 15px; color: #0070f3; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .card-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">IDC<span>sistema</span></div>
        <a href="#" class="menu-item active">Inicio</a>
        <a href="#" class="menu-item">Mis Secciones</a>
        <a href="#" class="menu-item">Cargar Notas</a>
        <a href="#" class="menu-item">Asistencias</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Panel de Control Docente</h1>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>

        <div class="welcome-card">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?><span class="badge">Profesor</span></h2>
            <p>Acceso autorizado al módulo de gestión académica institucional.</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Evaluaciones Activas</h3>
                <p>Configura los porcentajes, asigna fechas y publica los cortes de notas para tus alumnos.</p>
                <a href="#" class="card-link">Gestionar notas &rarr;</a>
            </div>
            <div class="card">
                <h3>Listas de Asistencia</h3>
                <p>Lleva el control diario de asistencia de las secciones asignadas para el periodo actual.</p>
                <a href="#" class="card-link">Tomar asistencia &rarr;</a>
            </div>
            <div class="card">
                <h3>Reportes del Lapso</h3>
                <p>Genera archivos limpios y consolidados del rendimiento de tus estudiantes.</p>
                <a href="#" class="card-link">Ver reportes &rarr;</a>
            </div>
        </div>
    </div>

</body>
</html>