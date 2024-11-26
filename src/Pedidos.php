<?php
 // Ruta al autoload de Composer

 
 
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
             $pedido = $data['pedido'];
             $cliente = $pedido['cliente'];
             $destinatarios = ['restaurante', 'repartidor'];
 
             echo "Pedido recibido de {$cliente}: " . json_encode($pedido) . "\n";
 
             // Enviar a destinatarios específicos (restaurante y repartidor)
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
 