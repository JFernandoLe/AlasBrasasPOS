    <?php 
        require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        $consultaCategoria="SELECT DISTINCT categoria FROM productos;";
        $resultado=mysqli_query($db,$consultaCategoria);
        $errores=[];
        $nombre='';
        $categoria='';
        $precio='';
        $activo=1;
        $detalles='';
        $stock=0;
        
        $medida=10000*100;

        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            $nombre=mysqli_real_escape_string($db,$_POST['nombre']);
            $categoria=mysqli_real_escape_string($db,$_POST['categoria']);
            $precio=mysqli_real_escape_string($db,$_POST['precio']);
            $detalles=mysqli_real_escape_string($db,$_POST['detalles']);
            $activo=mysqli_real_escape_string($db,$_POST['activo']);
            $stock=mysqli_real_escape_string($db,$_POST['stock']);
            $imagen=$_FILES['imagen'];
            //Validar el formulario
            if(!$nombre){
                $errores[]='El nombre es obligatorio';
            }
            if(!$categoria){ 
                $errores[]='La categoria es obligatoria';
            }
            if(!$stock){ 
                $errores[]='El stock es obligatorio';
            }
            if(!$precio){
                $errores[]='El precio es obligatorio';
            }
            if(!$imagen || $imagen['error']){
                $errores[]= "La imagen es obligatoria";
            }
            if($imagen['size']>$medida){
                $errores[]="La imagen debe pesar menos de 1 MB";
            }
            if(strlen($detalles)>255 || strlen($detalles)<20){
                $errores[]='La descripcion es obligatoria y debe tener entre 20 y 255 caracteres';
            }

            $carpetaImagenes='../../imagenes/';
            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }

            if(empty($errores)){
                $nombreImagen=md5(uniqid(rand(),true).".webp");
                move_uploaded_file($imagen['tmp_name'],$carpetaImagenes.$nombreImagen.".webp");
                //Insertar en la base de datos
                $query="INSERT INTO productos (nombre, categoria, detalles, precio,imagen,activo,stock) VALUES ('$nombre', '$categoria', '$detalles', '$precio','$nombreImagen','$activo','$stock');";
              
                $resultado=mysqli_query($db, $query);
                if($resultado){
                    header('Location:'. BASE_URL . '/admin/avisos.php?resultado=1');
                }
                
            }
            
        }
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu">
        <section class="contenedor formulario--seccion">
            <?php foreach($errores as $error): ?>
                <div class="alerta error">
                    <?php echo $error;?> 
                </div>
            <?php endforeach; ?>
            <form class="formulario" method="POST" enctype="multipart/form-data">
                <fieldset class="formulario--fieldset">
                    <legend class="formulario--titulo">Agregar Productos</legend>
                    <div class="formulario--campo">
                        <label class="formulario--label">Nombre</label>
                        <input type="text" id="nombre" placeholder="alitas" class="formulario--input" name="nombre" value="<?php echo $nombre;?>">
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Categoria</label>
                        <div class="formulario--categoria">
                        <input type="text" id="categoria" placeholder="snacks" class="formulario--input" name="categoria" value="<?php echo $categoria;?>">
                        </div>
                    </div>  
                    <div class="formulario--campo">
                        <label class="formulario--label">Precio</label>
                        <input type="number" id="precio"placeholder="60.00" step="0.01" min="0"  class="formulario--input" name="precio" value="<?php echo $precio;?>">
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Stock</label>
                        <input type="number" id="stock" placeholder="5" step="1" min="0"  class="formulario--input" name="stock" value="<?php echo $stock;?>">
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Estado</label>
                        <div class="formulario--radio">
                            <label for="activo_si">Activado</label>
                            <input type="radio" id="activo_si" name="activo" value="1" checked>
                        </div>
                        <div class="formulario--radio">
                            <label for="activo_no">Desactivado</label>
                            <input type="radio" id="activo_no" name="activo" value="0">
                        </div>
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Imagen (.webp)</label>
                        <input type="file" id='imagen' accept="image/webp" name='imagen'>
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Descripción</label>
                        <div class="formulario--textarea">
                            <textarea id="detalles" placeholder="Alitas de pollo cocinadas lentamente sobre brasas que les dan un aroma ahumado único, y acompañadas de salsas que van desde el clásico picante hasta sabores dulces y exóticos." name="detalles"><?php echo $detalles;?></textarea>
                        </div>
                    </div>
                    
                </fieldset>
                <input type="submit" value="Agregar" class="boton--verde">
            </form> 
        </section>
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
