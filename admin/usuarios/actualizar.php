    <?php 
        require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        $errores=[];
        $id=$_GET['id'];
        $id=filter_var($id,FILTER_VALIDATE_INT);
        if(!$id){
            header('Location:'. BASE_URL . '/admin');
        }
        $query="SELECT nombre,apellido,correo,rol FROM usuarios WHERE id_usuario=$id;";
        $resultado=mysqli_query($db,$query);
        $usuario=mysqli_fetch_assoc($resultado);
        $nombre=$usuario['nombre'];
        $apellido=$usuario['apellido'];
        $correo=$usuario['correo'];
        $rol=$usuario['rol'];
        
        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            $nombre=mysqli_real_escape_string($db,$_POST['nombre']);
            $apellido=mysqli_real_escape_string($db,$_POST['apellido']);
            $correo=mysqli_real_escape_string($db,$_POST['correo']);
            $password=mysqli_real_escape_string($db,$_POST['password']);
            $confirm_password = $_POST['confirm_password'] ?? '';
            $rol=(int)($_POST['rol']);

            //Validar el formulario
            if(!$nombre){
                $errores[]='El nombre es obligatorio';
            }
            if(!$apellido){ 
                $errores[]='El apellido es obligatorio';
            }
            if(!$correo){ 
                $errores[]='El correo es obligatorio';
            }
            if(!$password){
                $errores[]='La contraseña es obligatoria';
            }
            if(!$rol){
                $errores[]='El rol es obligatorio';
            }
            if ($password !== $confirm_password) {
                $errores[] = 'Las contraseñas no coinciden';
            }
            if (strlen($password) < 8) {
                $errores[] = 'La contraseña debe tener al menos 8 caracteres';
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'Correo no válido';
            }
            if (!preg_match('/@alasbrasas\.com$/', $correo)) {
                $errores[] = 'Solo correos @alasbrasas.com';
            }
            if(empty($errores)){
                $passwordHash=password_hash($password,PASSWORD_BCRYPT);
                $query="UPDATE usuarios SET nombre='$nombre', apellido='$apellido', correo='$correo', password_hash='$passwordHash',rol=$rol WHERE id_usuario=$id;";
                $resultado=mysqli_query($db, $query);
                if($resultado){
                    header('Location:'. BASE_URL . '/admin/avisos.php?resultado=2');
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
            <form class="formulario" method="POST" >
                <fieldset class="formulario--fieldset">
                    <legend class="formulario--titulo">Modificar Usuario</legend>
                    <div class="formulario--campo">
                        <label class="formulario--label">Nombre</label>
                        <input type="text" id="nombre" required placeholder="Juan Fernando" class="formulario--input" name="nombre" value="<?php echo $nombre;?>">
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Apellido</label>
                        <div class="formulario--categoria">
                        <input type="text" id="apellido" required placeholder="Leon Medellin" class="formulario--input" name="apellido" value="<?php echo $apellido;?>">
                        </div>
                    </div>
                    <div class="formulario--campo">
                        <label class="formulario--label">Correo</label>
                        <div class="formulario--categoria">
                        <input  type="email" placeholder="correo@alasbrasas.com" required pattern=".+@alasbrasas\.com" autocomplete="email" maxlength="60" spellcheck="false" id="correo" class="formulario--input" name="correo" value="<?php echo $correo;?>">
                        </div>
                    </div>
                    <div class="formulario--campo">
                        <label for="password" class="formulario--label">Contraseña</label>
                        <div class="formulario--categoria">
                        <input  type="password" maxlength="60" spellcheck="false" id="password" class="formulario--input" name="password" required>
                        </div>
                    </div>
                    <div class="formulario--campo">
                        <label for="confirm_password" class="formulario--label">Confirmar Contraseña</label>
                        <div class="formulario--categoria">
                        <input  type="password" maxlength="60" spellcheck="false" id="confirm_password" class="formulario--input" name="confirm_password" required>
                        </div>
                    </div>
                    <small id="passwordError" style="color:red; display:none;">
                    Las contraseñas no coinciden
                    </small>       
                    <div class="formulario--campo">
                        <label class="formulario--label">Rol</label>
                        <?php 
                        $query2="SELECT *FROM roles;";
                        $resultado=mysqli_query($db,$query2);
                        ?>
                        <?php while($rolResult=mysqli_fetch_assoc($resultado)):?>
                            <?php $tipo=htmlspecialchars($rolResult['nombre']);?>
                            <?php $id_tipo=htmlspecialchars($rolResult['id']);?>
                            <div class="formulario--radio">
                                <label for="rol[<?= $id_tipo?>]"><?= $tipo ?></label>
                                <input type="radio" id="rol[<?= $id_tipo?>]" name="rol" value="<?= $id_tipo ?>" required <?php echo $rol==$id_tipo?'checked':'';?>>
                            </div>
                            <?php endwhile;?>
                    </div>
                    
                </fieldset>
                <input type="submit" value="Actualizar" class="boton--verde">
            </form> 
        </section>
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
