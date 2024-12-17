const express = require('express');
const https = require('https'); // Reemplaza http por https
const fs = require('fs');
const socketIo = require('socket.io');
const mysql = require('mysql2');
const cors = require('cors');
const EventEmitter = require('events');
const detallesPedidoHandler = require('./pedidos_js/detallePedidos');
const agregarProductosHandler = require('./pedidos_js/agregarProductos');
const actualizadarcantidadHandler = require('./pedidos_js/actualizarcantidad');
const listarPedidiosClienteHandler = require('./pedidos_js/listarPedidiosCliente');
const listarPedidiosRestauranteHandler = require('./pedidos_js/listarPedidiosRestaurante');
const listarPedidiosRepartidorHandler = require('./pedidos_js/ListarPedidiosRepartidor');

const app = express();

// Configuración SSL
const options = {
    key: fs.readFileSync('/etc/letsencrypt/live/royner.ddns.net/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/royner.ddns.net/fullchain.pem'),
};

// Crear servidor HTTPS
const server = https.createServer(options, app);

// Middleware para manejo de CORS
app.use(cors({
    origin: '*',
    methods: ['GET', 'POST'],
    allowedHeaders: ['Content-Type'],
}));

// Middleware para manejar JSON en solicitudes POST
app.use(express.json());

// Configuración de CORS para Socket.IO
const io = socketIo(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST'],
        allowedHeaders: ['Content-Type'],
        credentials: true,
    }
});

const pedidosEmitter = new EventEmitter();
let connectedSockets = [];

// Conexión a la base de datos
const connection = mysql.createConnection({
    host: 'cespedes.ddns.net',
    user: 'root',
    password: '*Royner123123*',
    database: 'deli',
});

connection.connect(err => {
    if (err) {
        console.error('Error de conexión a la base de datos:', err);
        return;
    }
    console.log('Conectado a la base de datos MySQL');
});

// Socket.IO: Manejo de eventos
io.on('connection', (socket) => {
    console.log('Un cliente se ha conectado');
    connectedSockets.push(socket);

    detallesPedidoHandler(socket, connection);
    agregarProductosHandler(app, connection, io);
    actualizadarcantidadHandler(socket, connection, io);
    listarPedidiosClienteHandler(socket, connection, io, pedidosEmitter);
    listarPedidiosRestauranteHandler(socket, connection, io, pedidosEmitter);
    listarPedidiosRepartidorHandler(socket, connection, io, pedidosEmitter);

    socket.on('disconnect', () => {
        console.log('Un cliente se ha desconectado');
        connectedSockets = connectedSockets.filter(existingSocket => existingSocket !== socket);
    });
});

// Servir archivos estáticos
app.use(express.static('pedidos'));

// Iniciar servidor en HTTPS
server.listen(8080, () => {
    console.log('Servidor HTTPS en https://royner.ddns.net:8080');
});
