const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql2');
const detallesPedidoHandler = require('./pedidos_js/detallePedidos');
const agregarProductosHandler = require('./pedidos_js/agregarProductos');
const actualizadarcantidadHandler = require('./pedidos_js/actualizarcantidad');
const listarPedidiosClienteHandler = require('./pedidos_js/listarPedidiosCliente');
const listarPedidiosRestauranteHandler = require('./pedidos_js/listarPedidiosRestaurante');
const listarPedidiosRepartidorHandler = require('./pedidos_js/ListarPedidiosRepartidor');
const cors = require('cors');
const EventEmitter = require('events');
const app = express();
const server = http.createServer(app);

// Crear un EventEmitter para manejar eventos de pedidos
const pedidosEmitter = new EventEmitter();

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

// Lista para almacenar sockets conectados
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

    // Añadir el socket a la lista de sockets conectados
    connectedSockets.push(socket);

    // Emitir un evento a todos los clientes ya conectados cuando un nuevo cliente se conecta
    connectedSockets.forEach(existingSocket => {
        existingSocket.emit('nuevoClienteConectado', {
            message: 'Un nuevo cliente se ha conectado',
        });
    });

    // Manejadores de eventos
    detallesPedidoHandler(socket, connection);
    agregarProductosHandler(app, connection, io);
    actualizadarcantidadHandler(socket, connection, io);

    // Listar pedidos de clientes y restaurantes
    listarPedidiosClienteHandler(socket, connection, io, pedidosEmitter);
    listarPedidiosRestauranteHandler(socket, connection, io, pedidosEmitter);
    listarPedidiosRepartidorHandler(socket, connection, io, pedidosEmitter);
    // Manejar eventos de actualizaciones de pedidos emitidos por pedidosEmitter
    pedidosEmitter.on('pedidoActualizado', (pedidoActualizado) => {
        // Emitir la actualización del pedido a todos los clientes
        io.emit('actualizarPedidoCliente', pedidoActualizado);

        // También emitir a un restaurante específico
        io.to(pedidoActualizado.idRestaurante).emit('actualizarPedidoRestaurante', pedidoActualizado);
    });

    // Evento para emitir cambios a todos los clientes y restaurantes
    socket.on('pedidoActualizadoCliente', (pedidoActualizado) => {
        // Emitir la actualización del pedido a todos los clientes
        io.emit('actualizarPedidoCliente', pedidoActualizado);

        // También puedes emitir a un restaurante específico si lo necesitas
        io.to(pedidoActualizado.idRestaurante).emit('actualizarPedidoRestaurante', pedidoActualizado);
    });

    // Evento para emitir cambios a todos los clientes y restaurantes
    socket.on('pedidoActualizadoRestaurante', (pedidoActualizado) => {
        // Emitir la actualización del pedido a todos los clientes
        io.emit('actualizarPedidoCliente', pedidoActualizado);

        // También puedes emitir a un cliente específico si lo necesitas
        io.to(pedidoActualizado.idCliente).emit('actualizarPedidoCliente', pedidoActualizado);

        // Emitir a todos los restaurantes
        io.emit('actualizarPedidoRestaurante', pedidoActualizado);
    });

    // Desconexión
    socket.on('disconnect', () => {
        console.log('Un cliente se ha desconectado');
        
        // Eliminar el socket de la lista de sockets conectados
        connectedSockets = connectedSockets.filter(existingSocket => existingSocket !== socket);
    });
});

// Servir archivos estáticos
app.use(express.static('pedidos'));

// Iniciar servidor
server.listen(3000, () => {
    console.log('Servidor en http://cespedes.ddns.net:3000');
});
