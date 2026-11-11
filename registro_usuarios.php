<?php
include("conexion.php");

$cedula_ingresada = "";
$nombre_val = "";
$correo_val = "";
$id_persona_val = "";
$id_nivel_val = "";
$nivel_texto_val = "";
$encontrado = false;
$mensaje_cedula = "";
$rol_seleccionado = "estudiante";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rol_seleccionado = $_POST['rol'] ?? 'estudiante';
    
    if (isset($_POST['buscar_cedula']) && $rol_seleccionado === 'estudiante') {
        $cedula_ingresada = trim($_POST['cedula']);
        
        if ($cedula_ingresada !== '') {
            $cedula_esc = mysqli_real_escape_string($conexion, $cedula_ingresada);
            // Ajustado a tu estructura donde el email está en estudiantes o personas según corresponda
            $sql = "SELECT p.id_persona, p.nombre, p.apellido, e.email, 
                           e.id_nivel, n.nivel_academico 
                    FROM personas p
                    LEFT JOIN estudiantes e ON p.id_persona = e.id_persona
                    LEFT JOIN niveles n ON e.id_nivel = n.id_nivel
                    WHERE p.cedula = '$cedula_esc'
                    LIMIT 1";
            
            $res = mysqli_query($conexion, $sql);
            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $encontrado = true;
                $nombre_val = $row['nombre'] . ' ' . $row['apellido'];
                $correo_val = $row['email'] ?? '';
                $id_persona_val = $row['id_persona'];
                $id_nivel_val = $row['id_nivel'] ?? '';
                $nivel_texto_val = $row['nivel_academico'] ?? 'Sin nivel asignado';
                $mensaje_cedula = "👋 ¡Estudiante encontrado con éxito!";
            } else {
                $mensaje_cedula = "❌ Esta cédula no está registrada. Debe inscribirse primero.";
            }
        }
    }
}
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

        .btn-buscar { background-color: #1f6feb; border: none; color: #fff; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 8px; width: 100%; font-size: 0.95rem; }
        .btn-buscar:hover { background-color: #388bfd; }

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

            <!-- Usamos novalidate para evitar que el navegador bloquee la verificación por falta de contraseña -->
            <form action="procesar_registro_usuarios.php" method="POST" id="form-registro" novalidate>
                
                <!-- TIPO DE CUENTA -->
                <div class="form-group">
                    <label>Tipo de Cuenta</label>
                    <select name="rol" id="selector-rol" class="u-fullwidth" onchange="evaluarRol()" required style="height: 4.5rem;">
                        <option value="estudiante" <?php echo ($rol_seleccionado == 'estudiante') ? 'selected' : ''; ?> style="background: #111; color: #fff;">Estudiante (Acceso a clases y notas)</option>
                        <option value="profesor" <?php echo ($rol_seleccionado == 'profesor') ? 'selected' : ''; ?> style="background: #111; color: #fff;">Profesor (Subir notas y evaluar)</option>
                        <option value="admin" <?php echo ($rol_seleccionado == 'admin') ? 'selected' : ''; ?> style="background: #111; color: #fff;">Administrador (Control Total)</option>
                    </select>
                </div>

                <!-- CÉDULA DE IDENTIDAD -->
                <div class="form-group">
                    <label>Cédula de Identidad</label>
                    <input class="u-fullwidth" type="text" name="cedula" id="cedula" value="<?php echo htmlspecialchars($cedula_ingresada); ?>" required placeholder="Ej. 30999887">
                    
                    <!-- Botón de verificación. Al usar formaction, procesa la cédula aquí mismo sin importar los campos obligatorios -->
                    <button type="submit" name="buscar_cedula" id="btn-verificar" class="btn-buscar" formaction="registro_usuarios.php" formnovalidate>Verificar Cédula en el Sistema</button>
                    
                    <span id="msg-cedula" style="font-size: 0.85rem; display: block; margin-top: 4px; color: <?php echo $encontrado ? '#2ecc71' : '#ff4d4d'; ?>;">
                        <?php echo $mensaje_cedula; ?>
                    </span>
                </div>

                <!-- NOMBRE COMPLETO (Bloqueado en solo lectura si ya fue encontrado) -->
                <div class="form-group">
                    <label>Nombre Completo (Registrado en la escuela)</label>
                    <input class="u-fullwidth" type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre_val); ?>" <?php echo $encontrado ? 'readonly style="background:#090d12; color:#888; cursor:not-allowed;"' : 'readonly placeholder="Se completará al verificar la cédula" style="background: rgba(255,255,255,0.02); color: #888; cursor: not-allowed;"'; ?> required>
                    <input type="hidden" name="id_persona" id="id_persona" value="<?php echo htmlspecialchars($id_persona_val); ?>">
                </div>

                <!-- CORREO ELECTRÓNICO (Bloqueado en solo lectura si ya fue encontrado) -->
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input class="u-fullwidth" type="email" name="email" id="email" value="<?php echo htmlspecialchars($correo_val); ?>" <?php echo $encontrado ? 'readonly style="background:#090d12; color:#888; cursor:not-allowed;"' : 'readonly placeholder="correo@ejemplo.com" style="background: rgba(255,255,255,0.02); color: #888; cursor: not-allowed;"'; ?> required>
                    <span id="msg-email" style="font-size: 0.85rem; display: block; margin-top: 4px; color: #2ecc71;">✔ Verificado por el sistema</span>
                </div>

                <!-- NIVEL ACADÉMico -->
                <div class="form-group">
                    <label>Nivel Académico (Asignado en su inscripción)</label>
                    <input class="u-fullwidth" type="text" id="nivel_texto" value="<?php echo htmlspecialchars($nivel_texto_val); ?>" readonly placeholder="Se cargará automáticamente al verificar la cédula" style="background: rgba(255,255,255,0.02); color: #888; cursor: not-allowed;">
                    <input type="hidden" name="id_nivel" id="id_nivel" value="<?php echo htmlspecialchars($id_nivel_val); ?>">
                </div>

                <div class="form-group">
                    <label>Contraseña de Acceso</label>
                    <input class="u-fullwidth" type="password" name="password" id="password" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input class="u-fullwidth" type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
                    <span id="msg-password" style="font-size: 0.85rem; display: block; margin-top: 4px;"></span>
                </div>

                <div id="bloque-codigo" class="form-group" style="display: none; margin-bottom: 3.5rem;">
                    <label style="font-weight: bold;">Clave de Autorización Especial</label>
                    <input class="u-fullwidth" type="password" name="codigo_autorizacion" id="codigo_autorizacion" placeholder="Introduce la clave de la escuela">
                </div>

                <!-- Botón de Registro final -->
                <button type="submit" id="btn-registrar" name="btn_registrar_final" class="u-fullwidth btn-primary" <?php echo ($rol_seleccionado == 'estudiante' && !$encontrado) ? 'disabled' : ''; ?>>Registrar Cuenta</button>

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
        var btnVerificar = document.getElementById('btn-verificar');
        var btnRegistrar = document.getElementById('btn-registrar');
        var spanCedula = document.getElementById('msg-cedula');

        if (rolSeleccionado === "profesor" || rolSeleccionado === "admin") {
            btnVerificar.style.display = "none";
            bloqueCodigo.style.display = "block";
            inputCodigo.required = true;

            // Profesor y Admin sí escriben sus datos libremente
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

            inputNivelTexto.value = "Personal Docente / Administrativo";
            inputIdNivel.value = "0"; 

            btnRegistrar.disabled = false;
            spanCedula.textContent = "";

        } else {
            btnVerificar.style.display = "block";
            bloqueCodigo.style.display = "none";
            inputCodigo.required = false;
            inputCodigo.value = "";

            inputNombre.readOnly = true;
            inputNombre.style.background = "rgba(255,255,255,0.02)";
            inputNombre.style.color = "#888";
            inputNombre.style.cursor = "not-allowed";
            inputNombre.value = "";
            inputNombre.placeholder = "Se completará al verificar la cédula";

            inputEmail.readOnly = true;
            inputEmail.style.background = "rgba(255,255,255,0.02)";
            inputEmail.style.color = "#888";
            inputEmail.style.cursor = "not-allowed";
            inputEmail.value = "";
            inputEmail.placeholder = "correo@ejemplo.com";

            inputNivelTexto.value = "";
            inputIdNivel.value = "";

            <?php echo !$encontrado ? 'btnRegistrar.disabled = true;' : ''; ?>
        }
    }

    window.onload = function() {
        var rolInicial = document.getElementById("selector-rol").value;
        if (rolInicial === "profesor" || rolInicial === "admin") {
            document.getElementById("btn-verificar").style.display = "none";
            document.getElementById("nombre").readOnly = false;
            document.getElementById("email").readOnly = false;
        }
    };
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputPass = document.getElementById('password');
    const inputConfirmPass = document.getElementById('confirm_password');
    const spanPass = document.getElementById('msg-password');
    const formRegistro = document.getElementById('form-registro');

    function validarContrasenas() {
        if (!inputPass || !inputConfirmPass) return;
        const pass = inputPass.value;
        const confirmPass = inputConfirmPass.value;

        if (confirmPass === '') {
            spanPass.textContent = '';
            inputConfirmPass.style.borderColor = '';
            return;
        }

        if (pass !== confirmPass) {
            spanPass.textContent = '❌ Las contraseñas no coinciden.';
            spanPass.style.color = '#ff4d4d';
            inputConfirmPass.style.borderColor = '#ff4d4d';
        } else {
            spanPass.textContent = '✓ Las contraseñas coinciden.';
            spanPass.style.color = '#2ecc71';
            inputConfirmPass.style.borderColor = '#2ecc71';
        }
    }

    if (inputPass && inputConfirmPass) {
        inputPass.addEventListener('input', validarContrasenas);
        inputConfirmPass.addEventListener('input', validarContrasenas);
    }

    // Validación al momento de dar clic en "Registrar Cuenta" (para evitar que se envíe si faltan datos de contraseña)
    formRegistro.addEventListener('submit', function(e) {
        // Si hicieron clic en el botón de verificar cédula, dejamos que pase sin validar contraseñas
        if (document.activeElement && document.activeElement.name === 'buscar_cedula') {
            return;
        }

        // Si van a completar el registro final, validamos que las contraseñas estén escritas
        if (inputPass.value === '' || inputConfirmPass.value === '') {
            e.preventDefault();
            alert('Por favor, complete y confirme su contraseña antes de registrarse.');
            inputPass.focus();
            return;
        }

        if (inputPass.value !== inputConfirmPass.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden.');
        }
    });
});
</script>
</body>
</html>