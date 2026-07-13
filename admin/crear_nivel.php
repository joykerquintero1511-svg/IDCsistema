<?php
    session_start();// Es como una "llave". Sin esto, el sistema no sabe quién está entrando y no te dejaría guardar nada por seguridad.
include("conexion.php"); // este comando l dice a crear_nivel.php , Oye, usa la configuración de conexión que ya existe para que podamos guardar cosas en la base de datos".

if ($_SERVER["REQUEST_METHOD"] == "POST"){ // Esto es como una puerta de seguridad.Solo deja que el código de adentro se ejecute si realmente se presionó un botón de "Enviar" o "Guardar" en el formulario.
    // Aqui capturamos los datos q vienen del formulario
    $nombre_nivel = $_POST['nombre_nivel'];
    $codigo_nivel = $_POST['codigo_nivel'];
    $nivel_academico = $_POST['nivel_academico'];
    $periodo_academico = date("Y"); // ESTO obtiene el año actual (2026) automáticamente

// Validamos q los campos no esten vacio,osea para q no envie informacion vacia

if(!empty($nombre_nivel) && !empty($codigo_nivel)){
  # Aca pondremos el codigo para guardar en la BD  
    echo " Todo esta listo para Guardar";

# Ya validamos que los datos no lleguen vacíos,Ahora el siguiente es preparar la orden para la base de datos.usamos algo llamado INSERT INTO (insertar adentro). Esto es lo que le dice: "Toma estos valores y mételos en la tabla de niveles".

    // Consulta SQL
    $sql = "INSERT INTO niveles (nivel_academico , codigo_nivel, periodo_academico) VALUES ('$nivel_academico', '$codigo_nivel', '$periodo_academico')"; 
    if (mysqli_query($conexion, $sql)) { // Con mysqli_query sirve para que tu archivo actual sepa cómo conectarse a la base de datos
    echo " ¡Nivel guardado con éxito";

    }else{
        echo "Error" . mysqli_error($conexion);
    }
 }else{
    echo "Por favor,completa todos los campos";
    }
}
 ?>
 <!DOCTYPE html>
 <html lang="es">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
 </head>
 <body>
    <h1> Registro de Niveles</h1>

    <form action= "crear_nivel.php" method="POST">
    <input type="text" name="nombre_nivel" placeholder="Nombre del Nivel" required>
    <input type="text" name="codigo_nivel" placeholder="Codigo del Nivel" required>
     <input type="text" name="nivel_academico" placeholder="Nivel_Academico" required>
    <button type="submit">Guardar</button> 
  
  </form>
    
 </body>
 </html>   