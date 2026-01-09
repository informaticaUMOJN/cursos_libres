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
	require_once ("funciones/fxAlumnos.php");

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
		$mbPermisoUsuario = fxPermisoUsuario("catAlumnos");
		
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
			if (isset($_POST["txtAlumno"]))
			{
				$msCodigo        = $_POST["txtAlumno"];
				$msFechaIns      = $_POST["dtpFechaIns"];
				$msUniversidad   = $_POST["cboUniversidad"];
				$msNombres       = $_POST["txtNombres"];
				$msApellidos     = $_POST["txtApellidos"];
				$msFechaNac      = $_POST["dtpFechaNac"];
				$msNacionalidad = $_POST["txtNacionalidad"] ?? "";
				$msMunicipio     = $_POST["cboMunicipio"];
				$msCedula        = $_POST["txtCedula"];
				$msDeficiencia   = $_POST["txtDeficiencia"];
				$msSexo          = $_POST["optSexo"];
				$mnEstadoCivil   = $_POST["cboEstadoCivil"];
				$mnHijos         = $_POST["txnHijos"];
				$mbDiscapacidad  = $_POST["optDiscapacidad"];
				$mnNivelEstudio  = $_POST["cboNivelEstudio"];
				$msColegio       = $_POST["cboColegio"];
				$msCurso         = $_POST["cboCurso"];
				$msTelefono      = $_POST["txtTelefono"];
				$msCelular       = $_POST["txtCelular"];
				$msEmail         = $_POST["txtEmail"];
				$mbOtroIdioma = isset($_POST["optOtroIdioma"]) ? intval($_POST["optOtroIdioma"]) : 0;

				$msIdioma        = $_POST["txtIdioma"];
				$msDominioIdioma = $_POST["txtDominioIdioma"];
				$msDireccion     = $_POST["txtDireccion"];
				$mnMedio         = $_POST["cboMedio"];
				$mbLaboral       = $_POST["optLaboral"];
				$msOcupacion     = $_POST["txtOcupacion"];
				$mnSector        = $_POST["optSector"];
				$mnIngresoMensual= $_POST["txnSalario"];
				$msEntidad       = $_POST["optEntidad"];
				$msNombreRef     = $_POST["txtNombreRef"];
				$msCedulaRef     = $_POST["txtCedulaRef"];
				$msCelularRef    = $_POST["txtCelularRef"];
				$msDireccionRef  = $_POST["txtDireccionRef"];

				try {
					if ($msCodigo == "") {
						$msCodigo = fxGuardarAlumnos(
							$msFechaIns, $msUniversidad, $msNombres, $msApellidos, $msFechaNac, $msNacionalidad, $msMunicipio,
							$msCedula, $msDeficiencia, $msSexo, $mnEstadoCivil, $mnHijos, $mbDiscapacidad, $mnNivelEstudio,
							$msColegio, $msCurso, $msTelefono, $msCelular, $msEmail, $mbOtroIdioma,$msIdioma, $msDominioIdioma, $msDireccion,
							$mnMedio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad,
							$msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef
						);
						$msBitacora = $msCodigo . "; " . $msFechaIns . "; " . $msUniversidad . "; " . $msNombres . "; " . $msApellidos . "; " . $msFechaNac . "; " . $msNacionalidad . "; " . $msMunicipio . "; " . $msCedula . "; " . $msDeficiencia . "; " . $msSexo . "; " . $mnEstadoCivil . "; " . $mnHijos . "; " . $mbDiscapacidad . "; " . $mnNivelEstudio . "; " . $msColegio . "; " . $msCurso . "; " . $msTelefono . "; " . $msCelular . "; " . $msEmail . "; ".$mbOtroIdioma.";" . $msIdioma . "; " . $msDominioIdioma . "; " . $msDireccion . "; " . $mnMedio . "; " . $mbLaboral . "; " . $msOcupacion . "; " . $mnSector . "; " . $mnIngresoMensual . "; " . $msEntidad . "; " . $msNombreRef . "; " . $msCedulaRef . "; " . $msCelularRef . "; " . $msDireccionRef;

						fxAgregarBitacora($_SESSION["gsUsuario"], "UMO200A", $msCodigo, "", "Agregar", $msBitacora);
						echo json_encode(["status"=>"ok","msg"=>"Alumno agregado","codigo"=>$msCodigo]);
					} else {
						fxModificarAlumnos(
							$msCodigo, $msFechaIns, $msUniversidad, $msNombres, $msApellidos, $msFechaNac, $msNacionalidad, $msMunicipio,
							$msCedula, $msDeficiencia, $msSexo, $mnEstadoCivil, $mnHijos, $mbDiscapacidad, $mnNivelEstudio,
							$msColegio, $msCurso, $msTelefono, $msCelular, $msEmail, $mbOtroIdioma,$msIdioma, $msDominioIdioma, $msDireccion,
							$mnMedio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad,
							$msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef
						);

						$msBitacora = $msCodigo . "; " . $msFechaIns . "; " . $msUniversidad . "; " . $msNombres . "; " . $msApellidos . "; " . $msFechaNac . "; " . $msNacionalidad . "; " . $msMunicipio . "; " . $msCedula . "; " . $msDeficiencia . "; " . $msSexo . "; " . $mnEstadoCivil . "; " . $mnHijos . "; " . $mbDiscapacidad . "; " . $mnNivelEstudio . "; " . $msColegio . "; " . $msCurso . "; " . $msTelefono . "; " . $msCelular . "; " . $msEmail . "; ".$mbOtroIdioma.";" . $msIdioma . "; " . $msDominioIdioma . "; " . $msDireccion . "; " . $mnMedio . "; " . $mbLaboral . "; " . $msOcupacion . "; " . $mnSector . "; " . $mnIngresoMensual . "; " . $msEntidad . "; " . $msNombreRef . "; " . $msCedulaRef . "; " . $msCelularRef . "; " . $msDireccionRef;

						echo json_encode(["status"=>"ok","msg"=>"Alumno actualizado","codigo"=>$msCodigo]);
						fxAgregarBitacora($_SESSION["gsUsuario"], "UMO200A", $msCodigo, "", "Modificar", $msBitacora);
					}
				} catch (Exception $e) {
					echo json_encode(["status"=>"error","msg"=>$e->getMessage()]);
				}
				exit;
				
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";

				if ($msCodigo != "")
				{
					$objRecordSet = fxDevuelveAlumnos(0, $msCodigo);
					$mFila = $objRecordSet->fetch();
					$msFechaIns      = $mFila["FECHAINS_200"];
					$msUniversidad   = $mFila["UNIVERSIDAD_REL"];
					$msNombres       = htmlentities($mFila["NOMBRES_200"]);
					$msApellidos     = htmlentities($mFila["APELLIDOS_200"]);
					$msFechaNac      = $mFila["FECHANAC_200"];
					$msNacionalidad  = $mFila["NACIONALIDAD_200"];
					$msMunicipio     = $mFila["MUNICIPIO_REL"];
					$msCedula        = $mFila["CEDULA_200"];
					$msDeficiencia   = htmlentities($mFila["DEFICIENCIA_200"]);
					$msSexo          = $mFila["SEXO_200"];
					$mnEstadoCivil   = $mFila["ESTADOCIVIL_200"];
					$mnHijos         = $mFila["HIJOS_200"];
					$mbDiscapacidad  = $mFila["DISCAPACIDAD_200"];
					$mnNivelEstudio  = $mFila["NIVELESTUDIOS_200"];
					$msColegio       = $mFila["COLEGIO_REL"];
					$msCurso         = $mFila["CURSOS_REL"];
					$msTelefono      = $mFila["TELEFONO_200"];
					$msCelular       = $mFila["CELULAR_200"];
					$msEmail         = $mFila["EMAIL_200"];
					$mbOtroIdioma 	 = $mFila["OTROIDIOMA_200"];
					$msIdioma        = $mFila["IDIOMA_200"];
					$msDominioIdioma = $mFila["DOMINIOIDIOMA_200"];
					$msDireccion     = htmlentities($mFila["DIRECCION_200"]);
					$mnMedio         = $mFila["MEDIO_200"];
					$mbLaboral       = $mFila["CONDICIONLAB_200"];
					$msOcupacion     = $mFila["OCUPACION_200"];
					$mnSector        = $mFila["SECTOR_200"];
					$mnIngresoMensual= $mFila["INGRESOMENSUAL_200"];
					$msEntidad       = $mFila["ENTIDADLAB_200"];
					$msNombreRef     = $mFila["NOMBREREF_200"];
					$msCedulaRef     = $mFila["CEDULAREFERENTE_200"];
					$msCelularRef    = $mFila["CELULARREFERENTE_200"];
					$msDireccionRef  = htmlentities($mFila["DIRECCIONREF_200"]);
				}
				else
				{
					$msFechaIns      = date('Y-m-d');
					$msUniversidad   = "";
					$msNombres       = "";
					$msApellidos     = "";
					$msFechaNac      = date('Y-m-d');
					$msNacionalidad  = "";
					$msMunicipio     = "";
					$msCedula        = "";
					$msDeficiencia   = "";
					$msSexo          = "M";
					$mnEstadoCivil   = 0;
					$mnHijos         = 0;
					$mbDiscapacidad  = 0;
					$mnNivelEstudio  = 0;
					$msColegio       = "";
					$msCurso         = "";
					$msTelefono      = "";
					$msCelular       = "";
					$msEmail         = "";
					$mbOtroIdioma = 0;
					$msIdioma        = "";
					$msDominioIdioma = "";
					$msDireccion     = "";
					$mnMedio         = 0;
					$mbLaboral       = 0;
					$msOcupacion     = "";
					$mnSector        = "";
					$mnIngresoMensual= 0;
					$msEntidad       = "";
					$msNombreRef     = "";
					$msCedulaRef     = "";
					$msCelularRef    = "";
					$msDireccionRef  = "";
				}
?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
                <div class="col-xs-12 col-md-12">
					<form name="catAlumnos" id="catAlumnos" enctype="multipart/form-data">
						<div class = "row">
							<div class="col-auto col-md-11">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAlumnos.php';"/>
							</div>
						</div>

						<div class="easyui-tabs tabs-narrow" style="width:100%;height:auto">
							<!--Inicio del DIV de Tabs-->
							<div title="Generales" style="padding-left: 20px; padding-top: 10px">
								<div class="col-sm-auto col-md-12">
									<div class = "form-group row">
										<label for="txtAlumno" class="col-sm-12 col-md-3 form-label">Estudiante</label>
										<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtAlumno" name="txtAlumno" value="' . $msCodigo . '" readonly />'); 
										?>
										</div>
									</div>
										<div class = "form-group row">
										<label for="dtpFechaIns" class="col-sm-12 col-md-3 form-label">Fecha de inscripción</label>
										<div class="col-sm-12 col-md-2">
										<?php echo('<input type="date" class="form-control" id="dtpFechaIns" name="dtpFechaIns" value="' . $msFechaIns . '" />'); ?>
										</div>
									</div>
							
									<div class="form-group row">
										<label for="cboCurso" class="col-sm-12 col-md-3 col-form-label">Curso</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control" id="cboCurso" name="cboCurso">
												<?php
												$msCurso = isset($msCurso) ? trim($msCurso) : "";
												$msConsulta = "SELECT CURSOS_REL, NOMBRE_190 FROM UMO190A ORDER BY NOMBRE_190";
												$mDatos = $m_cnx_MySQL->prepare($msConsulta);
												$mDatos->execute();
												while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["CURSOS_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_190"]);
														if ($msCurso == ""){
																$selected = ($msValor == $msValor) ? "selected" : "";
																$msCurso = $msValor; // asignamos el valor iniciaL	
															} else {
																$selected = ($msCurso == $msValor) ? "selected" : "";
															}
															echo "<option value='$msValor' $selected>$msTexto</option>";										
													}
												?>
											</select>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNombres" class="col-sm-12 col-md-3 form-label">Nombres</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNombres" name="txtNombres" value="' . $msNombres . '"" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtApellidos" class="col-sm-12 col-md-3 form-label">Apellidos</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtApellidos" name="txtApellidos" value="' . $msApellidos . '"" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="dtpFechaNac" class="col-sm-12 col-md-3 form-label">Fecha de nacimiento</label>
										<div class="col-sm-12 col-md-2">
										<?php echo('<input type="date" class="form-control" id="dtpFechaNac" name="dtpFechaNac" value="' . $msFechaNac . '" onchange="calcularEdad()" />'); ?>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="txtEdad" class="col-sm-12 col-md-3 form-label">Edad</label>
										<div class="col-sm-12 col-md-2">
											<input type="text" class="form-control" id="txtEdad" name="txtEdad" value="" disabled />
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNacionalidad" class="col-sm-12 col-md-3 form-label">Nacionalidad</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNacionalidad" name="txtNacionalidad" value="' . $msNacionalidad . '" />'); ?>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboDepartamento" class="col-sm-12 col-md-3 col-form-label">Departamento</label>
										<div class="col-sm-12 col-md-4">
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
																else{
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
										<label for="cboMunicipio" class="col-sm-12 col-md-3 col-form-label">Municipio</label>
										<div class="col-sm-12 col-md-4">
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
										<label for="txtCedula" class="col-sm-12 col-md-3 form-label">Cédula</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtCedula" name="txtCedula" maxlength="20" value="' . $msCedula . '" />'); ?>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="txtDeficiencia" class="col-sm-12 col-md-3 form-label">Deficiencia</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtDeficiencia" name="txtDeficiencia" maxlength="20" value="' . $msDeficiencia . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="optSexo" class="col-sm-12 col-md-3 form-label">Sexo</label>
										<div class="col-sm-12 col-md-3">
											<div class = "radio">
											<?php
												if ($msSexo == "M")
													echo('<input type="radio" id="optSexo1" name="optSexo" value="M" checked="checked" /> Masculino &emsp;');
												else
													echo('<input type="radio" id="optSexo1" name="optSexo" value="M" /> Masculino &emsp;');

												if ($msSexo == "F")
													echo('<input type="radio" id="optSexo2" name="optSexo" value="F" checked="checked" /> Femenino');
												else
													echo('<input type="radio" id="optSexo2" name="optSexo" value="F" /> Femenino');
											?>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboEstadoCivil" class="col-sm-12 col-md-3 col-form-label">Estado civil</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboEstadoCivil" name="cboEstadoCivil">
												<?php
													if ($mnEstadoCivil == 0)
														echo("<option value='0' selected>Soltero</option>");
													else
														echo("<option value='0'>Soltero</option>");

													if ($mnEstadoCivil == 1)
														echo("<option value='1' selected>Casado</option>");
													else
														echo("<option value='1'>Casado</option>");

													if ($mnEstadoCivil == 2)
														echo("<option value='2' selected>Unión de hecho</option>");
													else
														echo("<option value='2'>Unión de hecho</option>");

													if ($mnEstadoCivil == 3)
														echo("<option value='3' selected>Viudo</option>");
													else
														echo("<option value='3'>Viudo</option>");
												?>
											</select>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="txnHijos" class="col-sm-12 col-md-3 form-label">Hijos</label>
										<div class="col-sm-12 col-md-3">
										<?php echo('<input type="number" class="form-control" id="txnHijos" name="txnHijos" value="' . $mnHijos . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="optDiscapacidad" class="col-sm-12 col-md-3 form-label">Discapacidad</label>
										<div class="col-sm-12 col-md-3">
											<div class = "radio">
											<?php
												if ($mbDiscapacidad == 1)
													echo('<input type="radio" id="optDiscapacidad1" name="optDiscapacidad" value="1" checked="checked" /> Si &emsp;');
												else
													echo('<input type="radio" id="optDiscapacidad1" name="optDiscapacidad" value="1" /> Si &emsp;');

												if ($mbDiscapacidad == 0)
													echo('<input type="radio" id="optDiscapacidad2" name="optDiscapacidad" value="0" checked="checked" /> No');
												else
													echo('<input type="radio" id="optDiscapacidad2" name="optDiscapacidad" value="0" /> No');
											?>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboNivelEstudio" class="col-sm-12 col-md-3 col-form-label">Nivel de estudios</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboNivelEstudio" name="cboNivelEstudio">
												<?php

												if ($mnNivelEstudio == 0)
														echo("<option value='0' selected>Primaria</option>");
													else
														echo("<option value='0'>Primaria</option>");
													if ($mnNivelEstudio == 1)
														echo("<option value='1' selected>Bachiller</option>");
													else
														echo("<option value='1'>Bachiller</option>");

													if ($mnNivelEstudio == 2)
														echo("<option value='2' selected>Técnico</option>");
													else
														echo("<option value='2'>Técnico</option>");

													if ($mnNivelEstudio == 3)
														echo("<option value='3' selected>Licenciado</option>");
													else
														echo("<option value='3'>Licenciado</option>");

													if ($mnNivelEstudio == 4)
														echo("<option value='4' selected>Ingeniero</option>");
													else
														echo("<option value='4'>Ingeniero</option>");

													if ($mnNivelEstudio == 5)
														echo("<option value='5' selected>Doctor</option>");
													else
														echo("<option value='5'>Doctor</option>");
												?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label for="cboColegio" class="col-sm-12 col-md-3 col-form-label">Colegio de procedencia</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control" id="cboColegio" name="cboColegio">
												<?php
													$msConsulta = "select COLEGIO_REL, NOMBRE_020 from UMO020A order by NOMBRE_020";
													$mDatos = $m_cnx_MySQL->prepare($msConsulta);
													$mDatos->execute();
													while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["COLEGIO_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_020"]);
														if ($msCodigo == "")
															echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
														else
														{
															if ($msColegio == "")
															{
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
																$msColegio = $msValor;
															}
															else
															{
																if ($msColegio == $msValor)
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
										<label for="cboUniversidad" class="col-sm-12 col-md-3 col-form-label">Universidad de procedencia</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control" id="cboUniversidad" name="cboUniversidad">
												<option value=""></option>
												<?php
													$msConsulta = "select UNIVERSIDAD_REL, NOMBRE_180 from UMO180A order by NOMBRE_180";
													$mDatos = $m_cnx_MySQL->prepare($msConsulta);
													$mDatos->execute();
													while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["UNIVERSIDAD_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_180"]);
														if ($msCodigo == "")
															echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
														else
														{
															if ($msUniversidad == "")
															{
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
																$msUniversidad = $msValor;
															}
															else
															{
																if ($msUniversidad == $msValor)
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
										<label for="txtTelefono" class="col-sm-12 col-md-3 form-label">Teléfono</label>
										<div class="col-sm-12 col-md-3">
										<?php echo('<input type="text" class="form-control" id="txtTelefono" name="txtTelefono" maxlength="20" value="' . $msTelefono . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCelular" class="col-sm-12 col-md-3 form-label">Celular</label>
										<div class="col-sm-12 col-md-3">
										<?php echo('<input type="text" class="form-control" id="txtCelular" name="txtCelular" maxlength="20" value="' . $msCelular . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtEmail" class="col-sm-12 col-md-3 form-label">Correo electrónico</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtEmail" name="txtEmail" maxlength="100" value="' . $msEmail . '" />'); ?>
										</div>
									</div>

										<div class = "form-group row">
										<label for="optOtroIdioma" class="col-sm-12 col-md-3 form-label">Habla otro idioma</label>
										<div class="col-sm-12 col-md-3">
											<div class = "radio">
											<?php
												if ($mbOtroIdioma == 1)
													echo('<input type="radio" id="optOtroIdioma1" name="optOtroIdioma" value="1" checked="checked" onchange="fxOptIdioma()" /> Si &emsp;');
												else
													echo('<input type="radio" id="optOtroIdioma1" name="optOtroIdioma" value="1" onchange="fxOptIdioma()" /> Si &emsp;');

												if ($mbOtroIdioma == 0)
													echo('<input type="radio" id="optOtroIdioma2" name="optOtroIdioma" value="0" checked="checked" onchange="fxOptIdioma()" /> No');
												else
													echo('<input type="radio" id="optOtroIdioma2" name="optOtroIdioma" value="0" onchange="fxOptIdioma()" /> No');
											?>
											</div>
										</div>
									</div>


									<div class = "form-group row">
										<label for="txtIdioma" class="col-sm-12 col-md-3 form-label">Idioma(s) que habla</label>
										<div class="col-sm-12 col-md-4">
										<?php
											if ($mbOtroIdioma == 1)
												echo('<input type="text" class="form-control" id="txtIdioma" name="txtIdioma" value="' . $msIdioma . '" />');
											else
												echo('<input type="text" class="form-control" id="txtIdioma" name="txtIdioma" value="" disabled />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtDominioIdioma" class="col-sm-12 col-md-3 form-label">Dominio del(los) Idioma(s)</label>
										<div class="col-sm-12 col-md-6">
										<?php 
											if ($mbOtroIdioma == 1)
												echo('<input type="text" class="form-control" id="txtDominioIdioma" name="txtDominioIdioma" value="' . $msDominioIdioma . '" />');
											else
												echo('<input type="text" class="form-control" id="txtDominioIdioma" name="txtDominioIdioma" value="" disabled />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtDireccion" class="col-sm-12 col-md-3 form-label">Dirección</label>
										<div class="col-sm-12 col-md-7">
										<?php echo('<textarea class="form-control" id="txtDireccion" name="txtDireccion" rows="3">' . $msDireccion . '</textarea>'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="cboMedio" class="col-sm-12 col-md-3 form-label">Medio por el cual se enteró</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboMedio" name="cboMedio">
												<?php
													if ($mnMedio == 1)
														echo("<option value='1' selected>Visita al colegio</option>");
													else
														echo("<option value='1'>Visita al colegio</option>");

													if ($mnMedio == 2)
														echo("<option value='2' selected>Facebook</option>");
													else
														echo("<option value='2'>Facebook</option>");

													if ($mnMedio == 3)
														echo("<option value='3' selected>Instagram</option>");
													else
														echo("<option value='3'>Instagram</option>");

													if ($mnMedio == 4)
														echo("<option value='4' selected>Clínica ODM</option>");
													else
														echo("<option value='4'>Clínica ODM</option>");

													if ($mnMedio == 5)
														echo("<option value='5' selected>Radio</option>");
													else
														echo("<option value='5'>Radio</option>");

													if ($mnMedio == 6)
														echo("<option value='6' selected>Estudiante UMO-JN</option>");
													else
														echo("<option value='6'>Estudiante UMO-JN</option>");

													if ($mnMedio == 7)
														echo("<option value='7' selected>Publicidad en la Calle</option>");
													else
														echo("<option value='7'>Publicidad en la Calle</option>");

													if ($mnMedio == 8)
														echo("<option value='8' selected>Por un amigo o familiar</option>");
													else
														echo("<option value='8'>Por un amigo o familiar</option>");

													if ($mnMedio == 9)
														echo("<option value='9' selected>Feria de Salud</option>");
													else
														echo("<option value='9'>Feria de Salud</option>");

													if ($mnMedio == 10)
														echo("<option value='10' selected>Tik Tok</option>");
													else
														echo("<option value='10'>Tik Tok</option>");

													if ($mnMedio == 11)
														echo("<option value='11' selected>Clínica PAMIC</option>");
													else
														echo("<option value='11'>Clínica PAMIC</option>");

													if ($mnMedio == 12)
														echo("<option value='12' selected>Televisión</option>");
													else
														echo("<option value='12'>Televisión</option>");

													if ($mnMedio == 13)
														echo("<option value='13' selected>Búsqueda en la web</option>");
													else
														echo("<option value='13'>Búsqueda en la web</option>");

													if ($mnMedio == 14)
														echo("<option value='14' selected>Feria universitaria</option>");
													else
														echo("<option value='14'>Feria universitaria</option>");

													if ($mnMedio == 15)
														echo("<option value='15' selected>Sitio web UMO-JN</option>");
													else
														echo("<option value='15'>Sitio web UMO-JN</option>");

													if ($mnMedio == 16)
														echo("<option value='16' selected>Funcionario UMO-JN</option>");
													else
														echo("<option value='16'>Funcionario UMO-JN</option>");

													if ($mnMedio == 17)
														echo("<option value='17' selected>WhatsApp</option>");
													else
														echo("<option value='17'>WhatsApp</option>");

													if ($mnMedio == 18)
														echo("<option value='18' selected>Cursos libres</option>");
													else
														echo("<option value='18'>Cursos libres</option>");

													if ($mnMedio == 19)
														echo("<option value='19' selected>Otros</option>");
													else
														echo("<option value='19'>Otros</option>");
												?>
											</select>
										</div>
									</div>

						
								</div>
							</div>
							<!--Fin del DIV de Tab GENERALES-->

							<!--Inicio del DIV de Tab LABORAL-->
							<div title="Situación laboral" style="padding-left: 20px; padding-top: 10px">
								<div class = "form-group row">
									<label for="optLaboral" class="col-sm-12 col-md-3 form-label">¿Está laborando?</label>
									<div class="col-sm-12 col-md-3">
										<div class = "radio">
										<?php
											if ($mbLaboral == 1)
												echo('<input type="radio" id="optLaboral1" name="optLaboral" value="1" checked="checked" onchange="fxOptLaboral()" /> Empleado &emsp;');
											else
												echo('<input type="radio" id="optLaboral1" name="optLaboral" value="1" onchange="fxOptLaboral()" /> Empleado &emsp;');

											if ($mbLaboral == 0)
												echo('<input type="radio" id="optLaboral2" name="optLaboral" value="0" checked="checked" onchange="fxOptLaboral()" /> Desempleado');
											else
												echo('<input type="radio" id="optLaboral2" name="optLaboral" value="0" onchange="fxOptLaboral()" /> Desempleado');
										?>
										</div>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtOcupacion" class="col-sm-12 col-md-3 form-label">Ocupación/Profesión</label>
									<div class="col-sm-12 col-md-6">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="text" class="form-control" id="txtOcupacion" name="txtOcupacion" value="' . $msOcupacion . '" />');
										else
											echo('<input type="text" class="form-control" id="txtOcupacion" name="txtOcupacion" value="" disabled />');
									?>
									</div>
								</div>

								<div class="form-group row">
									<label for="optSector" class="col-sm-12 col-md-3 col-form-label">Sector</label>
									<div class="col-sm-12 col-md-7">
										<select class="form-control" id="optSector" name="optSector">
											<?php
											if ($msEntidad == 0)
													echo("<option value='0' selected>No aplica</option>");
												else
													echo("<option value='0'>No aplica</option>");
													if ($mnSector == 1)
														echo("<option value='1' selected>Agricultura, ganadería, caza y silvicultura</option>");
													else
														echo("<option value='1' >Agricultura, ganadería, caza y silvicultura</option>");
													if ($mnSector == 2)
														echo("<option value='2' selected> Pesca</option>");
													else
														echo("<option value='2' >Pesca</option>");
													if ($mnSector == 3)
														echo("<option value='3' selected>Minas y canteras</option>");
													else
														echo("<option value='3' >Minas y canteras</option>");
													if ($mnSector == 4)
														echo("<option value='4' selected>Industria manufacturas</option>");
													else
														echo("<option value='4' > Industria manufacturas</option>");
													if ($mnSector == 5)
														echo("<option value='5' selected>Electricidad, gas y agua</option>");
													else
														echo("<option value='5'>Electricidad, gas y agua</option>");
													if ($mnSector == 6)
														echo("<option value='6' selected>Construcción</option>");
													else
														echo("<option value='6' >Construcción</option>");
													if ($mnSector == 7)
														echo("<option value='7' selected>Comercio</option>");
													else
														echo("<option value='7' > Comercio</option>");
													if ($mnSector == 8)
														echo("<option value='8' selected> Hoteles y restaurantes</option>");
													else
														echo("<option value='8'> Hoteles y restaurantes</option>");
													 if ($mnSector == 9)
														echo("<option value='9' selected> Transporte, almacenamiento y comunicación</option>");
													else
														echo("<option value='9'> Transporte, almacenamiento y comunicación</option>");
													if ($mnSector == 10)
														echo("<option value='10' selected> Actividades inmobiliarias, empresariales y de alquiler</option>");
													else
														echo("<option value='10'> Actividades inmobiliarias, empresariales y de alquiler</option>");
													if ($mnSector == 11)
														echo("<option value='11' selected> Administración pública y defensa, planes de seguridad social</option>");
													else
														echo("<option value='11' > Administración pública y defensa, planes de seguridad social</option>");
													if ($mnSector == 12)
														echo("<option value='12' selected> Enseñanza</option>");
													else
														echo("<option value='12' > Enseñanza</option>");
													if ($mnSector == 13)
														echo("<option value='13' selected> Servicios sociales y de salud</option>");
													else
														echo("<option value='13'> Servicios sociales y de salud</option>");
													if ($mnSector == 14)
														echo("<option value='14' selected> Otros servicios comunales, sociales y personales</option>");
													else
														echo("<option value='14' > Otros servicios comunales, sociales y personales</option>");
													if ($mnSector == 15)
														echo("<option value='15' selected>Hogares privados con servicio doméstico</option>");
													else
														echo("<option value='15' > Hogares privados con servicio doméstico</option>");
													if ($mnSector == 16)
														echo("<option value='16' selected>Organizaciones y órganos extraterritoriales</option>");
													else
														echo("<option value='16' > Organizaciones y órganos extraterritoriales</option>");
											?>		
										</select>
									</div>
								</div>
								
								<div class = "form-group row">
									<label for="txnSalario" class="col-sm-12 col-md-3 form-label">Ingreso mensual</label>
									<div class="col-sm-10 col-md-2">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="number" class="form-control" id="txnSalario" name="txnSalario" value="' . $mnIngresoMensual . '" />');
										else
											echo('<input type="number" class="form-control" id="txnSalario" name="txnSalario" value="0" disabled />');
									?>
									</div>	
								</div>

								<div class="form-group row">
									<label for="optEntidad" class="col-sm-12 col-md-3 col-form-label">Entidad Laboral</label>
									<div class="col-sm-12 col-md-2">
										<select class="form-control" id="optEntidad" name="optEntidad">
												<?php
												if ($msEntidad == 0)
													echo("<option value='0' selected>No aplica</option>");
												else
													echo("<option value='0'>No aplica</option>");
												if ($msEntidad == 1)
													echo("<option value='1' selected>Publica</option>");
												else
													echo("<option value='1'>Publica</option>");
												if ($msEntidad == 2)
													echo("<option value='2' selected>Privada</option>");
												else
													echo("<option value='2'>Privada</option>");
												if ($msEntidad == 3)
													echo("<option value='3' selected>Cuenta propia</option>");
												else
													echo("<option value='3'>Cuenta propia</option>");
												?>	
										</select>
									</div>
								</div>
							</div><!--Fin del DIV de Tab LABORAL-->
							
							<div title="Referentes" style="padding-left: 20px; padding-top: 10px">
								<!--Inicio del DIV de Tab SOPORTE-->
								<div class="col-xs-auto col-md-12">
									<!--Inicio del DIV Columna SOPORTE-->
									<div style="height:auto; padding-top:1%; padding-bottom:2%"></div>
									<div id="dvDocumentos" style="height:300px; padding-top:1%; padding-bottom:2%">
										<div class = "form-group row">
											<label for="txtNombreRef" class="col-sm-12 col-md-3 form-label">Nombre del referente</label>
											<div class="col-sm-12 col-md-4">
												<?php echo('<input type="text" class="form-control" id="txtNombreRef" name="txtNombreRef" value="' . $msNombreRef . '"" />'); ?>
											</div>
										</div>
										
										<div class = "form-group row">
											<label for="txtCedulaRef" class="col-sm-12 col-md-3 form-label">Cedula del referente</label>
											<div class="col-sm-12 col-md-4">
												<?php echo('<input type="text" class="form-control" id="txtCedulaRef" name="txtCedulaRef" value="' . $msCedulaRef . '"" />'); ?>
											</div>
										</div>
									
										<div class = "form-group row">
											<label for="txtCelularRef" class="col-sm-12 col-md-3 form-label">Celular del referente</label>
											<div class="col-sm-12 col-md-3">
												<?php echo('<input type="text" class="form-control" id="txtCelularRef" name="txtCelularRef" maxlength="20" value="' . $msCelularRef . '" />'); ?>
											</div>
										</div>
										
										<div class = "form-group row">
											<label for="txtDireccionRef" class="col-sm-12 col-md-3 form-label">Dirección</label>
											<div class="col-sm-12 col-md-7">
												<?php echo('<textarea class="form-control" id="txtDireccionRef" name="txtDireccionRef" rows="3">' . $msDireccionRef . '</textarea>'); ?>
											</div>
										</div>
									</div>
								</div><!--aqui finaliza del DIV Columna SOPORTE-->
							</div><!--aqui finaliza del DIV de Tab SOPORTE-->
							
							<div title="Documentos" style="padding-left: 20px; padding-top: 10px"">
								<!--Inicio del DIV de Tab SOPORTE-->
								<div class="col-xs-auto col-md-12">
									<!--Inicio del DIV Columna SOPORTE-->
									<div style="height:auto; padding-top:1%; padding-bottom:2%">
										<table width="100%">
											<tr>
												<td valign="top" style="width: 15%;">Tipo de documento</td>
												<td style="width:70%">
													<select class="form-control" id="cboTipoDoc" name="cboTipoDoc">
														<option value="0">Diploma de bachiller</option>
														<option value="1">Calificaciones de secundaria</option>
														<option value="2">Cédula de identidad</option>
														<option value="3">Acta de nacimiento</option>
														<option value="4">Fotografía</option>
														<option value="5">Cédula de residencia</option>
														<option value="6">Pasaporte</option>
														<option value="7">Plan de estudio</option>
														<option value="8">Acta de aprobación monográfica</option>
														<option value="9">Calificaciones universitarias</option>
														<option value="10">Certificación del título universitario</option>
														<option value="11">Datos generales del título</option>
														<option value="12">Publicación en la gaceta</option>
														<option value="13">Título universitario</option>
													</select>
												</td>
												<td></td>
											</tr>
											<tr>
												<td valign="top" style="width: 15%;">Imagen</td>
												<td style="width:70%">
													<input id="txtRutaLocal" class="form-control" readonly>
												</td>
												<td>
													<label for="archivo" style="margin-left:1%; padding:0.5%" data-toggle="tooltip" data-placement="top" title="Agregar imagen">
													<img src="imagenes/imageAdd.png" height="100%" style="cursor:pointer" /></label>
													<input type="file" accept=".pdf, image/*" id="archivo" style="display:none"	onchange="llenaArchivo()" />
											<label id="cmdSubir" data-toggle="tooltip" data-placement="top"	title="Subir imagen"><img src="imagenes/imageUp.png" height="100%" style="cursor:pointer" /></label>
										</td>
											</tr>
											<tr>
												<td></td>
												<td style="width:70%">
													<label style="font-size:small; font-style:italic; color:rgb(130,130,130)">El nombre del archivo no debe contener espacios en blanco.</label>
												</td>
												<td></td>
											</tr>
										</table>
									</div>
									<div id="dvDOC" style="height:300px; padding-top:1%; padding-bottom:2%">
										<?php
											$mnCuenta = 0;
											$texto = '<table width="100%">';
											
											$mDatos = fxDevuelveDetDocumento($msCodigo);
											while ($mFila = $mDatos->fetch())
											{
												$extensionImg = strtoupper(substr($mFila["EVIDENCIAS_REL"], -3));
												if ($mnCuenta == 0) {
													$texto .= '<tr>';
												}
												$texto .= '<td width="23%" valign="top" style="margin-left:1%; margin-right:1%">';
												$texto .= '<img src="imagenes/imageDel.png"  id="' . trim($mFila["EVIDENCIAS_REL"]) . '" style="cursor:pointer" onclick="borrarImagen(this)"/><label style="font-size: small"> Borrar ' . trim($mFila["EVIDENCIAS_REL"]) . '</label>';
												if ($extensionImg != 'PDF')
													$texto .= '<br/><a href="' . trim($mFila["RUTA_201"]) . '" target="_blank"><img src="' . trim($mFila["RUTA_201"]) . '" style="width:100%"/></a>';
												else
													$texto .= '<br/><a href="' . trim($mFila["RUTA_201"]) . '" target="_blank"><img src="imagenes/pdf.png" style="width:80%"/></a>';
												$texto .= '<br/><div>' . trim($mFila["DESC_201"]) . '</div';
												$texto .= '</td>';
												$mnCuenta++;
												if ($mnCuenta == 4) {
													$texto .= '</tr>';
													$mnCuenta = 0;
												}
											}
											if ($mnCuenta == 1) {
												$texto .= '<td></td><td></td><td></td></tr>';
											}
											if ($mnCuenta == 2) {
												$texto .= '<td></td><td></td></tr>';
											}
											if ($mnCuenta == 3) {
												$texto .= '<td></td></tr>';
											}
											
											$texto .= '</table>';
											
											echo($texto);
										?>
									</div>
								</div>
							</div>
							</div><!--aqui finaliza del DIV de Tab SOPORTE-->
						</div>
					</form>
                </div>
	<?php	}
		}
	}
?>
			</div>
		</div>
	</div>
</body>
</html>
<script>
	var mCedula;
	var msResultado;
	var codEstudiante;
	var existeCedula;
	var parametros;
	var datosJson;

	function verificarFormulario()
	{

		if(document.getElementById('txtNombres').value=="")
		{
			document.getElementById('txtNombres').focus();
			$.messager.alert('UMOJN','Faltan los Nombre.','warning');
			return false;
		}
		if(document.getElementById('txtApellidos').value=="")
		{
			document.getElementById('txtApellidos').focus();
			$.messager.alert('UMOJN','Faltan los Apellido.','warning');
			return false;
		}

		if (document.getElementById('txtNacionalidad').value=="")
		{
			document.getElementById('txtNacionalidad').focus();
			$.messager.alert('UMOJN','Falta la nacionalidad.','warning');
			return false;
		}
		
		if (document.getElementById('txtCedula').value=="")
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','Falta la Cédula.','warning');
			return false;
		}
		if (existeCedula == true)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','La Cédula ya fue registrada con el estudiante ' + codEstudiante,'warning');
			return false;
		}

		if (document.getElementById('optOtroIdioma1').checked)
		{
			if (document.getElementById('txtIdioma').value=="")
			{
				document.getElementById('txtIdioma').focus();
				$.messager.alert('UMOJN','Falta el idioma.','warning');
				return false;
			}

			if (document.getElementById('txtDominioIdioma').value=="")
			{
				document.getElementById('txtDominioIdioma').focus();
				$.messager.alert('UMOJN','Falta el nivel del idioma.','warning');
				return false;
			}
		}

		if (document.getElementById('optLaboral1').checked)
		{
			if (document.getElementById('txtOcupacion').value=="")
			{
				document.getElementById('txtOcupacion').focus();
				$.messager.alert('UMOJN','Falta la ocupación.','warning');
				return false;
			}
		}

		return true;
	}
	window.onload=function()
	{
		if (document.getElementById("dtpFechaNac").value != "")
			calcularEdad();
	}

		function fxOptIdioma()
	{
		var mbIdioma = document.getElementById('optOtroIdioma1').checked;

		if (mbIdioma)
		{
			document.getElementById('txtIdioma').disabled = false;
			document.getElementById('txtDominioIdioma').disabled = false;
		}
		else
		{
			document.getElementById('txtIdioma').disabled = true;
			document.getElementById('txtDominioIdioma').disabled = true;
		}
	}
	
	function fxOptLaboral() {
    var empleado = document.getElementById('optLaboral1').checked; // true si es empleado

    let optSector = document.getElementById('optSector');
    let optEntidad = document.getElementById('optEntidad');

    if (empleado) {
        // Restaurar opciones originales
        restaurarOpciones(optSector, 'sector');
        restaurarOpciones(optEntidad, 'entidad');

        // Seleccionar la segunda opción por defecto (índice 1)
        if (optSector.options.length > 1) {
            optSector.selectedIndex = 1;
        }
        if (optEntidad.options.length > 1) {
            optEntidad.selectedIndex = 1;
        }

        // Habilitar selects
        optSector.disabled = false;
        optEntidad.disabled = false;
        document.getElementById('txtOcupacion').disabled = false;
        document.getElementById('txnSalario').disabled = false;

    } else {
        // Desempleado: solo "No aplica"
        optSector.innerHTML = "<option value='0' selected>No aplica</option>";
        optEntidad.innerHTML = "<option value='0' selected>No aplica</option>";

        // Deshabilitar selects y otros campos
        optSector.disabled = true;
        optEntidad.disabled = true;
        document.getElementById('txtOcupacion').disabled = true;
        document.getElementById('txnSalario').disabled = true;
    }
}

function restaurarOpciones(select, tipo) {
    if (tipo === 'sector') {
        select.innerHTML = `
            <option value="0">No aplica</option>
            <option value="1">Agricultura, ganadería, caza y silvicultura</option>
            <option value="2">Pesca</option>
            <option value="3">Minas y canteras</option>
            <option value="4">Industria manufacturas</option>
            <option value="5">Electricidad, gas y agua</option>
            <option value="6">Construcción</option>
            <option value="7">Comercio</option>
            <option value="8">Hoteles y restaurantes</option>
            <option value="9">Transporte, almacenamiento y comunicación</option>
            <option value="10">Actividades inmobiliarias, empresariales y de alquiler</option>
            <option value="11">Administración pública y defensa</option>
            <option value="12">Enseñanza</option>
            <option value="13">Servicios sociales y de salud</option>
            <option value="14">Otros servicios comunales</option>
            <option value="15">Hogares privados con servicio doméstico</option>
            <option value="16">Organizaciones y órganos extraterritoriales</option>
        `;
    } else if (tipo === 'entidad') {
        select.innerHTML = `
            <option value="0">No aplica</option>
            <option value="1">Pública</option>
            <option value="2">Privada</option>
            <option value="3">Cuenta propia</option>
        `;
    }
}



	function calcularEdad()
	{
	  var today_date = new Date();
	  var today_year = today_date.getFullYear();
	  var today_month = today_date.getMonth();
	  var today_day = today_date.getDate();
	  var birth_date = document.getElementById("dtpFechaNac").value;
	  var birth_year = parseInt(birth_date.substr(0,4));
	  var birth_month = parseInt(birth_date.substr(5,2));
	  var birth_day = parseInt(birth_date.substr(7,2));

	  var age = today_year - birth_year;
	
	  if (today_month < (birth_month - 1)) {
		age--;
	  }
	  if (((birth_month - 1) == today_month) && (today_day < birth_day)) {
		age--;
	  }
	  document.getElementById("txtEdad").value = age + " años";
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

function llenaArchivo() {
    $('#txtRutaLocal').val($('#archivo')[0].files[0].name);
}

function borrarImagen(objeto) {

    var datos = new FormData();
    datos.append('CodEstudiante', $('#txtAlumno').val());
    datos.append('CodImagen', objeto.id);

    $.ajax({
        url: 'funciones/fxAlumnosImagenes.php',
        type: 'POST',
        data: datos,
        contentType: false,
        processData: false,
        success: function(resp) {
            if (resp != 0) {
                $('#dvDOC').html(resp);
            } else {
                $.messager.alert('UMOJN','Error al borrar imagen','warning');
            }
        }
    });
}

	window.onload=function()
	{
		if (document.getElementById("dtpFechaNac").value != "")
			calcularEdad();
	}

	$('#cmdSubir').click(function () {

    if ($('#txtAlumno').val() == '') {
        $.messager.alert('UMOJN','Debe guardar el alumno primero','warning');
        return;
    }

    if ($('#archivo')[0].files.length == 0) {
        $.messager.alert('UMOJN','Seleccione un archivo','warning');
        return;
    }

    var datos = new FormData();
    datos.append('archivo', $('#archivo')[0].files[0]);
    datos.append('cboTipoDoc', $('#cboTipoDoc').val());
    datos.append('txtAlumno', $('#txtAlumno').val());
    datos.append('txtDescripcion', $('#cboTipoDoc option:selected').text());

    $.ajax({
        url: 'funciones/fxAlumnosImagenes.php',
        type: 'POST',
        data: datos,
        contentType: false,
        processData: false,
        success: function(resp) {
            if (resp != 0) {
				console.log(resp);

                 $('#dvDOC').html(resp);
                $('#txtRutaLocal').val('');
                $('#archivo').val('');
            } else {
                $.messager.alert('UMOJN','Error al subir imagen','warning');
            }
        }
    });
});

	
$('form').submit(function(e){ 
    e.preventDefault(); 

    if (verificarFormulario() == true) { 

        let datos = {
            txtAlumno: document.getElementById("txtAlumno").value,
            dtpFechaIns: document.getElementById("dtpFechaIns").value,
            cboColegio: document.getElementById("cboColegio").value,
            cboUniversidad: document.getElementById("cboUniversidad").value,
            txtNombres: document.getElementById("txtNombres").value,
            txtApellidos: document.getElementById("txtApellidos").value,
            dtpFechaNac: document.getElementById("dtpFechaNac").value,
            cboMunicipio: document.getElementById("cboMunicipio").value,
            txtCedula: document.getElementById("txtCedula").value,
            txtDeficiencia: document.getElementById("txtDeficiencia").value,
            optSexo: document.getElementById("optSexo1").checked ? "M" : "F",
            cboEstadoCivil: document.getElementById("cboEstadoCivil").value,
            txnHijos: document.getElementById("txnHijos").value,
            optDiscapacidad: document.getElementById("optDiscapacidad1").checked ? "1" : "0",
            cboNivelEstudio: document.getElementById("cboNivelEstudio").value,
            cboCurso: document.getElementById("cboCurso").value,
            txtTelefono: document.getElementById("txtTelefono").value,
            txtCelular: document.getElementById("txtCelular").value,
            txtEmail: document.getElementById("txtEmail").value,
            optOtroIdioma: document.getElementById("optOtroIdioma1").checked ? 1 : 0,
            txtIdioma: document.getElementById("optOtroIdioma1").checked ? document.getElementById("txtIdioma").value : "",
            txtDominioIdioma: document.getElementById("optOtroIdioma1").checked ? document.getElementById("txtDominioIdioma").value : "",
            txtDireccion: document.getElementById("txtDireccion").value,
            cboMedio: document.getElementById("cboMedio").value,
            optLaboral: document.getElementById("optLaboral1").checked ? "1" : "0",
            txtOcupacion: document.getElementById("txtOcupacion").value,
            txnSalario: document.getElementById("txnSalario").value,
            optSector: document.getElementById("optSector").value,
            optEntidad: document.getElementById("optEntidad").value,
            txtNombreRef: document.getElementById("txtNombreRef").value,
            txtCedulaRef: document.getElementById("txtCedulaRef").value,
            txtCelularRef: document.getElementById("txtCelularRef").value,
			txtDireccionRef: document.getElementById("txtDireccionRef").value,
			  txtNacionalidad: document.getElementById("txtNacionalidad").value,
        };
$.ajax({
    url: 'catAlumnos.php',
    type: 'POST',
    data: datos,
  //  dataType: 'json', 
   beforeSend: function(){console.log(datos)}
})
.done(function(){location.href="gridAlumnos.php"})
			.fail(function(){console.log('Error')});
    } 
});
</script>