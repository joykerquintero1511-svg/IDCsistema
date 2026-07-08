<?php
session_start();

// Si no existe la sesión "usuario", redirige al login de una vez
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php"); // Ajusta la ruta a tu archivo de login real
    exit();
}

include '../conexion.php';

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #ffffff; color: #000000; display: flex; min-height: 100vh; }

        /* BARRA LATERAL */
        .sidebar { width: 260px; background-color: #111111; border-right: 1px solid rgba(255, 255, 255, 0.05); padding: 2.5rem 1.5rem; display: flex; flex-direction: column; justify-content: flex-start; position: fixed; height: 100vh; z-index: 10; }
        .sidebar-brand { display: flex; align-items: center; gap: 1rem; margin-bottom: 3rem; }
        .sidebar-brand img { max-width: 35px; height: auto; }
        .sidebar-brand h2 { font-size: 1.2rem; font-weight: bold; color: #fff; letter-spacing: 1px; }
        
        .menu-links { list-style: none; display: flex; flex-direction: column; gap: 0.8rem; }
        .menu-links a { color: #a0a0a0; text-decoration: none; padding: 0.8rem 1.2rem; border-radius: 6px; display: block; font-size: 1rem; font-weight: 500; transition: all 0.3s ease; }
        .menu-links a:hover, .menu-links a.active { background: rgba(36, 82, 133, 0.15); color: #3a7bc8; font-weight: bold; }
        
        .btn-logout { color: #f53030; text-decoration: none; padding: 0.8rem 1.2rem; font-weight: bold; border-radius: 6px; transition: background 0.3s; }
        .btn-logout:hover { background: rgba(255, 85, 85, 0.24); }

        /* CONTENEDOR PRINCIPAL REALINEADO */
        .main-content { 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            padding: 3.5rem; 
            flex-grow: 1;
        }
        
        .welcome-header { margin-bottom: 3rem; }
        .welcome-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .welcome-header p { color: #666; font-size: 1rem; }

        /* REJILLA TOTALMENTE EXPANDIDA */
        .dashboard-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 2.5rem; 
            width: 100%;
        }

        @media (min-width: 1024px) {
            .dashboard-grid { 
                grid-template-columns: 2fr 1fr; /* Columna izquierda grande, derecha agenda */
            }
        }

        .info-card { background: #021326da; border: 1px solid rgba(56, 55, 55, 0.05); padding: 2rem; border-radius: 8px; width: 100%; }
        .info-card h3 { font-size: 1.1rem; color: #ffffff; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; }0

        .card-link { color: #f6f8f9; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 1rem; }

        .task-list, .class-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; }
        .task-item, .class-item { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem; background: rgba(255,255,255,0.02); border-radius: 6px; border-left: 4px solid #245285; }
        .class-item { border-left-color: #3a7bc8; }
        
        .item-info h4 { font-size: 1.05rem; margin-bottom: 0.3rem; color: #fff; }
        .item-info p { color: #666; font-size: 0.9rem; }
        .item-info span { color: #a0a0a0; font-weight: 500; }
        
        .btn-action { color: #3a7bc8; text-decoration: none; font-size: 0.95rem; font-weight: bold; }
        .btn-action:hover { text-decoration: underline; }

        .data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .data-table th { color: #666; font-size: 0.85rem; text-transform: uppercase; padding: 1rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .data-table td { padding: 1rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03); color: #e0e0e0; font-size: 0.95rem; }
        .badge-nota { background: rgba(36, 82, 133, 0.2); color: #3a7bc8; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; }
        .no-data { color: #555; font-style: italic; text-align: center; padding: 2rem 0; }

        /* Blanquear el icono del calendario nativo en inputs de fecha */
        input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
}
    </style>
</head>
<body>
        <aside class="sidebar">
    <div class="sidebar-brand">
        <img src="../images/EFB.png" alt="Logo">
        <h2>Administrador</h2>
    </div>
        <ul class="menu-links">
        <li><a href="index.php" class=" active">Gestión Central</a></li>
        <li><a href="estudiantes/listar.php" class="active">Gestión de Estudiantes</a></li>
        <li><a href="reportes/reporte_estudiantes.php" class="active">Reporte de Estudiantes</a></li>
         <li><a href="../logout.php" class="btn-logout">Cerrar Sesión</a></li>
    </div>
     </aside>

        <main class="main-content">
        <div class="welcome-header">
            <h1><?php echo htmlspecialchars($_SESSION['usuario']); ?><span class="badge">/ Admin</span></h1>
            <p>Control maestro global sobre los parámetros del sistema y accesos de usuarios.</p>
        </div>

        <div class="dashboard-grid">
        
        <div style="display: flex; flex-direction: column; gap: 2.5rem;">
            
            <div class="info-card">
                <h3>Gestión de Personal 👤</h3>
                <p>Audita, aprueba, suspende o da de alta cuentas tanto de docentes como de estudiantes.</p>
                <a href="#" class="card-link">Administrar cuentas &rarr;</a>
            </div>
            <div class="info-card">
                <h3>Seguridad y Logins ⚙️ </h3>
                <p>Supervisa los accesos recientes, bloqueos automáticos y el estado de la base de datos.</p>
                <a href="#" class="card-link">Ver logs de acceso &rarr;</a>
            </div>
            <div class="info-card">
                <h3>Periodo Académico 📅</h3>
                <p>Abre o cierra lapsos para la carga de notas o habilita procesos globales de inscripción.</p>
                <a href="#" class="card-link">Configurar periodos &rarr;</a>
            </div>
        </div>
    </div>

</body>
</html>