<?php
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/config/database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php'; 

if (!isset($_SESSION['id_venta'])) {
    header('Location: ' . BASE_URL . '/iniciarPedido.php');
    exit;
}

$db = conectarDB();
$id_venta = (int) $_SESSION['id_venta'];

/* =====================
   PRODUCTOS DEL CARRITO
===================== */
$query = "
SELECT 
    dv.id_detalle_venta,
    dv.cantidad,
    dv.precio_unitario,
    dv.notas,
    p.nombre AS nombre_producto,
    p.imagen
FROM detalle_ventas dv
JOIN productos p 
    ON dv.id_producto = p.id_producto
WHERE dv.id_venta = $id_venta
";
$resultado = mysqli_query($db, $query);

/* =====================
   VARIANTES / EXTRAS
===================== */
$queryVar = "
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
$resultadoVar = mysqli_query($db, $queryVar);

$variantesPorDetalle = [];
while ($row = mysqli_fetch_assoc($resultadoVar)) {
    $id = (int)$row['id_detalle_venta'];
    $variantesPorDetalle[$id][] = $row;
}

/* =====================
   ACCIONES POST
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* SUMAR / RESTAR */
    if (isset($_POST['accion'], $_POST['id_detalle'])) {

        $id_detalle = filter_var($_POST['id_detalle'], FILTER_VALIDATE_INT);
        $accion = $_POST['accion'];

        if ($id_detalle && in_array($accion, ['sumar', 'restar'])) {

            if ($accion === 'sumar') {
                $sql = "
                    UPDATE detalle_ventas
                    SET cantidad = cantidad + 1
                    WHERE id_detalle_venta = ?
                    AND id_venta = ?
                ";
            } else {
                $sql = "
                    UPDATE detalle_ventas
                    SET cantidad = IF(cantidad > 1, cantidad - 1, 1)
                    WHERE id_detalle_venta = ?
                    AND id_venta = ?
                ";
            }

            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $id_detalle, $id_venta);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ELIMINAR PRODUCTO */
    elseif (isset($_POST['id'])) {

        $id_detalle = filter_var($_POST['id'], FILTER_VALIDATE_INT);

        if ($id_detalle) {
            $stmt = mysqli_prepare(
                $db,
                "DELETE FROM detalle_ventas 
                 WHERE id_detalle_venta = ? AND id_venta = ?"
            );
            mysqli_stmt_bind_param($stmt, "ii", $id_detalle, $id_venta);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: " . BASE_URL . "/carrito.php");
    exit;
}

$inicio = true;
incluirTemplate('header', $inicio);
?>

<main class="bg-menu">
    <section class="contenedor">
        <h2>Carrito</h2>

        <?php if (mysqli_num_rows($resultado) === 0): ?>
            <p class="carrito-vacio">Tu carrito está vacío</p>
        <?php else: ?>

        <table class="tabla--carrito">
            <thead>
                <tr>
                    <td>Imagen</td>
                    <td>Producto</td>
                    <td>Extras</td>
                    <td>Notas</td>
                    <td>Cantidad</td>
                    <td>Subtotal</td>
                    <td>¿Eliminar?</td>
                </tr>
            </thead>
            <tbody>

            <?php 
            $total = 0;

            while ($item = mysqli_fetch_assoc($resultado)): 
                $id_detalle = (int)$item['id_detalle_venta'];
                $cantidad = (int)$item['cantidad'];
                $precio_unitario = (float)$item['precio_unitario'];
                $subtotal = $cantidad * $precio_unitario;

                $notas = htmlspecialchars($item['notas']);
                $nombre = htmlspecialchars($item['nombre_producto']);
                $imagen = htmlspecialchars($item['imagen']);

                $precio_extra_final = 0;
            ?>
                <tr>
                    <td data-label="Imagen">
                        <img src="<?= BASE_URL ?>/imagenes/<?= $imagen ?>.webp" alt="Imagen producto">
                    </td>

                    <td data-label="Producto">
                        <p><?= $nombre ?></p>
                        <p>$<?= number_format($precio_unitario, 2) ?></p>
                    </td>

                    <td data-label="Extras">
                        <?php if (!empty($variantesPorDetalle[$id_detalle])): ?>
                            <?php foreach ($variantesPorDetalle[$id_detalle] as $var): 
                                $precio_extra_final += (float)$var['precio_extra_aplicado'];
                            ?>
                                <p>
                                    <?= htmlspecialchars($var['nombre_variante']) ?>
                                    +$<?= number_format($var['precio_extra_aplicado'], 2) ?>
                                </p>
                            <?php endforeach; ?>
                            <p><strong>Total extras:</strong> $<?= number_format($precio_extra_final, 2) ?></p>
                        <?php else: ?>
                            <p>—</p>
                        <?php endif; ?>
                    </td>

                    <td data-label="Notas"><?php echo $notas?$notas:'—' ?></td>

                    <td data-label="Cantidad">
                        <div class="boton--accion--carrito">
                            <form method="POST" class="cantidad-form">
                            <input type="hidden" name="id_detalle" value="<?= $id_detalle ?>">
                            <input type="hidden" name="accion" value="restar">
                            <button type="submit" class="btn-sub">−</button>
                            </form>
                            <?= $cantidad ?>
                            <form method="POST" class="cantidad-form">
                                <input type="hidden" name="id_detalle" value="<?= $id_detalle ?>">
                                <input type="hidden" name="accion" value="sumar">
                                <button type="submit" class="btn-add">+</button>
                            </form>
                        </div>    
                    </td>

                    <?php
                        $subtotal_final = $subtotal + ($precio_extra_final * $cantidad);
                        $total += $subtotal_final;
                    ?>
                    <td data-label="Subtotal">$<?= number_format($subtotal_final, 2) ?></td>

                    <td data-label="¿Eliminar?" class="acciones">

                        

                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $id_detalle ?>">
                            <input 
                                type="submit" 
                                value="x" 
                                class="boton--rojo"
                                onclick="return confirm('¿Seguro que deseas eliminar este elemento?')"
                            >
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5"><strong>Total</strong></td>
                    <td><strong>$<?= number_format($total, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
        <section class="botones--carrito">
            <a class="boton--naranja seguir" href="<?= BASE_URL ?>/iniciarPedido.php">Seguir Comprando</a>
            <form method="POST" action="pago.php">
                <button type="submit" class="boton--naranja pagar">
                    Proceder al pago
                </button>
            </form>
        </section>
        <?php endif; ?>
    </section>
</main>

<?php
mysqli_free_result($resultado);
mysqli_close($db);
incluirTemplate('footer', $inicio);
?>
