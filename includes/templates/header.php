<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/build/css/app.css">
    
    <title>AlasBrasasPos</title>
</head>
<body class="body--menu">
    <header class="header__menu bg <?php echo $incio?'inicio':'';?>">
        <div class="header__menu--logo">
            <img src="<?= BASE_URL ?>/src/img/full/logo.png" alt="Logo AlasBrasas">
        </div>
        
        <div class="header__menu--data">
            <div class="header__menu--saludo">
            <h3>
                ¡Hola 
                <?= isset($_SESSION['login']) && $_SESSION['login'] 
                    ? $_SESSION['nombre'] 
                    : 'Invitado'; 
                ?>!
            </h3>
            </div>
            <div class="header__menu--rol">
            <p>Soy <span><?php echo isset($_SESSION['login']) && $_SESSION['login'] 
                    ? $_SESSION['rol'] 
                    : 'Invitado';  ?></span></p>
            </div>
        </div>
        
        <div class="header__menu--carrito">
            <a href="<?= BASE_URL ?>/carrito.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-basket3-fill" viewBox="0 0 16 16">
                <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-1A.5.5 0 0 1 .5 6h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM2.468 15.426.943 9h14.114l-1.525 6.426a.75.75 0 0 1-.729.574H3.197a.75.75 0 0 1-.73-.574z"/>
                </svg>
            </a>
        </div>
        
    </header>