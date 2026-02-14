let carritoVariantes=[]

document.addEventListener('DOMContentLoaded', () => {

    eventLinsteners();

    if (document.getElementById('lista-productos')) {
        cargarProductos();
        filtroCategoria();
    }
    if (document.getElementById('input-variantes')) {
        formularioVariantes();
    }
    if (document.getElementById('btn-agregar')) {
        formularioVariantes();
        formularioPreparar();
        calcularCantidad();
    }

    if (document.getElementById('metodoPago')) {
        metodoPago();
    }

    if(document.getElementById('btn-pagar')){
        imprimirTicket();
    }
    if(document.getElementById('password')){
        confirmPassword();
    }
});


function eventLinsteners() {
    const mobileMenu = document.querySelector('.mobile-menu');
    if (mobileMenu) {
        mobileMenu.addEventListener('click', navegacionResponsive);
    }
}

function navegacionResponsive() {
    const navegacion=document.querySelector('.nav__pedidos');
    navegacion.classList.toggle('mostrar');
}


function formularioVariantes(){
    document.querySelectorAll(".chk-opcion").forEach(chk => {
        chk.addEventListener("change", () => {
            
            const input = document.getElementById(chk.dataset.target);

            if (!input) return;

            if (chk.checked) {
                input.disabled = false;
                input.focus();
            } else {
                input.disabled = true;
                input.value = "";
            }
        });
    });
}

function calcularCantidad(){
    const inputCantidad=document.getElementById('input-cantidad')
    let cantidad=Number(inputCantidad.dataset.cantidad);
    const btnMinus=document.getElementById('cantidad-minus');
    const btnAdd=document.getElementById('cantidad-plus');
    btnMinus.addEventListener("click",()=>{
        cantidad--;
        if(cantidad<=1){
            cantidad=1;
        }
            inputCantidad.value=cantidad;
            inputCantidad.dataset.cantidad = cantidad;
    })
    btnAdd.addEventListener("click",()=>{
        cantidad++;
        inputCantidad.value=cantidad;
        inputCantidad.dataset.cantidad = cantidad;
    })
    return cantidad;   
}

function formularioPreparar() {
    const btnAgregar = document.getElementById('btn-agregar');
    const precioBase = parseFloat(btnAgregar.dataset.base);
    
    const totalDisplay = document.getElementById('total-display');
    
    const inputCantidad=document.getElementById('input-cantidad')
    let cantidad=Number(inputCantidad.dataset.cantidad);
    const btnMinus=document.getElementById('cantidad-minus');
    const btnAdd=document.getElementById('cantidad-plus');
    btnMinus.addEventListener("click",()=>{
        cantidad--;
        if(cantidad<=1){
            cantidad=1;
        }
            inputCantidad.value=cantidad;
            inputCantidad.dataset.cantidad = cantidad;
            calcularTotal(precioBase,totalDisplay); // Actualizar visualmente el precio
    })
    btnAdd.addEventListener("click",()=>{
        cantidad++;
        inputCantidad.value=cantidad;
        inputCantidad.dataset.cantidad = cantidad;
            calcularTotal(precioBase,totalDisplay); // Actualizar visualmente el precio
    })


    document.querySelectorAll(".checkbox").forEach(chk => {
        chk.addEventListener("change", () => {
            const idVariante = chk.dataset.id;
            const precioExtra = parseFloat(chk.dataset.precio); 
            if (chk.checked) {
                if (!carritoVariantes.some(v => v.id === idVariante)) {
                    carritoVariantes.push({ id: idVariante, precio: precioExtra });
                }
            } else {
                carritoVariantes = carritoVariantes.filter(p => p.id !== idVariante);
            }

            calcularTotal(precioBase,totalDisplay); 
            actualizarServidor();
        });
    });
}

function calcularTotal(precioBase,totalDisplay) {
    const cantidad=calcularCantidad();
    console.log(cantidad);
    // Sumamos el precio base + todos los extras en el arreglo
    const totalExtras = carritoVariantes.reduce((acc, item) => acc + item.precio, 0);
    const totalFinal = (precioBase + totalExtras)*cantidad;
    
    // Actualizamos el texto del botón
    totalDisplay.textContent = totalFinal.toFixed(2);
}

function actualizarServidor() {
    fetch(`${BASE_URL}/api/carritoVariantes.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(carritoVariantes)
    })
    .then(res => res.json())
    .then(data => {
        console.log("Sincronizado:", data);
    })
    .catch(err => console.error("Error de red:", err));
}

function filtroCategoria(){
    
    const botones=document.querySelectorAll('#categorias a');
    botones.forEach(btn=>{
        btn.addEventListener('click',(e)=>{
            e.preventDefault();
            const categoria=btn.dataset.categoria??'';
            cargarProductos(categoria);
            //marcar activo
            botones.forEach(b=>b.classList.remove('activo'));
            btn.classList.add('activo');

        })
    });

}

function cargarProductos(categoria=''){
    console.log(`${BASE_URL}/api/productos.php?categoria=${categoria}`)
    const contenedor=document.getElementById('lista-productos');
    fetch(`${BASE_URL}/api/productos.php?categoria=${categoria}`)
        .then(res=>res.text())
        .then(html=>{
            contenedor.innerHTML=html;
        })
        .catch(err=>console.log(err));
}

function metodoPago() {
    const metodoPago = document.getElementById('metodoPago');
    if (!metodoPago) return;

    const efectivoBox = document.getElementById('efectivoBox');
    const transferenciaBox = document.getElementById('transferenciaBox');
    const recibidoInput = document.getElementById('recibido');
    const cambioSpan = document.getElementById('cambio');
    const totalSpan = document.getElementById('total');
    const btnPagar = document.getElementById('btn-pagar');

    if (!efectivoBox || !transferenciaBox || !recibidoInput || !cambioSpan || !totalSpan || !btnPagar) return;

    const total = parseFloat(totalSpan.textContent.replace(/,/g, ''));

    btnPagar.disabled = true;

    function validarPago() {
        const metodo = metodoPago.value;
        const recibido = parseFloat(recibidoInput.value) || 0;

        if (metodo === '') {
            btnPagar.classList.add('btnOculto');
            btnPagar.disabled = true;
            return;
        }

        if (metodo === 'transferencia') {
            btnPagar.classList.remove('btnOculto');
            btnPagar.disabled = false;
            cambioSpan.textContent = '0.00';
            return;
        }

        if (metodo === 'efectivo') {
            const cambio = recibido - total;

            if (cambio >= 0) {
                btnPagar.classList.remove('btnOculto');
                cambioSpan.textContent = cambio.toFixed(2);
                btnPagar.disabled = false;
            } else {
                btnPagar.classList.add('btnOculto');
                cambioSpan.textContent = 'Fondos insuficientes';
                btnPagar.disabled = true;
            }
        }
    }

    metodoPago.addEventListener('change', () => {
        efectivoBox.classList.add('oculto');
        transferenciaBox.classList.add('oculto');
        recibidoInput.value = '';
        cambioSpan.textContent = '0.00';

        recibidoInput.required = false;

        if (metodoPago.value === 'efectivo') {
            efectivoBox.classList.remove('oculto');
            recibidoInput.required = true;
        }

        if (metodoPago.value === 'transferencia') {
            transferenciaBox.classList.remove('oculto');
        }

        validarPago();
    });

    recibidoInput.addEventListener('input', validarPago);
}

function imprimirTicket(){
    document.getElementById('btn-pagar').addEventListener('click', () => {
    window.print();
});

}

function confirmPassword(){
    const password = document.getElementById('password');
    const confirm = document.getElementById('confirm_password');
    const error = document.getElementById('passwordError');

    function validarPassword() {
    if (confirm.value === '') {
        error.style.display = 'none';
        return;
    }

    if (password.value !== confirm.value) {
        error.style.display = 'block';
        confirm.setCustomValidity('Las contraseñas no coinciden');
    } else {
        error.style.display = 'none';
        confirm.setCustomValidity('');
    }
    }

    password.addEventListener('input', validarPassword);
    confirm.addEventListener('input', validarPassword);
}


