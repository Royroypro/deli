
<?php

    
	/* @include 'plant/control/veri.php'; */
	include "app/config.php";
    include "layout/sesion.php";
    include "layout/parte1.php";
  

	/* include_once "reportes/index.php"; */
    ?>

<!--Add ends-->
<!--Specials-->
<div class="spl">
        <h2>Nuestros Especiales</h2>
</div>
<div class="table">
    <?php
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen, c.nombre as categoria, r.nombre as restaurante FROM productos p LEFT JOIN productos_categorias pc ON p.id_producto = pc.id_producto LEFT JOIN categorias c ON pc.id_categoria = c.id_categoria LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll();
    foreach ($productos as $producto) { ?>
    <div style="display: flex; flex-direction: column; align-items: center;">
        <a href="./detalle.php?id_producto=<?php echo $producto['id_producto']; ?>"><img src="<?php echo $URL.'/admin/imgs/productos/productos/'.$producto['imagen']; ?>" height="150" width="150" style="object-fit: cover;"><h4><?php echo $producto['nombre']; ?></h4></a>
        <p><?php echo $producto['descripcion']; ?></p>
        <p>Precio: <?php echo $producto['precio']; ?></p>
        <p>Categoría: <?php echo $producto['categoria']; ?></p>
        <p>Restaurante: <?php echo $producto['restaurante']; ?></p>
        <div class="btns">
            <button class="btn-info" style="font-size: 12px; padding: 5px 10px;">Mas</button>
            <button class="btn-success" style="font-size: 12px; padding: 5px 10px;"><i class="fas fa-shopping-cart"></i> carrito</button>
        </div>
    </div>
    <?php } ?>
</div>

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


