<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxUniversidad.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catUniversidad");
		
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
			if (isset($_POST["txtUniversidad"]))
			{
				$msCodigo = $_POST["txtUniversidad"];
				$msNombre = $_POST["txtNombre"];
				$msMunicipio = $_POST["cboMunicipio"];
				$mnTipo = $_POST["cboTipo"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarUniversidad ($msMunicipio, $msNombre, $mnTipo);
						$msBitacora = $msCodigo . "; " . $msMunicipio . "; " . $msNombre . "; " . $mnTipo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO180A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarUniversidad ($msCodigo, $msMunicipio, $msNombre, $mnTipo);
						$msBitacora = $msCodigo . "; " . $msMunicipio . "; " . $msNombre . "; " . $mnTipo;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO180A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridUniversidad.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveUniversidad(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msMunicipio = $mFila["MUNICIPIO_REL"];
					$msNombre = $mFila["NOMBRE_180"];
					$mnTipo = $mFila["TIPO_180"];
				}
				else
				{
					$msMunicipio = "";
					$msNombre = "";
					$mnTipo = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de Universidad</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catUniversidad" name="catUniversidad" action="catUniversidad.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtUniversidad" class="col-sm-12 col-md-2 col-form-label">Universidad</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtUniversidad" name="txtUniversidad" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboDepartamento" class="col-sm-12 col-md-2 col-form-label">Departamento</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboDepartamento" name="cboDepartamento" onchange="llenaMunicipios(this.value)">
									<?php
										if ($msCodigo!="")
										{
											$msConsulta = "select DEPARTAMENTO_REL from UMO120A where MUNICIPIO_REL = ?";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute([$msMunicipio]);
											$mFila = $mDatos->fetch();
											$msDepartamento = rtrim($mFila["DEPARTAMENTO_REL"]);
										}
										else
											$msDepartamento = "";
										
										$msConsulta = "select DEPARTAMENTO_REL, NOMBRE_110 from UMO110A order by NOMBRE_110";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["DEPARTAMENTO_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_110"]);
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
									?>
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboMunicipio" class="col-sm-12 col-md-2 col-form-label">Municipio</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboMunicipio" name="cboMunicipio">
									<?php
										$msConsulta = "select MUNICIPIO_REL, NOMBRE_120 from UMO120A where DEPARTAMENTO_REL = ? order by NOMBRE_120";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute([$msDepartamento]);
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["MUNICIPIO_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_120"]);
											if ($msCodigo == "")
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											else
											{
												if ($msMunicipio == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msMunicipio = $msValor;
												}
												else
												{
													if ($msMunicipio == $msValor)
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
							<label for="txtNombre" class="col-sm-12 col-md-2 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="cboTipo" class="col-sm-12 col-md-2 col-form-label">Tipo</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" name="cboTipo" id="cboTipo">
								<?php
									switch ($mnTipo)
									{
										case 0:
											echo('<option value="0" selected>Privado</option>');
											echo('<option value="1" >Público</option>');
											echo('<option value="2" >Subvencionado</option>');
											echo('<option value="3" >Otro</option>');
											break;
										case 1:
											echo('<option value="0" >Privado</option>');
											echo('<option value="1" selected>Público</option>');
											echo('<option value="2" >Subvencionado</option>');
											echo('<option value="3" >Otro</option>');
											break;
										case 2:
											echo('<option value="0" >Privado</option>');
											echo('<option value="1" >Público</option>');
											echo('<option value="2" selected>Subvencionado</option>');
											echo('<option value="3" >Otro</option>');
											break;
										case 3:
											echo('<option value="0" >Privado</option>');
											echo('<option value="1" >Público</option>');
											echo('<option value="2" >Subvencionado</option>');
											echo('<option value="3" selected>Otro</option>');
									}
								?>
								</select>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridUniversidad.php';"/>
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

function llenaMunicipios (departamento)
{
    var datos = new FormData();
    datos.append('departamento', departamento);

    $.ajax({
        url: 'funciones/fxDatosColegios.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboMunicipio').innerHTML = response;
        }
    })
}
</script>