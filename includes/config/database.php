<?php 
function conectarDB() {
    $db = new mysqli(
        '',
        '',
        '',
        ''
    );

    if ($db->connect_error) {
        die('Error DB: ' . $db->connect_error);
    }

    return $db;
}