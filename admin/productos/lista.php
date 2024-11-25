




<div class="mdl-tabs__panel" id="tabListProducts">
				<div class="mdl-grid">
					<div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
						<form action="#">
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
								<label class="mdl-button mdl-js-button mdl-button--icon" for="searchProduct">
									<i class="zmdi zmdi-search"></i>
								</label>
								<div class="mdl-textfield__expandable-holder">
									<input class="mdl-textfield__input" type="text" id="searchProduct">
									<label class="mdl-textfield__label"></label>
								</div>
							</div>
						</form>
						<nav class="full-width menu-categories">
							<ul class="list-unstyle text-center">
								<?php
									try {
										$stmt = $pdo->prepare("SELECT id_categoria, nombre FROM categorias");
										$stmt->execute();
										if ($stmt->rowCount() > 0) {
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo '<li><a href="#!">' . $row['nombre'] . '</a></li>';
											}
										} else {
											echo "0 results";
										}
									} catch (PDOException $e) {
										echo "Error al conectar a la base de datos";
									}
								?>
							</ul>
						</nav>
						<div class="full-width text-center" style="padding: 30px 0;">
							<?php
								try {
									$stmt = $pdo->prepare("SELECT p.id_producto, p.id_restaurante, p.nombre, p.descripcion, p.precio, p.stock, p.imagen, c.nombre as categoria FROM productos p INNER JOIN productos_categorias pc ON p.id_producto = pc.id_producto INNER JOIN categorias c ON pc.id_categoria = c.id_categoria");
									$stmt->execute();
									if ($stmt->rowCount() > 0) {
										while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
											echo '<div class="mdl-card mdl-shadow--2dp full-width product-card">';
											echo '  <div class="mdl-card__title">';
											                                            echo '    <img src="../imgs/productos/productos/' . $row['imagen'] . '" alt="product" class="img-responsive" style="max-width: 100px; max-height: 100px;">';
											echo '  </div>';
											echo '  <div class="mdl-card__supporting-text">';
											echo '    <small>Stock: ' . $row['stock'] . '</small><br>';
											echo '    <small>Categoria: ' . $row['categoria'] . '</small><br>';
											echo '  </div>';
											echo '  <div class="mdl-card__actions mdl-card--border">';
											echo '    <span class="mdl-chip"><span class="mdl-chip__text">' . $row['nombre'] . '</span></span>';
											echo '    <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">';
											echo '      <i class="zmdi zmdi-more"></i>';
											echo '    </button>';
											echo '  </div>';
											echo '</div>';
										}
									} else {
										echo "0 results";
									}
								} catch (PDOException $e) {
									echo "Error al conectar a la base de datos";
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
