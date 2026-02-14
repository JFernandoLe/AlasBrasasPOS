<?php 
function conectarDB() {
    $db = new mysqli(
        'localhost',
        'root',
        'escom',
        'alasbrasaspos'
    );

    if ($db->connect_error) {
        die('Error DB: ' . $db->connect_error);
    }

    return $db;
}