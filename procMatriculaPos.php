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
	require_once ("funciones/fxMatriculaPosgrado.php");
	require_once ("funciones/fxEstudiantesPosgrado.php");
	require_once ("funciones/fxCursosPosgrado.php");

	$m_cnx_MySQL = fxAbrirConexion();
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
		$mbPermisoUsuario = fxPermisoUsuario("procMatriculaPos");
		
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
				$msCodigo = $_POST["txtCodMatricula"];
				$msEstudiante = $_POST["cboEstudiante"];
				$msCarrera = $_POST["cboCarrera"];
				$msCurso = $_POST["cboCurso"];
				$mdFecha = $_POST["dtpFecha"];
				$mnAnnoIngreso = $_POST["txnAnnoIngreso"];
				$mnCohorte = $_POST["txnCohorte"];
				$msRecibo = $_POST["txtRecibo"];
				if (isset($_POST["chkTitulo"])) $mbTitulo = 1; else $mbTitulo = 0;
				if (isset($_POST["chkNotas"])) $mbNotas = 1; else $mbNotas = 0;
				if (isset($_POST["chkCedula"])) $mbCedula = 1; else $mbCedula = 0;
				if (isset($_POST["chkCurriculum"])) $mbCurriculum = 1; else $mbCurriculum = 0;
				$mnEstado = $_POST["cboEstado"];

				if ($msCodigo == "")
				{
					$msConsulta = "select MATRICULAPOS_REL from UMO260A where ESTUDIANTEPOS_REL = ? and CURSOPOSGRADO_REL = ? and COHORTE_260 = ?";
					$mDatos = $m_cnx_MySQL->prepare($msConsulta);
					$mDatos->execute([$msEstudiante, $msCurso, $mnCohorte]);
					
					if ($mDatos->rowCount() == 0)
					{
						$msCodigo = fxGuardarMatriculaPos ($msEstudiante, $msCarrera, $msCurso, $mdFecha, $mnAnnoIngreso, $mnCohorte, $msRecibo, $mbTitulo, $mbNotas, $mbCedula, $mbCurriculum, $mnEstado);
						$msBitacora = $msCodigo . "; " . $msEstudiante . "; " . $msCarrera . "; " . $msCurso . "; " . $mdFecha . "; " . $mnAnnoIngreso . "; " . $mnCohorte . "; " . $msRecibo . "; " . $mbTitulo . "; " . $mbNotas . "; " . $mbCedula . "; " . $mbCurriculum . "; " .  $mnEstado;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO260A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						?><script>$.messager.alert('UMOJN', $('#cboEstudiante option:selected').text() + ' ya fue matriculado en ' + $('#cboCurso option:selected').text(),'warning');</script><?php
					}
				}
				else
				{
					fxModificarMatriculaPos ($msCodigo, $msEstudiante, $msCarrera, $msCurso, $mdFecha, $mnAnnoIngreso, $mnCohorte, $msRecibo, $mbTitulo, $mbNotas, $mbCedula, $mbCurriculum, $mnEstado);
					fxBorrarDetMatricula ($msCodigo);
					$msBitacora = $msCodigo . "; " . $msEstudiante . "; " . $msCarrera . "; " . $msCurso . "; " . $mdFecha . "; " . $mnAnnoIngreso . "; " . $mnCohorte . "; " . $msRecibo . "; " . $mbTitulo . "; " . $mbNotas . "; " . $mbCedula . "; " . $mbCurriculum . "; " .  $mnEstado;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO030A", $msCodigo, "", "Modificar", $msBitacora);
				}

				?><meta http-equiv="Refresh" content="0;url=gridMatriculaPos.php"/><?php
			}
			else
			{
				if (isset($_POST["mAccion"]))
					$mAccion = $_POST["mAccion"];
				else
					$mAccion = 0;
				
				if ($mAccion == 0)
				{
					if (isset($_POST["mCodigo"]))
						$msCodigo = $_POST["mCodigo"];
					else
						$msCodigo = "";
				}
				else
					$msCodigo = "";

				$RecordSet = fxDevuelveMatriculaPos (0, $msCodigo);
				$mFila = $RecordSet->fetch();
				if ($msCodigo != "")
				{
					$msEstudiante = $mFila["ESTUDIANTE_REL"];
					$msCarrera = $mFila["CARRERA_REL"];
					$msCurso = $mFila["CURSOPOSGRADO_REL"];
					$mdFecha = $mFila["FECHA_260"];
					$mnAnnoIngreso = $mFila["ANNOINGRESO_260"];
					$mnCohorte = $mFila["COHORTE_260"];
					$msRecibo = $mFila["RECIBO_260"];
					$mbTitulo = $mFila["TITULO_260"];
					$mbNotas = $mFila["NOTAS_260"];
					$mbCedula = $mFila["CEDULA_260"];
					$mbCurriculum = $mFila["CURRICULUM_260"];
					$mnEstado = $mFila["ESTADO_260"];
				}
				else
				{
					if (isset($_POST["mEstudiante"]))
						$msEstudiante = $_POST["mEstudiante"];
					else
						$msEstudiante = "";
					$msCarrera = "";
					$msCurso = "";
					$mdFecha = "";
					$mnAnnoIngreso = date('Y');
					$mnCohorte = 1;
					$msRecibo = "";
					$mbTitulo = 0;
					$mbNotas = 0;
					$mbCedula = 0;
					$mbCurriculum = 0;
					$mnEstado = 2; //Pre-matriculado
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Matrícula de estudiantes de posgrado</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-xs-12 offset-sm-none col-md-10 offset-md-1">
					<form id="procMatriculaPos" name="procMatriculaPos" action="procMatriculaPos.php" method="post" onsubmit="return verificarFormulario()">
						<div class = "form-group row">
							<label for="txtCodMatricula" class="col-sm-12 col-md-3 col-form-label">Código de la Matrícula</label>
							<div class="col-sm-12 col-md-3">
								<?php echo('<input type="text" class="form-control" id="txtCodMatricula" name="txtCodMatricula" value="' . $msCodigo . '" readonly />'); ?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboEstudiante" class="col-sm-12 col-md-3 col-form-label">Estudiante</label>
							<div class="col-sm-12 col-md-7">
								<?php
									if ($msEstudiante == "")
										echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" onchange="llenaGeneracion(this.value)">');
									else
										echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" readonly>');

									$msConsulta = "select ESTUDIANTEPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250 from UMO250A order by APELLIDO1_250, NOMBRE1_250 desc";
									$mDatos = $m_cnx_MySQL->prepare($msConsulta);
									$mDatos->execute();

									while ($mFila = $mDatos->fetch())
									{
										$msValor = trim($mFila["ESTUDIANTEPOS_REL"]);
										$msTexto = trim($mFila["APELLIDO1_250"]);
										if (trim($mFila["APELLIDO2_250"]) != "")
											$msTexto .= " " . $mFila["APELLIDO2_250"];
										$msTexto .= ", " . $mFila["NOMBRE1_250"];
										if (trim($mFila["NOMBRE2_250"]) != "")
											$msTexto .= " " . $mFila["NOMBRE2_250"];

										if ($msEstudiante == "")
										{
											echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											$msEstudiante = $msValor;
										}
										else
										{
											if ($msEstudiante == $msValor)
												echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
											else
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
										}
									}
									echo('</select>');
								?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboCarrera" class="col-sm-12 col-md-3 col-form-label">Carrera</label>
							<div class="col-sm-12 col-md-7">
								<?php
									echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaCursos(this.value)">');

									$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A where POSGRADO_040 = 1 order by NOMBRE_040";
									$mDatos = $m_cnx_MySQL->prepare($msConsulta);
									$mDatos->execute();
									while ($mFila = $mDatos->fetch())
									{
										$msValor = rtrim($mFila["CARRERA_REL"]);
										$msTexto = rtrim($mFila["NOMBRE_040"]);
										if ($msCarrera == "")
										{
											echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											$msCarrera = $msValor;
										}
										else
										{
											if ($msCarrera == $msValor)
												echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
											else
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
										}
									}

									echo('</select>');
								?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboCurso" class="col-sm-12 col-md-3 col-form-label">Curso</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboCurso" name="cboCurso">
									<?php
										$msConsulta = "select CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A where CARRERA_REL = ? and ACTIVO_240 = ? order by NOMBRE_240";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute([$msCarrera, 1]);
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["CURSOPOSGRADO_REL"]);
											$msTexto = "Período " . trim($mFila["NOMBRE_240"]);
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
									?>
								</select>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="dtpFecha" class="col-sm-12 col-md-3 col-form-label">Fecha</label>
							<div class="col-sm-12 col-md-3">
								<?php
									if ($msCodigo == "")
										echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . date("Y-m-d") . '" readonly />');
									else
										echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />');
								?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txnAnnoIngreso" class="col-sm-12 col-md-3 col-form-label">Año de ingreso</label>
							<div class="col-sm-12 col-md-3">
								<?php
									echo('<input type="number" style="text-align:right" class="form-control" id="txnAnnoIngreso" name="txnAnnoIngreso" value="' . $mnAnnoIngreso . '" />');
								?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txnCohorte" class="col-sm-12 col-md-3 col-form-label">Cohorte</label>
							<div class="col-sm-12 col-md-3">
								<?php
									echo('<input type="number" style="text-align:right" class="form-control" id="txnCohorte" name="txnCohorte" value="' . $mnCohorte . '" />');
								?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtRecibo" class="col-sm-12 col-md-3 col-form-label">Recibo</label>
							<div class="col-sm-12 col-md-3">
								<?php
									echo('<input type="text" class="form-control" id="txtRecibo" name="txtRecibo" value="' . $msRecibo . '" />');
								?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label class="col-sm-12 col-md-3 form-label">Documentos entregados</label>
							<div class="col-sm-12 col-md-8">
								<?php
									if ($mbTitulo == 1)
										echo('<input type="checkbox" name="chkTitulo" id="chkTitulo" checked > Diploma de grado<br>');
									else
										echo('<input type="checkbox" name="chkTitulo" id="chkTitulo" > Diploma de grado<br>');

									if ($mbNotas == 1)
										echo('<input type="checkbox" name="chkNotas" id="chkNotas" checked > Notas de grado<br>');
									else
										echo('<input type="checkbox" name="chkNotas" id="chkNotas" > Notas de grado<br>');

									if ($mbCedula == 1)
										echo('<input type="checkbox" name="chkCedula" id="chkCedula" checked > Cédula de identidad<br>');
									else
										echo('<input type="checkbox" name="chkCedula" id="chkCedula" > Cédula de identidad<br>');

									if ($mbCurriculum == 1)
										echo('<input type="checkbox" name="chkCurriculum" id="chkCurriculum" checked > Curriculum');
									else
										echo('<input type="checkbox" name="chkCurriculum" id="chkCurriculum" > Curriculum');
								?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="cboEstado" class="col-sm-12 col-md-3 form-label">Estado</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboEstado" name="cboEstado">
									<?php
										if ($mnEstado == 0)
											echo("<option value='0' selected >Activo</option>");
										else
											echo("<option value='0' >Activo</option>");

										if ($mnEstado == 1)
											echo("<option value='1' selected >Inactivo</option>");
										else
											echo("<option value='1' >Inactivo</option>");

										if ($mnEstado == 2)
											echo("<option value='2' selected >Pre-matriculado</option>");
										else
											echo("<option value='2' >Pre-matriculado</option>");
									?>
								</select>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-none col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridMatriculaPos.php';"/>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php }}} ?>
</body>
</html>
<script>
	function verificarFormulario()
	{
		if(document.getElementById('txnAnnoIngreso').value<=0)
		{
			$.messager.alert('UMOJN','Falta el año de ingreso.','warning');
			return false;
		}
		
		if(document.getElementById('txnCohorte').value<=0)
		{
			$.messager.alert('UMOJN','Falta la cohorte.','warning');
			return false;
		}

		/*
		if(document.getElementById('txtRecibo').value=="" && document.getElementById('cboEstado').value!=2)
		{
			$.messager.alert('UMOJN','Falta el recibo.','warning');
			return false;
		}
		*/
		return true;
	}

	function cambiaEstudiante(estudiante)
	{
		llenaCarrera (estudiante);
	}

	function llenaCarrera (estudiante)
	{
		var datos = new FormData();
		datos.append('estudianteCrr', estudiante);

		$.ajax({
			url: 'funciones/fxDatosMatriculaPos.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboCarrera').value = response;
				llenaCursos(response);
			}
		})
	}

	function llenaCursos (carrera)
	{
		var datos = new FormData();
		datos.append('carrera', carrera);

		$.ajax({
			url: 'funciones/fxDatosMatriculaPos.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboCurso').innerHTML = response;
			}
		})
	}

	window.onload = function() 
	{
		var estudiante = $('#cboEstudiante').val();
		llenaCarrera(estudiante)
	}
</script>