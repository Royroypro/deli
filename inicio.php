<?php

	@include 'plant/control/veri.php';
   
    ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Presentación - Señales Internacionales</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="fullscreen-background">
        <div class="intro">
            <img src="img/señales_logo.png" alt="Logo" class="logo">
            <div class="text-background">
            <h1>Bienvenido a Señales Internacionales</h1>
            <p>Conectando personas a través de nuestros servicios de internet y televisión de alta calidad.</p>
            </div>
            <!-- Botón de ingreso -->
            <button id="ingresar-btn">Ingresar</button>
            <script>
                document.getElementById("ingresar-btn").addEventListener("click", function() {
                    window.location.href = "carga.php"; // Redirige a la página de inicio de sesión
                });
            </script>
        </div>
    </div>
</body>
</html>
    <script src="js/scripts.js"></script>
