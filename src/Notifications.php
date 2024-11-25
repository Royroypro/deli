<?php
class Notifications {
    private $db;

    public function __construct($dbConfig) {
        $this->db = new Database($dbConfig);
    }

    public function sendNotification($type, $message) {
        // Lógica para enviar notificaciones, dependiendo del tipo
        $this->db->query(
            "INSERT INTO notificaciones (tipo, mensaje, fecha) VALUES (?, ?, NOW())",
            ['ss', $type, $message]
        );
    }
}
?>
