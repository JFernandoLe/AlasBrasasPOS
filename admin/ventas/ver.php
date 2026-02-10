<?php 
    require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';
    
    $db = conectarDB();


    $fecha_inicio = $_GET['fecha_inicio'] ?? '';
    $fecha_fin = $_GET['fecha_fin'] ?? '';
    $estado_filtro = $_GET['estado'] ?? '';

    $query = "SELECT v.*, u.nombre as responsable 
              FROM ventas v 
              LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario 
              WHERE 1=1";

    if ($fecha_inicio) $query .= " AND v.fecha >= '$fecha_inicio'";
    if ($fecha_fin) $query .= " AND v.fecha <= '$fecha_fin'";
    if ($estado_filtro) $query .= " AND v.estado = '$estado_filtro'";

    $query .= " ORDER BY v.fecha ASC"; 
    $resultado = mysqli_query($db, $query);

    $ventas_lista = [];
    $total_acumulado = 0;
    $conteo = 0;

    while($row = mysqli_fetch_assoc($resultado)) {
        $ventas_lista[] = $row;
        $total_acumulado += (float)$row['total'];
        $conteo++;
    }

    $inicio = true;
    incluirTemplate('header', $inicio);
?>

<style>

    .grid-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        text-align: center;
    }
    .stat-card h3 { margin: 0; color: #666; font-size: 0.9rem; }
    .stat-card p { margin: 5px 0 0; font-size: 1.8rem; font-weight: bold; color: #333; }
    
    .filtros-contenedor {
        background: #f4f4f4;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 2rem;
    }
    .filtros-contenedor input, .filtros-contenedor select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .tag-estado {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .completado { background: #d4edda; color: #155724; }
    .pendiente { background: #fff3cd; color: #856404; }
</style>

<main class="bg-menu">
    <section class="contenedor">
        <h2>Panel de Control de Ventas</h2>

        <div class="grid-stats">
            <div class="stat-card">
                <h3>Total Ingresos</h3>
                <p>$ <?php echo number_format($total_acumulado, 2); ?></p>
            </div>
            <div class="stat-card">
                <h3>Nº de Operaciones</h3>
                <p><?php echo $conteo; ?></p>
            </div>
            <div class="stat-card">
                <h3>Promedio de Venta</h3>
                <p>$ <?php echo $conteo > 0 ? number_format($total_acumulado / $conteo, 2) : '0'; ?></p>
            </div>
        </div>

        <form method="GET" class="filtros-contenedor">
            <div>
                <label>Desde:</label>
                <input type="date" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
            </div>
            <div>
                <label>Hasta:</label>
                <input type="date" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
            </div>
            <div>
                <label>Estado:</label>
                <select name="estado">
                    <option value="">-- Todos --</option>
                    <option value="pagada" <?php echo $estado_filtro == 'pagada' ? 'selected' : ''; ?>>Pagada</option>
                    <option value="abierta" <?php echo $estado_filtro == 'abierta' ? 'selected' : ''; ?>>Abierta</option>
                    <option value="cancelada" <?php echo $estado_filtro == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>

                </select>
            </div>
            <input type="submit" value="Filtrar" class="boton--verde">
            <a href="<?= BASE_URL ?>/admin/ventas/ver.php" class="boton--naranja">Restablecer</a>
        </form>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 2rem;">
            <canvas id="graficaVentas" height="100"></canvas>
        </div>

        <div class="tabla-productos">
            <table class="producto">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Responsable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ventas_lista as $venta): ?>
                    <tr>
                        <td><?php echo $venta['id_venta']; ?></td>
                        <td><?php echo $venta['fecha']; ?></td>
                        <td>$ <?php echo number_format($venta['total'], 2); ?></td>
                        <td>
                            <span class="tag-estado <?php echo strtolower($venta['estado']); ?>">
                                <?php echo $venta['estado']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($venta['responsable']); ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/detalles.php?id=<?php echo $venta['id_venta'];?>" class="boton--naranja">Detalles</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="producto--boton">
            <a href="<?= BASE_URL ?>/crear.php" class="boton--verde">Nueva Venta</a>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficaVentas').getContext('2d');
    
    const etiquetas = [<?php echo '"'.implode('","', array_column($ventas_lista, 'fecha')).'"'; ?>];
    const montos = [<?php echo implode(',', array_column($ventas_lista, 'total')); ?>];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Monto de Ventas ($)',
                data: montos,
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php 
    incluirTemplate('footer', $inicio);
?>