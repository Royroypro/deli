<?php


// Ruta al autoload de Composer
require_once __DIR__ . '/Database.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
class PedidoServer implements MessageComponentInterface {
    protected $clients; // Lista de clientes conectados
    protected $roles;   // Roles de los clientes conectados
    protected $pedidos; // Almacenamiento temporal de pedidos

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->roles = [];
        $this->pedidos = []; // Inicializar almacenamiento de pedidos
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nuevo cliente conectado: ({$conn->resourceId})\n";
    }
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (isset($data['role'])) {
            $this->roles[$from->resourceId] = $data['role'];
            echo "Cliente {$from->resourceId} asignado como {$data['role']}\n";
            return;
        }

        if (isset($data['pedido'])) {
            $pedido = $data['pedido'];
            $pedidoId = $pedido['id_pedido'];
            $this->pedidos[$pedidoId] = $pedido; // Guardar el pedido temporalmente

            echo "Pedido recibido: " . json_encode($pedido) . "\n";

            // Enviar al restaurante
            foreach ($this->clients as $client) {
                if ($client !== $from && $this->roles[$client->resourceId] == 'restaurante') {
                    $client->send(json_encode([
                        'origen' => 'cliente',
                        'mensaje' => 'Nuevo pedido recibido',
                        'pedido' => $pedido,
                    ]));
                }
            }
        }

        if (isset($data['accion']) && $data['accion'] == 'respuesta_pedido') {
            $pedidoId = $data['pedido_id'];
            $accion = $data['respuesta']; // 'aceptar' o 'rechazar'
            $restaurante = $this->roles[$from->resourceId] == 'restaurante';

            if ($restaurante) {
                echo "Restaurante ha {$accion} el pedido {$pedidoId}\n";

                // Notificar al cliente
                foreach ($this->clients as $client) {
                    if ($client !== $from && $this->roles[$client->resourceId] == 'cliente') {
                        $client->send(json_encode([
                            'origen' => 'restaurante',
                            'mensaje' => $accion == 'aceptar' ? 'Pedido aceptado' : 'Pedido rechazado',
                            'pedido_id' => $pedidoId,
                            'estado' => $accion,
                        ]));
                    }
                }

                if ($accion == 'aceptar' && isset($this->pedidos[$pedidoId])) {
                    $pedidoCompleto = $this->pedidos[$pedidoId];

                    // Enviar detalles completos del pedido al repartidor, incluyendo el nombre del cliente
                    foreach ($this->clients as $client) {
                        if ($client !== $from && $this->roles[$client->resourceId] == 'repartidor') {
                            $client->send(json_encode([
                                'origen' => 'restaurante',
                                'mensaje' => 'Nuevo pedido para reparto',
                                'pedido_id' => $pedidoId,
                                'estado' => 'aceptado',
                                'pedido' => $pedidoCompleto,
                                'nombre_cliente' => $pedidoCompleto['cliente'],
                            ]));
                        }
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->roles[$conn->resourceId]);
        echo "Cliente desconectado: ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

// Crear el servidor WebSocket en el puerto 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new PedidoServer()
        )
    ),
    8080
);

$server->run();
?>
