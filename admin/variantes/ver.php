    <?php
         require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        $consultaVariante="SELECT* FROM variantes;";
        $resultado=mysqli_query($db,$consultaVariante);
        $incio = false;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $id=$_POST['id'];
            $id=filter_var($id,FILTER_VALIDATE_INT);
            if($id){
                //Eliminar el archivo
                $query="SELECT imagen FROM variantes WHERE id_variante=$id";
                $resultado=mysqli_query($db,$query);
                $variante=mysqli_fetch_assoc($resultado);
                unlink('../imagenes/'.$variante['imagen']);
                //Eliminar el producto
                $query="DELETE FROM variantes WHERE id_variante=$id";
                $resultado=mysqli_query($db,$query);
                if($resultado){
                    header('location:/admin?resultado=3');
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
                        <th>Estado</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($variante = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                        $id = htmlspecialchars($variante['id_variante']);
                        $nombre = htmlspecialchars($variante['nombre']);
                        $activo = htmlspecialchars($variante['activo']);
                        $imagen = basename($variante['imagen']);
                        $rutaImagen = BASE_URL."/imagenes/".$imagen.".webp";
                        ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $nombre; ?></td>
                            <td><?php echo $activo?'Activo':'Desactivado'; ?></td>
                            <td><img src="<?= htmlspecialchars($rutaImagen)?>" alt="Null" class="imagen-small"></td>
                            <td>
                                
                                <form method="POST">
                                    <div class="input">
                                        <a href="actualizar.php?id=<?php echo $id;?>" class="boton--verde">Modificar</a>
                                    </div>
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
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>