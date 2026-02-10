<?php
require_once '../includes/config/database.php';

$db = conectarDB();

$categoria = $_GET['categoria'] ?? '';
$where = "WHERE activo='1'";

if ($categoria !== '') {
    $categoria = mysqli_real_escape_string($db, $categoria);
    $where = "WHERE categoria='$categoria' AND activo='1'";
}
if (!defined('BASE_URL')) {
    define('BASE_URL', ''); 
}

$consultaProducto = "SELECT * FROM productos $where;";
$resultado = mysqli_query($db, $consultaProducto);
?>

<h2 class="titulo--categoria"><?= $categoria !== '' ? htmlspecialchars($categoria) : 'Todos' ?></h2>

<div class="items--productos">
<?php while($producto = mysqli_fetch_assoc($resultado)): ?>
    <?php
        $id=(int)$producto['id_producto'];        
        $nombre   = htmlspecialchars($producto['nombre']);
        $detalles = htmlspecialchars($producto['detalles']);
        $precio   = htmlspecialchars($producto['precio']);
        $imagen   = basename($producto['imagen']);
        $rutaImagen = BASE_URL."/imagenes/".$imagen.".webp";
    ?>
    <a href="<?= BASE_URL ?>/producto.php?id=<?= $id; ?>" class="item">
        <div class="circle">
            <img src="<?= $rutaImagen ?>" alt="<?= $nombre ?>">
        </div>
        <h4><?= $nombre ?></h4>
        <p><?= $detalles ?></p>
        <p><span>$ <?= $precio ?></span></p>
    </a>
<?php endwhile; ?>
</div>
