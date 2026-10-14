<?php
include("conexion.php"); // Asegúrate de tener esto al inicio
$query_niveles = "SELECT id_nivel, nivel_academico FROM niveles"; // Ajusta 'nivel_academico' al nombre real de tu columna
$result_niveles = mysqli_query($conexion, $query_niveles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <style>
        /* Mantenemos tus estilos base exactos */
        body { background-color: #0c0c0c; font-family: sans-serif; }
        .s-content { padding: 5rem 0; }
        .main-card { max-width: 500px; margin: 0 auto; background: #111111; padding: 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05); }
        
        /* Forzamos a que tanto inputs como el select y el botón respeten el ancho interno */
        .u-fullwidth { width: 100%; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; font-size: 1.1rem; outline: none; box-sizing: border-box; }
        
        /* El botón con tu color azul original pero contenido */
        .btn-primary { background-color: #245285; border: none; cursor: pointer; font-weight: bold; height: 4.5rem; line-height: 4.5rem; padding: 0; }
        .btn-primary:hover { background-color: #1c426d; }

        .form-group { 
            margin-bottom: 20px; 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
        }
        
        .form-group label { 
            font-size: 0.85rem; 
            color: #888888; 
            font-weight: 500; 
        }
        
        .form-control { 
            background-color: #161b22; 
            border: 1px solid #30363d; 
            border-radius: 6px; 
            padding: 11px 14px; 
            color: #ffffff; 
            font-size: 0.95rem; 
            transition: border-color 0.2s; 
            width: 100%; 
        }
        
        .form-control:focus { 
            outline: none; 
            border-color: #58a6ff; 
        }
        
        select.form-control { 
            cursor: pointer; 
            color-scheme: dark; 
        }
        
        textarea.form-control { 
            resize: vertical; 
            min-height: 110px; 
        }

    </style>
</head>
<body>

    <main class="s-content">
        <div class="main-card">
            
            <div style="text-align: center; margin-bottom: 3rem;">
                <img src="images/EFB.png" alt="Logo EFB" style="max-width: 100px; height: auto; margin-bottom: 1.5rem;">
                <h2 style="color: #fff; margin: 0; font-size: 2rem;">Registro de Personal</h2>
                <p style="color: #666; margin-top: 0.5rem; font-size: 1.1rem;">Profesores, Administradores y Estudiantes</p>
            </div>

            <form action="procesar_registro_usuarios.php" method="POST">
                
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input class="u-fullwidth" type="text" name="nombre" required placeholder="Ej. Juan Pérez">
                </div>

                <div class="form-group">
    <label>Correo Electrónico</label>
    <input class="u-fullwidth" type="email" name="email" id="email" required placeholder="correo@ejemplo.com">
    <span id="msg-email" class="mensaje-validacion"></span>
</div>
                <div class="form-group">
                <label>Nivel Académico</label>
                <select class="form-control" name="id_nivel">
                <option value="">-- Seleccione el nivel --</option>
                <?php if ($result_niveles): ?>
                <?php while($nivel = mysqli_fetch_assoc($result_niveles)): ?>
                <option value="<?php echo $nivel['id_nivel']; ?>">
                    <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                </option>
            <?php endwhile; ?>
        <?php endif; ?>
    </select>
</div>


                <div class="form-group">
                    <label>Contraseña de Acceso</label>
                    <input class="u-fullwidth" type="password" name="password" required placeholder="••••••••">
                </div>


                <div class="form-group">
                    <label>Tipo de Cuenta</label>
                    <select name="rol" id="selector-rol" class="u-fullwidth" onchange="evaluarRol()" required style="height: 4.5rem;">
                        <option value="estudiante" style="background: #111; color: #fff;">Estudiante (Acceso a clases y notas)</option>
                        <option value="profesor" style="background: #111; color: #fff;">Profesor (Subir notas y evaluar)</option>
                        <option value="admin" style="background: #111; color: #fff;">Administrador (Control Total)</option>
                    </select>
                </div>


                <div id="bloque-codigo" class="form-group" style="display: none; margin-bottom: 3.5rem;">
                    <label style="font-weight: bold;">Clave de Autorización Especial</label>
                    <input class="u-fullwidth" type="password" name="codigo_autorizacion" id="codigo_autorizacion" placeholder="Introduce la clave de la escuela">
                </div>

                <button type="submit" class="u-fullwidth btn-primary">Registrar Cuenta</button>

                <div style="text-align: center; margin-top: 2.5rem;">
                    <a href="index.php" style="color: rgba(255, 255, 255, 0.4); font-size: 1.3rem; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255, 255, 255, 0.4)'">
                        Volver a la Página Principal
                    </a>
                </div>

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

   <script>
document.addEventListener('DOMContentLoaded', function() {
    let esCorreoValido = false;

    const inputEmail = document.getElementById('email');
    const spanMensaje = document.getElementById('msg-email');
    const miFormulario = inputEmail ? inputEmail.closest('form') : document.querySelector('form');

    // 1. INYECCIÓN AUTOMÁTICA DE ESTILOS (Hace que la alerta sea 100% independiente del CSS)
    if (!document.getElementById('estilos-alerta-autonomos')) {
        const estiloModal = document.createElement('style');
        estiloModal.id = 'estilos-alerta-autonomos';
        estiloModal.innerHTML = `
            .modal-alerta-bg {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                background: rgba(0, 0, 0, 0.8) !important;
                backdrop-filter: blur(4px) !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                z-index: 999999 !important;
            }
            .modal-alerta-box {
                background: #1e293b !important;
                border: 1px solid #334155 !important;
                border-radius: 12px !important;
                padding: 28px 24px !important;
                max-width: 380px !important;
                width: 90% !important;
                text-align: center !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.6) !important;
                color: #ffffff !important;
                font-family: system-ui, -apple-system, sans-serif !important;
            }
            .modal-alerta-box h3 {
                margin: 12px 0 8px 0 !important;
                color: #ffffff !important;
                font-size: 1.25rem !important;
                font-weight: bold !important;
            }
            .modal-alerta-box p {
                color: #cbd5e1 !important;
                font-size: 0.95rem !important;
                line-height: 1.4 !important;
                margin-bottom: 22px !important;
            }
            .modal-alerta-btn {
                background: linear-gradient(135deg, #0976fc 0%, #0042a5 100%) !important;
                color: #ffffff !important;
                border: none !important;
                padding: 10px 26px !important;
                border-radius: 20px !important;
                font-weight: bold !important;
                font-size: 0.9rem !important;
                cursor: pointer !important;
                box-shadow: 0 4px 12px rgba(9, 118, 252, 0.4) !important;
            }
            .modal-alerta-btn:hover {
                background: linear-gradient(135deg, #1d82ff 0%, #0052cc 100%) !important;
            }
        `;
        document.head.appendChild(estiloModal);
    }

    // 2. FUNCIÓN PARA MOSTRAR EL MODAL
    function mostrarAlerta(mensaje, titulo = 'Atención', icono = '❌') {
        let overlay = document.getElementById('modal-alerta-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'modal-alerta-overlay';
            overlay.className = 'modal-alerta-bg';
            document.body.appendChild(overlay);
        }

        overlay.innerHTML = `
            <div class="modal-alerta-box">
                <div style="font-size: 2.8rem; line-height: 1;">${icono}</div>
                <h3>${titulo}</h3>
                <p>${mensaje}</p>
                <button type="button" class="modal-alerta-btn" onclick="cerrarModalAlerta()">Entendido</button>
            </div>
        `;
        overlay.style.display = 'flex';
    }

    // FUNCIÓN GLOBAL PARA CERRAR
    window.cerrarModalAlerta = function() {
        const overlay = document.getElementById('modal-alerta-overlay');
        if (overlay) overlay.style.display = 'none';
    };

    // 3. VALIDACIÓN AJAX AL PERDER EL FOCO (BLUR)
    if (inputEmail) {
        inputEmail.addEventListener('blur', function() {
            let valor = this.value.trim();

            if (valor === '') {
                if (spanMensaje) spanMensaje.textContent = '';
                this.style.borderColor = '';
                esCorreoValido = false;
                return;
            }

            let formData = new FormData();
            formData.append('email', valor);

            fetch('validar_campo.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (spanMensaje) spanMensaje.textContent = data.mensaje;

                if (data.status === 'error') {
                    if (spanMensaje) spanMensaje.style.color = '#ff4d4d';
                    this.style.borderColor = '#ff4d4d';
                    esCorreoValido = false;
                } else {
                    if (spanMensaje) spanMensaje.style.color = '#2ecc71';
                    this.style.borderColor = '#2ecc71';
                    esCorreoValido = true;
                }
            })
            .catch(err => console.error('Error en validación AJAX:', err));
        });
    }

    // 4. INTERCEPCIÓN DEL BOTÓN ENVIAR (SUBMIT)
    if (miFormulario) {
        miFormulario.addEventListener('submit', function(e) {
            if (!esCorreoValido) {
                e.preventDefault(); // Bloquea el envío del formulario
                
                mostrarAlerta(
                    'El correo ingresado ya existe en la base de datos o aún no ha sido validado.',
                    'Correo No Disponible',
                    '❌'
                );
                
                if (inputEmail) inputEmail.focus();
            }
        });
    }
});
</script>
</body>
</html>