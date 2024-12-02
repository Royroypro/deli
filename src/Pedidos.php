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

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->roles = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nuevo cliente conectado: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (isset($data['role'])) {
            // Asignamos el rol al cliente (cliente, restaurante, repartidor)
            $this->roles[$from->resourceId] = $data['role'];
            echo "Cliente {$from->resourceId} asignado como {$data['role']}\n";
            return;
        }

        if (isset($data['pedido'])) {
            // Recibimos un pedido
            $pedido = $data['pedido'];
            $cliente = $pedido['cliente'];
            $destinatarios = ['restaurante', 'repartidor'];

            echo "Pedido recibido de {$cliente}: " . json_encode($pedido) . "\n";

            // Enviar el pedido a los destinatarios
            foreach ($this->clients as $client) {
                $resourceId = $client->resourceId;

                if ($client !== $from && in_array($this->roles[$resourceId], $destinatarios)) {
                    $client->send(json_encode([
                        'origen' => $cliente,
                        'mensaje' => 'Nuevo pedido recibido',
                        'pedido' => $pedido,
                    ]));
                }
            }
        }

        // Acciones de aceptación o rechazo del pedido
        if (isset($data['accion']) && $data['accion'] == 'respuesta_pedido') {
            $pedidoId = $data['pedido_id'];
            $accion = $data['respuesta']; // 'aceptar' o 'rechazar'
            $restaurante = $this->roles[$from->resourceId] == 'restaurante';

            if ($restaurante) {
                echo "Restaurante ha {$accion} el pedido {$pedidoId}\n";

                // Notificar a los clientes del restaurante sobre la acción tomada
                foreach ($this->clients as $client) {
                    $resourceId = $client->resourceId;

                    if ($client !== $from && in_array($this->roles[$resourceId], ['cliente', 'repartidor'])) {
                        $client->send(json_encode([
                            'origen' => 'restaurante',
                            'mensaje' => $accion == 'aceptar' ? 'Pedido aceptado' : 'Pedido rechazado',
                            'pedido_id' => $pedidoId,
                            'estado' => $accion,
                        ]));
                    }
                }

                // Notificar al cliente que realizó el pedido
                foreach ($this->clients as $client) {
                    $resourceId = $client->resourceId;

                    if ($client !== $from && $this->roles[$resourceId] == 'cliente') {
                        $client->send(json_encode([
                            'origen' => 'restaurante',
                            'mensaje' => $accion == 'aceptar' ? 'Su pedido ha sido aceptado' : 'Su pedido ha sido rechazado',
                            'pedido_id' => $pedidoId,
                            'estado' => $accion,
                        ]));
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

