<?php
include("conexion.php");
$query_niveles = "SELECT id_nivel, nivel_academico FROM niveles";
$result_niveles = mysqli_query($conexion,$query_niveles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <style>
        body { background-color: #0c0c0c; font-family: sans-serif; }
        .s-content { padding: 5rem 0; }
        .main-card { max-width: 500px; margin: 0 auto; background: #111111; padding: 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05); }
        
        .u-fullwidth { width: 100%; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; font-size: 1.1rem; outline: none; box-sizing: border-box; }
        
        .btn-primary { background-color: #245285; border: none; cursor: pointer; font-weight: bold; height: 4.5rem; line-height: 4.5rem; padding: 0; }
        .btn-primary:hover { background-color: #1c426d; }
        .btn-primary:disabled { background-color: #333; color: #666; cursor: not-allowed; opacity: 0.6; }

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

            <form action="procesar_registro_usuarios.php" method="POST" id="form-registro">
                
                <!-- CÉDULA (Primer campo para buscar a la persona) -->
                <div class="form-group">
                    <label>Cédula de Identidad</label>
                    <input class="u-fullwidth" type="text" name="cedula" id="cedula" required placeholder="Ej. 30999887">
                    <span id="msg-cedula" style="font-size: 0.85rem; display: block; margin-top: 4px;"></span>
                </div>

                <!-- NOMBRE COMPLETO (Autocompletado y bloqueado) -->
                <div class="form-group">
                    <label>Nombre Completo (Registrado en la escuela)</label>
                    <input class="u-fullwidth" type="text" name="nombre" id="nombre" readonly required placeholder="Se completará automáticamente al ingresar la cédula" style="background: rgba(255,255,255,0.02); color: #888; cursor: not-allowed;">
                    <input type="hidden" name="id_persona" id="id_persona">
                </div>

                <!-- CORREO ELECTRÓNICO (Autocompletado y verificado) -->
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input class="u-fullwidth" type="email" name="email" id="email" readonly required placeholder="correo@ejemplo.com" style="background: rgba(255,255,255,0.02); color: #888; cursor: not-allowed;">
                    <span id="msg-email" style="font-size: 0.85rem; display: block; margin-top: 4px; color: #2ecc71;">✔ Verificado automáticamente por el sistema</span>
                </div>

                <!-- NIVEL ACADÉMICO (Autocompletado y bloqueado) -->
                <div class="form-group">
                    <label>Nivel Académico (Asignado en su inscripción)</label>
                    <input class="u-fullwidth" type="text" id="nivel_texto" readonly placeholder="Se cargará automáticamente al ingresar la cédula" style="background: rgba(255,255,255,0.02); color: #888; cursor: not-allowed;">
                    <input type="hidden" name="id_nivel" id="id_nivel">
                </div>

                <div class="form-group">
                    <label>Contraseña de Acceso</label>
                    <input class="u-fullwidth" type="password" name="password" id="password" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input class="u-fullwidth" type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                    <span id="msg-password" style="font-size: 0.85rem; display: block; margin-top: 4px;"></span>
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

                <!-- Botón desactivado por defecto hasta que coloquen una cédula válida -->
                <button type="submit" id="btn-registrar" class="u-fullwidth btn-primary" disabled>Registrar Cuenta</button>

                <div style="text-align: center; margin-top: 2.5rem;">
                    <a href="index.php" style="color: rgba(255, 255, 255, 0.4); font-size: 1.3rem; text-decoration: none;">
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
        
        var inputNombre = document.getElementById('nombre');
        var inputEmail = document.getElementById('email');
        var inputNivelTexto = document.getElementById('nivel_texto');
        var inputIdNivel = document.getElementById('id_nivel');
        var inputCedula = document.getElementById('cedula');
        var btnRegistrar = document.getElementById('btn-registrar');
        var spanCedula = document.getElementById('msg-cedula');

        if (rolSeleccionado === "profesor" || rolSeleccionado === "admin") {
            // Mostrar clave especial
            bloqueCodigo.style.display = "block";
            inputCodigo.required = true;

            // Liberar campos para escritura manual
            inputNombre.readOnly = false;
            inputNombre.style.background = "#161b22";
            inputNombre.style.color = "#ffffff";
            inputNombre.style.cursor = "text";
            inputNombre.placeholder = "Ingrese su nombre completo";
            inputNombre.value = "";

            inputEmail.readOnly = false;
            inputEmail.style.background = "#161b22";
            inputEmail.style.color = "#ffffff";
            inputEmail.style.cursor = "text";
            inputEmail.placeholder = "correo@ejemplo.com";
            inputEmail.value = "";

            // En el nivel colocamos Personal Docente / Administrativo
            inputNivelTexto.value = "Personal Docente / Administrativo";
            inputIdNivel.value = "0"; 

            // Habilitar botón de registro directamente para personal
            btnRegistrar.disabled = false;
            spanCedula.textContent = "";
            inputCedula.style.borderColor = "";

        } else {
            // Si es Estudiante, restaurar flujo automático por cédula
            bloqueCodigo.style.display = "none";
            inputCodigo.required = false;
            inputCodigo.value = "";

            // Volver a bloquear para autocompletado por cédula
            inputNombre.readOnly = true;
            inputNombre.style.background = "rgba(255,255,255,0.02)";
            inputNombre.style.color = "#888";
            inputNombre.style.cursor = "not-allowed";
            inputNombre.value = "";
            inputNombre.placeholder = "Se completará automáticamente al ingresar la cédula";

            inputEmail.readOnly = true;
            inputEmail.style.background = "rgba(255,255,255,0.02)";
            inputEmail.style.color = "#888";
            inputEmail.style.cursor = "not-allowed";
            inputEmail.value = "";
            inputEmail.placeholder = "correo@ejemplo.com";

            inputNivelTexto.value = "";
            inputIdNivel.value = "";

            // Exigir validación por cédula de nuevo
            btnRegistrar.disabled = true;
        }
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCedula = document.getElementById('cedula');
    const spanCedula = document.getElementById('msg-cedula');
    const inputNombre = document.getElementById('nombre');
    const inputIdPersona = document.getElementById('id_persona');
    const inputEmail = document.getElementById('email');
    const btnRegistrar = document.getElementById('btn-registrar');

    const inputPass = document.getElementById('password');
    const inputConfirmPass = document.getElementById('confirm_password');
    const spanPass = document.getElementById('msg-password');
    let sonContrasenasValidas = false;
    let cedulaValida = false;

    // BÚSQUEDA AUTOMÁTICA DE CÉDULA EXCLUSIVA PARA ESTUDIANTES
    inputCedula.addEventListener('blur', async function() {
        let rolActual = document.getElementById("selector-rol").value;
        
        // Si es profesor o admin, no se requiere la búsqueda automática por cédula
        if (rolActual !== 'estudiante') return;

        let cedulaVal = this.value.trim();

        if (cedulaVal === '') {
            spanCedula.textContent = '';
            inputCedula.style.borderColor = '';
            inputNombre.value = '';
            inputEmail.value = '';
            inputIdPersona.value = '';
            document.getElementById('nivel_texto').value = '';
            document.getElementById('id_nivel').value = '';
            btnRegistrar.disabled = true;
            cedulaValida = false;
            return;
        }

        let formData = new FormData();
        formData.append('cedula', cedulaVal);

        try {
            // Usamos tu archivo dedicado de búsqueda para no alterar validar_campo.php de inscripciones
            let res = await fetch('buscar_persona_registro.php', { method: 'POST', body: formData });
            let data = await res.json();

            spanCedula.textContent = data.mensaje;

            if (data.encontrado) {
                spanCedula.style.color = '#2ecc71';
                inputCedula.style.borderColor = '#2ecc71';
                
                // Rellenamos los datos automáticamente del estudiante
                inputNombre.value = data.nombre_completo;
                inputEmail.value = data.correo;
                inputIdPersona.value = data.id_persona;
                
                // Rellenamos el nivel académico automatizado
                document.getElementById('nivel_texto').value = data.nivel_academico;
                document.getElementById('id_nivel').value = data.id_nivel;
                
                cedulaValida = true;
                btnRegistrar.disabled = false;
            } else {
                spanCedula.style.color = '#ff4d4d';
                inputCedula.style.borderColor = '#ff4d4d';
                
                inputNombre.value = '';
                inputEmail.value = '';
                inputIdPersona.value = '';
                document.getElementById('nivel_texto').value = '';
                document.getElementById('id_nivel').value = '';
                
                cedulaValida = false;
                btnRegistrar.disabled = true;
            }
        } catch (err) {
            console.error('Error al buscar la cédula:', err);
        }
    });

    // VALIDAR CONTRASEÑAS
    function validarContrasenas() {
        if (!inputPass || !inputConfirmPass) return;
        const pass = inputPass.value;
        const confirmPass = inputConfirmPass.value;

        if (confirmPass === '') {
            spanPass.textContent = '';
            inputConfirmPass.style.borderColor = '';
            sonContrasenasValidas = false;
            return;
        }

        if (pass !== confirmPass) {
            spanPass.textContent = '❌ Las contraseñas no coinciden.';
            spanPass.style.color = '#ff4d4d';
            inputConfirmPass.style.borderColor = '#ff4d4d';
            sonContrasenasValidas = false;
        } else {
            spanPass.textContent = '✓ Las contraseñas coinciden.';
            spanPass.style.color = '#2ecc71';
            inputConfirmPass.style.borderColor = '#2ecc71';
            sonContrasenasValidas = true;
        }
    }

    if (inputPass && inputConfirmPass) {
        inputPass.addEventListener('input', validarContrasenas);
        inputConfirmPass.addEventListener('input', validarContrasenas);
    }
});
</script>
</body>
</html>