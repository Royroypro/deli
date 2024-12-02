<?php
include "../app/config.php";

$nombre_usuario = urldecode($_GET['nombre_usuario']);

echo "el cliente es este: ".$nombre_usuario;
?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/normalize.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/sweetalert2.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/material.min.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="<?php echo $URL;?>admin/dis/css/main.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/jquery-1.11.2.min.js"><\/script>')</script>
	<script src="<?php echo $URL;?>admin/dis/js/material.min.js" ></script>
	<script src="<?php echo $URL;?>admin/dis/js/sweetalert2.min.js" ></script>
	<script src="<?php echo $URL;?>admin/dis/js/jquery.mCustomScrollbar.concat.min.js" ></script>
	<script src="<?php echo $URL;?>admin/dis/js/main.js" ></script>
</head>
<section class="full-width header-well">
			<div class="full-width header-well-icon">
				<i class="zmdi zmdi-accounts"></i>
			</div>
			<div class="full-width header-well-text">
				<p class="text-condensedLight">
					Completa los siguientes datos para continuar
				</p>
			</div>
		</section>
		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
			<div class="mdl-tabs__tab-bar">
				<a href="#tabNewClient" class="mdl-tabs__tab is-active">Completar</a>
		
			</div>
			<div class="mdl-tabs__panel is-active" id="tabNewClient">
				<div class="mdl-grid">
					<div class="mdl-cell mdl-cell--12-col">
						<div class="full-width panel mdl-shadow--2dp">
							<div class="full-width panel-tittle bg-primary text-center tittles">
								Nuevo cliente
							</div>
							<div class="full-width panel-content">
								<form action="../app/controllers/clientes/registrar_cliente.php" method="post">
									<div class="mdl-grid">
										<div class="mdl-cell mdl-cell--12-col">
									        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; CLIENT DATA</legend><br>
									    </div>
										<input type="hidden" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>">
									    <div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="text" id="nombreCliente" name="nombreCliente" required>
												<label class="mdl-textfield__label" for="nombreCliente">Client Name</label>
												<span class="mdl-textfield__error">Invalid name</span>
											</div>
									    </div>
									    <div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="text" id="direccion" name="direccion" required>
												<label class="mdl-textfield__label" for="direccion">Address</label>
												<span class="mdl-textfield__error">Invalid address</span>
											</div>
									    </div>
									    <div class="mdl-cell mdl-cell--12-col">
											<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
												<input class="mdl-textfield__input" type="tel" pattern="-?[0-9+()- ]*(\.[0-9]+)?" id="telefono" name="telefono">
												<label class="mdl-textfield__label" for="telefono">Phone</label>
												<span class="mdl-textfield__error">Invalid phone number</span>
											</div>
									    </div>
									    
									</div>
									<p class="text-center">
										<button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="btn-addClient">
											<i class="zmdi zmdi-plus"></i>
										</button>
										<div class="mdl-tooltip" for="btn-addClient">Add client</div>
									</p>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</section>