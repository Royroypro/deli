<?php
session_start();
echo array_sum(array_column($_SESSION['carrito'], 'cantidad'));
?>
