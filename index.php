<?php
    require 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="en" class="html--index">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="build/css/app.css">
    <link rel="apple-touch-icon" href="src/img/full/ALASBRASAS.png">
    <link rel="apple-touch-icon" sizes="152x152" href="src/img/full/ALASBRASAS.png">
    <link rel="apple-touch-icon" sizes="180x180" href="src/img/full/ALASBRASAS.png">
    <meta name="apple-mobile-web-app-title" content="AlasBrasasPos">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#FF4500">
    <title>AlasBrasasPos</title>
</head>
<body class="bg">
    <main class="fondo">
        <div class="fondo__logo  contenedor">
            <img src="src/img/full/logo.png" alt="Logo AlasBrasas">
        </div>
        <div class="fondo__login contenedor">
            <a href="<?= isset($_SESSION['login']) && $_SESSION['login'] ? 'menu.php' : 'login.php' ?>" class="boton--rojo">
                <?= isset($_SESSION['login']) && $_SESSION['login'] ? 'Inicio' : 'Iniciar Sesión' ?>
            </a>
        </div>
        <!--<div class="fondo__texto contenedor">
            <p><?php //isset($_SESSION['login']) && $_SESSION['login'] ? '' : '¿No tienes una cuenta? <span>Contáctanos</span>' ?></p>
        </div>-->
    </main>
</body>
</html>