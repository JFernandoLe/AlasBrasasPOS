<?php


require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 

$db = conectarDB();
$id_venta = (int) $_SESSION['id_venta'];

/* TOTAL PRODUCTOS */
$query = "
SELECT 
    dv.cantidad,
    dv.precio_unitario,
    IFNULL(SUM(dvv.precio_extra_aplicado), 0) AS extras
FROM detalle_ventas dv
LEFT JOIN detalle_ventas_variantes dvv 
    ON dv.id_detalle_venta = dvv.id_detalle_venta
WHERE dv.id_venta = $id_venta
GROUP BY dv.id_detalle_venta
";




$resultado = mysqli_query($db, $query);

$total = 0;
$tipo='';
while ($row = mysqli_fetch_assoc($resultado)) {
    $subtotal = ($row['precio_unitario'] + $row['extras']) * $row['cantidad'];
    $total += $subtotal;
}
/* Actualizar el total  */
$query="UPDATE ventas  SET total='$total' WHERE id_venta=$id_venta;";
$resultado=mysqli_query($db,$query);
if(!$resultado){
    echo"Error en la Base de datos";
}

$mensajeError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = isset($_POST['tipo']) 
        ? mysqli_real_escape_string($db, $_POST['tipo']) 
        : 'local';

    if (!isset($_POST['metodo']) || $_POST['metodo'] === '') {
        $mensajeError = 'No seleccionaste un método de pago';
    } else {
        $metodo = mysqli_real_escape_string($db, $_POST['metodo']);

        // Guardar método de pago
        $query = "UPDATE ventas 
                SET metodo_pago='$metodo',
                    tipo='$tipo',
                    estado='pagada' 
                WHERE id_venta=$id_venta";

        mysqli_query($db, $query);

        /* ==========================
        DESCONTAR STOCK
        ========================== */
        $queryStock = "
            SELECT id_producto, cantidad 
            FROM detalle_ventas 
            WHERE id_venta = $id_venta
        ";

        $resultadoStock = mysqli_query($db, $queryStock);

        while ($item = mysqli_fetch_assoc($resultadoStock)) {

            $id_producto = (int)$item['id_producto'];
            $cantidad    = (int)$item['cantidad'];

            $updateStock = "
                UPDATE productos 
                SET stock = stock - $cantidad 
                WHERE id_producto = $id_producto
            ";

            mysqli_query($db, $updateStock);
        }

        $_SESSION['venta_finalizada'] = true;
        header('Location: '. BASE_URL . '/gracias.php');
        exit;

    }
}



$incio = true;
incluirTemplate('header', $incio);
?>
<main class="bg-menu">
    <section class="contenedor">
        <form method="POST">
                    <div class="pago-card">
            <h2>Pagar Pedido</h2>

            <!-- TOTAL -->
            <div class="total">
                Total: $<span id="total"><?php echo(number_format($total,2));  ?></span>
            </div>
            <!-- TIPO -->
            <label for="tipo">Modo de Consumo</label>
            <select name="tipo" id="tipo" required> 
                <option value="" disabled>Selecciona</option>
                <option value="local" selected>Local</option>
                <option value="llevar">Para llevar</option>
                <option value="delivery">Delivery</option>
            </select>
            <!-- MÉTODO DE PAGO -->
            <label for="metodoPago">Método de pago</label>
            <select name="metodo" id="metodoPago" required> 
                <option value="" disabled selected>Selecciona</option>
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
            </select>

            <!-- EFECTIVO -->
            <div id="efectivoBox" class="oculto">
                <label for="recibido">Dinero recibido</label>
                <input type="number" id="recibido" min="0" step="0.01" placeholder="Ej. 300" required>

                <div class="cambio">
                    Cambio: $<span id="cambio">0.00</span>
                </div>
            </div>

            <!-- TRANSFERENCIA -->
            <div id="transferenciaBox" class="oculto">
                <p class="info-transferencia">
                    El cliente pagará por transferencia
                </p>
            </div>

            <button id="btn-pagar" type="submit" class="btn-pagar btnOculto">Confirmar Pago</button>
            </div>
        </form>

    </section>
    <!-- TICKET (OCULTO) -->
    <?php
    $query="SELECT fecha,metodo_pago FROM ventas WHERE id_venta=$id_venta;";
    $resultado=mysqli_query($db,$query);
    $venta=mysqli_fetch_assoc($resultado);
    $fecha=$venta['fecha'];
    $metodo_pago=$venta['metodo_pago'];
    /* =====================
    PRODUCTOS PARA TICKET
    ===================== */
    $queryTicket = "
    SELECT 
        dv.id_detalle_venta,
        dv.cantidad,
        dv.precio_unitario,
        p.nombre AS nombre_producto
    FROM detalle_ventas dv
    JOIN productos p 
        ON dv.id_producto = p.id_producto
    WHERE dv.id_venta = $id_venta
    ";

    $resultadoTicket = mysqli_query($db, $queryTicket);

    /* VARIANTES */
    $queryVarTicket = "
    SELECT 
        dvv.id_detalle_venta,
        v.nombre AS nombre_variante,
        dvv.precio_extra_aplicado
    FROM detalle_ventas_variantes dvv
    JOIN variantes v 
        ON dvv.id_variante = v.id_variante
    WHERE dvv.id_detalle_venta IN (
        SELECT id_detalle_venta 
        FROM detalle_ventas 
        WHERE id_venta = $id_venta
    )
    ";

    $resultadoVarTicket = mysqli_query($db, $queryVarTicket);

    $variantesTicket = [];
    while ($row = mysqli_fetch_assoc($resultadoVarTicket)) {
        $variantesTicket[$row['id_detalle_venta']][] = $row;
    }

    ?>
    
    <div id="ticket">
        <h2>ALAS BRASAS</h2>
        <p>Dirección: San Pedro 2 Lt1 Mz2 55712</p>
        <p> Los Héroes Coacalco. Estado de México, México</p>
        <hr>
        <p>Comprobante de Pago</p>
        <hr>



        <p>Pedido #<?= $id_venta ?></p>
        <p>Fecha <?= $fecha ?></p>
        <p>Le atendió: <?= $_SESSION['nombre'] ?></p>
        <hr>

    <div id="ticket-productos">
        <?php while ($item = mysqli_fetch_assoc($resultadoTicket)): 
            $id_detalle = (int)$item['id_detalle_venta'];
            $cantidad = (int)$item['cantidad'];
            $nombre = htmlspecialchars($item['nombre_producto']);
            $precio = (float)$item['precio_unitario'];

            $totalExtras = 0;
        ?>
        <p>
            <?= $cantidad ?> x <?= $nombre ?>
            $<?= number_format($precio , 2)?> C/u
        </p>

        <?php if (!empty($variantesTicket[$id_detalle])): ?>
            <?php foreach ($variantesTicket[$id_detalle] as $var): 
                $totalExtras += (float)$var['precio_extra_aplicado'];
            ?>
                <p style="margin-left:10px;">
                    + <?= htmlspecialchars($var['nombre_variante']) ?>
                    $<?= number_format($var['precio_extra_aplicado'], 2) ?> C/u
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php endwhile; ?>

        </div>

        <hr>
        <p>Total: $<span id="ticket-total"><?= number_format($total,2) ?></span></p>
        <p>Método de pago: <span id="ticket-metodo"><?= $metodo_pago ?></span></p>
        <hr>
        <p class="gracias">¡Gracias por su compra!</p>
        <p class="gracias">Siguenos en nuestras redes sociales para enterarte de promociones:</p>
        <p class="gracias">Instagram: @alas_brasasoficial</p>
        <p class="gracias">Facebook: Alas Brasas</p>
        <p class="gracias">Telefono: +52 5546802024</p>
        <p>alasbrasas 2026</p>
        <hr>
        <hr>
        <hr>
        <div style="height: 800px;"></div>
    </div>
</main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>

