<?php
        require_once __DIR__ . '/includes/config/database.php';

$db = conectarDB();

$id_venta = filter_input(INPUT_POST, 'id_venta', FILTER_VALIDATE_INT);

if($id_venta){
    mysqli_query($db, "
        UPDATE ventas
        SET progreso = 'hecho'
        WHERE id_venta = $id_venta
    ");
}

header('Location: verPedidos.php');
