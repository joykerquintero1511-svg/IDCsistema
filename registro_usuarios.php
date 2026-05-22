<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Personal - EFB</title>
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body style="background-color: #0c0c0c;">

    <main class="s-content" style="padding: 5rem 0;">
        <div style="max-width: 500px; margin: 0 auto; background: #111111; padding: 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.7);">
            
            <div style="text-align: center; margin-bottom: 3rem;">
                <img src="images/EFB.png" alt="Logo EFB" style="max-width: 130px; margin-bottom: 1.5rem;">
                <h2 style="color: #ffffff; margin: 0; font-size: 2.8rem;">Registro de Personal</h2>
                <p style="color: rgba(255,255,255,0.4); margin: 0; font-size: 1.4rem;">Profesores y Administradores</p>
            </div>

            <form action="procesar_registro_usuarios.php" method="POST">
                
                <div style="margin-bottom: 2rem;">
                    <label style="color: #fff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem;">Nombre Completo</label>
                    <input class="u-fullwidth" type="text" name="nombre" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem;">
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="color: #fff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem;">Correo Electrónico</label>
                    <input class="u-fullwidth" type="email" name="email" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem;">
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="color: #fff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem;">Contraseña de Acceso</label>
                    <input class="u-fullwidth" type="password" name="password" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem;">
                </div>


</div> <div style="margin-bottom: 2rem;">
    <label style="color: #fff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem;">Tipo de Cuenta</label>
    <select name="rol" id="selector-rol" onchange="evaluarRol()" required style="width: 100%; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem; cursor: pointer;">
        <option value="estudiante" style="background: #111; color: #fff;">Estudiante (Acceso a clases y notas)</option>
        <option value="profesor" style="background: #111; color: #fff;">Profesor (Subir notas y evaluar)</option>
        <option value="admin" style="background: #111; color: #fff;">Administrador (Control Total)</option>
    </select>
</div>

<div id="bloque-codigo" style="margin-bottom: 3.5rem; display: none;">
    <label style="color: #fff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem; font-weight: bold;">Clave de Autorización Especial</label>
    <input class="u-fullwidth" type="password" name="codigo_autorizacion" id="codigo_autorizacion" placeholder="Introduce la clave de la escuela" style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem;">
</div>
<button type="submit" class="btn btn--primary u-fullwidth" style="height: 5.5rem; line-height: 5.5rem;">
    Registrar Cuenta
</button>

</form>
</div>
</main>

<script>
function evaluarRol() {
    var rolSeleccionado = document.getElementById("selector-rol").value;
    var bloqueCodigo = document.getElementById("bloque-codigo");
    var inputCodigo = document.getElementById("codigo_autorizacion");

    if (rolSeleccionado === "profesor" || rolSeleccionado === "admin") {
        bloqueCodigo.style.display = "block";
        inputCodigo.required = true;
    } else {
        bloqueCodigo.style.display = "none";
        inputCodigo.required = false;
        inputCodigo.value = "";
    }
}
</script>

</body>
</html>
</script>