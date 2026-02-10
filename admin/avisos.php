    <?php
        require '../includes/funciones.php';
        require_once __DIR__ . '/includes/auth.php'; 
        $incio = true;
        incluirTemplate('header', $incio);
        $resultado=$_GET['resultado']??null;
    
    ?>
    <main class="bg-menu">
        <?php
            $resultado = intval($resultado);
            if ($resultado === 1) {
                echo '<p class="alerta accept">Agregado Correctamente</p>';
            } elseif ($resultado === 2) {
                echo '<p class="alerta accept">Modificado Correctamente</p>';
            } elseif ($resultado === 3) {
                echo '<p class="alerta accept">Eliminado Correctamente</p>';
            } elseif($resultado===4){
                echo '<p class="alerta error">No puedes eliminar este usuario porque tiene ventas registradas</p>';
            }
        ?>
        <section class="contenedor seleccion">
            <div>
                <a href="index.php" class="boton--naranja">Menu</a>
            </div>
            
        </section>
    </main>
    
    <?php 
        incluirTemplate('footer', $incio);
    ?>