<?php
    include'../conexion.php'; // para la conexion con la BD
    include'../nav.php'; // incluye menu de navegacion,y asi el menu aparece en todas las paginas


?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"> <!--	Asegura que los textos con tildes y la ñ se vean bien-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--Hace que la página sea responsive (se adapte a móviles)-->
        <title>Lista de Estudiantes</title>
        <link rel="stylesheet" href="../estilos/style.css">
    </head>
    <body>
        <h1> Lista de Estudiantes</h1>

     <table border="1"> <!-- Crea tabla de Bordes visibles-->
<!-- Border esta en rojo xq solo es una manera vieja d colocarlo,hay otra manera pero yo lo deje asi igual funciona-->


            <thead> <!-- Define la sección de encabezado de la tabla-->

            <tr>  <!--Encabezado de cada columna (en negrita)-->
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Nivel Académico</th>
            </tr>    
         </thead>
         <tbody> <!-- Cuerpo de la tabla (donde van los datos)-->
          <?php
          $sql="SELECT * FROM estudiantes";  // Consulta SQL para seleccionar todos los estudiantes
        $resultado = mysqli_query($conexion,$sql);   // Ejecuta la consulta en la base de datos
    
         /* $conexion esta subrayado pero NO ES ERROR 
        solo q la variable viene del archivo conexion.php 
        (la extensión Intelephense)piensa q esa variable
        no est adefinida pero El código funciona perfectamente."
    */
        while ($fila= mysqli_fetch_assoc($resultado)){ // Recorre cada fila de resultados obtenidos
           
            echo "<tr>"; // Imprime una celda con el dato del estudiante

            echo "<td>" . htmlspecialchars($fila['id_estudiante']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['email']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['nivel_academico']) . "</td>";
            echo "</tr>";
        }

     /* htmlspecialchars Convierte caracteres 
     especiales en texto seguro (evita problemas) */
        
     ?>
  </tbody>

</table>
    <br>
        <a href="agregar.php"> Agregar nuevo estudiante</a>
    </body>
    </html>