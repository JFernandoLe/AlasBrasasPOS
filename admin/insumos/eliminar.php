<?php
    require '../../includes/funciones.php';
        require_once '../includes/auth.php'; 
        require_once '../../includes/config/database.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ver.php');
        exit;
    }

    $db = conectarDB();

    // Validar ID
    $id = filter_input(INPUT_POST, 'id_insumo', FILTER_VALIDATE_INT);

    if (!$id) {
        header('location:'. BASE_URL . '/admin/avisos.php');
        exit;
    }

    // Eliminado lógico
    $query = "UPDATE insumos SET activo = 0 WHERE id_insumo = $id LIMIT 1";
    $resultado = mysqli_query($db, $query);

    if ($resultado) {
        header('location:'. BASE_URL . '/admin/avisos.php?resultado=3');// eliminado
    } else {
        header('location:'. BASE_URL . '/admin/avisos.php');
    }
