    <?php 
        require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
        $incio = true;
        incluirTemplate('header', $incio);
    ?>
    <main class="bg-menu main--ajustado">
        <section class="info--transferencia contenedor">
            <h2>Datos Transferencia</h2>
            <p>Para realizar una transferencia, utiliza los siguientes datos:</p>
            <ul>
                <li><strong>Banco:</strong> BBVA</li>
                <li><strong>Cuenta:</strong> 1568660055</li>
                <li><strong>CLABE:</strong> 0121 8001 5686 6005 52</li>
                <li><strong>SWIFT:</strong> BCMRMXMMPYM</li>
            </ul>
            <p>Asegúrate de incluir tu nombre y número de pedido como referencia.</p>
        </section>
        <section class="info--contacto contenedor">
            <h2>¡Síguenos!</h2>
            <div class="info--redes">
                <div class="info--facebook">
                    <img src="<?= BASE_URL ?>/src/img/full/facebook.png" alt="Facebook AlasBrasas">
                    <a href="https://www.facebook.com/people/Alas-Brasas/61562188284494/" target="_blank">Alas Brasas</a>  
                </div>
                <div class="info--instagram">
                    <img src="<?= BASE_URL ?>/src/img/full/instagram.png" alt="Instagram AlasBrasas">
                    <a href="https://www.instagram.com/alas_brasasoficial/" target="_blank">@alas_brasasoficial</a>
                </div>
                <div class="info--whatsapp">
                    <img src="<?= BASE_URL ?>/src/img/full/whatsapp.png" alt="WhatsApp AlasBrasas">
                    <a href="https://wa.me/5546802024" target="_blank">+52 55 46802024</a>
                </div>
                <div class="info--google-maps">
                    <img src="<?= BASE_URL ?>/src/img/full/maps.png" alt="Google Maps AlasBrasas">
                    <a href="https://www.google.com/maps/place/Alas+Brasas/@19.6375308,-99.1064026,21z/data=!4m15!1m8!3m7!1s0x85d1f54d19dcd759:0x213594c29c9f7f2e!2sAlas+Brasas!8m2!3d19.6374566!4d-99.1062962!10e1!16s%2Fg%2F11vppd846z!3m5!1s0x85d1f54d19dcd759:0x213594c29c9f7f2e!8m2!3d19.6374566!4d-99.1062962!16s%2Fg%2F11vppd846z?hl=es-419&entry=ttu&g_ep=EgoyMDI1MDgxOC4wIKXMDSoASAFQAw%3D%3D" target="_blank">Ubicación Maps</a>
                </div>
            </div>
        </section>
    
        
    </main>
    <?php 
        incluirTemplate('footer', $incio);
    ?>