<?php
include "../../app/config.php";

include "../layout/parte1.php";


?>


		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">

        <p class="text-condensedLight">
                    AGREGAR PRODUCTO
            </p>

			<div class="mdl-tabs__tab-bar">
				<a href="#tabNewProduct" class="mdl-tabs__tab is-active">NUEVO</a>
				<a href="#tabListProducts" class="mdl-tabs__tab">LISTA</a>
			</div>
			<div class="mdl-tabs__panel is-active" id="tabNewProduct">
				<div class="mdl-grid">
					<div class="mdl-cell mdl-cell--12-col">
						<div class="full-width panel mdl-shadow--2dp">
							<div class="full-width panel-tittle bg-primary text-center tittles">
								Nuevo producto
							</div>
							<div class="full-width panel-content">
								<form action="<?php echo $URL; ?>admin/controllers/productos/crear.php" method="post" enctype="multipart/form-data">
									<div class="mdl-grid">
										<div class="mdl-cell mdl-cell--12-col">
									        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACI N B SICA</legend><br>
									    </div>
										<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="text" name="Nombre" pattern="-?[A-Za-z0-9 ]*(\.[0-9]+)?" id="Nombre">
												<label class="mdl-textfield__label" for="Nombre">Nombre</label>
												<span class="mdl-textfield__error">Nombre invalido</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="number" name="Precio" pattern="-?[0-9]*(\.[0-9]+)?" id="Precio">
												<label class="mdl-textfield__label" for="Precio">Precio</label>
												<span class="mdl-textfield__error">N mero invalido</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="number" name="Stock" pattern="-?[0-9]*(\.[0-9]+)?" id="Stock">
												<label class="mdl-textfield__label" for="Stock">Unidades</label>
												<span class="mdl-textfield__error">N mero invalido</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield">
												<input type="text" name="Descripcion" class="mdl-textfield__input" id="Descripcion">
												<label class="mdl-textfield__label" for="Descripcion">Descripci n</label>
												<span class="mdl-textfield__error">Descripci n invalida</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--12-col">
									        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; CATEGOR A</legend><br>
									    </div>
										<div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield">
												<select class="mdl-textfield__input" name="id_categoria" id="id_categoria" multiple>
													<option value="" disabled="" selected="">Seleccione una o varias categor as</option>
													<?php
													try {
													    
													    $sql = "SELECT * FROM categorias";
													    $stmt = $pdo->query($sql);

													    if ($stmt->rowCount() > 0) {
													        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
													            echo '<option value="' . $row['id_categoria'] . '" >' . $row['nombre'] . '</option>';
													        }
													    } else {
													        echo "0 results";
													    }
													} catch (PDOException $e) {
													    echo "Error al conectar a la base de datos";
													}
													?>
												</select>
											</div>
									    </div>
										<div class="mdl-cell mdl-cell--12-col">
									        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; IMAGEN</legend><br>
									    </div>
										<div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield">
												<input type="file" name="imagen" class="mdl-textfield__input" id="imagen">
											</div>
									    </div>
									</div>
									<p class="text-center">
										<button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="btn-addProduct">
											<i class="zmdi zmdi-plus"></i>
										</button>
										<div class="mdl-tooltip" for="btn-addProduct">Agregar</div>
									</p>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>


             <!-- tabla de productos -->

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
										$stmt = $pdo->prepare("SELECT id, Nombre FROM categoria_producto");
										$stmt->execute();
										if ($stmt->rowCount() > 0) {
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo '<li><a href="#!">' . $row['Nombre'] . '</a></li>';
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
									$stmt = $pdo->prepare("SELECT p.id, p.Nombre, p.Stock, p.Descripcion, p.Estado, p.id_categoria_producto, p.imagen, cp.Nombre as categoria FROM producto p INNER JOIN categoria_producto cp ON p.id_categoria_producto = cp.id");
									$stmt->execute();
									if ($stmt->rowCount() > 0) {
										while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
											echo '<div class="mdl-card mdl-shadow--2dp full-width product-card">';
											echo '  <div class="mdl-card__title">';
											echo '    <img src="' . $row['imagen'] . '" alt="product" class="img-responsive">';
											echo '  </div>';
											echo '  <div class="mdl-card__supporting-text">';
											echo '    <small>Stock: ' . $row['Stock'] . '</small><br>';
											echo '    <small>Categoria: ' . $row['categoria'] . '</small><br>';
											echo '  </div>';
											echo '  <div class="mdl-card__actions mdl-card--border">';
											echo '    <span class="mdl-chip"><span class="mdl-chip__text">' . $row['Nombre'] . '</span></span>';
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
	</section>