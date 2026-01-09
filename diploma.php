<?php
	#Inicia la sesión antes de la respuesta HTML
	session_start();
	require_once ("funciones/fxGeneral.php");
?>
<!DOCTYPE html>
<html lang="ES-NI" class="no-js">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Diplomas UMOJN."/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="icon" href="imagenes/favicon.png" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css" />
<link rel="stylesheet" type="text/css" href="css/icon.css" />
<link rel="stylesheet" type="text/css" href="css/StyleUMO.css"/>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-3.4.1.js"></script>
<script src="js/jquery.easyui.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<title>Diplomas UMOJN</title>
</head>

<body>
	<div id="cabecera">
        <div class="container-fluid">
            <img src="imagenes/header.png" width="100%" />
        </div>
    </div>
<?php
	if (isset($_GET['UMOJN']))
	{
		$_SESSION["gnVerifica"] = $_GET["UMOJN"];
		$mnVerificacion = $_GET['UMOJN'];
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select RUTA_003 from UMO003B where VERIFICACION_003 = ?";
		$mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
		$mAuxiliar->execute([$mnVerificacion]);
		$mnRegistros = $mAuxiliar->rowCount();

		if ($mnRegistros == 0)
		{
?>
	<div class="container">
		<div id="DivContenido">
			<section class="row">
				<div class="col-xs-6 col-xs-offset-1 col-md-8 col-md-offset-2">
					<img src="imagenes/errordeacceso.png" class="img-responsive" height="180vh" alt="">
				</div>
			</div>
		</div>
	</div>
<?php
		}
		else
		{
			$mAuxFila = $mAuxiliar->fetch();
			$msRuta = $mAuxFila["RUTA_003"];
?>
    <div class="container">
		<div id="DivContenido">
			<section class="row">
				<div class="col-xs-10 col-xs-offset-1 col-md-12">
					<?php
						echo('<img src="' . $msRuta . '" class="img-fluid" alt="">');
					?>
				</div>
			</section>
		</div>
    </div>
		<?php } ?>

<?php
	}
	else
	{
?>
	<div class="container">
		<div id="DivContenido">
			<section class="row">
				<div class="col-xs-6 col-xs-offset-1 col-md-8 col-md-offset-2">
					<img src="imagenes/errordeacceso.png" class="img-responsive" height="180vh" alt="">
				</div>
			</div>
		</div>
	</div>
<?php } ?>
</body>
</html>