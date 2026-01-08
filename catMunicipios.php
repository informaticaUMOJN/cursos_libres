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
	require_once ("funciones/fxMunicipios.php");
	$Registro = fxVerificaUsuario();
	$m_cnx_MySQL = fxAbrirConexion();

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
		$mbPermisoUsuario = fxPermisoUsuario("catMunicipios");
		
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
			if (isset($_POST["txtMunicipio"]))
			{
				$msCodigo = $_POST["txtMunicipio"];
				$msCarrera = $_POST["cboDepartamento"];
				$msNombre = $_POST["txtNombre"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarMunicipio($msDepartamento, $msNombre);
						$msBitacora = $msCodigo . "; " . $msDepartamento . "; " . $msNombre;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO120A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarAsignatura ($msCodigo, $msDepartamento, $msNombre);
						$msBitacora = $msCodigo . "; " . $msDepartamento . "; " . $msNombre;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO120A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridMunicipios.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveMunicipio(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msDepartamento = $mFila["DEPARTAMENTO_REL"];
					$msNombre = $mFila["NOMBRE_120"];
				}
				else
				{
					$msDepartamento = "";
					$msNombre = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de municipios</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catMunicipios" name="catMunicipios" action="catMunicipios.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtMunicipio" class="col-sm-12 col-md-3 col-form-label">Municipio</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtMunicipio" name="txtMunicipio" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboDepartamento" class="col-sm-12 col-md-3 col-form-label">Departamento</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboDepartamento" name="cboDepartamento">
									<?php
										$msConsulta = "select DEPARTAMENTO_REL, NOMBRE_110 from UMO110A order by NOMBRE_110";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["DEPARTAMENTO_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_110"]);
											if ($msCodigo == "")
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											else
											{
												if ($msDepartamento == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msDepartamento = $msValor;
												}
												else
												{
													if ($msDepartamento == $msValor)
														echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
													else
														echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
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

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridMunicipios.php';"/>
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