    <?php 
       require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        $consultaCategoria="SELECT DISTINCT categoria FROM productos;";
        $resultado=mysqli_query($db,$consultaCategoria);
        $errores=[];
        $nombre='';
        $activo=1;
        
        $medida=10000*100;

        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            $nombre=mysqli_real_escape_string($db,$_POST['nombre']);;
            $activo=mysqli_real_escape_string($db,$_POST['activo']);;
            $imagen=$_FILES['imagen'];
            //Validar el formulario
            if(!$nombre){
                $errores[]='El nombre es obligatorio';
            }
            if(!isset($_POST['activo'])){
                $errores[]='El estado es obligatorio';
            }
            if(!$imagen || $imagen['error']){
                $errores[]= "La imagen es obligatoria";
            }
            if($imagen['size']>$medida){
                $errores[]="La imagen debe pesar menos de 1 MB";
            }

            $carpetaImagenes='../../imagenes/';
            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }

            if(empty($errores)){
                $nombreImagen=md5(uniqid(rand(),true).".webp");
                move_uploaded_file($imagen['tmp_name'],$carpetaImagenes.$nombreImagen.".webp");
                //Insertar en la base de datos
                $query="INSERT INTO variantes (nombre,imagen,activo) VALUES ('$nombre', '$nombreImagen',$activo);";
                $resultado=mysqli_query($db, $query);
                if($resultado){
                    header('Location:'. BASE_URL . '/admin?resultado=1');
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
                    <legend class="formulario--titulo">Agregar Variante</legend>
                    <div class="formulario--campo">
                        <label class="formulario--label">Nombre</label>
                        <input type="text" id="nombre" placeholder="alitas" class="formulario--input" name="nombre" value="<?php echo $nombre;?>">
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
                </fieldset>
                <input type="submit" value="Agregar" class="boton--verde">
            </form> 
        </section>
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
