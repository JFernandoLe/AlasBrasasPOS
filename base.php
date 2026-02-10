    <?php
        require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu">

    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
