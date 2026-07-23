<?php
include '../session-start.php';
include '../conexion.php';

// --- CONSULTAS ANALÍTICAS PARA EL DASHBOARD ---
// 1. Total estudiantes activos
$res_est = mysqli_query($conexion, "SELECT COUNT(*) as total FROM estudiantes");
$total_estudiantes = mysqli_fetch_assoc($res_est)['total'] ?? 0;

// 2. Conteo exacto de verificados vs pendientes para el gráfico circular
$res_verif = mysqli_query($conexion, "SELECT 
    SUM(CASE WHEN verificado = 1 THEN 1 ELSE 0 END) as verificados,
    SUM(CASE WHEN verificado = 0 THEN 1 ELSE 0 END) as pendientes,
    COUNT(*) as total
    FROM usuarios");

$datos_verif = mysqli_fetch_assoc($res_verif);
$verificados = intval($datos_verif['verificados'] ?? 0);
$pendientes  = intval($datos_verif['pendientes'] ?? 0);
$total_usr   = intval($datos_verif['total'] ?? 1);
$porcentaje_verif = round(($verificados / $total_usr) * 100);

// Consulta para saber si las inscripciones están abiertas o cerradas
$q_estado = mysqli_query($conexion, "SELECT inscripciones_abiertas FROM periodos_academicos LIMIT 1");
$res_estado = mysqli_fetch_assoc($q_estado);
$estado_actual = $res_estado['inscripciones_abiertas'] ?? 0;
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - EFB</title>
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
        <aside class="sidebar">
    <div class="sidebar-brand">
        <img src="../images/EFB.png" alt="Logo">
        <h2>Administrador</h2>
    </div>
        <ul class="menu-links">
        <li><a href="index.php" class=" active">Gestión Central</a></li>
        <li><a href="estudiantes/listar.php">Gestión de Estudiantes</a></li>
        <li><a href="reportes/reporte_estudiantes.php" >Reporte de Estudiantes</a></li>
        <li><a href="crear_nivel.php" >Crear Nivel Académico</a></li>
        <li><a href="../logout.php" class="closed">Cerrar Sesión</a></li>
    </div>
     </aside>

        <main class="main-content">
        <div class="welcome-header">
            <h1><?php echo htmlspecialchars($_SESSION['usuario']); ?><span class="badge">/ Admin</span></h1>
            <p>Control maestro global sobre los parámetros del sistema y accesos de usuarios.</p>
        </div>

        <div class="dashboard-grid">
        
        <div style="display: flex; flex-direction: column; gap: 2.5rem;">
            
            <div class="info-card">
                <h3>Administrar Calificaciones 📝</h3>
                <p>Edita y administra las calificaciones de los estudiantes por período académico.</p>
                <a href="../calificaciones/index.php" class="link">Administrar calificaciones &rarr;</a>
            </div>

        <div class="info-card">
                
   <!-- TARJETA SOFISTICADA DE ESTADÍSTICAS CON CHART.JS -->
<div style="background: #0f172a; border: 1px solid #1e293b; padding: 25px; border-radius: 14px; margin-bottom: 25px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);">
    
    <!-- Encabezado del Módulo -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="color: #fff; margin: 0; font-size: 19px; font-weight: 700; letter-spacing: 0.5px;">
                ESTADÍSTICAS Y CONTROL 📊
            </h3>
            <p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">Monitoreo en tiempo real de métricas y validación de usuarios.</p>
        </div>
        <a href="estadisticas.php" style="background: #2563eb; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
            Ver Reporte Detallado →
        </a>
    </div>

    <!-- Grid: Tarjetas Numéricas a la Izquierda y Círculo Gráfico a la Derecha -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; align-items: center;">
        
        <!-- Bloque Izquierdo: Métricas Destacadas -->
        <div style="display: flex; flex-direction: column; gap: 12px;">
            
            <div style="background: #1e293b; padding: 16px 20px; border-radius: 10px; border-left: 4px solid #3b82f6; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Estudiantes Activos</span>
                    <h2 style="color: #ffffff; margin: 4px 0 0 0; font-size: 26px; font-weight: 800;"><?php echo $total_estudiantes; ?></h2>
                </div>
                <div style="background: rgba(59, 130, 246, 0.1); padding: 10px; border-radius: 50%; color: #3b82f6; font-size: 20px;">🎓</div>
            </div>

            <div style="background: #1e293b; padding: 16px 20px; border-radius: 10px; border-left: 4px solid #10b981; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Tasa de Verificación</span>
                    <div style="display: flex; align-items: baseline; gap: 8px; margin-top: 4px;">
                        <h2 style="color: #10b981; margin: 0; font-size: 26px; font-weight: 800;"><?php echo $porcentaje_verif; ?>%</h2>
                        <span style="color: #64748b; font-size: 12px; font-weight: 500;">(<?php echo $verificados; ?> de <?php echo $total_usr; ?> cuentas)</span>
                    </div>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); padding: 10px; border-radius: 50%; color: #10b981; font-size: 20px;">✉️</div>
            </div>

        </div>

        <!-- Bloque Derecho: Gráfico Circular / Donut Renderizado con Canvas -->
        <div style="background: #1e293b; padding: 20px; border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
            <span style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px;">
                Distribución de Cuentas
            </span>
            
            <!-- Lienzo donde Chart.js dibuja la dona -->
            <div style="position: relative; width: 140px; height: 140px;">
                <canvas id="graficoVerificacion"></canvas>
                <!-- Texto central superpuesto en la dona -->
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                    <span style="color: #fff; font-size: 18px; font-weight: bold; display: block; line-height: 1;"><?php echo $total_usr; ?></span>
                    <span style="color: #64748b; font-size: 9px; text-transform: uppercase; font-weight: 600;">Usuarios</span>
                </div>
            </div>

            <!-- Leyenda personalizada debajo del gráfico -->
            <div style="display: flex; gap: 15px; margin-top: 15px; font-size: 12px;">
                <div style="display: flex; align-items: center; gap: 6px; color: #cbd5e1;">
                    <span style="width: 10px; height: 10px; background-color: #10b981; border-radius: 50%; display: inline-block;"></span>
                    Verificados (<?php echo $verificados; ?>)
                </div>
                <div style="display: flex; align-items: center; gap: 6px; color: #cbd5e1;">
                    <span style="width: 10px; height: 10px; background-color: #ef4444; border-radius: 50%; display: inline-block;"></span>
                    Pendientes (<?php echo $pendientes; ?>)
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JavaScript para Renderizar el Gráfico Circular -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('graficoVerificacion').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Verificados', 'Pendientes'],
            datasets: [{
                data: [<?php echo $verificados; ?>, <?php echo $pendientes; ?>],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '78%', // Crea el anillo delgado estilizado
            plugins: {
                legend: { display: false }, // Ocultamos la leyenda nativa para usar la personalizada
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' cuentas';
                        }
                    }
                }
            }
        }
    });
});
</script>
        </div>
            <div class="info-card">
                <h3>Periodo Académico 📅</h3>
                <p>Abre o cierra lapsos para la carga de notas o habilita procesos globales de inscripción.</p>
                <a href="periodos.php" class="link">Configurar periodos academicos &rarr;</a>
            </div>
            <div class="info-card">
            <div style="background: #0f172a; border: 1px solid #1e293b; padding: 22px; border-radius: 12px; margin-bottom: 20px;">
    <h3 style="color: #fff; margin-top: 0;">Habilitar/Deshabilitar Inscripciones</h3>
    <p style="color: #94a3b8; font-size: 13px;">
        Abre o cierra el proceso web de pre-inscripciones públicas para la Escuela.
    </p>

    <div style="display: flex; align-items: center; gap: 15px; margin-top: 15px;">
    <span style="color: #fff; font-size: 14px; font-weight: bold;">Estatus de Inscripciones:</span>

    <?php if ($estado_actual == 1): ?>
        <span style="background: #10b981; color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold;">ABIERTAS</span>
        
        <a href="toggle_inscripciones.php?estado=0" style="background: #ef4444; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: bold; display: inline-block; cursor: pointer; position: relative; z-index: 100; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            Cerrar Inscripciones
        </a>

    <?php else: ?>
        <span style="background: #ef4444; color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold;">CERRADAS</span>
        
        <a href="toggle_inscripciones.php?estado=1" style="background: #10b981; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: bold; display: inline-block; cursor: pointer; position: relative; z-index: 100; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            Abrir Inscripciones
        </a>
        
    <?php endif; ?>
</div>

        <div style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
    <p style="color: #ef4444; font-size: 13px; margin-bottom: 10px;">⚠️ Zona de Mantenimiento:</p>
    <a href="purgar_pendientes.php" onclick="return confirm('¿Estás seguro? Esto eliminará permanentemente a todos los alumnos pre-inscritos que NO validaron su asistencia presencial con el QR.');" style="background: #dc2626; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block;">
        🗑️ Ejecutar Purga Trimestral (Borrar Pendientes)
    </a>
</div>
        </form>
        
    
</div>
            </div>
        </div>
    </div>
        </main>

        <!-- AQUÍ LLAMAS A TU SCRIPT MATA-FANTASMAS -->
    <?php include '../script-seguridad.php'; ?>

</body>
</html>