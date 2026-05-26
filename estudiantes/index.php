<?php
session_start();
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'estudiante' && $_SESSION['rol'] !== 'alumno')) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiantes</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, sans-serif; }
        body { background-color: #f8f9fa; color: #1a1a1a; display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background-color: #111111; color: #ffffff; padding: 30px 20px; flex-shrink: 0; }
        .brand { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 40px; color: #ffffff; }
        .brand span { color: #0070f3; }
        .menu-item { display: block; color: #a0a0a0; text-decoration: none; padding: 12px 15px; margin-bottom: 8px; border-radius: 6px; font-size: 0.95rem; transition: all 0.2s; }
        .menu-item:hover, .menu-item.active { background-color: #222222; color: #ffffff; }
        .menu-item.active { border-left: 4px solid #0070f3; }

        .main-content { flex-grow: 1; padding: 40px; background-color: #fafdff; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eaeaea; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 1.75rem; font-weight: 700; color: #111111; }
        
        .btn-logout { background-color: transparent; border: 1px solid #eaeaea; color: #666666; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
        .btn-logout:hover { background-color: #000000; color: #ffffff; border-color: #000000; }

        .welcome-card { background: #ffffff; border: 1px solid #eaeaea; border-radius: 8px; padding: 30px; margin-bottom: 30px; }
        .welcome-card p { color: #666666; margin-top: 8px; font-size: 1rem; }
        .badge { display: inline-block; background-color: #f0f0f0; color: #111111; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-left: 10px; border: 1px solid #eaeaea; }

        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card { background: #ffffff; border: 1px solid #eaeaea; border-radius: 8px; padding: 24px; transition: transform 0.2s; }
        .card:hover { transform: translateY(-2px); }
        .card h3 { font-size: 1.1rem; font-weight: 600; color: #111111; margin-bottom: 10px; }
        .card p { color: #666666; font-size: 0.9rem; line-height: 1.5; }
        .card-link { display: inline-block; margin-top: 15px; color: #0070f3; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .card-link:hover { text-underline-offset: 3px; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">IDC<span>sistema</span></div>
        <a href="#" class="menu-item active">Mi Portal</a>
        <a href="#" class="menu-item">Mis Materias</a>
        <a href="#" class="menu-item">Calificaciones</a>
        <a href="#" class="menu-item">Horarios</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Portal de Estudiantes</h1>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>

        <div class="welcome-card">
            <h2>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?><span class="badge">Estudiante</span></h2>
            <p>Consulta tus notas en tiempo real y verifica tu estatus académico actual.</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Boleta de Calificaciones</h3>
                <p>Revisa las notas cargadas por tus profesores correspondientes a los lapsos evaluados.</p>
                <a href="#" class="card-link">Ver mis notas &rarr;</a>
            </div>
            <div class="card">
                <h3>Asignaturas Inscritas</h3>
                <p>Verifica las secciones, profesores y las materias que tienes dadas de alta este periodo.</p>
                <a href="#" class="card-link">Ver asignaturas &rarr;</a>
            </div>
            <div class="card">
                <h3>Cronograma de Clases</h3>
                <p>Visualiza la distribución de tus bloques de horas asignados en la semana.</p>
                <a href="#" class="card-link">Consultar horario &rarr;</a>
            </div>
        </div>
    </div>

</body>
</html>