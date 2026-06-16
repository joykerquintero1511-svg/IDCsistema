<?php require_once('../conexion.php'); ?>
<!DOCTYPE html>
<html>
<head>
    
    <title>Registrar Inscripción</title>
    <link rel="stylesheet" href="../css/vendor.css">
    <link rel="stylesheet" href="../css/styles.css">

   <style>
    .formulario-inscripcion {
        max-width: 600px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.05);
        padding: 2rem;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .formulario-inscripcion label {
        display: block;
        margin-bottom: 0.5rem;
        color: #ffffff;
    }
    .formulario-inscripcion input,
    .formulario-inscripcion select {
        width: 100%;
        padding: 10px;
        margin-bottom: 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        color: #fff;
    }
    .formulario-inscripcion button {
        background: #007bff;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
    }
    .formulario-inscripcion button:hover {
        background: #0056b3;
    }
    .btn-cancelar {
        display: inline-block;
        text-align: center;
        width: 100%;
        margin-top: 1rem;
        padding: 12px;
        background: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 6px;
    }
    .btn-cancelar:hover {
        background: #5a6268;
    }
</style>
</head>
<body class="s-pagewrap ss-home">
    <header class="s-header">
    <div class="container s-header__content">
        <div class="s-header__block">
            <div class="header-logo">
                <a class="logo" href="../index.php">
                    <img src="../images/EFB.png" alt="logoEFB">
                </a>
            </div>
            <a class="header-menu-toggle" href="#0"><span>Menú</span></a>
        </div>
        <nav class="header-nav">
            <ul class="header-nav__links">
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="index.php">Inscripciones</a></li>
                <li><a href="registrar.php" class="active">Nueva Inscripción</a></li>
            </ul>
            <div class="header-contact">
                <a href="../logout.php" class="header-contact__num btn" style="background: #dc3545; color: white;">
                    Cerrar Sesión
                </a>
            </div>
        </nav>
    </div>
</header>
   
    <main class="s-content">
    <section class="container" style="padding: 4rem 2rem;">
        <div class="row">
            <div class="column xl-12">
         <h1> Nueva Inscripción</h1>

    <form action="guardar.php" method="POST" class="formulario-inscripcion">
        <label>ID Estudiante:</label>
        <input type="number" name="id_estudiante" required><br>
        
        <label>Nivel Académico:</label>
        <input type="text" name="nivel_academico" required><br>
        
        <label>Fecha Inscripción:</label>
        <input type="date" name="fecha_inscripcion" required><br>
        
        <label>Periodo:</label>
        <input type="text" name="periodo" placeholder="Ej: 2026-1" required><br>
        
        <label>Estado:</label>
        <select name="estado">
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select><br><br>
        
        <button type="submit">Guardar</button>
       <a href="listar.php" class="btn-cancelar">Cancelar</a>
    </form>
        </div>   <!-- cierra column xl-12 -->
        </div>       <!-- cierra row -->
        </section>       <!-- cierra section -->
</main>   <!-- cierra main -->
<footer style="text-align: center; padding: 3rem 0; background: #0c0c0c; color: rgba(255,255,255,0.4); margin-top: 3rem;">
    <p>© <?php echo date('Y'); ?> Escuela de Formación Bíblica. Todos los derechos reservados.</p>
</footer>

<script src="../js/plugins.js"></script>
<script src="../js/main.js"></script>
</body>
</html>