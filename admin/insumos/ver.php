<?php 
    require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';

    $db = conectarDB();

    $sql = "SELECT *,
        CASE
            WHEN stock_actual <= 0 THEN 'CRITICO'
            WHEN stock_actual < stock_minimo THEN 'COMPRAR'
            ELSE 'OK'
        END AS estado
    FROM insumos
    WHERE activo = 1
    ORDER BY 
        CASE
            WHEN stock_actual <= 0 THEN 1
            WHEN stock_actual < stock_minimo THEN 2
            ELSE 3
        END,
        categoria, nombre";

    $resultado = mysqli_query($db, $sql);

    $inicio = true;
    incluirTemplate('header', $inicio);
?>

<main class="bg-menu">
    <section class="contenedor">
        <h2>Checklist de Insumos</h2>

        <div class="tabla--carrito insumos">
            <table class="producto">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Categoría</th>
                        <th>Stock actual</th>
                        <th>Mínimo</th>
                        <th>Días</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($i = mysqli_fetch_assoc($resultado)): ?>
                    <tr class="<?= strtolower($i['estado']) ?>">
                        <td><?= htmlspecialchars($i['nombre']) ?></td>
                        <td><?= htmlspecialchars($i['categoria']) ?></td>
                        <td><?= $i['stock_actual'].' '.$i['unidad'] ?></td>
                        <td><?= $i['stock_minimo'].' '.$i['unidad'] ?></td>
                        <td><?= $i['dias_cobertura'] ?></td>
                        <td>
                            <span class="estado <?= strtolower($i['estado']) ?>">
                                <?= $i['estado'] ?>
                            </span>
                        </td>

                        <td>
                            <div class="botones--insumos">
                                <form method="POST" action="actualizar_stock.php" class="form-inline">
                                    <input type="hidden" name="id_insumo" value="<?= $i['id_insumo'] ?>">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        name="stock_actual"
                                        value="<?= $i['stock_actual'] ?>" 
                                        required
                                    >
                                    <button type="submit">
                                        Confirmar
                                    </button>
                                </form>
                                <a 
                                    href="actualizar.php?id=<?= $i['id_insumo'] ?>" 
                                    class="boton--naranja"
                                    title="Editar insumo"
                                >
                                    Editar
                                </a>
                                <form 
                                    method="POST" 
                                    action="eliminar.php" 
                                    class="form-inline"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar <?= $i['nombre'] ?>?')"
                                >
                                    <input type="hidden" name="id_insumo" value="<?= $i['id_insumo'] ?>">
                                    <button type="submit" class="boton--eliminar">
                                        Archivar
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="producto--boton">
            <a href="crear.php" class="boton--verde">+ Agregar insumo</a>
        </div>
    </section>
</main>

<?php 
    incluirTemplate('footer', $inicio);
?>
