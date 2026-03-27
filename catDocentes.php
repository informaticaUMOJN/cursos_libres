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
	require_once ("funciones/fxDocentes.php");
	$Registro = fxVerificaUsuario();
	
	if ($Registro == 0)
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
		$mbPermisoUsuario = fxPermisoUsuario("catDocentes");
		
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
			if (isset($_POST["txtDocente"]))
			{
				$msCodigo = $_POST["txtDocente"];
				$msNombre = $_POST["txtNombre"];
				$mbActivo = $_POST["optActivo"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarDocentes($msNombre, $mbActivo);
						$msBitacora = $msCodigo . "; " . $msNombre . "; " . $mbActivo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO340A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarDocentes($msCodigo, $msNombre, $mbActivo);
						$msBitacora = $msCodigo . "; " . $msNombre . "; " . $mbActivo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO340A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridDocentes.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveDocentes(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msNombre = $mFila["NOMBRE_340"];
					$mbActivo = $mFila["ACTIVO_340"];
				}
				else
				{
					$msNombre = "";
					$mbActivo = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de docentes</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catDocentes" name="catDocentes" action="catDocentes.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtDocente" class="col-sm-12 col-md-3 col-form-label">Docente</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtDocente" name="txtDocente" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-3 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
                            <label for="optActivo" class="col-sm-auto col-md-3 form-label">Activo</label>
                            <div class="col-sm-12 col-md-3">
                                <div class="radio">
                                <?php
									if ($mbActivo == 1)
										echo('<input type="radio" id="optActivo1" name="optActivo" value="0" /> No <input type="radio" id="optActivo2" name="optActivo" value="1" checked/> Si');
									else
										echo('<input type="radio" id="optActivo1" name="optActivo" value="0" checked/> No <input type="radio" id="optActivo2" name="optActivo" value="1" /> Si');
								?>
								</div>
							</div>
                        </div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridDocentes.php';"/>
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
		if(document.getElementById('txtNombre').value=="")
		{
			$.messager.alert('UMOJN','Falta el Nombre.','warning');
			return false;
		}

		return true;
	}
</script>