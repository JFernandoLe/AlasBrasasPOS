<?php
        require_once __DIR__ . '/includes/config/database.php';
$db = conectarDB();

$query = "
SELECT 
    v.id_venta,
    v.fecha,
    v.tipo,
    v.progreso,
    v.estado,
    u.nombre AS mesero
FROM ventas v
JOIN usuarios u ON u.id_usuario = v.id_usuario
WHERE v.estado = 'pagada'
AND v.progreso IN ('pendiente','hecho')
ORDER BY v.fecha ASC
";

$resultado = mysqli_query($db, $query);

while($pedido = mysqli_fetch_assoc($resultado)):
$id_venta = (int)$pedido['id_venta'];
$progreso = $pedido['progreso'];
$estado=$pedido['estado'];
?>

<div class="pedido-card <?= $progreso ?>" data-id="<?= $id_venta ?>">

    <div class="pedido-header">
        <h2>Pedido #<?= $id_venta ?></h2>
        <span><?= htmlspecialchars($pedido['tipo']) ?></span>
    </div>

    <p><strong>Atendió:</strong> <?= htmlspecialchars($pedido['mesero']) ?></p>
    <p><strong>Hora:</strong> <?= $pedido['fecha'] ?></p>

    <hr>

    <?php
    $detalles = mysqli_query($db, "
        SELECT dv.id_detalle_venta, dv.cantidad, dv.notas, p.nombre
        FROM detalle_ventas dv
        JOIN productos p ON p.id_producto = dv.id_producto
        WHERE dv.id_venta = $id_venta
    ");

    while($d = mysqli_fetch_assoc($detalles)):
    ?>
        <div class="producto-cocina">
            <strong><?= $d['cantidad'] ?> × <?= htmlspecialchars($d['nombre']) ?></strong>

            <?php if($d['notas']): ?>
                <p class="nota">📝 <?= htmlspecialchars($d['notas']) ?></p>
            <?php endif; ?>

            <?php
            $id_detalle = (int)$d['id_detalle_venta'];
            $vars = mysqli_query($db, "
                SELECT v.nombre
                FROM detalle_ventas_variantes dvv
                JOIN variantes v ON v.id_variante = dvv.id_variante
                WHERE dvv.id_detalle_venta = $id_detalle
            ");
            while($var = mysqli_fetch_assoc($vars)):
            ?>
                <p class="variante">➕ <?= htmlspecialchars($var['nombre']) ?></p>
            <?php endwhile; ?>
        </div>
    <?php endwhile; ?>

    <!-- BOTONES -->
    <?php if($progreso === 'pendiente' && $estado!='cancelado'): ?>
        <form method="POST" action="marcar_hecho.php">
            <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
            <button class="btn-hecho">✔ Pedido Hecho</button>
        </form>
        <form method="POST" action="marcar_cancelar.php">
            <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
            <button class="btn-cancelar">X Cancelar Pedido</button>
        </form>
    <?php else: ?>
        <button class="btn-hecho oscuro" disabled>✔ Pedido Hecho</button>

        <form method="POST" action="marcar_entregado.php">
            <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
            <button class="btn-entregado"> Entregado</button>
        </form>
    
    <?php endif; ?>

</div>

<?php endwhile; ?>
