    <?php 
        require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
        if(!isset($_SESSION['id_venta'])){
            header('Location:'. BASE_URL . '/iniciarPedido.php');
            exit;
        }
        $id=$_GET['id'];
        $id=filter_var($id,FILTER_VALIDATE_INT);
        if(!$id){
            header('Location:'. BASE_URL . '/iniciarPedido.php');
        }
    
        
        $incio = true;
        incluirTemplate('header', $incio);
        $db=conectarDB();
        $consultaProducto="SELECT *FROM productos WHERE id_producto=$id";
        $consultaVariante="SELECT v.id_variante AS 'ID_VARIANTE',v.nombre AS 'VARIANTE', v.imagen AS 'IMAGENVAR', vp.precio_extra AS 'PRECIO_EXTRA' FROM variantes v JOIN productos_variantes vp ON v.id_variante=vp.id_variante WHERE vp.id_producto=$id;";
        $resultado=mysqli_query($db,$consultaProducto);
        $resultado2=mysqli_query($db,$consultaVariante);

        $producto=mysqli_fetch_assoc($resultado);
        $nombre   = htmlspecialchars($producto['nombre']);
        $categoria   = htmlspecialchars($producto['categoria']);
        $detalles = htmlspecialchars($producto['detalles']);
        $precio   = htmlspecialchars($producto['precio']);
        $notas='';
        $subtotal=$precio;
        $cantidad=1;
        $stock   = htmlspecialchars($producto['stock']);
        $activo=(int)$producto['activo'];
        $imagen   = basename($producto['imagen']);
        $rutaImagen = "imagenes/".$imagen.".webp";
        $agotado=$stock<=0?true:false;
        $estado=$activo==1?true:false;
        echo $agotado;
        if($agotado or !$activo){
            http_response_code(403);
            die('Acción no permitida');
        }

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $erroresInsercion=[];
            //Creacion del ticket
            $cantidad=(int)$_POST['cantidad'];
            $notas=mysqli_real_escape_string($db,$_POST['notas']);
            $id_venta=(int)$_SESSION['id_venta'];
            $subtotal=$cantidad*$precio;

            $query="INSERT INTO detalle_ventas (cantidad,precio_unitario,subtotal,notas,id_venta,id_producto) VALUES ('$cantidad','$precio','$subtotal','$notas','$id_venta','$id');";
            $resultado=mysqli_query($db, $query);
            if($resultado){
                $id_detalle_venta = mysqli_insert_id($db);
            } else {
                echo "Error al insertar detalle de venta";
            }
            
            //Creacion del detalle de variantes
            $carritoVariantes = $_SESSION['carrito_variantes'] ?? [];
            foreach ($carritoVariantes as $item) {
                $itemV= (int)$item['id'];
                $query="SELECT precio_extra FROM productos_variantes WHERE id_producto=$id AND id_variante=$itemV;";
                $resultado=mysqli_query($db,$query);
                $fila = mysqli_fetch_assoc($resultado);
                $precio_extra_aplicado = $fila['precio_extra'] ?? 0;
                $query="INSERT INTO detalle_ventas_variantes (id_detalle_venta,id_producto,precio_extra_aplicado,id_variante) VALUES ($id_detalle_venta,$id,$precio_extra_aplicado,$itemV);";
                $resultado=mysqli_query($db,$query);
                if(!$resultado){
                    $erroresInsercion[]='Error al insertar en la Base de Datos';
                }
            
            }
            unset($_SESSION['carrito_variantes']);
            if(empty($erroresInsercion)){
                header('Location:'. BASE_URL . '/carrito.php');
            }else{
                header('Location:'. BASE_URL . '/iniciarPedido.php');
                echo "Error en la Base de Datos";
            }
            

        }
    ?>
    <main class="bg-menu">
        <section class="contenedor">
            <h2 class="display--titulo"><?= $nombre ?></h2>
            <section class="display--preparar">
                <div class="display--preparar__img">
                    <img src="<?= $rutaImagen ?>" alt="Imagen producto">
                    <br>
                    <h2>Detalles</h2>
                    <p class="descripcion"><?= $detalles ?></p>
                    <p class="precio"><span>$<?= $precio ?></span></P>
                </div>
                <div class="display--preparar__variantes">
                    <h2>Extras</h2>

                    <table class="tabla--preparar">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Complemento</th>
                                    <th>Imagen</th>
                                    <th>Precio Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($variante= mysqli_fetch_assoc($resultado2)): ?>
                                    <?php
                                        $id_variante=(int)$variante['ID_VARIANTE'];
                                        $nombre_variante=htmlspecialchars($variante['VARIANTE']);
                                        $precio_variante = $variante['PRECIO_EXTRA'];
                                        $imagen = basename($variante['IMAGENVAR']);
                                        $rutaImagen = BASE_URL."/imagenes/".$imagen.".webp";
                                    ?>
                                    <tr>
                                        <td><label class="check">
                                            <input type="checkbox" class="checkbox" data-id="<?= $id_variante ?>" data-precio="<?= $precio_variante?>" name="relacion[<?= $id_variante ?>]">
                                            <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td><?= $nombre_variante ?></td>
                                        <td><img src="<?= htmlspecialchars($rutaImagen)?>" alt="Null" class="imagen-small"></td>
                                        <td>$ <?= $precio_variante ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                    </table>
                    
                    <form method="POST">
                    <div class="adder">
                        <a class="minus" id="cantidad-minus">-</a>
                        <input type="number" name="cantidad" id="input-cantidad" data-cantidad="<?= $cantidad ?>" value="<?= $cantidad ?>" min="1" step="1">
                        <a class="plus" id="cantidad-plus">+</a>
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Notas</label>
                        <div class="formulario--textarea">
                            <textarea id="notas" placeholder="Detalles adicionales del pedido" name="notas"><?php echo $notas;?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="boton--rojo" id="btn-agregar" data-base="<?= $precio ?>">
                        <span>
                            Agregar a mi Pedido $
                            <span id="total-display"><?= $precio ?></span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                        </svg>
                    </button>
                    </form>

                </div>
                
            </section>
            
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
