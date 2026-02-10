    <?php 
       require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
        $id=$_GET['id'];
        $id=filter_var($id,FILTER_VALIDATE_INT);
        if(!$id){
            header('Location:'. BASE_URL . '/iniciarPedido.php');
        }
        $incio = true;
        incluirTemplate('header', $incio);
        $db=conectarDB();
        $consultaProducto="SELECT *FROM productos WHERE id_producto=$id";
        $consultaVariante="SELECT v.nombre AS 'VARIANTE', vp.precio_extra AS 'PRECIO_EXTRA' FROM variantes v JOIN productos_variantes vp ON v.id_variante=vp.id_variante WHERE vp.id_producto=$id;";
        $resultado=mysqli_query($db,$consultaProducto);
        $resultado2=mysqli_query($db,$consultaVariante);
        
        $producto=mysqli_fetch_assoc($resultado);
        $nombre   = htmlspecialchars($producto['nombre']);
        $categoria   = htmlspecialchars($producto['categoria']);
        $detalles = htmlspecialchars($producto['detalles']);
        $precio   = htmlspecialchars($producto['precio']);
        $stock   = htmlspecialchars($producto['stock']);
        $activo  = htmlspecialchars($producto['activo']);
        $imagen   = basename($producto['imagen']);
        $rutaImagen = "imagenes/".$imagen.".webp";
        $estado=$activo==0?false:true;
        if(!$estado){
            http_response_code(403);
            die('Producto Fuera de Servicio');
        }
    ?>
    <main class="bg-menu">
        <section class="contenedor">
            <h2 class="display--titulo"><?= $nombre ?></h2>
            <section class="display--producto">
                <div class="item-1 display--producto__img ">
                    <img src="<?= BASE_URL ?>/<?= $rutaImagen ?>" alt="Imagen producto">
                </div>
                <div class="item-2 display--producto__info">
                    <h2>Detalles</h2>
                    <p class="descripcion"><?= $detalles ?></p>
                    <p class="precio"><span>$<?= $precio ?></span></P>
                    <p class="stock"><span><?php echo $stock<=0?'AGOTADO':'' ?></span></P>
                    <br>
                    <div class="display--producto__preparar ">
                        <a href="<?= BASE_URL ?>/preparar.php?id=<?= $id ?>" class=" boton--rojo" <?= $stock<=0?'hidden':'' ?>>
                            <span>Preparar</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fork-knife" viewBox="0 0 16 16">
                                <path d="M13 .5c0-.276-.226-.506-.498-.465-1.703.257-2.94 2.012-3 8.462a.5.5 0 0 0 .498.5c.56.01 1 .13 1 1.003v5.5a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5zM4.25 0a.25.25 0 0 1 .25.25v5.122a.128.128 0 0 0 .256.006l.233-5.14A.25.25 0 0 1 5.24 0h.522a.25.25 0 0 1 .25.238l.233 5.14a.128.128 0 0 0 .256-.006V.25A.25.25 0 0 1 6.75 0h.29a.5.5 0 0 1 .498.458l.423 5.07a1.69 1.69 0 0 1-1.059 1.711l-.053.022a.92.92 0 0 0-.58.884L6.47 15a.971.971 0 1 1-1.942 0l.202-6.855a.92.92 0 0 0-.58-.884l-.053-.022a1.69 1.69 0 0 1-1.059-1.712L3.462.458A.5.5 0 0 1 3.96 0z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="display--producto__variantes">
                    <h2>Extras</h2>
                    <ul>
                        <?php while($variante=mysqli_fetch_assoc($resultado2)):?>
                        <?php $nombre_variante=htmlspecialchars($variante['VARIANTE']) ?>
                        <?php $precio_variante=htmlspecialchars($variante['PRECIO_EXTRA']) ?>
                    <li><?= $nombre_variante ?><span> + $<?= $precio_variante ?></span></li>
                    <?php endwhile;?>
                    </ul>
                </div>
                
                
            </section>
            
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
