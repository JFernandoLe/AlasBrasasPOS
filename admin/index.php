    <?php
        require '../includes/funciones.php';
        require_once __DIR__ . '/includes/auth.php'; 
        

        $incio = true;
        incluirTemplate('header', $incio);
       
        

    ?>
    <main class="bg-menu">
        
        <section class="admin--seleccion contenedor">
            <div>
                <a href="<?= BASE_URL ?>/admin/productos/ver.php" class="boton--gris">Productos</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/variantes/ver.php" class="boton--gris">Variantes</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/usuarios/ver.php" class="boton--gris">Usuarios</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/insumos/ver.php" class="boton--gris">Insumos</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/ventas/ver.php" class="boton--gris">Ventas</a>
            </div>
            <div>
                <a href="#" class="boton--gris">Configuracion del Sistema</a>
            </div>
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
