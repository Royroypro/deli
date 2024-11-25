<?php
// Requiere el archivo Database.php que contiene la clase para la conexión a la base de datos
require_once __DIR__ . '/Database.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    // Almacena todos los clientes conectados
    private $clients;
    
    // Conexión a la base de datos
    private $db;

    // Constructor de la clase ChatServer
    // Recibe la configuración de la base de datos como parámetro
    public function __construct($dbConfig) {
        // Inicializa la colección de clientes conectados
        $this->clients = new \SplObjectStorage();
        
        // Inicializa la conexión a la base de datos con la configuración proporcionada
        $this->db = new Database($dbConfig);
    }
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nueva conexión: ({$conn->resourceId})\n";

        // Cargar todos los mensajes guardados
        $result = $this->db->query(
            "SELECT u.nombre, m.mensaje FROM mensajes m JOIN usuarios u ON m.id_usuario = u.id ORDER BY m.fecha ASC"
        )->get_result();

        while ($mensaje = $result->fetch_assoc()) {
            $new_data = ['nombre' => $mensaje['nombre'], 'mensaje' => $mensaje['mensaje'] ?? ''];
            $new_msg = json_encode($new_data);
            $conn->send($new_msg);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $id_usuario = $data['id_usuario'] ?? 0;
        $mensaje = $data['mensaje'] ?? '';
        if ($data['accion'] === 'obtener_mensajes') {
            $result = $this->db->query(
                "SELECT u.nombre, m.mensaje FROM mensajes m JOIN usuarios u ON m.id_usuario = u.id ORDER BY m.fecha ASC"
            )->get_result();

            $mensajes = [];
            while ($mensaje = $result->fetch_assoc()) {
                $mensajes[] = $mensaje;
            }

            $new_data = ['accion' => 'mensajes', 'datos' => $mensajes];
            $new_msg = json_encode($new_data);
            $from->send($new_msg);
        } else if ($data['accion'] === 'nuevo_mensaje') {
            // Guardar el mensaje en la base de datos
            $this->db->query(
                "INSERT INTO mensajes (id_usuario, mensaje, fecha) VALUES (?, ?, NOW())",
                ['is', $id_usuario, $mensaje]
            );

            // Recuperar el nombre del usuario
            $result = $this->db->query(
                "SELECT nombre FROM usuarios WHERE id = ?",
                ['i', $id_usuario]
            )->get_result();
            $nombre = $result->fetch_assoc()['nombre'] ?? '';
            // Crear el mensaje para enviar a todos los clientes
            $new_data = ['accion' => 'nuevo_mensaje', 'datos' => ['nombre' => $nombre, 'mensaje' => $mensaje]];
            $new_msg = json_encode($new_data);

            foreach ($this->clients as $client) {
                $client->send($new_msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Conexión {$conn->resourceId} cerrada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}
?>

