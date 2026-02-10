<?php 
require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 


if (!isset($_SESSION['venta_finalizada']) || $_SESSION['venta_finalizada'] !== true) {
    header('Location: ' . BASE_URL . '/menu.php');
    exit;
}

unset($_SESSION['id_venta']);
$_SESSION['gracias_mostrada'] = true;

$inicio = true;
incluirTemplate('header', $inicio);
?>

<!-- 🔒 Evitar cache -->
<meta http-equiv="Cache-Control" content="no-store">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<main class="bg-menu">
    <section class="contenedor gracias">
        <h2>¡Gracias por su compra!</h2>

        <button class="boton--naranja" onclick="irMenu()">Volver al menú</button>
    </section>
</main>

<script>
function irMenu() {
    window.location.replace("menu.php");
}
window.history.pushState(null, "", window.location.href);
window.onpopstate = function () {
    window.history.pushState(null, "", window.location.href);
};
</script>

<?php 
incluirTemplate('footer', $inicio);
?>
