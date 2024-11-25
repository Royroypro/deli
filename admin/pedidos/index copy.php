<?php
include "../../app/config.php";
include "../../layout/sesion.php";
include "../layout/parte1.php";




?>





<body>
	
	<!-- pageContent -->

		
		<section class="full-width header-well">
			<div class="full-width header-well-icon">
				<i class="zmdi zmdi-shopping-cart"></i>
			</div>
			<div class="full-width header-well-text">
				<p class="text-condensedLight">
                    <span class="text-dark">Pedidos</span>
				</p>
			</div>
		</section>
		<div class="full-width divider-menu-h"></div>
		<div class="mdl-grid">
			<div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
				<div class="table-responsive">
					<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive">
						<thead>
							<tr>
								<th class="mdl-data-table__cell--non-numeric">Date</th>
								<th>Client</th>
								<th>Payment</th>
								<th>Total</th>
								<th>Options</th>
							</tr>
						</thead>
						<tbody>
							<?php
								// Si el usuario es restaurante, seleccionar todos los pedidos asociados al restaurante
								if ($rol_sesion === 'restaurante') {
									$sql = "SELECT p.id_pedido, c.nombre_cliente as cliente, p.estado, p.total, p.fecha FROM pedidos p LEFT JOIN clientes c ON p.id_cliente = c.id_cliente WHERE p.id_restaurante = :id_restaurante";
									$stmt = $pdo->prepare($sql);
									$stmt->bindParam(':id_restaurante', $id_restaurante_sesion);
									$stmt->execute();
									$pedidos = $stmt->fetchAll();
									foreach ($pedidos as $pedido) {
										echo "<tr>
												<td class='mdl-data-table__cell--non-numeric'>" . date('d/m/Y H:i:s', strtotime($pedido['fecha'])) . "</td>
												<td>" . $pedido['cliente'] . "</td>
												<td>" . $pedido['estado'] . "</td>
												<td>" . $pedido['total'] . "</td>
												<td><button class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect'><i class='zmdi zmdi-more'></i></button></td>
											</tr>";
									}
								// Si el usuario es cliente, seleccionar todos los pedidos asociados al cliente
								} else if ($rol_sesion === 'cliente') {
									$sql = "SELECT p.id_pedido, r.nombre as restaurante, p.estado, p.total, p.fecha FROM pedidos p LEFT JOIN restaurantes r ON p.id_restaurante = r.id_restaurante LEFT JOIN clientes c ON p.id_cliente = c.id_cliente WHERE c.id_usuario = :id_usuario";
									$stmt = $pdo->prepare($sql);
									$stmt->bindParam(':id_usuario', $id_usuario_sesion);
									$stmt->execute();
									$pedidos = $stmt->fetchAll();
									foreach ($pedidos as $pedido) {
										echo "<tr>
												<td class='mdl-data-table__cell--non-numeric'>" . date('d/m/Y H:i:s', strtotime($pedido['fecha'])) . "</td>
												<td>" . $pedido['restaurante'] . "</td>
												<td>" . $pedido['estado'] . "</td>
												<td>" . $pedido['total'] . "</td>
												<td><button class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect'><i class='zmdi zmdi-more'></i></button></td>
											</tr>";
									}
								// Si el usuario es repartidor, seleccionar todos los pedidos asignados al repartidor
								} else if ($rol_sesion === 'repartidor') {
									$sql = "SELECT p.id_pedido, c.nombre_cliente as cliente, p.estado, p.total, p.fecha FROM pedidos p LEFT JOIN clientes c ON p.id_cliente = c.id_cliente WHERE p.id_repartidor = :id_usuario";
									$stmt = $pdo->prepare($sql);
									$stmt->bindParam(':id_usuario', $id_usuario_sesion);
									$stmt->execute();
									$pedidos = $stmt->fetchAll();
									foreach ($pedidos as $pedido) {
										echo "<tr>
												<td class='mdl-data-table__cell--non-numeric'>" . date('d/m/Y H:i:s', strtotime($pedido['fecha'])) . "</td>
												<td>" . $pedido['cliente'] . "</td>
												<td>" . $pedido['estado'] . "</td>
												<td>" . $pedido['total'] . "</td>
												<td><button class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect'><i class='zmdi zmdi-more'></i></button></td>
											</tr>";
									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
</body>
</html>