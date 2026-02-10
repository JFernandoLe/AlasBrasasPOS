<?php
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: '.BASE_URL.'/index.php');
    exit;
}

$tiempoMax = 720 * 60; // 12 horas

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $tiempoMax)) {
    header('Location: '.BASE_URL.'/logout.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

