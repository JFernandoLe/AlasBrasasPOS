    <?php 
        require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $id=$_GET['id'];
        $id=filter_var($id,FILTER_VALIDATE_INT);
        if(!$id){
            header('Location:'. BASE_URL . '/admin');
        }
        $db=conectarDB();
        $consulta="SELECT *FROM variantes WHERE id_variante=$id";
        $resultado=mysqli_query($db,$consulta);
        $variante=mysqli_fetch_assoc($resultado);
        $errores=[];
        $nombre=$variante['nombre'];
        $activo=$variante['activo'];
        $imagenVariante=$variante['imagen'];
        
        $medida=10000*100;
        
        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            $nombre=mysqli_real_escape_string($db,$_POST['nombre']);
            $activo=mysqli_real_escape_string($db,$_POST['activo']);
            $imagen=$_FILES['imagen'];
            //Validar el formulario
            if(!$nombre){
                $errores[]='El nombre es obligatorio';
            }
            if(!isset($_POST['activo'])){
                $errores[]='El estado es obligatorio';
            }
            if($imagen['size']>$medida){
                $errores[]="La imagen debe pesar menos de 1 MB";
            }

            if(empty($errores)){
                $carpetaImagenes='../../imagenes/';
                if(!is_dir($carpetaImagenes)){
                    mkdir($carpetaImagenes);
                }
                $nombreImagen='';
                if($imagen['name']){
                //Eliminamos la imagen previa
                unlink($carpetaImagenes.$variante['imagen'].".webp");
                //Generar un nombre unico
                $nombreImagen=md5(uniqid(rand(),true));
                move_uploaded_file($imagen['tmp_name'],$carpetaImagenes.$nombreImagen.".webp");
                }else{
                    $nombreImagen=$variante['imagen'];
                }
                //Insertar en la base de datos
                $query="UPDATE variantes SET nombre='$nombre', activo='$activo',imagen='$nombreImagen'  WHERE id_variante=$id ;";
                $resultado=mysqli_query($db, $query);
                if($resultado){
                    header('Location:'. BASE_URL . '/admin?resultado=2');
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
                            <input type="radio" id="activo_si" name="activo" value="1" <?php echo($activo?'checked':''); ?>>
                        </div>
                        <div class="formulario--radio">
                            <label for="activo_no">Desactivado</label>
                            <input type="radio" id="activo_no" name="activo" value="0" <?php echo($activo?'':'checked'); ?>>
                        </div>
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Imagen (.webp)</label>
                        <input type="file" id='imagen' accept="image/webp" name='imagen'>
                        <img src="<?= BASE_URL ?>/imagenes/<?php echo $imagenVariante.".webp"?>" class="imagen-small">
                    </div>
                </fieldset>
                <input type="submit" value="Modificar" class="boton--verde">
            </form> 
        </section>
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
