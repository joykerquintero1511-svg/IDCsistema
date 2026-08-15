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
        <h2>Profesor</h2>
    </div>
    <ul class="menu-links">
        <li><a href="/IDCsistema/profesores/index.php">Inicio</a></li>
        <li><a href="/IDCsistema/validar.php">Validar Asistencia y QR</a></li>
        <li><a href="/IDCsistema/profesores/crear_asignacion.php">Nueva Asignación</a></li>
        <li><a href="/IDCsistema/profesores/registrar_asistencias.php">Registrar Asistencias</a></li>
        <li><a href="/IDCsistema/calificaciones/index.php">Calificaciones</a></li>
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