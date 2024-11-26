

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="<?php echo $URL;?>/food/Images/Restaurants/download.png" type="image/icon type">
<link rel="stylesheet" href="<?php echo $URL;?>/food/home.css">
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body onload="myload()">
<div class="loader-container" id = "loader">
    <img src="<?php echo $URL;?>/food/Images/loader/loader.gif">
    
</div>
    <!--Encabezado secci n inicio-->
    <header>
        <a href="#" class="logo" style="display: flex; align-items: center;">
            <img src="<?php echo $URL;?>/img/logo.png" width="40px" height="40px" style="margin-right: 5px;">
            <i class="fa fa-utensils" style="margin-right: 5px;"></i>
            Chaskifood
        </a>
        <nav class="navbar">
            <a class="active" href="./home.html">Inicio</a>
            <a href="./dishes.html">platillos</a>
            <a href="#" onclick="openAbout()">acerca de</a>
            <a href="./contact.html" >Contacto</a>
            <a class="feed" id="feedback" style="display: none;"></a>

         
            <?php if(isset($_SESSION['id_usuario'])): ?>
            <a href="/layout/index.php" >Mi cuenta</a>
            <?php else: ?>
            <a href="./login/cliente.php" >Iniciar sesión</a>
            <?php endif; ?>

          
            
            <a href="./login/index.php" >Trabaja con nosotros</a>
            <a href="#" class="cart" onclick="abrirModal()">


            
    <img src="<?php echo $URL;?>/img/carrito.png" width="25px" height="25px">
    <span class="cart-number">0</span>




</a>

        </nav>
        <div class="icons">
            <i class="fas fa-bars" id="menu-bars"></i>
            <i class="fas fa-search" id="search-icon"></i>
            <a href="#" class="fas fa-heart"></a>
            <a href="#" class="fas fa-shopping-cart"></a>
            <i class="fa fa-user" aria-hidden="true"></i>
            <a href="login.html" class="fas fa-sign-in-alt"></a>
            
        </div>
    </header>

    <div class="back">
    <div class="container1" id="co1">
        <div class="post">
            <div class="text">¡Gracias por calificarnos!</div>
            <div class="edit">Editar</div>
            <i class="fas fa-times" id="close"></i>
    
        </div>
        <div class="star-widget">
        <input type="radio" name="rate" id="rate-5">
        <label for="rate-5" class="fas fa-star"></label>
        <input type="radio" name="rate" id="rate-4">
        <label for="rate-4" class="fas fa-star"></label>
        <input type="radio" name="rate" id="rate-3">
        <label for="rate-3" class="fas fa-star"></label>
        <input type="radio" name="rate" id="rate-2">
        <label for="rate-2" class="fas fa-star"></label>
        <input type="radio" name="rate" id="rate-1">
        <label for="rate-1" class="fas fa-star"></label>
        <form action="#">
            <i class="fas fa-times" id="close"></i>
            <h4></h4>
            <div class="textarea">
                <textarea cols="30" placeholder="Describe your experience"></textarea>
    
            </div>
            
            <div class="btn">
                <button type="submit">Publicar</button>
            </div>
        </form>
        </div>    
    </div>
    </div>
    <!-- Header section ends-->

    <!--search form-->
    <form action="" id="search-form">
        <input type="search" placeholder="search here..." name="" id="search-box">
        <label for="search-box" class="fas fa-search"></label>
        <i class="fas fa-times" id="close1"></i>
    </form>
    <!--Search Form ends-->

    <!--Home section start-->
    <section class="home" id="home-section">
<!--Oferta especial-->
<div class="add">
 <div class="add-container">
 <img src="<?php echo $URL;?>/food/Images/banner/burger banner.jpg">
 <div class="textimg"><h2>30% de descuento en tu<br> primer pedido</h2>
 <a href="./ksbakers.html"><button class="ordr">Pedir ahora</button></a></div>
 
 </div>
</div>





<!--Home  ends-->

    

    <!--Java Script-->
    <script>
        let menu = document.querySelector('#menu-bars');
        let navbar = document.querySelector('.navbar');
        
        menu.onclick = () => {
            menu.classList.toggle('fa-times');
            navbar.classList.toggle('active');
        }
        window.onscroll=() => {
            menu.classList.remove('fa-times');
            navbar.classList.remove('active');
        }
        document.querySelector('#search-icon').onclick=() => {
            document.querySelector('#search-form').classList.toggle('active');
        }
        document.querySelector('#close1').onclick=() => {
            document.querySelector('#search-form').classList.toggle('active');
        }
        document.querySelector("#feedback").onclick=() =>{
        document.querySelector("#co1").classList.toggle("active");
    }
        document.querySelector("#close").onclick=() =>{
        document.querySelector("#co1").classList.toggle("active");
    }
    var preloader = document.getElementById("loading");
    function myload() {
        preloader.style.display="none";
    }


        const btn = document.querySelector("button");
        const post = document.querySelector(".post");
        const widget = document.querySelector(".star-widget");
        const editBtn = document.querySelector(".edit");
    
        btn.onclick = () =>{
        widget.style.display = "none";
        post.style.display = "block";
        editBtn.onclick = () =>{
            widget.style.display = "block";
            post.style.display = "none";
        }
        return false;
    }
    function openAbout(){
        document.getElementById("about").style.width = "100%";

    }
    function closeNav(){
        document.getElementById("about").style.width = "0%";
    }
    var preloader = document.getElementById("loader");
    
    function myloader(){
        preloader.style.display = "none";
    }
    function mygrocery(){
        confirm("Order Now on Whatsapp click ok to continue");
    }
    </script>
    
    <!--JavaScript ends -->

</body>
</html>