<?php
require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 

$db = conectarDB();

$id_venta = filter_input(INPUT_POST, 'id_venta', FILTER_VALIDATE_INT);
if($_SESSION['rol']!='admin'){
    echo 'Solo un administrador puede cancelar pedidos';
    header('Location: '. BASE_URL . '/verPedidos.php?error=1');
}else{
    if($id_venta){
    mysqli_query($db, "
        UPDATE ventas
        SET estado = 'cancelada'
        WHERE id_venta = $id_venta
    ");
    header('Location: verPedidos.php');
}
}



