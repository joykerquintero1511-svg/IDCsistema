<!-- Encabezado Móvil (Barra superior fija con botón de 3 rayitas) -->
<header class="mobile-header">
    <div class="mobile-brand">
        <img src="/IDCsistema/images/EFB.png" alt="Logo">
        <span>IDCsistema</span>
    </div>
    <button class="btn-hamburger" id="btn-toggle-menu" aria-label="Abrir Menú">
        <span></span>
        <span></span>
        <span></span>
    </button>
</header>

<!-- Capa oscura de fondo para cerrar el menú en móviles al hacer clic afuera -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Barra Lateral (Sidebar) -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="/IDCsistema/images/EFB.png" alt="Logo">
        <h2>Administrador</h2>
    </div>
    <ul class="menu-links">
        <li><a href="/IDCsistema/admin/index.php">Gestión Central</a></li>
        <li><a href="/IDCsistema/admin/estudiantes/listar.php">Gestión de Estudiantes</a></li>
        <li><a href="/IDCsistema/admin/reportes/reporte_estudiantes.php">Reporte de Estudiantes</a></li>
        <li><a href="/IDCsistema/validar.php">Validar Asistencia y QR</a></li>
        <li><a href="/IDCsistema/admin/promover_estudiantes.php">Promover Estudiantes</a></li>
        <li><a href="/IDCsistema/admin/asignar_nivel_profesor.php">Asignar Nivel a Profesores</a></li>
        <li><a href="/IDCsistema/admin/crear_nivel.php">Crear Nivel Académico</a></li>
        <li><a href="/IDCsistema/calificaciones/ver_notas.php?origen=admin">Consulta de Calificaciones</a></li>
        <li><a href="/IDCsistema/admin/cronograma.php">Cronogramas Clases</a></li>
        <li><a href="/IDCsistema/logout.php" class="closed">Cerrar Sesión</a></li>
    </ul>
</aside>

<!-- Script para la interacción del menú en móvil -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('btn-toggle-menu');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function toggleMenu() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    if (menuBtn && sidebar && overlay) {
        menuBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
    }
});
</script>