<?php 
function conectarDB() {
    $db = new mysqli(
        'localhost',
        'alas_user',
        'escom',
        'alasbrasaspos'
    );

    if ($db->connect_error) {
        die('Error DB: ' . $db->connect_error);
    }

    return $db;
}