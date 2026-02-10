<?php
require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';

$db = conectarDB();

$id = filter_input(INPUT_POST, 'id_insumo', FILTER_VALIDATE_INT);
$stock = filter_input(INPUT_POST, 'stock_actual', FILTER_VALIDATE_FLOAT);

if($id !== false && $stock !== false) {
    $stmt = $db->prepare(
        "UPDATE insumos SET stock_actual = ? WHERE id_insumo = ?"
    );
    $stmt->bind_param("di", $stock, $id);
    $stmt->execute();
}

header('Location: ver.php');
exit;
