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
	require_once ("funciones/fxClases.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catClases");
		
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
			if (isset($_POST["txtClases"]))
			{
				$msCodigo = $_POST["txtClases"];
                $msModulo = $_POST["cboModulo"];
				$msCurso = $_POST["cboCurso"];
				$msNombre = $_POST["txtNombre"];
				$msContenido = $_POST["txtContenido"];
				$nC = $_POST["txnC"];
                $mnCP = $_POST ["txnCP"];
                $mnTotal = $_POST ["txnTotal"];

				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarClases($msModulo, $msCurso, $msNombre, $msContenido, $nC, $mnCP, $mnTotal);
					$msBitacora = $msCodigo . "; " . $msCurso . "; " . $msCodigo . "; " . $msNombre . "; " . $msContenido . "; " . $nC . ";". $mnCP.";". $mnTotal;

					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO290A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarClases($msCodigo, $msModulo, $msCurso, $msNombre, $msContenido, $nC, $mnCP, $mnTotal);
				    $msBitacora = $msCodigo . "; " . $msCurso . "; " . $msCodigo . "; " . $msNombre . "; " . $msContenido . "; " . $nC . ";". $mnCP.";". $mnTotal;

					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO290A", $msCodigo, "", "Modificar", $msBitacora);
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridClases.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveClases(0, $msCodigo);
					$mFila = $mDatos->fetch();
                    $msCodigo = $mFila["CLASES_REL"];
                    $msModulo = $mFila["MODULO_REL"];
                    $msCurso = $mFila["CURSOS_REL"];
                    $msNombre = $mFila["NOMBRE_290"];
                    $msContenido = $mFila["CONTENIDO_290"];
                    $mnC = $mFila["C_290"];
                    $mnCP = $mFila ["CP_290"];
                    $mnTotal = $mFila ["HRSTOTAL_290"];
				}
				else
				{
					$msCodigo = "";
                    $msModulo = "";
                    $msCurso = "";
                    $msNombre = "";
                    $msContenido = "";
                    $mnC = "";
                    $mnCP = "";
                    $mnTotal = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de clases</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catClases" name="catClases" action="catClases.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtClases" class="col-sm-12 col-md-3 col-form-label">Clases</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtClases" name="txtClases" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboCurso" class="col-sm-12 col-md-3 col-form-label">Curso</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboCurso" name="cboCurso">
									<?php
										$msConsulta = "select CURSOS_REL, NOMBRE_190 from UMO190A order by NOMBRE_190";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["CURSOS_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_190"]);
											if ($msCodigo == "")
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											else
											{
												if ($msCurso == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msCurso = $msValor;
												}
												else
												{
													if ($msCurso == $msValor)
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

                        <div class="form-group row">
							<label for="cboMoludo" class="col-sm-12 col-md-3 col-form-label">Modulo</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboModulo" name="cboModulo">
									<?php
										$msConsulta = "select MODULO_REL, NOMBRE_280 from UMO280A order by NOMBRE_280";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["MODULO_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_280"]);
											if ($msCodigo == "")
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											else
											{
												if ($msModulo == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msModulo = $msValor;
												}
												else
												{
													if ($msModulo == $msValor)
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

						<div class="form-group row">
							<label for="txtContenido" class="col-sm-12 col-md-3 col-form-label">Descripción general</label>
							<div class="col-sm-12 col-md-7">
								<?php echo('<textarea class="form-control" id="txtContenido" name="txtContenido" rows="5" maxlength="300">' . $msContenido . '</textarea>'); ?>
							</div>
						</div>

                        <div class = "form-group row">
							<label for="txnC" class="col-sm-12 col-md-3 col-form-label">C</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="number" class="form-control" id="txnC" name="txnC" value="' . $mnC . '" />'); ?>
							</div>
						</div>

                        <div class = "form-group row">
							<label for="txnCP" class="col-sm-12 col-md-3 col-form-label">CP</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="number" class="form-control" id="txnCP" name="txnCP" value="' . $mnCP . '" />'); ?>
							</div>
						</div>

                        <div class = "form-group row">
							<label for="txnCP" class="col-sm-12 col-md-3 col-form-label">Horas total</label>
							<div class="col-sm-12 col-md-3">
							<input type="number" class="form-control" id="txnTotal" name="txnTotal" value="<?php echo $mnTotal; ?>" />

							</div>
						</div>


						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridClases.php';"/>
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