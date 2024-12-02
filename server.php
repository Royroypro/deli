<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/ChatServer.php';
require __DIR__ . '/src/Notifications.php';
require __DIR__ . '/src/Pedidos.php';
require_once __DIR__ . '/src/Database.php';

use Ratchet\App;

// Cargar configuración
$config = require __DIR__ . '/config.php';

// Iniciar servidor WebSocket
$app = new App('localhost', 8080, '0.0.0.0');
$app->route('/deli', new ChatServer($config), ['*']);
$app->run();
?>
