    <?php 
       require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
        $db=conectarDB();
        
        $id_venta = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if(!$id_venta){
            header('Location: '. BASE_URL . 'index.php');
        exit;
        }
        $query = "
        SELECT 
            dv.id_detalle_venta,
            dv.cantidad,
            dv.precio_unitario,
            dv.subtotal,
            dv.notas,
            dv.id_producto,
            p.nombre AS producto
        FROM detalle_ventas dv
        JOIN productos p ON p.id_producto = dv.id_producto
        WHERE dv.id_venta = $id_venta
        ";
        $resultado = mysqli_query($db, $query);
        $queryVar = "
        SELECT 
            dvv.id_detalle_venta,
            v.nombre AS variante,
            dvv.precio_extra_aplicado
        FROM detalle_ventas_variantes dvv
        JOIN variantes v ON v.id_variante = dvv.id_variante
        JOIN detalle_ventas dv ON dv.id_detalle_venta = dvv.id_detalle_venta
        WHERE dv.id_venta = $id_venta
        ";
        $resultadoVar = mysqli_query($db, $queryVar);
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
<main class="bg-menu">
        
        <section class="contenedor">
        <h2>Detalles de la Venta #<?php echo $id_venta; ?></h2>

        <table class="producto">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Notas</th>


                </tr>
            </thead>
            <tbody>
                <?php while($detalle = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($detalle['producto']); ?></td>
                    <td><?php echo $detalle['cantidad']; ?></td>
                    <td>$<?php echo $detalle['precio_unitario']; ?></td>
                    <td>$<?php echo $detalle['subtotal']; ?></td>
                    <td><?php echo htmlspecialchars($detalle['notas']); ?></td>
                </tr>

                <?php
                
                $id_detalle = (int)$detalle['id_detalle_venta'];

                $queryVar = "
                SELECT 
                    v.nombre AS variante,
                    dvv.precio_extra_aplicado
                FROM detalle_ventas_variantes dvv
                JOIN variantes v ON v.id_variante = dvv.id_variante
                WHERE dvv.id_detalle_venta = $id_detalle
                ";

                $resultadoVar = mysqli_query($db, $queryVar);

                while($var = mysqli_fetch_assoc($resultadoVar)):
                ?>
                <tr class="fila-variante">
                    <td colspan="5">
                        ➕ <?php echo htmlspecialchars($var['variante']); ?>
                        (+$<?php echo $var['precio_extra_aplicado']; ?>)
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php endwhile; ?>
            </tbody>
        </table>
        </section>
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>