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
				$msUsuario = $_POST["cboUsuario"];
				$msNombre = $_POST["txtNombre"];
				$mnTipo = $_POST["cboTipo"];
				$mbActivo = $_POST["optActivo"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarDocentes($msUsuario, $msNombre, $mnTipo, $mbActivo);
						$msBitacora = $msCodigo . "; " . $msUsuario. "; " . $msNombre . "; " . $mnTipo . "; " . $mbActivo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO100A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarDocentes($msCodigo, $msUsuario, $msNombre, $mnTipo, $mbActivo);
						$msBitacora = $msCodigo . "; " . $msUsuario. "; " . $msNombre . "; " . $mnTipo . "; " . $mbActivo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO100A", $msCodigo, "", "Modificar", $msBitacora);
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
					$msUsuario = $mFila["USUARIO_REL"];
					$msNombre = $mFila["NOMBRE_100"];
					$mnTipo = $mFila["TIPO_100"];
					$mbActivo = $mFila["ACTIVO_100"];
				}
				else
				{
					$msUsuario = "";
					$msNombre = "";
					$mnTipo = 0;
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

						<div class="form-group row">
							<label for="cboUsuario" class="col-sm-auto col-md-3 col-form-label">Usuario relacionado</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboUsuario" name="cboUsuario">
								<?php
									$msConsulta = "select USUARIO_REL, NOMBRE_002, ACTIVO_002 from UMO002A order by NOMBRE_002";
									$m_cnx_MySQL = fxAbrirConexion();
									$mDatos = $m_cnx_MySQL->prepare($msConsulta);
									$mDatos->execute();
									while ($mFila = $mDatos->fetch())
									{
										$mnValor = rtrim($mFila["USUARIO_REL"]);
										$msTexto = rtrim($mFila["USUARIO_REL"]) . " - " . rtrim($mFila["NOMBRE_002"]);
										$mbActivoUsr = $mFila["ACTIVO_002"];
										if ($msCodigo == "")
										{
											if ($mbActivoUsr == 1)
												echo("<option value='" . $mnValor . "'>" . $msTexto . "</option>");
										}
										else
										{
											if ($msUsuario == "")
											{
												echo("<option value='" . $mnValor . "'>" . $msTexto . "</option>");
												$msUsuario= $mnValor;
											}
											else
											{
												if ($msUsuario == $mnValor)
													echo("<option value='" . $mnValor . "' selected>" . $msTexto . "</option>");
												else
													echo("<option value='" . $mnValor . "'>" . $msTexto . "</option>");
											}
										}
									}
								?>
								</select>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-3 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboTipo" class="col-sm-auto col-md-3 col-form-label">Tipo de docencia</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboTipo" name="cboTipo">
								<?php
								if ($mnTipo == 0)
									echo("<option value='0' selected>De planta</option>");
								else
									echo("<option value='0'>De planta</option>");

								if ($mnTipo == 1)
									echo("<option value='1' selected>Medio tiempo</option>");
								else
									echo("<option value='1'>Medio tiempo</option>");

								if ($mnTipo == 2)
									echo("<option value='2' selected>Horario</option>");
								else
									echo("<option value='2'>Horario</option>");
								?>
								</select>
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