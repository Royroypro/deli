<?php

include_once 'total.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat en Tiempo Real </title>
    <style>
        * {
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
        }

        #messages {
            width: 50%;
            max-height: 300px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            margin: 10px auto;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }

        #messages div {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }

        #messages div:last-child {
            border-bottom: none;
        }

        #messages div + div {
            margin-top: 10px;
        }

        #messages div:before {
            content: attr(data-nombre);
            font-weight: bold;
            margin-right: 10px;
        }

        #mensaje {
            width: 50%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px auto;
        }

        button {
            padding: 10px;
            font-size: 16px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <h1>Chat en Tiempo Real </h1>
    <div id="messages"></div>
    <input type="text" id="mensaje" placeholder="Escribe un mensaje">
    <button onclick="enviarMensaje()">Enviar</button>

    <script>
        const ws = new WebSocket('ws://localhost:8080/deli');

        ws.onopen = () => {
            console.log('Conectado al servidor');
            ws.send(JSON.stringify({ accion: 'obtener_mensajes' }));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.accion === 'mensajes') {
                const messagesDiv = document.getElementById('messages');
                messagesDiv.innerHTML = '';
                data.datos.forEach(msg => {
                    const message = document.createElement('div');
                    message.dataset.nombre = msg.nombre;
                    message.textContent = msg.mensaje;
                    messagesDiv.appendChild(message);
                });
            } else if (data.accion === 'nuevo_mensaje') {
                const messagesDiv = document.getElementById('messages');
                const message = document.createElement('div');
                message.dataset.nombre = data.datos.nombre;
                message.textContent = data.datos.mensaje;
                messagesDiv.appendChild(message);
            }
        };

        function enviarMensaje() {
            const mensaje = document.getElementById('mensaje').value;
            const nombre = <?php echo $_SESSION['id_usuario']; ?>;

            ws.send(JSON.stringify({
                accion: 'nuevo_mensaje',
                id_usuario: nombre,
                nombre: nombre,
                mensaje: mensaje
            }));
        }
    </script>


