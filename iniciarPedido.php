<?php 
require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 

$db = conectarDB();

if(!isset($_SESSION['id_venta'])){

    $id_usuario = $_SESSION['user_id'] ?? 1;
    $queryVenta = "INSERT INTO ventas (fecha, total,metodo_pago,id_usuario,estado) VALUES (NOW(),0,'efectivo',$id_usuario,'abierta')";

    $resultadoVenta = mysqli_query($db, $queryVenta);

    if($resultadoVenta){
        $_SESSION['id_venta'] = mysqli_insert_id($db);
    } else {
        die("Error al crear la venta");
    }
}

$consultaCategoria = "SELECT DISTINCT categoria FROM productos;";
$resultado2 = mysqli_query($db, $consultaCategoria);

$inicio = true;
incluirTemplate('header', $inicio);
?>

<main class="bg-menu">
    <div class="mobile-menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#bfbfbf" d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/></svg></div>
    </div>
    <nav class="nav__pedidos contenedor" id="categorias">
    <a href="#" data-categoria="">Todos</a>    
    <?php while($cat = mysqli_fetch_assoc($resultado2)): ?>
            <?php $categoria = htmlspecialchars($cat['categoria']); ?>
            <a href="#" data-categoria="<?= $categoria ?>">
                <?= $categoria ?>
            </a>
        <?php endwhile; ?>
    </nav>

    <section class="contenedor menu__pedidos" id="lista-productos">
        <!-- api/productos.php -->
    </section>

</main>

<script src="<?= BASE_URL ?>/assets/js/filtro.js"></script>

<?php incluirTemplate('footer', $inicio); ?>
