<!DOCTYPE html>
<html>
    <head>
        <title>Registro de miembros</title>
        <link rel="stylesheet" href="estilos/style.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="background-titulo">
            <div class="header-container">
            <img src="imagenes/logo_azul.png" alt="Logo Iglesia Dios en Casa" class="header-logo">
    <h1 class="header-title">Registro de Miembros IDC</h1>
        </div>
</div>
        <h5 class="subtitulos">Bienvenido al Registro de Miembros</h5>

        <?php include("nav.php"); 
        
        //esta es la barra de navegacion que se incluye en todas las paginas, se llama nav.php y se encuentra en la raiz del proyecto, contiene los enlaces a las diferentes paginas del sitio web, como inicio, ofrendar, registro de miembros, noticias y contacto.

        ?>

        <p class="descripcion">Este formulario  te permite  registrarte en el sitema de miembros IDC.</p>

        <form class="formulario_registro registro-form" action="registrar_usuarios.php" method="POST">
            
            <input class="formulario-input" type="text" id="nombre" name="nombre" placeholder="Nombre" required> <br>

            
            <input class="formulario-input" type="text" id="apellido" name="apellido" placeholder="Apellido" required> <br>

            
            <input class="formulario-input" type="email" id="email" name="email" placeholder="Correo Electrónico" required> <br>

            
            <input class="formulario-input" type="tel" id="telefono" name="telefono" placeholder="Número de Teléfono"> 
            <br>

            <select class="inscripciones-input" type="text" id="red" name="red" required aria-placeholder="Seleccione una Red">
                <option value="Ebenezer">Ebenezer (Lider Mayvel y Vanessa de Mora)</option>
                <option value="Kairos">Kairos (Lider Darwin y Zulimar de Hidalgo)</option>
                <option value="otro">Otro</option>
            </select>
            <br>
            
            
            <button class="boton-input" type="submit">Registrar</button>




        </form>
        
        


 


    </body>
</html>