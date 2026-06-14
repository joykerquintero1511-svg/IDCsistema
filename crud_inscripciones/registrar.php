<?php require_once('../conexion.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Inscripción</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        label { display: inline-block; width: 150px; margin-bottom: 10px; }
        input, select { padding: 5px; width: 200px; margin-bottom: 10px; }
        button { padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .cancelar { margin-left: 10px; text-decoration: none; background: gray; color: white; padding: 10px 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1> Nueva Inscripción</h1>
    <form action="guardar.php" method="POST">
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
        <a href="listar.php" class="cancelar">Cancelar</a>
    </form>
</body>
</html>