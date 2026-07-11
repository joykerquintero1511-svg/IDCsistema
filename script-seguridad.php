<!-- scripts_seguridad.php -->
<script>
    // Detector de caché del botón "Atrás" (Bfcache)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            // Fuerza la recarga para que PHP detecte que ya no hay sesión
            window.location.reload();
        }
    });
</script>