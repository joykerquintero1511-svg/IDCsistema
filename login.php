<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - EFB</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body style="background-color: #0c0c0c;">

    <main class="s-content" style="padding: 8rem 0;">
        <div style="max-width: 450px; margin: 0 auto; background: #111111; padding: 5rem 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.7);">
            
            <div style="text-align: center; margin-bottom: 3.5rem;">
                <img src="images/EFB.png" alt="Logo EFB" style="max-width: 140px; margin-bottom: 1.5rem;">
                <h2 style="color: #ffffff; margin: 0; font-size: 2.8rem; letter-spacing: 0.5px;">Acceso al Sistema</h2>
                <p style="color: rgba(255,255,255,0.4); margin: 0.5rem 0 0 0; font-size: 1.4rem;">Panel Administrativo / Docente</p>
            </div>

            <form action="procesar_login.php" method="POST">
                
                <div style="margin-bottom: 2.5rem;">
                    <label style="color: #ffffff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem;">Correo Electrónico</label>
                    <input class="u-fullwidth" type="email" name="email" required placeholder="ejemplo@correo.com" 
                           style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem; margin-bottom: 0;">
                </div>

                <div style="margin-bottom: 3.5rem;">
                    <label style="color: #ffffff; display: block; margin-bottom: 0.8rem; font-size: 1.4rem;">Contraseña</label>
                    <input class="u-fullwidth" type="password" name="password" required placeholder="••••••••" 
                           style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); height: 5rem; margin-bottom: 0;">
                </div>

                <button type="submit" class="btn btn--primary u-fullwidth" style="height: 5.5rem; line-height: 5.5rem; margin-bottom: 2rem;">
                    Ingresar al Sistema
                </button>

                <div style="text-align: center; margin-top: 2.5rem;">
                    <a href="registro_usuarios.php" style="color: rgba(255, 255, 255, 0.4); font-size: 1.3rem; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255, 255, 255, 0.4)'">
                        ¿No tienes cuenta de personal? Regístrate aquí
                    </a>
                </div>

            </form>
        </div>
    </main>

</body>
</html>