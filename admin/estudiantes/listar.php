<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

require_once '../../conexion.php';

$sql = "
    SELECT 
        estudiantes.id_estudiante,
        personas.nombre,
        personas.apellido,
        estudiantes.email,
        personas.telefono,
        niveles.nivel_academico,
        estudiantes.id_nivel
    FROM estudiantes
    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
    INNER JOIN niveles
        ON estudiantes.id_nivel = niveles.id_nivel
    ORDER BY personas.apellido ASC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Estudiantes</title>
    <link rel="icon" type="image/png" href="../../images/EFB.png">

    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #ffffff; color: #000000; display: flex; min-height: 100vh; }

        body {
    background-color: #ffffff;
    margin: 0;
    padding: 0;
}

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

        /* --- CONTENEDOR PRINCIPAL PARA EVITAR QUE SE META DEBAJO DEL SIDEBAR --- */
.main-content {
    margin-left: 260px; /* El mismo ancho de tu sidebar */
    width: calc(100% - 260px); /* 👈 1. ESTO obliga a ocupar todo el espacio derecho restante */
    box-sizing: border-box;    /* 👈 2. ESTO evita que el padding deforme la pantalla */
    padding: 2.5rem;
    color: #000000;
    min-height: 100vh;
    background-color: #eaeaea;
}

.main-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.main-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
}

/* --- ESTILOS DE LA TABLA MODERNA --- */
.custom-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #111111;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.custom-table th {
    background-color: #161616;
    color: #3a7bc8; /* Color azul de tu diseño */
    font-weight: 600;
    text-align: left;
    padding: 1rem;
    font-size: 0.9rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.05);
}

.custom-table td {
    padding: 1rem;
    color: #e0e0e0;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.custom-table tr:hover {
    background-color: rgba(255, 255, 255, 0.02); /* Efecto sutil al pasar el mouse */
}

/* --- BOTONES DE ACCIÓN --- */
.btn-add {
    background-color: #3a7bc8;
    color: #fff;
    text-decoration: none;
    padding: 0.6rem 1.2rem;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: background 0.2s;
}
.btn-add:hover { background-color: #2b5f9e; }

.btn-action {
    text-decoration: none;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
    margin-right: 0.3rem;
}

.btn-edit {
    background-color: rgba(58, 123, 200, 0.15);
    color: #3a7bc8;
}
.btn-edit:hover { background-color: rgba(58, 123, 200, 0.25); }

.btn-delete {
    background-color: rgba(255, 85, 85, 0.15);
    color: #ff5555;
}
.btn-delete:hover { background-color: rgba(255, 85, 85, 0.25); }

    </style>    

</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="../../images/EFB.png" alt="Logo">
        <h2>Administrador</h2>
    </div>
        <ul class="menu-links">
        <li><a href="../index.php" class=" active">Gestión Central</a></li>
        <li><a href="estudiantes/listar.php" class="active">Gestión de Estudiantes</a></li>
        <li><a href="../reportes/reporte_estudiantes.php" class="active">Reporte de Estudiantes</a></li>
         <li><a href="../../logout.php" class="btn-logout">Cerrar Sesión</a></li>
    </div>
     </aside>
    
<!-- Busca donde empieza el contenido principal (debajo de tu sidebar) -->
<main class="main-content">
    
    <div class="main-header">
        <h1>Lista de Estudiantes</h1>
        <!-- Movimos el botón de agregar aquí arriba para que se vea ordenado -->
        <a href="agregar.php" class="btn-add">Agregar nuevo estudiante</a>
    </div>

    <!-- Quitamos el border="1" viejo y le ponemos nuestra clase -->
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Nivel Académico</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['id_estudiante']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($fila['email']); ?></td>
                    <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                    <td>
                        <!-- Agregamos las clases nuevas a tus links de acción -->
                        <a href="editar.php?id=<?php echo $fila['id_estudiante']; ?>" class="btn-action btn-edit">Editar</a>
                        <a href="eliminar.php?id=<?php echo $fila['id_estudiante']; ?>" class="btn-action btn-delete">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</main>
</body>
</html>
