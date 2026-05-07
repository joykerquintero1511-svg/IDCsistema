<?php
    require_once '../conexion.php'; // conexión a la BD (más seguro que include)
   

 $conexion = $conexion ?? null; 
/* Significa:

“si $conexion no existe, créala como null”

 Para PHP → no cambia nada
 Para VS Code → deja de marcar error,xq se tildaba
 $conexion(linea 56) x la extension intelephense q pensaba no
 estaba declarada la variable igual corria el sistema
 pero no m gustaba cm se veia.y tambien xq se usa
 require_once,la variable viene de otro archivo y
 el codigo funciona bien.
*/
?>

    <!DOCTYPE html>
    <html lang="es"> <!-- Coloco "es" xq la pag estara en español, "en"significa ingles-->
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
                <th>Acciones</th>
            </tr>    
         </thead>
         <tbody> <!-- Cuerpo de la tabla (donde van los datos)-->

          <?php
          $sql = "SELECT * FROM estudiantes";  // Consulta SQL para seleccionar todos los estudiantes

           
 /* Cree esta  global $conexion; para decirle 
 “hey PHP, esa variable que viene de otro archivo, úsala aquí también */


       $resultado = ejecutarConsulta($conexion, $sql); // Nva funcion creada en conexion
 

    while ($fila= mysqli_fetch_assoc($resultado)){ // Recorre cada fila de resultados obtenidos
           
            echo "<tr>"; // Imprime una celda con el dato del estudiante

            echo "<td>" . htmlspecialchars($fila['id_estudiante']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['email']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['nivel_academico']) . "</td>";
     // aca abajo viene el boton de EDITAR Y ELIMINAR, | SIGNIFICA UNA SEPARACION ENTRE LOS DOS BOTONES        
             echo "<td> 
                      <a href='editar.php?id=" . $fila['id_estudiante'] . "'>Editar</a> |
                        <a href='eliminar.php?id=" . $fila['id_estudiante'] . "' onclick='return confirm(\"¿Eliminar este estudiante?\")'>Eliminar</a>
                     </a>";

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