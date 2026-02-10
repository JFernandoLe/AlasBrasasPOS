    <?php 
        session_start();
        require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        $db=conectarDB();
        $incio = false;
        incluirTemplate('headerSession', $incio);
        $errores=[];
        //Autenticar al usuario
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $password=$_POST['password'];
            if(!$email){$errores[]="Ingresa email";}
            if(!$password){$errores[]="Ingresa contraseña";}
            
            if(empty($errores)){
                //Revisar si el usuario existe
                $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $resultado = $stmt->get_result();
                if ($usuario = $resultado->fetch_assoc()) {
                    if (password_verify($password, $usuario['password_hash'])) {

                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $usuario['id_usuario'];
                        $_SESSION['nombre'] = $usuario['nombre'];
                        $_SESSION['apellido'] = $usuario['apellido'];
                        $_SESSION['email'] = $usuario['correo'];
                        $_SESSION['rol'] = $usuario['rol'];
                        switch ($usuario['rol']) {
                            case 1:
                                $_SESSION['rol'] = 'admin';
                                break;
                            case 2:
                                $_SESSION['rol'] = 'camarero';
                                break;
                            case 3:
                                $_SESSION['rol'] = 'cocinero';
                                break;
                        }
                        $_SESSION['login'] = true;
                        $_SESSION['LAST_ACTIVITY'] = time();
                        switch ($_SESSION['rol']) {
                            case 'admin':
                                header("Location: " . BASE_URL . "/admin/index.php");
                                break;
                            case 'camarero':
                                header("Location: " . BASE_URL . "/menu.php");
                                break;
                            case 'cocinero':
                                header("Location: " . BASE_URL . "/menu.php");
                                break;
                        }
                        exit;
                        } else {
                            $errores[] = 'Contraseña incorrecta';
                    }
                } else {
                    $errores[] = 'Usuario no encontrado o inactivo';
                }
            }
        }
    ?>
    <main class="login-main">
        <div class="login-bg">
        <div class="header__menu--logo  contenedor">
            <img src="<?= BASE_URL ?>/src/img/full/logo.png" alt="Logo AlasBrasas">
        </div>
        <form method="POST" class="contenedor login-card login--form">
            <h1>Login</h1>
            <h2>¡Bienvenido a AlasBrasasPos!</h2>
            <?php foreach($errores as $error):?>
                <div class="alerta error">
                    <?php echo $error?>
                </div>
            <?php endforeach;?>
            <input type="email" name="email" placeholder="Correo" required>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button class="boton--rojo">Iniciar Sesión</button>
        </form>
        </div>

        


    </main>
