    <?php 
require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu">
        <section class="contenedor">
            <h2>Mi Perfil</h2>
            <p><span>Nombre: </span><?=  $_SESSION['nombre']; ?> <?=  $_SESSION['apellido']; ?></p>
            <p><span>Correo: </span><?=  $_SESSION['email']; ?></p>
            <p><span>Rol: </span><?=  $_SESSION['rol']; ?></p>
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
