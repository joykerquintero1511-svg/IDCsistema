<?php
session_start();
// Si no existe la sesión "usuario", redirige al login de una vez
if (!isset($_SESSION['usuario'])) {
    echo '<script>window.location.replace("../login.php");</script>';
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
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="icon" type="image/png" href="../images/EFB.png">
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
                <a href="#" class="link">Administrar cuentas &rarr;</a>
            </div>
            <div class="info-card">
                <h3>Seguridad y Logins ⚙️</h3>
                <p>Supervisa los accesos recientes, bloqueos automáticos y el estado de la base de datos.</p>
                <a href="#" class="link">Ver logs de acceso &rarr;</a>
            </div>
            <div class="info-card">
                <h3>Periodo Académico 📅</h3>
                <p>Abre o cierra lapsos para la carga de notas o habilita procesos globales de inscripción.</p>
                <a href="periodos.php" class="link">Configurar periodos academicos &rarr;</a>
            </div>
        </div>
    </div>
        </main>

        <!-- AQUÍ LLAMAS A TU SCRIPT MATA-FANTASMAS -->
    <?php include '../script-seguridad.php'; ?>

</body>
</html>