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
		$mbPermisoUsuario = fxPermisoUsuario("hrrCambiaClave");
		
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
			if (isset($_POST["Guardar"]))
			{
				$msCodigo = $_POST["txtCodUsuario"];
				$msClave = $_POST["txtClave"];
				$msClave1 = $_POST["txtClave1"];
				
				fxClaveUsuario ($msCodigo, $msClave);
				fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO002A", $msCodigo, "", "Cambio clave", "");
						
				?><meta http-equiv="Refresh" content="0;url=gridCambiaClave.php"/><?php
			}
			else
			{
				if (isset($_POST["UMO"]))
				{
					$msCodigo = $_POST["UMO"];
					$RecordSet = fxDevuelveUsuario(0, $msCodigo);
					$mFila = $RecordSet->fetch();
					$msNombre = $mFila["NOMBRE_002"];
					$msClave = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Cambio de contraseña</strong></div>
				</div>
			</div>
        	<div class = "row">
				<div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                    <form name="hrrCambiaClave" action="hrrCambiaClave.php" method="post" onsubmit="return verificarFormulario()">
                        <div class = "form-group row">
                            <label for="txtCodUsuario" class="col-sm-12 col-md-3 form-label">Código del Usuario</label>
                            <div class="col-sm-12 col-md-3">
                                <?php echo('<input type="text" class="form-control" id="txtCodUsuario" name="txtCodUsuario" value="' . $msCodigo . '"  readonly />'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtNomUsuario" class="col-sm-12 col-md-3 form-label">Nombre del Usuario</label>
                            <div class="col-sm-12 col-md-5">
                                <?php echo('<input type="text" class="form-control" id="txtNomUsuario" name="txtNomUsuario" value="' . $msNombre . '" readonly/>'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtClave" class="col-sm-12 col-md-3 col-form-label">Clave del Usuario</label>
                            <div class="col-sm-12 col-md-3">
                                <?php echo('<input type="password" class="form-control" id="txtClave" name="txtClave" value="' . $msClave . '" />'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtClave1" class="col-sm-12 col-md-3 form-label">Confirme la Clave</label>
                            <div class="col-sm-12 col-md-3">
                                <?php echo('<input type="password" class="form-control" id="txtClave1" name="txtClave1" value="' . $msClave . '" />'); ?>
                            </div>
                        </div>
                        <div class = "row">
                            <div class="col-auto offset-sm-0 col-md-10 offset-md-3">
                                <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
                                <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCambiaClave.php';"/>
                            </div>
                        </div>
                    </form>			
	<?php	}
		}
	}
?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<script type='text/javascript'>
	function verificarFormulario()
	{
		if (document.getElementById('txtClave').value!=document.getElementById('txtClave1').value)
		{
			$.messager.alert('UMOJN','La contraseña no se confirmó correctamente.','warning');
			return false;
		}
		
		return true;
	}
</script>