<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxLibroActas.php");
	$mnRegistro = fxVerificaUsuario();
	
	if ($mnRegistro == 0)
	{
?>

<div class="container text-center">
	<div id="DivContenido">
	    <img src="imagenes/errordeacceso.png"/>
    </div>
 </div>
<?php }
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("catLibroActas");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{?>
        <div class="container text-center">
        	<div id="DivContenido">
				<img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
		{
			if (isset($_POST["txtLibro"]))
			{
				$msCodigo = $_POST["txtLibro"];
				$msDescripcion = $_POST["txtDescripcion"];
				$mdApertura = $_POST["dtpApertura"];
				$msTomo = $_POST["txtTomo"];
				$mnPartida = $_POST["txnPartida"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarLibro($msDescripcion, $mdApertura, $msTomo, $mnPartida);
						$msBitacora = $msCodigo . "; " . $msDescripcion . "; " . $mdApertura . "; " . $msTomo . "; " . $mnPartida;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO330A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarLibro($msCodigo, $msDescripcion, $mdApertura, $msTomo, $mnPartida);
						$msBitacora = $msCodigo . "; " . $msDescripcion . "; " . $mdApertura . "; " . $msTomo . "; " . $mnPartida;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO330A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridLibroActas.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveLibro(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msDescripcion = $mFila["DESC_330"];
					$mdApertura = $mFila["APERTURA_330"];
					$msTomo = $mFila["TOMO_330"];
					$mnPartida = $mFila["ULTIMAPARTIDA_330"];
				}
				else
				{
					$msDescripcion = "";
					$mdApertura = date('Y-m-d');
					$msTomo = "";
					$mnPartida = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de Libros de acta</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catLibroActas" name="catLibroActas" action="catLibroActas.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtLibro" class="col-sm-12 col-md-2 col-form-label">Libro</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtLibro" name="txtLibro" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtDescripcion" class="col-sm-12 col-md-2 col-form-label">Descripción</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtDescripcion" name="txtDescripcion" value="' . $msDescripcion . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtTomo" class="col-sm-12 col-md-2 col-form-label">Tomo</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="text" class="form-control" id="txtTomo" name="txtTomo" value="' . $msTomo . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="dtpApertura" class="col-sm-12 col-md-2 col-form-label">Apertura</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="date" class="form-control" id="dtpApertura" name="dtpApertura" value="' . $mdApertura . '" />'); ?>
							</div>
						</div>
                        
                      	<div class = "form-group row">
							<label for="txnPartida" class="col-sm-12 col-md-2 col-form-label">Ultima partida</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="number" class="form-control" id="txnPartida" name="txnPartida" value="' . $mnPartida . '" />'); ?>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridLibroActas.php';"/>
							</div>
						</div>
					</form>
                </div>
			</div>
		</div>
	</div>
<?php	}
	}
}
?>
</body>
</html>
<script type='text/javascript'>
	function verificarFormulario()
	{
		if(document.getElementById('txtDescripcion').value=="")
		{
			$.messager.alert('UMOJN','Falta la descripción.','warning');
			return false;
		}

		if(document.getElementById('txtTomo').value=="")
		{
			$.messager.alert('UMOJN','Falta el tomo.','warning');
			return false;
		}
        
		return true;
	}
</script>