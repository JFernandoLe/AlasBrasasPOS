    <?php 
        require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        
        $query="SELECT *FROM usuarios;";
        $resultado=mysqli_query($db,$query);
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $id=$_POST['id'];
            $id=filter_var($id,FILTER_VALIDATE_INT);
            $query = "SELECT COUNT(*) total FROM ventas WHERE id_usuario = $id";
            $result = mysqli_query($db, $query);
            $data = mysqli_fetch_assoc($result);

            if ($data['total'] > 0) {
                header('Location:'. BASE_URL . '/admin/avisos.php?resultado=4');
            }
            if($id){
                $query="DELETE FROM usuarios WHERE id_usuario=$id";
                $resultado=mysqli_query($db,$query);
                if($resultado){
                    header('Location:'. BASE_URL . '/admin/avisos.php?resultado=3');
                }
            }
        }
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
<main class="bg-menu">
        
        <section class="contenedor">
        <h2>Usuarios</h2>
        <div class="tabla-productos">
        <table class="producto">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
        <tbody>
        <?php while($usuario=mysqli_fetch_assoc($resultado)):?>
            <?php $id_usuario=(int)$usuario['id_usuario'];?>
            <?php $nombre=htmlspecialchars($usuario['nombre']);?>
            <?php $apellido=htmlspecialchars($usuario['apellido']);?>
            <?php $correo=htmlspecialchars($usuario['correo']);?>
            <?php $rol=(int)($usuario['rol'])?>
            <?php $fechaRegistro=htmlspecialchars($usuario['fecha_registro'])?>
        <tr>
            <td><?php echo $id_usuario; ?></td>
            <td><?php echo $nombre; ?></td>
            <td><?php echo $apellido; ?></td>
            <td><?php echo $correo; ?></td>
            
            <?php $query3="SELECT nombre FROM roles WHERE id='$rol';";
            $resultado3=mysqli_query($db,$query3);
            $resultadoRol=mysqli_fetch_assoc($resultado3);
            $nombreRol=htmlspecialchars($resultadoRol['nombre']);
            ?>
            <td><?php echo $nombreRol; ?></td>
            <td><?php echo $fechaRegistro; ?></td>
            <td>
                <a href="actualizar.php?id=<?php echo $id_usuario;?>" class="boton--verde">Modificar</a>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $id_usuario?>">
                    <div class="input">
                        <input type="submit" value="Eliminar" class="boton--rojo" onclick="return confirm('¿Seguro que deseas eliminar a este usuario?')">
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