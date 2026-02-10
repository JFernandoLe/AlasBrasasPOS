<?php
require_once __DIR__ . '/includes/funciones.php';
        require_once __DIR__ . '/includes/config/database.php';
        require_once __DIR__ . '/includes/session.php';
        require_once __DIR__ . '/includes/auth.php'; 
$error = $_GET['error'] ?? '';
$incio = true;
incluirTemplate('headerSession', $incio);
?>

<div class="panel-ajustes">
    <div class="ajuste-grupo">
        <label>Navegar:</label>
        <div class="controles-nav-sup">
            <button onclick="navegar(-1)">⬅</button>
            <span id="indicadorPagina">Pag. 1</span>
            <button onclick="navegar(1)">➡</button>
        </div>
    </div>

    <div class="ajuste-grupo">
        <label>Carrusel:</label>
        <button id="btnAutoplay" class="btn-toggle activo" onclick="toggleAutoplay()">
            <span id="iconPlay">⏸ Pausar</span>
        </button>
    </div>

    <div class="ajuste-grupo">
        <label>Ver:</label>
        <select id="cardsPorPagina">
            <option value="1">1 Tarjeta</option>
            <option value="2" selected>2 Tarjetas</option>
            <option value="3">3 Tarjetas</option>
        </select>
    </div>

    <div class="ajuste-grupo">
        <label>Zoom:</label>
        <div class="zoom-controls">
            <button onclick="ajustarZoom(-0.1)">A-</button>
            <button onclick="ajustarZoom(0.1)">A+</button>
        </div>
    </div>
</div>

<div id="notificacion" class="notificacion-flotante">🔔 ¡Nuevo Pedido Recibido!</div>

<main id="mainCocina">
    <?php if($error==='1'):?>
        <div class="alerta error">
            <?php echo "Sólo un administrador puede cancelar pedidos";?> 
        </div>
    <?php endif; ?>
    <section class="contenedorPedidos">
        <div class="carril-pedidos" id="pedidos"></div>
    </section>
</main>

<audio id="sonidoPedido" preload="auto">
    <source src="<?= BASE_URL ?>/sounds/nuevo_pedido.mp3" type="audio/mpeg">
</audio>

<script>
/* ==========================================================================
   VARIABLES DE ESTADO
   ========================================================================== */
let index = 0;
let intervalo;
let totalPedidosActual = 0;
let zoomLevel = 1.0;
let visibles = 2; // Cantidad de tarjetas por pantalla (default)
let autoplayActivo = true;

/* ==========================================================================
   GESTIÓN DE LAYOUT Y ANCHOS
   ========================================================================== */

/**
 * Fuerza el ancho correcto en cada tarjeta basándose en la cantidad 
 * de elementos visibles seleccionados. Evita que se encojan al actualizar.
 */
function aplicarAnchoTarjetas() {
    const cards = document.querySelectorAll('.pedido-card');
    if (cards.length === 0) return;

    // gap de 2rem (coincide con el SCSS)
    const anchoCalculado = `calc(${100 / visibles}% - ${(2 * (visibles - 1)) / visibles}rem)`;
    
    cards.forEach(card => {
        card.style.flex = `0 0 ${anchoCalculado}`;
        card.style.width = anchoCalculado;
    });
}

/**
 * Controla el zoom de la interfaz mediante la propiedad scale.
 */
function ajustarZoom(valor) {
    zoomLevel += valor;
    const main = document.getElementById('mainCocina');
    main.style.transform = `scale(${zoomLevel})`;
    main.style.transformOrigin = 'top center';
}

/**
 * Cambia la configuración de cuántas tarjetas se ven por página.
 */
document.getElementById('cardsPorPagina').onchange = function() {
    visibles = parseInt(this.value);
    index = 0; // Reiniciamos para evitar errores de cálculo visual
    aplicarAnchoTarjetas();
    moverCarril();
};

/* ==========================================================================
   NAVEGACIÓN Y CARRUSEL
   ========================================================================== */

/**
 * Mueve el carril a la posición correspondiente al índice actual.
 */
function moverCarril() {
    const carril = document.getElementById('pedidos');
    const cards = carril.querySelectorAll('.pedido-card');
    const indicador = document.getElementById('indicadorPagina');
    
    const totalPaginas = Math.ceil(cards.length / visibles);

    if (cards.length === 0) {
        indicador.innerText = "Sin pedidos";
        return;
    }

    // Lógica de bucle (Loop infinito)
    if (index >= totalPaginas) index = 0;
    if (index < 0) index = totalPaginas - 1;

    // El desplazamiento es siempre por múltiplos de 100% (una "página" completa)
    carril.style.transform = `translateX(-${index * 100}%)`;
    indicador.innerText = `Pag. ${index + 1} / ${totalPaginas}`;
}

/**
 * Función vinculada a los botones de navegación manual.
 */
function navegar(direccion) {
    index += direccion;
    moverCarril();
    // Si el usuario mueve manualmente, reiniciamos el temporizador para no interrumpirlo
    if(autoplayActivo) iniciarAuto(); 
}

/* ==========================================================================
   AUTOMATIZACIÓN (PLAY / PAUSE)
   ========================================================================== */

function toggleAutoplay() {
    autoplayActivo = !autoplayActivo;
    const btn = document.getElementById('btnAutoplay');
    const icon = document.getElementById('iconPlay');
    
    btn.classList.toggle('activo', autoplayActivo);
    icon.innerText = autoplayActivo ? "⏸ Pausar" : "▶ Play";
    
    autoplayActivo ? iniciarAuto() : detenerAuto();
}

function iniciarAuto() {
    detenerAuto();
    if (!autoplayActivo) return;
    intervalo = setInterval(() => {
        index++;
        moverCarril();
    }, 20000); // Cambio cada 20 segundos
}

function detenerAuto() {
    clearInterval(intervalo);
}

/* ==========================================================================
   CRONÓMETRO DE TIEMPO REAL
   ========================================================================== */

/**
 * Calcula el tiempo transcurrido desde que se creó el pedido.
 */
function actualizarRelojes() {
    const ahora = new Date();
    document.querySelectorAll('.pedido-card').forEach(card => {
        // No contamos tiempo para pedidos ya marcados como hechos
        if(card.classList.contains('hecho')) return;

        const fechaPedido = new Date(card.dataset.fecha);
        const diferencia = Math.floor((ahora - fechaPedido) / 1000);
        
        const mins = Math.floor(diferencia / 60);
        const segs = diferencia % 60;
        
        const timer = card.querySelector('.tiempo-espera');
        if(timer) {
            timer.innerText = `${mins}:${segs.toString().padStart(2, '0')}`;
            // Alerta visual tras 15 minutos
            if(mins >= 15) timer.classList.add('urgente');
        }
    });
}

/* ==========================================================================
   AJAX, SONIDO Y NOTIFICACIONES
   ========================================================================== */

function cargarPedidos() {
    fetch('pedidos_ajax.php')
        .then(res => res.text())
        .then(html => {
            const carril = document.getElementById('pedidos');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const nuevosCardsCount = tempDiv.querySelectorAll('.pedido-card').length;

            // Disparar sonido y banner solo si entra un pedido nuevo (no en la carga inicial)
            if (nuevosCardsCount > totalPedidosActual && totalPedidosActual !== 0) {
                const sonido = document.getElementById('sonidoPedido');
                sonido.play().catch(e => console.warn("Permiso de audio pendiente"));
                mostrarNotificacion();
            }

            // Solo actualizamos el DOM si el contenido es realmente distinto
            if (carril.innerHTML !== html) {
                carril.innerHTML = html;
                totalPedidosActual = nuevosCardsCount;
                
                // IMPORTANTE: Re-aplicar anchos tras insertar nuevo HTML
                aplicarAnchoTarjetas();
                moverCarril(); 
            }
        });
}

function mostrarNotificacion() {
    const noti = document.getElementById('notificacion');
    noti.classList.add('visible');
    setTimeout(() => noti.classList.remove('visible'), 5000);
}

/* ==========================================================================
   INICIALIZACIÓN
   ========================================================================== */

// Ejecutar carga de datos
cargarPedidos();

// Intervalos
setInterval(cargarPedidos, 5000);       // Cada 5 segundos busca pedidos nuevos
setInterval(actualizarRelojes, 1000);     // Cada segundo actualiza los cronómetros
iniciarAuto();                           // Inicia carrusel automático

// Asegurar ancho inicial
window.onload = aplicarAnchoTarjetas;
</script>
    <?php 
        incluirTemplate('footer', $incio);
    ?>
