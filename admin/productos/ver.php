    <?php
        require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        $consultaProducto="SELECT* FROM productos;";
        $resultado=mysqli_query($db,$consultaProducto);
        $incio = false;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $id=$_POST['id'];
            $id=filter_var($id,FILTER_VALIDATE_INT);
            if($id){
                //Eliminar el archivo
                $query="SELECT imagen FROM productos WHERE id_producto=$id";
                $resultado=mysqli_query($db,$query);
                $producto=mysqli_fetch_assoc($resultado);
                unlink('../imagenes/'.$producto['imagen']);
                //Eliminar el producto
                $query="DELETE FROM productos WHERE id_producto=$id";
                $resultado=mysqli_query($db,$query);
                if($resultado){
                    header('location:'. BASE_URL . '/admin/avisos.php?resultado=3');
                }
            }
        }
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu">
        <section class="contenedor">
            <div class="tabla-productos">
               
                <table class="producto">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Detalles</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($producto = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                        $id = htmlspecialchars($producto['id_producto']);
                        $nombre = htmlspecialchars($producto['nombre']);
                        $categoria = htmlspecialchars($producto['categoria']);
                        $detalles = htmlspecialchars($producto['detalles']);
                        $activo=htmlspecialchars($producto['activo']);
                        $precio = htmlspecialchars($producto['precio']);
                        $stock = htmlspecialchars($producto['stock']);
                        $imagen = basename($producto['imagen']);
                        $rutaImagen = BASE_URL."/imagenes/".$imagen.".webp";
                        ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $nombre; ?></td>
                            <td><?php echo $categoria; ?></td>
                            <td><?php echo $detalles; ?></td>
                            <td><?php echo $activo?'Activo':'Desactivado'; ?></td>
                            <td><?php echo $precio; ?></td>
                            <td><?php echo $stock; ?></td>
                            <td><img src="<?= htmlspecialchars($rutaImagen)?>" alt="Null" class="imagen-small"></td>
                            <td>
                                <a href="relaciones.php?id=<?php echo $id;?>" class="boton--naranja">Complementos</a>
                                <a href="actualizar.php?id=<?php echo $id;?>" class="boton--verde">Modificar</a>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?php echo $producto['id_producto']?>">
                                    <div class="input">
                                        <input type="submit" value="Eliminar" class="boton--rojo" onclick="return confirm('¿Seguro que deseas eliminar este elemento?')">
                                    </div>
                                    
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
                
            <div class="producto--boton">
                <a href="crear.php" class="boton--verde">Agregar</a>
            </div>
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>