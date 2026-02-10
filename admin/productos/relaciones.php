    
    <?php 
        require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';

        $id=$_GET['id'];
        $id=filter_var($id,FILTER_VALIDATE_INT);
        if(!$id){
            header('Location:'. BASE_URL . '/admin');
        }
        $incio = false;
        $db=conectarDB();
        
        $consultaVariantes="SELECT *FROM variantes";
        $resultado=mysqli_query($db,$consultaVariantes);

        $consultaRelacion="SELECT* FROM productos_variantes WHERE id_producto=$id;";
        $resultado2=mysqli_query($db,$consultaRelacion);
        $precio_extra=[];
        $id_variante_relacion=[];
        while($relacion=mysqli_fetch_assoc($resultado2)){
            
            $id_variante_relacion[] = (int)$relacion['id_variante'];
            $precio_extra[(int)$relacion['id_variante']]=(float)($relacion['precio_extra']);
            
        }

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $idsActuales=array_keys($precio_extra);
            
            $relaciones = $_POST['relacion'] ?? [];
            $precios    = $_POST['precio_extra'] ?? [];
            $eliminar=array_diff($idsActuales,$relaciones);
            foreach ($eliminar as $id_variante) {
                $resultado=mysqli_query($db,"DELETE FROM productos_variantes WHERE id_producto = $id AND id_variante = $id_variante"
                );
                if($resultado){
                    header('location:'. BASE_URL . '/admin/avisos.php?resultado=2');
                }
            }
            foreach ($relaciones as $id_variante => $on) {

                $precio = $precios[$id_variante] ?? 0;

                $id_variante = (int)$id_variante;
                $precio = (float)$precio;

                $query = "INSERT INTO productos_variantes (id_producto, id_variante, precio_extra) VALUES ($id, $id_variante, $precio) ON DUPLICATE KEY UPDATE precio_extra = $precio";

                $resultado=mysqli_query($db, $query);
                if($resultado){
                    header('location:'. BASE_URL . '/admin/avisos.php?resultado=2');
                }
        }
        }
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu">
        <section class="contenedor">
            <div class="tabla-productos">
                <form method="POST">  
                    <table class="producto">
                            <thead>
                                <tr>
                                    <th>Complemento</th>
                                    <th>Imagen</th>
                                    <th>¿Agregar?</th>
                                    <th>Precio Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($variante= mysqli_fetch_assoc($resultado)): ?>
                                    <?php
                                        $id_variante = htmlspecialchars($variante['id_variante']);
                                        $nombre_variante=htmlspecialchars($variante['nombre']);
                                        $existe = array_key_exists($id_variante, $precio_extra);
                                        $precio = $existe ? $precio_extra[$id_variante] : '';
                                        $imagen = basename($variante['imagen']);
                                        
                                        $rutaImagen = BASE_URL."/imagenes/".$imagen.".webp";
                                    ?>
                                    <tr>
                                        <td><?php echo $nombre_variante; ?></td>
                                        <td><img src="<?= htmlspecialchars($rutaImagen)?>" alt="Null" class="imagen-small"></td>
                                        <td><input type="checkbox" class="chk-opcion" data-target="precio_<?= $id_variante ?>" name="relacion[<?= $id_variante ?>]" <?= $existe?'checked':'' ?>></td>
                                        <td><input value="<?=$precio ?>" type="number" class="input-opcion" placeholder="0.00" min="0" id="precio_<?= $id_variante ?>" name="precio_extra[<?= $id_variante ?>]" required <?= $existe?'':'disabled' ?>></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                    </table>
                    <button type="submit" value="Enviar" class="boton--mediano boton--verde">ACTUALIZAR</button>   
            </form>        
        </div>
        
            
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>