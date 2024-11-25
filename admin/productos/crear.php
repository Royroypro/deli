<?php
include "../../app/config.php";
include "../../layout/sesion.php";
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
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="text" name="nombre" pattern="-?[A-Za-z0-9 ]*(\.[0-9]+)?" id="nombre">
												<label class="mdl-textfield__label" for="nombre">Nombre</label>
												<span class="mdl-textfield__error">Nombre invalido</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield">
												<input type="text" name="descripcion" class="mdl-textfield__input" id="descripcion">
												<label class="mdl-textfield__label" for="descripcion">Descripción</label>
												<span class="mdl-textfield__error">Descripci n invalida</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="number" name="precio" pattern="-?[0-9]*(\.[0-9]+)?" id="precio">
												<label class="mdl-textfield__label" for="precio">Precio</label>
												<span class="mdl-textfield__error">N mero invalido</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="number" name="stock" pattern="-?[0-9]*(\.[0-9]+)?" id="stock">
												<label class="mdl-textfield__label" for="stock">Unidades</label>
												<span class="mdl-textfield__error">N mero invalido</span>
											</div>
										</div>
										<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
											<div class="mdl-textfield mdl-js-textfield">
												<input type="file" name="imagen" class="mdl-textfield__input" id="imagen">
											</div>
										</div>
										<div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield">
												<select class="mdl-textfield__input" name="categorias[]" id="categorias" multiple>
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
			  <?php
			  include "lista.php";
			  ?>

	</section>