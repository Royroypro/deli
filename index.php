<?php


/* @include 'plant/control/veri.php'; */
include "app/config.php";

session_start();
if (isset($_SESSION['id_usuario'])) {
    $id_usuario_sesion = $_SESSION['id_usuario'];
} else {
    $id_usuario_sesion = null;
}


include "layout/parte1.php";


/* include_once "reportes/index.php"; */
?>

<!--Add ends-->
<!--Specials-->


<link rel="stylesheet" href="styles.css">

<!--Oferta especial-->
<div class="add">
    <div class="add-container">
        <?php
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen, c.nombre as categoria, r.nombre as restaurante FROM productos p LEFT JOIN productos_categorias pc ON p.id_producto = pc.id_producto LEFT JOIN categorias c ON pc.id_categoria = c.id_categoria LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante ORDER BY RAND() LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $producto = $stmt->fetch();
        ?>
        <div class="img-elipse">
            <img src="<?php echo $URL; ?>/admin/imgs/productos/productos/<?php echo $producto['imagen']; ?>" class="img-elipse-img">
        </div>
        <div class="textimg">
            <h2>30% de descuento en tu<br> primer pedido</h2>
            <div class="btns"> <button class="btn-info" style="font-size: 10px; padding: 10px 10px;">Mas</button> <button class="btn-success" style="font-size: 10px; padding: 10px 10px;" <?php if (isset($_SESSION['id_usuario'])) { ?> onclick="agregarAlCarrito(                         '<?php echo $producto['id_producto']; ?>',                         '<?php echo addslashes($producto['nombre']); ?>',                         '<?php echo $producto['precio']; ?>',                         '<?php echo addslashes($producto['restaurante']); ?>')" <?php } else { ?> onclick="mostrarAlerta()" <?php } ?>> <i class="fas fa-shopping-cart"></i> carrito </button> </div>
        </div>
    </div>

    <br>
    <div class="spl">
        <h2>Nuestros Especiales</h2>
    </div>

    <div class="table">
        <?php
        $sql = "SELECT r.id_restaurante, r.nombre as restaurante FROM restaurantes r";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $restaurantes = $stmt->fetchAll();
        foreach ($restaurantes as $restaurante) { ?>

            <div class="restaurante-container">
                <h2 class="restaurante-name"><?php echo $restaurante['restaurante']; ?></h2>

                <?php
                $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen FROM productos p WHERE p.id_restaurante = :id_restaurante";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id_restaurante' => $restaurante['id_restaurante']]);
                $productos = $stmt->fetchAll();
                ?>

                <div class="productos-grid">
                    <?php foreach ($productos as $producto) { ?>

                        <div class="producto-card">
                            <a href="./detalle.php?id_producto=<?php echo $producto['id_producto']; ?>" class="producto-link">
                                <img src="<?php echo $URL . '/admin/imgs/productos/productos/' . $producto['imagen']; ?>"
                                    height="150" width="150" class="producto-img" alt="<?php echo $producto['nombre']; ?>">
                                <h4 class="producto-name"><?php echo $producto['nombre']; ?></h4>
                            </a>
                            <p class="producto-desc"><?php echo $producto['descripcion']; ?></p>
                            <p class="producto-price">Precio: <?php echo $producto['precio']; ?></p>
                            <div class="btns">
                                <button class="btn-info">Más</button>
                                <button class="btn-success"
                                    <?php if (isset($_SESSION['id_usuario'])) { ?>
                                    onclick="agregarAlCarrito(
                                '<?php echo $producto['id_producto']; ?>',
                                '<?php echo addslashes($producto['nombre']); ?>',
                                '<?php echo $producto['precio']; ?>',
                                '<?php echo addslashes($restaurante['restaurante']); ?>')"
                                    <?php } else { ?>
                                    onclick="mostrarAlerta()"
                                    <?php } ?>>
                                    <i class="fas fa-shopping-cart"></i> Carrito
                                </button>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>

        <?php } ?>
    </div>

    <style>
        .table {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .restaurante-container {
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
        }

        .restaurante-name {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* 4 productos por fila */
            gap: 20px;
            padding: 0 10px;
        }

        .producto-card {
            width: 100%;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .producto-card:hover {
            transform: translateY(-10px);
        }

        .producto-img {
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .producto-name {
            font-size: 18px;
            margin: 10px 0;
        }

        .producto-desc {
            font-size: 12px;
            color: #777;
            margin-bottom: 10px;
        }

        .producto-price {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .btns {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-info,
        .btn-success {
            padding: 8px 15px;
            font-size: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-info:hover,
        .btn-success:hover {
            opacity: 0.8;
        }

        .producto-link {
            text-decoration: none;
            color: inherit;
        }

        /* Responsividad */
        @media (max-width: 1200px) {
            .productos-grid {
                grid-template-columns: repeat(3, 1fr);
                /* 3 productos por fila en pantallas medianas */
            }
        }

        @media (max-width: 800px) {
            .productos-grid {
                grid-template-columns: repeat(2, 1fr);
                /* 2 productos por fila en pantallas pequeñas */
            }
        }

        @media (max-width: 500px) {
            .productos-grid {
                grid-template-columns: 1fr;
                /* 1 producto por fila en pantallas muy pequeñas */
            }
        }
    </style>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Definir la función mostrarAlerta
            const mostrarAlerta = () => {
                const alerta = document.createElement('div');
                alerta.style.position = 'fixed';
                alerta.style.top = '50%';
                alerta.style.left = '50%';
                alerta.style.transform = 'translate(-50%, -50%)';
                alerta.style.padding = '30px';
                alerta.style.backgroundColor = '#fff5e6';
                alerta.style.color = '#d35400';
                alerta.style.border = '2px solid #f39c12';
                alerta.style.borderRadius = '12px';
                alerta.style.boxShadow = '0px 8px 12px rgba(0, 0, 0, 0.2)';
                alerta.style.textAlign = 'center';
                alerta.style.fontSize = '18px';
                alerta.style.fontWeight = 'bold';
                alerta.innerHTML = `
            <p>Debe <strong>iniciar sesión</strong> para agregar al carrito.</p>
            <button style="
                padding: 12px 18px; 
                background-color: #f39c12; 
                color: white; 
                border: none; 
                border-radius: 6px; 
                font-size: 16px; 
                cursor: pointer;" 
                onclick="window.location.href='<?php echo $URL; ?>/login/cliente.php'">
                Ir a Iniciar Sesión
            </button>
        `;
                document.body.appendChild(alerta);

                // Quitar la alerta después de 5 segundos
                setTimeout(() => {
                    alerta.remove();
                }, 5000);
            };

            // Verificar si el usuario tiene la sesión iniciada en el frontend
            const session = <?php echo isset($_SESSION['id_usuario']) ? 'true' : 'false'; ?>;

            // Agregar el evento solo si no hay sesión iniciada
            if (!session) {
                const buttons = document.querySelectorAll('.btn-success');
                buttons.forEach(button => {
                    button.addEventListener('click', event => {
                        event.preventDefault();
                        mostrarAlerta();
                    });
                });
            }
        });
    </script>

    <style>
        /* Estilo base del modal */
        .modal {
            display: none;
            /* Oculto por defecto */
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
            font-size: 12px;
            /* Aumenta el tamaño de la letra */
        }

        /* Botones dentro del carrito */
        .cart-item-buttons {
            display: flex;
            justify-content: flex-end;
            /* Alinea los botones a la derecha */
            gap: 5px;
            /* Espacio reducido entre botones */
            margin-top: 3px;
        }

        .cart-item-buttons button {
            font-size: 16px;
            /* Reduce el tamaño de la letra */
            padding: 3px 8px;
            /* Ajusta el espacio interno */
            border: none;
            border-radius: 3px;
            background-color: #FF6F61;
            /* Color para "Quitar" */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .cart-item-buttons button:hover {
            background-color: #E55D50;
            /* Color al pasar el mouse */
        }

        .cart-item-buttons .reduce-button {
            background-color: #FFC107;
            /* Color para "Reducir" */
        }

        .cart-item-buttons .reduce-button:hover {
            background-color: #D4A007;
            /* Color hover */
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
            background-color: #28a745;
            /* Verde atractivo */
            font-size: 16px;
            /* Tamaño de letra más grande */
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            display: block;
            margin: 20px auto 0 auto;
            /* Centrar botón */
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
                    body: JSON.stringify({
                        id_producto,
                        nombre,
                        precio,
                        cantidad,
                        restaurante
                    }),
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
                carrito[id_producto] = {
                    nombre,
                    precio: parseFloat(precio),
                    cantidad,
                    restaurante
                };
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
            carritoDataInput.value = JSON.stringify({
                total,
                productos
            });

            // Agrega los productos por restaurante
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






    

    <!--Restaurantes-->
    <div class="res">
        <h2>Restaurantes</h2>
        <hr class="line">
    </div>
    <section class="barb" id="biryani">
        <h1 class="barbeque">Restaurantes</h1>
        <hr class="line">
        <div class="box-container">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM restaurantes");
            $stmt->execute();
            $restaurantes = $stmt->fetchAll();
            foreach ($restaurantes as $restaurante) {
                echo '<div class="box">
                    <a href="./detalle.php?id_restaurante=' . $restaurante["id_restaurante"] . '"><img src="' . $URL . '/food/Images/Restaurants/' . $restaurante["imagen_logo"] . '"></a>
                    <h3>' . $restaurante["nombre"] . '</h3>
                    <div class="stars">';
                for ($i = 0; $i < 5; $i++) {
                    if ($i < $restaurante["horarios_flexibles_restaurantes"]) {
                        echo '<i class="fas fa-star"></i>';
                    } else {
                        echo '<i class="fas fa-star-half-alt"></i>';
                    }
                }
                echo '<br>
                    <p>' . $restaurante["direccion"] . '<br>
                    Horario: ' . $restaurante["horario"] . '<br>
                    Contacto: ' . $restaurante["contacto"] . '</p>

                </div>
            </div>';
            }
            ?>


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