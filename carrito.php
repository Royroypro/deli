<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrito de Compras</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <h1>Tienda Virtual</h1>
  </header>

  <main>
    <section id="productos">
      <h2>Productos</h2>
      <div class="producto" data-id="1" data-nombre="Producto 1" data-precio="10">
        <h3>Producto 1</h3>
        <p>Precio: $10</p>
        <button class="btn-agregar">Agregar al Carrito</button>
      </div>
      <div class="producto" data-id="2" data-nombre="Producto 2" data-precio="20">
        <h3>Producto 2</h3>
        <p>Precio: $20</p>
        <button class="btn-agregar">Agregar al Carrito</button>
      </div>
    </section>

    <section id="carrito">
      <h2>Carrito de Compras</h2>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="lista-carrito">
          <!-- Los productos añadidos aparecerán aquí -->
        </tbody>
      </table>
      <p id="total-carrito">Total: $0</p>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>


