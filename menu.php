    <?php 
        require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
        unset($_SESSION['venta_finalizada']);
        unset($_SESSION['gracias_mostrada']);
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu main--ajustado">
        <section class="seleccion contenedor">
            <div>
                <a href="<?= BASE_URL ?>/iniciarPedido.php" class="boton--rojo">Iniciar Pedido</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/verPedidos.php" class="boton--gris">Ver Pedidos</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/informacion.php" class="boton--verde">Información</a>
            </div>
            <a  href="<?= BASE_URL ?>/src/pdf/menu.pdf" target="_blank" class="boton2">
                <img src="<?= BASE_URL ?>/src/img/full/iconoMenu.png" alt="Icono Menu">
                <p>Carta</p>
            </a>
        </section>
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>