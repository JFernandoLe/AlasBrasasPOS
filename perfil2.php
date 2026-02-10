    <?php 
        require_once __DIR__ . '/includes/funciones.php';

        $incio = true;
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu">
        <section class="contenedor">
            <h2>Mi Perfil</h2>
            <p><span>Nombre: </span></p>
            <p><span>Correo: </span></p>
            <p><span>Rol: </span></p>
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
