
<?php

    
	/* @include 'plant/control/veri.php'; */
	include "app/config.php";

    include "layout/parte1.php";
  

	/* include_once "reportes/index.php"; */
    ?>

<!--Add ends-->
<!--Specials-->
<div class="spl">
        <h2>Nuestros Especiales</h2>
</div>

<link rel="stylesheet" href="styles.css">




<div class="table">
    <?php
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen, c.nombre as categoria, r.nombre as restaurante FROM productos p LEFT JOIN productos_categorias pc ON p.id_producto = pc.id_producto LEFT JOIN categorias c ON pc.id_categoria = c.id_categoria LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll();
    foreach ($productos as $producto) { ?>
    <div style="display: flex; flex-direction: column; align-items: center;">
        <a href="./detalle.php?id_producto=<?php echo $producto['id_producto']; ?>"><img src="<?php echo $URL.'/admin/imgs/productos/productos/'.$producto['imagen']; ?>" height="150" width="150" style="object-fit: cover;"><h4 style="font-size: 20px;"><?php echo $producto['nombre']; ?></h4></a>
        <p style="font-size: 12px;"><?php echo $producto['descripcion']; ?></p>
        <p style="font-size: 12px;">Precio: <?php echo $producto['precio']; ?></p>
        <p style="font-size: 12px;">Categoría: <?php echo $producto['categoria']; ?></p>
        <p style="font-size: 12px;">Restaurante: <?php echo $producto['restaurante']; ?></p>
        <div class="btns">
            <button class="btn-info" style="font-size: 10px; padding: 10px 10px;">Mas</button>
            
    <button class="btn-success" style="font-size: 10px; padding: 10px 10px;"
            onclick="agregarAlCarrito(
            '<?php echo $producto['id_producto']; ?>',
            '<?php echo addslashes($producto['nombre']); ?>',
            '<?php echo $producto['precio']; ?>',
            '<?php echo addslashes($producto['restaurante']); ?>')">
                <i class="fas fa-shopping-cart"></i> carrito
    </button>
            </div>

       
    </div>
    <?php } ?>
</div>



<style>

/* Estilo base del modal */
.modal {
    display: none; /* Oculto por defecto */
    position: fixed;
    right: 0;
    top: 0;
    width: 350px;
    height: 100%;
    background-color: #f9f9f9;
    box-shadow: -2px 0px 10px rgba(0, 0, 0, 0.5);
    overflow-y: auto;
    transition: transform 0.3s ease-in-out;
    transform: translateX(100%);
    z-index: 1000;
    border-radius: 5px;
}

/* Cuando se muestra el modal */
.modal.show {
    display: block;
    transform: translateX(0);
}

/* Contenido del modal */
.modal-content {
    padding: 10px;
    overflow-y: auto;
    font-size: 12px; /* Aumenta el tamaño de la letra */
}

/* Botones dentro del carrito */
.cart-item-buttons {
    display: flex;
    justify-content: flex-end; /* Alinea los botones a la derecha */
    gap: 5px; /* Espacio reducido entre botones */
    margin-top: 3px;
}

.cart-item-buttons button {
    font-size: 16px; /* Reduce el tamaño de la letra */
    padding: 3px 8px; /* Ajusta el espacio interno */
    border: none;
    border-radius: 3px;
    background-color: #FF6F61; /* Color para "Quitar" */
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.cart-item-buttons button:hover {
    background-color: #E55D50; /* Color al pasar el mouse */
}

.cart-item-buttons .reduce-button {
    background-color: #FFC107; /* Color para "Reducir" */
}

.cart-item-buttons .reduce-button:hover {
    background-color: #D4A007; /* Color hover */
}

/* Boton de cerrar la modal */
.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 28px;
    color: #333;
    cursor: pointer;
    background-color: transparent;
    border: none;
    padding: 0;
    outline: none;
}


#checkoutForm .btn {
    background-color: #28a745; /* Verde atractivo */
    font-size: 16px; /* Tamaño de letra más grande */
    font-weight: bold;
    text-transform: uppercase;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
    display: block;
    margin: 20px auto 0 auto; /* Centrar botón */
    text-align: center;
    transition: background-color 0.3s ease;
}

#checkoutForm .btn:hover {
    background-color: #218838;
}


</style>


<div id="cartModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h3>Carrito de Compras</h3>
        <div id="restaurantSections"></div>
        <ul id="cartItems"></ul>
        <p id="totalPrice">Total: S/ 0.00</p>
        <!-- Botón para ir a pagar -->
        <form id="checkoutForm" method="POST" action="./app/controllers/pedidos/proceso_compra.php">
            <input type="hidden" name="carrito" id="carritoData">
            <button type="submit" class="btn">Ir a Pagar</button>
        </form>
    </div>
</div>



<script>
function agregarAlCarrito(id_producto, nombre, precio, restaurante) {
    const cantidad = 1; // Cantidad por defecto

    fetch('./agregar_carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_producto, nombre, precio, cantidad, restaurante }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                actualizarCarrito();
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al agregar al carrito.');
        });
}

function actualizarCarrito() {
    fetch('./actualizar_carrito.php')
        .then(response => response.text())
        .then(data => {
            document.querySelector('.cart-number').innerText = data;
        });
}


let carrito = {}; // Objeto para almacenar productos

// Función para abrir el modal
function abrirModal() {
    document.getElementById('cartModal').classList.add('show');
    actualizarVistaCarrito();
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('cartModal').classList.remove('show');
}

// Función para agregar un producto al carrito
function agregarAlCarrito(id_producto, nombre, precio, restaurante) {
    const cantidad = 1;

    if (!carrito[id_producto]) {
        carrito[id_producto] = { nombre, precio: parseFloat(precio), cantidad, restaurante };
    } else {
        carrito[id_producto].cantidad += cantidad;
    }

    actualizarVistaCarrito();
    actualizarContadorCarrito();
}

// Función para actualizar el contador del carrito
function actualizarContadorCarrito() {
    const totalProductos = Object.values(carrito).reduce((acc, item) => acc + item.cantidad, 0);
    document.querySelector('.cart-number').innerText = totalProductos;
}

function actualizarVistaCarrito() {
    const cartItems = document.getElementById('cartItems');
    const totalPrice = document.getElementById('totalPrice');
    const carritoDataInput = document.getElementById('carritoData');
    cartItems.innerHTML = '';

    let total = 0;
    let productos = [];

    const restaurantes = {};
    for (const [id, item] of Object.entries(carrito)) {
        total += item.precio * item.cantidad;

        productos.push({
            id_producto: id,
            nombre: item.nombre,
            precio: item.precio,
            cantidad: item.cantidad,
            restaurante: item.restaurante
        });

        if (!restaurantes[item.restaurante]) {
            restaurantes[item.restaurante] = [];
        }
        restaurantes[item.restaurante].push(item);
    }

    totalPrice.innerText = `Total: S/ ${total.toFixed(2)}`;

    // Actualiza el formulario con los datos del carrito
    carritoDataInput.value = JSON.stringify({ total, productos });

    // Agrega los productos por restautante
    for (const [restaurante, items] of Object.entries(restaurantes)) {
        const li = document.createElement('li');
        li.style.marginBottom = '10px';
        li.innerHTML = `
            <div>
                <span style="font-weight: bold;">${restaurante}</span><br>
                <ul>
                    ${items.map(item => `<li>${item.nombre} x ${item.cantidad} - S/ ${item.precio.toFixed(2)}</li>`).join('')}
                </ul>
            </div>
            <div class="cart-item-buttons">
                <button class="btn-success" onclick="comprarAhora('${restaurante}')">Comprar Ahora</button>
            </div>
        `;
        cartItems.appendChild(li);
    }
}

// Nueva función para reducir la cantidad de un producto
function reducirCantidad(id_producto) {
    if (carrito[id_producto].cantidad > 1) {
        carrito[id_producto].cantidad -= 1;
    } else {
        delete carrito[id_producto]; // Elimina el producto si la cantidad es 1
    }
    actualizarVistaCarrito();
    actualizarContadorCarrito();
}

// Función para quitar un producto del carrito
function quitarDelCarrito(id_producto) {
    delete carrito[id_producto];
    actualizarVistaCarrito();
    actualizarContadorCarrito();
}

// Función para ir a comprar
function comprarAhora(restaurante) {
    const productos = Object.values(carrito).filter(item => item.restaurante === restaurante);
    if (productos.length > 0) {
        const id_productos = productos.map(item => item.id_producto);
        window.location.href = `./detalle.php?id_productos=${id_productos.join(',')}`;
    } else {
        alert('No hay productos en el carrito para este restaurante.');
    }
}

</script>






<!--Top Restaurants-->
<div class="top-re">
    <h2>Productos</h2>
</div>
<div class="table1">
    <a href="./Barbeque.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/barbeque.jpg" height="150"><h4>Barbeque</h4><p>tandoori, biryani, Starters</p></a>
    <a href="./mehfil.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/mehfil.jpg" height="150"><h4>Mehfil</h4><p>tandoori, biryani</p></a>
    <a href="./paradise.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/paradise.jpg" height="150"><h4>Paradise</h4><p>tandoori, biryani, Starters, deserts</p></a>
    <a href="./ramkibandi.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/ramkibandi.jpg" height="150"><h4>Ram Ki Bandi</h4><p>dosa</p></a>
    <a href="./dominos.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/dominos.jpg" height="150"><h4>Domino's</h4><p>Pizza, Burger</p></a>
    <a href="./vantilu.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/vantilu.jpeg" height="150"><h4>Vantillu</h4><p>tandoori, biryani, Starters</p></a>
    <a href="./platform65.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/platform65.jpg" height="150"><h4>Platform65</h4><p>tandoori, biryani, Starters</p></a>
</div>
<div class="container-grocery">
  <img src="<?php echo $URL;?>/food/Images/banner/grocery-delivery.png" alt="Norway" style="width:100%;">
  <div class="text-block">
    <h2>Chotu!</h2>
    <h4>Lo que necesites, entregado</h4>
    <p>Obtenga su tienda de comestibles, carne, verduras a su puerta<br>
    Qu darse en casa y disfrute de nuestros servicios.</p>
    <div class="ordergrocery">
  <a href="https://api.whatsapp.com/send?phone=+917717406841&text=Quiero ordenar tienda de comestibles/carne/verduras" class="orderg" onclick="mygrocery()"><i class="fab fa-whatsapp"></i> Ordenar ahora</a>
  </div>
  </div>
  

</div>

<!--Restaurantes-->
<div class="res">
    <h2>14 Restaurantes</h2>
    <hr class="line">
</div>
<section class="barb" id="biryani">
    <h1 class="barbeque">Restaurantes</h1>
    <hr class="line"> 
    <div class="box-container">
    <div class="box">
        <a href="./barbeque.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/barbeque.jpg"></a>
        <h3>Barbeque</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./mehfil.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/mehfil.jpg"></a>
        <h3>Mehfil</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./dominos.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/dominos.jpg"></a>
        <h3>Domino's</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Pizza, hamburguesa, pan</p>

        </div>
        </div>
        <div class="box">
        <a href="./paradise.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/paradise.jpg"></a>
        <h3>Paraiso</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./ramkibandi.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/ramkibandi.jpg"></a>
        <h3>Ram Ki Bandi</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Idli, dosa, desayuno</p>

        </div>
        </div>
        <div class="box">
        <a href="./vantilu.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/vantilu.jpeg"></a>
        <h3>Vantillu</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./platform65.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/platform65.jpg"></a>
        <h3>Plataforma 65</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./hoteladaab.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/aadab.png"></a>
        <h3>Hotel Adaab</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./fishland.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/fishland.jpg"></a>
        <h3>Fish Land</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./hitech.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/hitech.jpg"></a>
        <h3>hitech</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./hotnspicy.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/hotnspicy.jpg"></a>
        <h3>Hot N Spicy</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./mughal.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/mughal.jpg"></a>
        <h3>Mughal Restaurants</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        <div class="box">
        <a href="./ksbakers.html"><img src="<?php echo $URL;?>/food/Images/Restaurants/ksbakers.png"></a>
        <h3>KS Bakers</h3>
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <br>
            <p>Entradas, biryani, tandoori</p>

        </div>
        </div>
        

        </div>
        </section>
    

</section>
<div id="about" class="about">
    <a href="#" class="closebtn" onclick="closeNav()">&times;</a>
    <div class="about-overlay">
        <h1>Sobre nosotros</h1>
        <p>Lanzado en 2021, nuestra plataforma tecnol gica conecta a clientes, socios de restaurantes y 
        socios de entrega, satisfaciendo sus m ltiples necesidades. <br>
        Los clientes utilizan nuestra plataforma para buscar y descubrir restaurantes, leer y escribir rese as 
        generadas por clientes y ver y subir fotos, <br> pedir comida a domicilio, reservar una mesa y realizar 
        pagos mientras comen en restaurantes. Por otro lado, <br> brindamos a nuestros socios de restaurantes 
        herramientas de marketing espec ficas para la industria que les permiten atraer y adquirir clientes <br> 
        para crecer su negocio mientras tambi n ofrecemos un servicio de entrega confiable y eficiente. <br>
        Tambi n operamos una soluci n de aprovisionamiento integral, <br> Hyperpure, que suministra ingredientes 
        y productos de alta calidad para cocinas a nuestros socios de restaurantes. <br>Adem s, brindamos a nuestros 
        socios de entrega oportunidades de ganancia transparentes y flexibles. </p>
    </div>
</div>

<?php

    

    include "layout/parte2.php";
	/* include_once "reportes/index.php"; */
    ?>


