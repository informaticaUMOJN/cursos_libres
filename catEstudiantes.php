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
	require_once ("funciones/fxEstudiantes.php");

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
		$mbPermisoUsuario = fxPermisoUsuario("catEstudiantes");
		
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
			if (isset($_POST["txtEstudiante"]))
			{
				$msCodigo = $_POST["txtEstudiante"];
				$msColegio = $_POST["cboColegio"];
				$msCarrera = $_POST["cboCarrera"];
				$msMunicipio = $_POST["cboMunicipio"];
				$msFechaIns = $_POST["dtpFechaIns"];
				$mnGeneracion = $_POST["txnGeneracion"];
				$msCarnet = $_POST["txtCarnet"];
				$msCodEstudiantil = $_POST["txtCodEstudiantil"];
				$msNombre1 = $_POST["txtNombre1"];
				$msNombre2 = $_POST["txtNombre2"];
				$msApellido1 = $_POST["txtApellido1"];
				$msApellido2 = $_POST["txtApellido2"];
				$mbNacional = $_POST["optNacional"];
				$msFechaNac = $_POST["dtpFechaNac"];
				$msLugarNac = $_POST["txtLugarNac"];
				$msPaisNac = $_POST["txtPaisNac"];
				$msNacionalidad = $_POST["txtNacionalidad"];
				$msEtnia = htmlentities($_POST["txtEtnia"]);
				$mnPeso = $_POST["txnPeso"];
				$mnTalla = $_POST["txnTalla"];
				$msTipoSangre = $_POST["cboTipoSangre"];
				$msCedula = $_POST["txtCedula"];
				$msPasaporte = $_POST["txtPasaporte"];
				$msSexo = $_POST["optSexo"];
				$mnEstadoCivil = $_POST["cboEstadoCivil"];
				$mnHijos = $_POST["txnHijos"];
				$msTelefono = $_POST["txtTelefono"];
				$msCelular = $_POST["txtCelular"];
				$msCorreoE = $_POST["txtCorreoE"];
				$msCorreoI = $_POST["txtCorreoI"];
				$msDireccion = $_POST["txtDireccion"];
				$msZona = htmlentities($_POST["txtZona"]);
				$mnMedio = $_POST["cboMedio"];
				$msEmergencia = ($_POST["txtEmergencia"]);
				$msTelEmergencia = $_POST["txtTelEmergencia"];
				$msCelEmergencia = $_POST["txtCelEmergencia"];
				$mbOtroIdioma = $_POST["optOtroIdioma"];
				$msIdioma = $_POST["txtIdioma"];
				$msDominioIdioma = $_POST["txtDominioIdioma"];
				$mnNivelEstudio = $_POST["cboNivelEstudio"];
				$mbLaboral = $_POST["optLaboral"];
				$msOcupacion = $_POST["txtOcupacion"];
				$mnSalarioEstudiante = $_POST["txnSalario"];
				$mbDiscapacidad = $_POST["optDiscapacidad"];
				$msNombreMadre = $_POST["txtNombreMadre"];
				$msNombrePadre = $_POST["txtNombrePadre"];
				$msTelefonoMadre = $_POST["txtTelefonoMadre"];
				$msTelefonoPadre = $_POST["txtTelefonoPadre"];
				$msCelularMadre = $_POST["txtCelularMadre"];
				$msCelularPadre = $_POST["txtCelularPadre"];
				$mbTrabajaMadre = $_POST["optTrabajaMadre"];
				$mbTrabajaPadre = $_POST["optTrabajaPadre"];
				$msTrabajoMadre = $_POST["txtTrabajoMadre"];
				$msTrabajoPadre = $_POST["txtTrabajoPadre"];
				$mnSalarioMadre = $_POST["txnSalarioMadre"];
				$mnSalarioPadre = $_POST["txnSalarioPadre"];
				$mnMenores = $_POST["txnMenores"];
				$mnMayores = $_POST["txnMayores"];
				$mnDependientes = $_POST["txnDependientes"];

				if (isset($_POST["gridDocumentos"]))
				    $gridDocumentos = $_POST["gridDocumentos"];
					
				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarEstudiantes ($msColegio, $msCarrera, $msMunicipio, $msCodEstudiantil, $msFechaIns, $mnGeneracion, $msCarnet, $msNombre1, $msNombre2, $msApellido1, $msApellido2, $mbNacional, $msFechaNac, $msLugarNac, $msPaisNac, $msNacionalidad, $msEtnia, $mnPeso, $mnTalla, $msTipoSangre, $msCedula, $msPasaporte, $msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $msZona, $mnMedio, $msEmergencia, $msTelEmergencia, $msCelEmergencia, $mbOtroIdioma, $msIdioma, $msDominioIdioma, $mnNivelEstudio, $mbLaboral, $msOcupacion, $mnSalarioEstudiante, $mbDiscapacidad, $msNombreMadre, $msNombrePadre, $msTelefonoMadre, $msTelefonoPadre, $msCelularMadre, $msCelularPadre, $mbTrabajaMadre, $mbTrabajaPadre, $msTrabajoMadre, $msTrabajoPadre, $mnSalarioMadre, $mnSalarioPadre, $mnMenores, $mnMayores, $mnDependientes);
					$msBitacora = $msCodigo . "; " . $msColegio . "; " . "; " . $msCarrera . "; " . $msMunicipio . "; " . $msCodEstudiantil . "; " . $msFechaIns . ";" . $mnGeneracion . "; " . $msCarnet . "; " . $msNombre1 . "; " . $msNombre2 . "; " . $msApellido1 . "; " . $msApellido2 . "; " . $msFechaNac . "; " . $msLugarNac . "; " . $msPaisNac . "; " . $msNacionalidad . "; " . $msEtnia . "; " . $mnPeso . "; " . $mnTalla . "; " . $msTipoSangre . "; " . $msCedula . "; " . $msPasaporte . "; " . $msSexo . "; " . $mnEstadoCivil . "; " . $mnHijos . "; " . $msTelefono . "; " . $msCelular . "; " . $msCorreoE . "; " . $msCorreoI . "; " . $msDireccion . "; " . $msZona . "; " . $mnMedio . "; " . $msEmergencia . "; " . $msTelEmergencia . "; " . $msCelEmergencia . "; " . $mbOtroIdioma . "; " . $msIdioma . "; " . $msDominioIdioma . "; " . $mnNivelEstudio . "; " . $mbLaboral . "; " . $msOcupacion . "; " . $mnSalarioEstudiante . "; " . $mbDiscapacidad . "; " . $msNombreMadre . "; " . $msNombrePadre . "; " . $msTelefonoMadre . "; " . $msTelefonoPadre . "; " . $msCelularMadre . "; " . $msCelularPadre . "; " . $mbTrabajaMadre . "; " . $mbTrabajaPadre . ";" . $msTrabajoMadre . "; " . $msTrabajoPadre . "; " . $mnSalarioMadre . "; " . $mnSalarioPadre . "; " .  $mnMenores . "; " . $mnMenores . "; " . $mnDependientes;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO010A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarEstudiantes ($msCodigo, $msColegio, $msCarrera, $msMunicipio, $msCodEstudiantil, $msFechaIns, $mnGeneracion, $msCarnet, $msNombre1, $msNombre2, $msApellido1, $msApellido2, $mbNacional, $msFechaNac, $msLugarNac, $msPaisNac, $msNacionalidad, $msEtnia, $mnPeso, $mnTalla, $msTipoSangre, $msCedula, $msPasaporte, $msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $msZona, $mnMedio, $msEmergencia, $msTelEmergencia, $msCelEmergencia, $mbOtroIdioma, $msIdioma, $msDominioIdioma, $mnNivelEstudio, $mbLaboral, $msOcupacion, $mnSalarioEstudiante, $mbDiscapacidad, $msNombreMadre, $msNombrePadre, $msTelefonoMadre, $msTelefonoPadre, $msCelularMadre, $msCelularPadre, $mbTrabajaMadre, $mbTrabajaPadre, $msTrabajoMadre, $msTrabajoPadre, $mnSalarioMadre, $mnSalarioPadre, $mnMenores, $mnMayores, $mnDependientes);
					$msBitacora = $msCodigo . "; " . $msColegio . "; " . "; " . $msCarrera . "; " . $msMunicipio . "; " . $msCodEstudiantil . "; " . $msFechaIns . ";" . $mnGeneracion . "; " . $msCarnet . "; " . $msNombre1 . "; " . $msNombre2 . "; " . $msApellido1 . "; " . $msApellido2 . "; " . $msFechaNac . "; " . $msLugarNac . "; " . $msPaisNac . "; " . $msNacionalidad . "; " . $msEtnia . "; " . $mnPeso . "; " . $mnTalla . "; " . $msTipoSangre . "; " . $msCedula . "; " . $msPasaporte . "; " . $msSexo . "; " . $mnEstadoCivil . "; " . $mnHijos . "; " . $msTelefono . "; " . $msCelular . "; " . $msCorreoE . "; " . $msCorreoI . "; " . $msDireccion . "; " . $msZona . "; " . $mnMedio . "; " . $msEmergencia . "; " . $msTelEmergencia . "; " . $msCelEmergencia . "; " . $mbOtroIdioma . "; " . $msIdioma . "; " . $msDominioIdioma . "; " . $mnNivelEstudio . "; " . $mbLaboral . "; " . $msOcupacion . "; " . $mnSalarioEstudiante . "; " . $mbDiscapacidad . "; " . $msNombreMadre . "; " . $msNombrePadre . "; " . $msTelefonoMadre . "; " . $msTelefonoPadre . "; " . $msCelularMadre . "; " . $msCelularPadre . "; " . $mbTrabajaMadre . "; " . $mbTrabajaPadre . ";" . $msTrabajoMadre . "; " . $msTrabajoPadre . "; " . $mnSalarioMadre . "; " . $mnSalarioPadre . "; " .  $mnMenores . "; " . $mnMenores . "; " . $mnDependientes;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO010A", $msCodigo, "", "Modificar", $msBitacora);
				}
				
				?><meta http-equiv="Refresh" content="0;url=gridEstudiantes.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";

				if ($msCodigo != "")
				{
					$objRecordSet = fxDevuelveEstudiantes(0, $msCodigo);
					$mFila = $objRecordSet->fetch();
					$msColegio = $mFila["COLEGIO_REL"];
					$msCarrera = $mFila["CARRERA_REL"];
					$msMunicipio = $mFila["MUNICIPIO_REL"];
					$msFechaIns = $mFila["FECHA_010"];
					$mnGeneracion = $mFila["GENERACION_010"];
					$msCarnet = $mFila["CARNET_010"];
					$msCodEstudiantil = $mFila["CODESTUDIANTIL_010"];
					$msNombre1 = htmlentities($mFila["NOMBRE1_010"]);
					$msNombre2 = htmlentities($mFila["NOMBRE2_010"]);
					$msApellido1 = htmlentities($mFila["APELLIDO1_010"]);
					$msApellido2 = htmlentities($mFila["APELLIDO2_010"]);
					$mbNacional = htmlentities($mFila["NACIONAL_010"]);
					$msFechaNac = $mFila["FECHANAC_010"];
					$msLugarNac = htmlentities($mFila["LUGARNAC_010"]);
					$msPaisNac = htmlentities($mFila["PAIS_010"]);
					$msNacionalidad = htmlentities($mFila["NACIONALIDAD_010"]);
					$msEtnia = htmlentities($mFila["ETNIA_010"]);
					$mnPeso = $mFila["PESO_010"];
					$mnTalla = $mFila["TALLA_010"];
					$msTipoSangre = $mFila["TIPOSANGRE_010"];
					$msCedula = $mFila["CEDULA_010"];
					$msPasaporte = $mFila["PASAPORTE_010"];
					$msSexo = $mFila["SEXO_010"];
					$mnEstadoCivil = $mFila["ESTADOCIVIL_010"];
					$mnHijos = $mFila["HIJOS_010"];
					$msTelefono = $mFila["TELEFONO_010"];
					$msCelular = $mFila["CELULAR_010"];
					$msCorreoE = $mFila["CORREOE_010"];
					$msCorreoI = $mFila["CORREOI_010"];
					$msDireccion = $mFila["DIRECCION_010"];
					$msZona = htmlentities($mFila["ZONA_010"]);
					$mnMedio = $mFila["MEDIO_010"];
					$msEmergencia = htmlentities($mFila["EMERGENCIA_010"]);
					$msTelEmergencia = $mFila["TEL_EMERGENCIA_010"];
					$msCelEmergencia = $mFila["CEL_EMERGENCIA_010"];
					$mbOtroIdioma = $mFila["OTROIDIOMA_010"];
					$msIdioma = $mFila["IDIOMA_010"];
					$msDominioIdioma = $mFila["DOMINIOIDIOMA_010"];
					$mnNivelEstudio = $mFila["NIVELESTUDIO_010"];
					$mbLaboral = $mFila["CONDICIONLABORAL_010"];
					$msOcupacion = $mFila["OCUPACION_010"];
					$mnSalarioEstudiante = $mFila["SALARIOESTUDIANTE_010"];
					$mbDiscapacidad = $mFila["DISCAPACIDAD_010"];
					$msNombreMadre = $mFila["NOMBREMADRE_010"];
					$msNombrePadre = $mFila["NOMBREPADRE_010"];
					$msTelefonoMadre = $mFila["TELEFONOMADRE_010"];
					$msTelefonoPadre = $mFila["TELEFONOPADRE_010"];
					$msCelularMadre = $mFila["CELULARMADRE_010"];
					$msCelularPadre = $mFila["CELULARPADRE_010"];
					$mbTrabajaMadre = $mFila["TRABAJAMADRE_010"];
					$mbTrabajaPadre = $mFila["TRABAJAPADRE_010"];
					$msTrabajoMadre = $mFila["TRABAJOMADRE_010"];
					$msTrabajoPadre = $mFila["TRABAJOPADRE_010"];
					$mnSalarioMadre = $mFila["SALARIOMADRE_010"];
					$mnSalarioPadre = $mFila["SALARIOPADRE_010"];
					$mnMenores = $mFila["MENORES_010"];
					$mnMayores = $mFila["MAYORES_010"];
					$mnDependientes = $mFila["DEPENDIENTES_010"];
				}
				else
				{
					$msColegio = "";
					$msCarrera = "";
					$msMunicipio = "";
					$msFechaIns = date('Y-m-d');
					$mnGeneracion = date('Y');
					$msCarnet = "";
					$msCodEstudiantil = "";
					$msNombre1 = "";
					$msNombre2 = "";
					$msApellido1 = "";
					$msApellido2 = "";
					$mbNacional = 1;
					$msFechaNac = date('Y-m-d');
					$msLugarNac = "";
					$msPaisNac = "";
					$msNacionalidad = "";
					$msEtnia = "";
					$mnPeso = 0;
					$mnTalla = 0;
					$msTipoSangre = "N/A";
					$msCedula = "";
					$msPasaporte = "";
					$msSexo = "M";
					$mnEstadoCivil = 0;
					$mnHijos = 0;
					$msTelefono = "";
					$msCelular = "";
					$msCorreoE = "";
					$msCorreoI = "";
					$msDireccion = "";
					$msZona = "";
					$mnMedio = 0;
					$msEmergencia = "";
					$msTelEmergencia = "";
					$msCelEmergencia = "";
					$mbOtroIdioma = 0;
					$msIdioma = "";
					$msDominioIdioma = "";
					$mnNivelEstudio = 0;
					$mbLaboral = 0;
					$msOcupacion = "";
					$mnSalarioEstudiante = 0;
					$mbDiscapacidad = 0;
					$msNombreMadre = "";
					$msNombrePadre = "";
					$msTelefonoMadre = "";
					$msTelefonoPadre = "";
					$msCelularMadre = "";
					$msCelularPadre = "";
					$mbTrabajaMadre = 0;
					$mbTrabajaPadre = 0;
					$msTrabajoMadre = "";
					$msTrabajoPadre = "";
					$mnSalarioMadre = 0;
					$mnSalarioPadre = 0;
					$mnMenores = 0;
					$mnMayores = 0;
					$mnDependientes = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
                <div class="col-xs-12 col-md-12">
					<form name="catEstudiantes" id="catEstudiantes">
						<div class = "row">
							<div class="col-auto col-md-11">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridEstudiantes.php';"/>
							</div>
						</div>

						<div class="easyui-tabs tabs-narrow" style="width:100%;height:auto">
							<!--Inicio del DIV de Tabs-->
							<div title="Generales" style="padding-left: 20px; padding-top: 10px">
								<div class="col-sm-auto col-md-12">
									<div class = "form-group row">
										<label for="txtEstudiante" class="col-sm-12 col-md-3 form-label">Estudiante</label>
										<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtEstudiante" name="txtEstudiante" value="' . $msCodigo . '" readonly />'); 
										?>
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

									<div class = "form-group row">
										<label for="dtpFechaIns" class="col-sm-12 col-md-3 form-label">Fecha de inscripción</label>
										<div class="col-sm-12 col-md-2">
										<?php echo('<input type="date" class="form-control" id="dtpFechaIns" name="dtpFechaIns" value="' . $msFechaIns . '" />'); ?>
										</div>
									</div>

									<div class="form-group row">
										<label for="cboCarrera" class="col-sm-12 col-md-3 col-form-label">Carrera</label>
										<div class="col-sm-12 col-md-7">
											<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="obtenerCarnet()">
												<?php
													$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A order by NOMBRE_040";
													$mDatos = $m_cnx_MySQL->prepare($msConsulta);
													$mDatos->execute();
													while ($mFila = $mDatos->fetch())
													{
														$msValor = rtrim($mFila["CARRERA_REL"]);
														$msTexto = rtrim($mFila["NOMBRE_040"]);
														if ($msCodigo == "")
															echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
														else
														{
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
													}
												?>
											</select>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txnGeneracion" class="col-sm-12 col-md-3 form-label">Generación</label>
										<div class="col-sm-12 col-md-2">
										<?php 
											echo('<input type="number" class="form-control" id="txnGeneracion" name="txnGeneracion" value="' . $mnGeneracion . '" onchange="calcularEdad()" />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCodEstudiantil" class="col-sm-12 col-md-3 form-label">Código estudiantil</label>
										<div class="col-sm-12 col-md-3">
										<?php 
											echo('<input type="text" class="form-control" id="txtCodEstudiantil" name="txtCodEstudiantil" value="' . $msCodEstudiantil . '" />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNombre1" class="col-sm-12 col-md-3 form-label">Primer nombre</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNombre1" name="txtNombre1" value="' . $msNombre1 . '" onchange="fxEscribeCorreo()" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNombre2" class="col-sm-12 col-md-3 form-label">Segundo nombre</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNombre2" name="txtNombre2" value="' . $msNombre2 . '" />'); ?>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="txtApellido1" class="col-sm-12 col-md-3 form-label">Primer apellido</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtApellido1" name="txtApellido1" value="' . $msApellido1 . '" onchange="fxEscribeCorreo()" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtApellido2" class="col-sm-12 col-md-3 form-label">Segundo apellido</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtApellido2" name="txtApellido2" value="' . $msApellido2 . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="optNacional" class="col-sm-12 col-md-3 form-label">Nacional</label>
										<div class="col-sm-12 col-md-3">
											<div class = "radio">
											<?php
												if ($mbNacional == 1)
													echo('<input type="radio" id="optNacional1" name="optNacional" value="1" checked="checked" onchange="fxOptNacional()" /> Si &emsp;');
												else
													echo('<input type="radio" id="optNacional1" name="optNacional" value="1" onchange="fxOptNacional()" /> Si &emsp;');

												if ($mbNacional == 0)
													echo('<input type="radio" id="optNacional2" name="optNacional" value="0" checked="checked" onchange="fxOptNacional()" /> No');
												else
													echo('<input type="radio" id="optNacional2" name="optNacional" value="0" onchange="fxOptNacional()" /> No');
											?>
											</div>
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
										<label for="txtCarnet" class="col-sm-12 col-md-3 form-label">Carnet</label>
										<div class="col-sm-12 col-md-3">
										<?php 
											echo('<input type="text" class="form-control" id="txtCarnet" name="txtCarnet" value="' . $msCarnet . '" />');
										?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtPaisNac" class="col-sm-12 col-md-3 form-label">País de nacimiento</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtPaisNac" name="txtPaisNac" value="' . $msPaisNac . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtNacionalidad" class="col-sm-12 col-md-3 form-label">Nacionalidad</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtNacionalidad" name="txtNacionalidad" value="' . $msNacionalidad . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtLugarNac" class="col-sm-12 col-md-3 form-label">Lugar de nacimiento</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtLugarNac" name="txtLugarNac" value="' . $msLugarNac . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtEtnia" class="col-sm-12 col-md-3 form-label">Etnia</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtEtnia" name="txtEtnia" value="' . $msEtnia . '" />'); ?>
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
										<label for="txtPasaporte" class="col-sm-12 col-md-3 form-label">Pasaporte</label>
										<div class="col-sm-12 col-md-4">
										<?php
											if ($mbNacional == 1)
												echo('<input type="text" class="form-control" id="txtPasaporte" name="txtPasaporte" value="" disabled />');
											else
												echo('<input type="text" class="form-control" id="txtPasaporte" name="txtPasaporte" value="' . $msPasaporte . '" />');
										?>
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
										<label for="txtCorreoE" class="col-sm-12 col-md-3 form-label">Correo electrónico</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtCorreoE" name="txtCorreoE" maxlength="100" value="' . $msCorreoE . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCorreoI" class="col-sm-12 col-md-3 form-label">Correo institucional</label>
										<div class="col-sm-12 col-md-4">
										<?php echo('<input type="text" class="form-control" id="txtCorreoI" name="txtCorreoI" maxlength="100" value="' . $msCorreoI . '" />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtDireccion" class="col-sm-12 col-md-3 form-label">Dirección</label>
										<div class="col-sm-12 col-md-7">
										<?php echo('<textarea class="form-control" id="txtDireccion" name="txtDireccion" rows="3">' . $msDireccion . '</textarea>'); ?>
										</div>
									</div>
																		
									<div class = "form-group row">
										<label for="txtZona" class="col-sm-12 col-md-3 form-label">Zona</label>
										<div class="col-sm-12 col-md-7">
										<?php echo('<input type="text" class="form-control" id="txtZona" name="txtZona" value="' . $msZona . '" />'); ?>
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

									<div class="form-group row">
										<label for="cboNivelEstudio" class="col-sm-12 col-md-3 col-form-label">Nivel de estudios</label>
										<div class="col-sm-12 col-md-4">
											<select class="form-control" id="cboNivelEstudio" name="cboNivelEstudio">
												<?php
													if ($mnNivelEstudio == 0)
														echo("<option value='0' selected>Bachiller</option>");
													else
														echo("<option value='0'>Bachiller</option>");

													if ($mnNivelEstudio == 1)
														echo("<option value='1' selected>Técnico</option>");
													else
														echo("<option value='1'>Técnico</option>");

													if ($mnNivelEstudio == 2)
														echo("<option value='2' selected>Licenciado</option>");
													else
														echo("<option value='2'>Licenciado</option>");

													if ($mnNivelEstudio == 3)
														echo("<option value='3' selected>Ingeniero</option>");
													else
														echo("<option value='3'>Ingeniero</option>");

													if ($mnNivelEstudio == 4)
														echo("<option value='4' selected>Doctor</option>");
													else
														echo("<option value='4'>Doctor</option>");
												?>
											</select>
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
								</div>
							</div>
							<!--Fin del DIV de Tab GENERALES-->

							<!--Inicio del DIV de Tab EMERGENCIA-->
							<div title="Datos para emergencias" style="padding-left: 20px; padding-top: 10px"">
								<div class = "form-group row">
									<label for="txtEmergencia" class="col-sm-12 col-md-3 form-label">En caso de emergencia avisar a</label>
									<div class="col-sm-12 col-md-6">
									<?php echo('<input type="text" class="form-control" id="txtEmergencia" name="txtEmergencia" value="' . $msEmergencia . '" />'); ?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtTelEmergencia" class="col-sm-12 col-md-3 form-label">Telefono de emergencia</label>
									<div class="col-sm-12 col-md-3">
									<?php echo('<input type="text" class="form-control" id="txtTelEmergencia" name="txtTelEmergencia" maxlength="20" value="' . $msTelEmergencia . '" />'); ?>
									</div>
								</div>
								
								<div class = "form-group row">
									<label for="txtCelEmergencia" class="col-sm-12 col-md-3 form-label">Celular de emergencia</label>
									<div class="col-sm-12 col-md-3">
									<?php echo('<input type="text" class="form-control" id="txtCelEmergencia" name="txtCelEmergencia" maxlength="20" value="' . $msCelEmergencia . '" />'); ?>
									</div>
								</div>

								<div class="form-group row">
									<label for="cboTipoSangre" class="col-sm-12 col-md-3 col-form-label">Tipo de sangre</label>
									<div class="col-sm-12 col-md-2">
										<select class="form-control" id="cboTipoSangre" name="cboTipoSangre">
											<?php
												if ($msTipoSangre == "O-")
													echo("<option value='O-' selected>O-</option>");
												else
													echo("<option value='O-'>O-</option>");

												if ($msTipoSangre == "O+")
													echo("<option value='O+' selected>O+</option>");
												else
													echo("<option value='O+'>O+</option>");

												if ($msTipoSangre == "A-")
													echo("<option value='A-' selected>A-</option>");
												else
													echo("<option value='A-'>A-</option>");

												if ($msTipoSangre == "A+")
													echo("<option value='A+' selected>A+</option>");
												else
													echo("<option value='A+'>A+</option>");

												if ($msTipoSangre == "B-")
													echo("<option value='B-' selected>B-</option>");
												else
													echo("<option value='B-'>B-</option>");

												if ($msTipoSangre == "B+")
													echo("<option value='B+' selected>B+</option>");
												else
													echo("<option value='B+'>B+</option>");

												if ($msTipoSangre == "AB-")
													echo("<option value='AB-' selected>AB-</option>");
												else
													echo("<option value='AB-'>AB-</option>");

												if ($msTipoSangre == "AB+")
													echo("<option value='AB+' selected>AB+</option>");
												else
													echo("<option value='AB+'>AB+</option>");

												if ($msTipoSangre == "N/A")
													echo("<option value='N/A' selected>N/A</option>");
												else
													echo("<option value='N/A'>N/A</option>");
											?>
										</select>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnPeso" class="col-sm-12 col-md-3 form-label">Peso (en libras)</label>
									<div class="col-sm-12 col-md-2">
										<?php echo('<input type="number" class="form-control" id="txnPeso" name="txnPeso" value="' . $mnPeso . '" />'); ?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnTalla" class="col-sm-12 col-md-3 form-label">Talla</label>
									<div class="col-sm-12 col-md-2">
										<?php echo('<input type="number" step="0.01" class="form-control" id="txnTalla" name="txnTalla" value="' . $mnTalla . '" />'); ?>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab EMERGENCIA-->

							<!--Inicio del DIV de Tab FAMILIAR-->
							<div title="Estructura familiar" style="padding-left: 20px; padding-top: 10px">
								<div class = "form-group row">
									<label for="txtNombreMadre" class="col-sm-12 col-md-3 form-label">Nombre de la madre</label>
									<div class="col-sm-12 col-md-6">
										<?php
											echo('<input type="text" class="form-control" id="txtNombreMadre" name="txtNombreMadre" value="' . $msNombreMadre . '" />');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtNombrePadre" class="col-sm-12 col-md-3 form-label">Nombre del padre</label>
									<div class="col-sm-12 col-md-6">
										<?php
											echo('<input type="text" class="form-control" id="txtNombrePadre" name="txtNombrePadre" value="' . $msNombrePadre . '" />');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtTelefonoMadre" class="col-sm-12 col-md-3 form-label">Teléfono de la madre</label>
									<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtTelefonoMadre" name="txtTelefonoMadre" maxlength="20" value="' . $msTelefonoMadre . '" />');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtTelefonoPadre" class="col-sm-12 col-md-3 form-label">Teléfono del padre</label>
									<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtTelefonoPadre" name="txtTelefonoPadre" maxlength="20" value="' . $msTelefonoPadre . '" />');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtCelularMadre" class="col-sm-12 col-md-3 form-label">Celular de la madre</label>
									<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtCelularMadre" name="txtCelularMadre" maxlength="20" value="' . $msCelularMadre . '" />');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtCelularMadre" class="col-sm-12 col-md-3 form-label">Celular del padre</label>
									<div class="col-sm-12 col-md-3">
										<?php
											echo('<input type="text" class="form-control" id="txtCelularPadre" name="txtCelularPadre" maxlength="20" value="' . $msCelularPadre . '" />');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="optTrabajaMadre" class="col-sm-12 col-md-3 form-label">¿Trabaja la madre?</label>
									<div class="col-sm-12 col-md-3">
										<div class = "radio">
											<?php
												if ($mbTrabajaMadre == 1)
													echo('<input type="radio" id="optTrabajaMadre1" name="optTrabajaMadre" value="1" checked="checked" onchange="fxOptTrabajaMadre()" /> Si &emsp;');
												else
													echo('<input type="radio" id="optTrabajaMadre1" name="optTrabajaMadre" value="1" onchange="fxOptTrabajaMadre()" /> Si &emsp;');

												if ($mbTrabajaMadre == 0)
													echo('<input type="radio" id="optTrabajaMadre2" name="optTrabajaMadre" value="0" checked="checked" onchange="fxOptTrabajaMadre()" /> No');
												else
													echo('<input type="radio" id="optTrabajaMadre2" name="optTrabajaMadre" value="0" onchange="fxOptTrabajaMadre()" /> No');
											?>
										</div>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtTrabajoMadre" class="col-sm-12 col-md-3 form-label">Trabajo de la madre</label>
									<div class="col-sm-12 col-md-6">
									<?php
										if ($mbTrabajaMadre == 1)
											echo('<input type="text" class="form-control" id="txtTrabajoMadre" name="txtTrabajoMadre" value="' . $msTrabajoMadre . '" />');
										else
											echo('<input type="text" class="form-control" id="txtTrabajoMadre" name="txtTrabajoMadre" value="" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnSalarioMadre" class="col-sm-12 col-md-3 form-label">Salario de la madre</label>
									<div class="col-sm-12 col-md-3">
									<?php
										if ($mbTrabajaMadre == 1)
											echo('<input type="number" class="form-control" id="txnSalarioMadre" name="txnSalarioMadre" value="' . $mnSalarioMadre . '" />');
										else
											echo('<input type="number" class="form-control" id="txnSalarioMadre" name="txnSalarioMadre" value="0" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="optTrabajaPadre" class="col-sm-12 col-md-3 form-label">¿Trabaja el padre?</label>
									<div class="col-sm-12 col-md-3">
										<div class = "radio">
											<?php
												if ($mbTrabajaPadre == 1)
													echo('<input type="radio" id="optTrabajaPadre1" name="optTrabajaPadre" value="1" checked="checked" onchange="fxOptTrabajaPadre()" /> Si &emsp;');
												else
													echo('<input type="radio" id="optTrabajaPadre1" name="optTrabajaPadre" value="1" onchange="fxOptTrabajaPadre()" /> Si &emsp;');

												if ($mbTrabajaPadre == 0)
													echo('<input type="radio" id="optTrabajaPadre2" name="optTrabajaPadre" value="0" checked="checked" onchange="fxOptTrabajaPadre()" /> No');
												else
													echo('<input type="radio" id="optTrabajaPadre2" name="optTrabajaPadre" value="0" onchange="fxOptTrabajaPadre()" /> No');
											?>
										</div>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txtTrabajoPadre" class="col-sm-12 col-md-3 form-label">Trabajo del padre</label>
									<div class="col-sm-12 col-md-6">
									<?php
										if ($mbTrabajaPadre == 1)
											echo('<input type="text" class="form-control" id="txtTrabajoPadre" name="txtTrabajoPadre" value="' . $msTrabajoPadre . '" />');
										else
											echo('<input type="text" class="form-control" id="txtTrabajoPadre" name="txtTrabajoPadre" value="" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnSalarioPadre" class="col-sm-12 col-md-3 form-label">Salario del padre</label>
									<div class="col-sm-12 col-md-3">
									<?php
										if ($mbTrabajaPadre == 1)
											echo('<input type="number" class="form-control" id="txnSalarioPadre" name="txnSalarioPadre" value="' . $mnSalarioPadre . '" />');
										else
											echo('<input type="number" class="form-control" id="txnSalarioPadre" name="txnSalarioPadre" value="0" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnMenores" class="col-sm-12 col-md-3 form-label">Hermanos menores de 21 años</label>
									<div class="col-sm-12 col-md-3">
									<?php
										echo('<input type="number" class="form-control" id="txnMenores" name="txnMenores" value="' . $mnMenores . '" />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnMayores" class="col-sm-12 col-md-3 form-label">Hermanos mayores de 22 años</label>
									<div class="col-sm-12 col-md-3">
									<?php
										echo('<input type="number" class="form-control" id="txnMayores" name="txnMayores" value="' . $mnMayores . '" />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnDependientes" class="col-sm-12 col-md-3 form-label">Cantidad de dependientes de los padres</label>
									<div class="col-sm-12 col-md-3">
									<?php
										echo('<input type="number" class="form-control" id="txnDependientes" name="txnDependientes" value="' . $mnDependientes . '" />');
									?>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab FAMILIAR-->

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
									<label for="txtOcupacion" class="col-sm-12 col-md-3 form-label">Ocupación</label>
									<div class="col-sm-12 col-md-6">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="text" class="form-control" id="txtOcupacion" name="txtOcupacion" value="' . $msOcupacion . '" />');
										else
											echo('<input type="text" class="form-control" id="txtOcupacion" name="txtOcupacion" value="" disabled />');
									?>
									</div>
								</div>

								<div class = "form-group row">
									<label for="txnSalario" class="col-sm-12 col-md-3 form-label">Salario</label>
									<div class="col-sm-12 col-md-3">
									<?php
										if ($mbLaboral == 1)
											echo('<input type="number" class="form-control" id="txnSalario" name="txnSalario" value="' . $mnSalarioEstudiante . '" />');
										else
											echo('<input type="number" class="form-control" id="txnSalario" name="txnSalario" value="0" disabled />');
									?>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab LABORAL-->

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
												$extensionImg = strtoupper(substr($mFila["ARCHIVO_REL"], -3));
												if ($mnCuenta == 0) {
													$texto .= '<tr>';
												}
												$texto .= '<td width="23%" valign="top" style="margin-left:1%; margin-right:1%">';
												$texto .= '<img src="imagenes/imageDel.png"  id="' . trim($mFila["ARCHIVO_REL"]) . '" style="cursor:pointer" onclick="borrarImagen(this)"/><label style="font-size: small"> Borrar ' . trim($mFila["ARCHIVO_REL"]) . '</label>';
												if ($extensionImg != 'PDF')
													$texto .= '<br/><a href="' . trim($mFila["RUTA_011"]) . '" target="_blank"><img src="' . trim($mFila["RUTA_011"]) . '" style="width:100%"/></a>';
												else
													$texto .= '<br/><a href="' . trim($mFila["RUTA_011"]) . '" target="_blank"><img src="imagenes/pdf.png" style="width:80%"/></a>';
												$texto .= '<br/><div>' . trim($mFila["DESC_011"]) . '</div';
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
								<!--Fin del DIV Columna SOPORTE-->
							</div>
							<!--Fin del DIV de Tab SOPORTE-->
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
		var msCorreoI = document.getElementById('txtCorreoI').value

		if(document.getElementById('txtCodEstudiantil').value=="")
		{
			document.getElementById('txtCodEstudiantil').focus();
			$.messager.alert('UMOJN','Falta el código estudiantil.','warning');
			return false;
		}

		if(document.getElementById('txtNombre1').value=="")
		{
			document.getElementById('txtNombre1').focus();
			$.messager.alert('UMOJN','Falta el Nombre.','warning');
			return false;
		}
		
		if(document.getElementById('txtApellido1').value=="")
		{
			document.getElementById('txtApellido1').focus();
			$.messager.alert('UMOJN','Falta el Apellido.','warning');
			return false;
		}

		if (document.getElementById('txtPaisNac').value=="")
		{
			document.getElementById('txtPaisNac').focus();
			$.messager.alert('UMOJN','Falta el país de nacimiento.','warning');
			return false;
		}

		if (document.getElementById('txtNacionalidad').value=="")
		{
			document.getElementById('txtNacionalidad').focus();
			$.messager.alert('UMOJN','Falta la nacionalidad.','warning');
			return false;
		}

		if (document.getElementById('txtLugarNac').value=="")
		{
			document.getElementById('txtLugarNac').focus();
			$.messager.alert('UMOJN','Falta el lugar de nacimiento.','warning');
			return false;
		}
		
		if (document.getElementById('txtCedula').value=="")
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','Falta la Cédula.','warning');
			return false;
		}

		if(mCedula.indexOf("-") > -1)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','Escriba la Cédula sin guiones.','warning');
			return false;
		}		

		if (existeCedula == true)
		{
			document.getElementById('txtCedula').focus();
			$.messager.alert('UMOJN','La Cédula ya fue registrada con el estudiante ' + codEstudiante,'warning');
			return false;
		}

		if (document.getElementById('optNacional2').checked)
		{
			if (document.getElementById('txtPasaporte').value=="")
			{
				document.getElementById('txtPasaporte').focus();
				$.messager.alert('UMOJN','Los extranjeros requieren el pasaporte.','warning');
				return false;
			}
		}

		if (msCorreoI.indexOf(" ") > -1)
		{
			document.getElementById('txtCorreoI').focus();
			$.messager.alert('UMOJN','Escriba el correo sin espacios en blanco.','warning');
			return false;
		}
/*
		msResultado = fxVerificaCorreo(msCorreoI)
		if (msResultado != "")
		{
			$.messager.alert('UMOJN','La dirección de correo ya fue usada con el estudiante ' + msResultado,'warning');
			return false;
		}
*/
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

	function fxVerificaCorreo(correo)
	{
		var datos = new FormData();
		var response;
		datos.append('correoEstudiante', correo);

		$.ajax({
			url: 'funciones/fxDatosEstudiantes.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: response
			}
		)
	}

	function fxEscribeCorreo()
	{
		var msNombre = document.getElementById('txtNombre1').value;
		var msApellido = document.getElementById('txtApellido1').value;
		var msCodigo = document.getElementById('txtEstudiante').value;

		if (msCodigo == "")
		{
			if (msNombre != "" && msApellido != "")
			{
				document.getElementById('txtCorreoI').value = msNombre.toLowerCase() + "." + msApellido.toLowerCase() + "@umojn.edu.ni"
			}
		}
	}

	function fxOptNacional()
	{
		var mbNacional = document.getElementById('optNacional1').checked;

		if (mbNacional)
		{
			document.getElementById('txtPasaporte').disabled = true;
		}
		else
		{
			document.getElementById('txtPasaporte').disabled = false;
		}
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

	function fxOptLaboral()
	{
		var mbLaboral = document.getElementById('optLaboral1').checked;

		if (mbLaboral)
		{
			document.getElementById('txtOcupacion').disabled = false;
			document.getElementById('txnSalario').disabled = false;
		}
		else
		{
			document.getElementById('txtOcupacion').disabled = true;
			document.getElementById('txnSalario').disabled = true;
		}
	}

	function fxOptTrabajaMadre()
	{
		var mbTrabaja = document.getElementById('optTrabajaMadre1').checked;

		if (mbTrabaja)
		{
			document.getElementById('txtTrabajoMadre').disabled = false;
			document.getElementById('txnSalarioMadre').disabled = false;
		}
		else
		{
			document.getElementById('txtTrabajoMadre').disabled = true;
			document.getElementById('txnSalarioMadre').disabled = true;
		}
	}

	function fxOptTrabajaPadre()
	{
		var mbTrabaja = document.getElementById('optTrabajaPadre1').checked;

		if (mbTrabaja)
		{
			document.getElementById('txtTrabajoPadre').disabled = false;
			document.getElementById('txnSalarioPadre').disabled = false;
		}
		else
		{
			document.getElementById('txtTrabajoPadre').disabled = true;
			document.getElementById('txnSalarioPadre').disabled = true;
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
	  obtenerCarnet();
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
		var objId = objeto.id;
		var datos = new FormData();
		var estudiante = $('#txtEstudiante').val();
		datos.append('CodEstudiante', estudiante);
		datos.append('CodImagen', objId);

		$.ajax({
			url: 'funciones/fxEstudiantesImagenes.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response) {
				if (response != 0) {
					document.getElementById('dvDOC').innerHTML = response;
				} else {
					$.messager.alert('UMOJN', 'Error en la eliminación de la imagen.', 'warning');
				}
			}
		});
	}
	window.onload=function()
	{
		if (document.getElementById("dtpFechaNac").value != "")
			calcularEdad();
	}

	$(document).ready(function() {
		$('#cmdSubir').on('click', function() {
			if ($('#txtEstudiante').val() == '') {
				$.messager.alert('UMOJN', 'Debe guardar la Información General antes de subir los Documentos de soporte.', 'warning');
				return false;
			}

			if ($('#txtRutaLocal').val() == '') {
				$.messager.alert('UMOJN', 'Falta el archivo de la imagen.', 'warning');
				return false;
			} else {
				var datos = new FormData();
				var files = $('#archivo')[0].files[0];
				var estudiante = $('#txtEstudiante').val();
				var tipo = $('#cboTipoDoc').val();
				var descripcion = $('select[name="cboTipoDoc"] option:selected').text();
				datos.append('archivo', files);
				datos.append('cboTipoDoc', tipo);
				datos.append('txtEstudiante', estudiante);
				datos.append('txtDescripcion', descripcion);

				$.ajax({
					url: 'funciones/fxEstudiantesImagenes.php',
					type: 'post',
					data: datos,
					contentType: false,
					processData: false,
					success: function(response) {
						if (response != 0) {
							document.getElementById('dvDOC').innerHTML = response;
							$('#txtRutaLocal').val('');
						} else {
							$.messager.alert('UMOJN', 'Error en la subida de la imagen.', 'warning');
						}
					}
				});
				return false;
			}
		});
	});

	$('form').submit(function(e){
		e.preventDefault();

		$.when(obtenerCedula()).done(function(respuesta)
		{
			if (respuesta != "")
				existeCedula = true;
			else
				existeCedula = false;

			codEstudiante = respuesta;
		})

		if (verificarFormulario() == true)
		{
			var texto;
			var datos;
			
			texto = '{"txtEstudiante":"' + document.getElementById("txtEstudiante").value + '", ';
			texto += '"cboColegio":"' + document.getElementById("cboColegio").value + '", ';
			texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
			texto += '"cboMunicipio":"' + document.getElementById("cboMunicipio").value + '", ';
			texto += '"dtpFechaIns":"' + document.getElementById("dtpFechaIns").value + '", ';
			texto += '"txnGeneracion":"' + document.getElementById("txnGeneracion").value + '", ';
			texto += '"txtCarnet":"' + document.getElementById("txtCarnet").value + '", ';
			texto += '"txtCodEstudiantil":"' + document.getElementById("txtCodEstudiantil").value + '", ';
			texto += '"txtNombre1":"' + document.getElementById("txtNombre1").value + '", ';
			texto += '"txtNombre2":"' + document.getElementById("txtNombre2").value + '", ';
			texto += '"txtApellido1":"' + document.getElementById("txtApellido1").value + '", ';
			texto += '"txtApellido2":"' + document.getElementById("txtApellido2").value + '", ';
			
			if (document.getElementById("optNacional1").checked)
			{
				texto += '"optNacional":"1", ';
				texto += '"txtPasaporte":"", ';
			}
			else
			{
				texto += '"optNacional":"0", ';
				texto += '"txtPasaporte":"' + document.getElementById("txtPasaporte").value + '", ';
			}

			texto += '"dtpFechaNac":"' + document.getElementById("dtpFechaNac").value + '", ';
			texto += '"txtPaisNac":"' + document.getElementById("txtPaisNac").value + '", ';
			texto += '"txtLugarNac":"' + document.getElementById("txtLugarNac").value + '", ';
			texto += '"txtNacionalidad":"' + document.getElementById("txtNacionalidad").value + '", ';
			texto += '"txtEtnia":"' + document.getElementById("txtEtnia").value + '", ';
			texto += '"txnPeso":"' + document.getElementById("txnPeso").value + '", ';
			texto += '"txnTalla":"' + document.getElementById("txnTalla").value + '", ';
			texto += '"cboTipoSangre":"' + document.getElementById("cboTipoSangre").value + '", ';
			texto += '"txtCedula":"' + document.getElementById("txtCedula").value + '", ';

			if (document.getElementById("optSexo1").checked)
				texto += '"optSexo":"M", ';
			else
				texto += '"optSexo":"F", ';
			
			texto += '"cboEstadoCivil":"' + document.getElementById("cboEstadoCivil").value + '", ';
			texto += '"cboNivelEstudio":"' + document.getElementById("cboNivelEstudio").value + '", ';
			texto += '"txnHijos":"' + document.getElementById("txnHijos").value + '", ';
			texto += '"txtTelefono":"' + document.getElementById("txtTelefono").value + '", ';
			texto += '"txtCelular":"' + document.getElementById("txtCelular").value + '", ';
			texto += '"txtCorreoE":"' + document.getElementById("txtCorreoE").value + '", ';
			texto += '"txtCorreoI":"' + document.getElementById("txtCorreoI").value + '", ';
			texto += '"txtDireccion":"' + document.getElementById("txtDireccion").value + '", ';
			texto += '"txtZona":"' + document.getElementById("txtZona").value + '", ';
			texto += '"cboMedio":"' + document.getElementById("cboMedio").value + '", ';
			texto += '"txtEmergencia":"' + document.getElementById("txtEmergencia").value + '", ';
			texto += '"txtTelEmergencia":"' + document.getElementById("txtTelEmergencia").value + '", ';
			texto += '"txtCelEmergencia":"' + document.getElementById("txtCelEmergencia").value + '",';

			if (document.getElementById("optNacional1").checked)
			{
				texto += '"optNacional":"1", ';
				texto += '"txtPasaporte":"", ';
			}
			else
			{
				texto += '"optNacional":"0", ';
				texto += '"txtPasaporte":"' + document.getElementById("txtPasaporte").value + '", ';
			}

			if (document.getElementById("optOtroIdioma1").checked)
			{
				texto += '"optOtroIdioma":"1", ';
				texto += '"txtIdioma":"' + document.getElementById("txtIdioma").value + '", ';
				texto += '"txtDominioIdioma":"' + document.getElementById("txtDominioIdioma").value + '", ';
			}
			else
			{
				texto += '"optOtroIdioma":"0", ';
				texto += '"txtIdioma":"", ';
				texto += '"txtDominioIdioma":"", ';
			}

			if (document.getElementById("optLaboral1").checked)
			{
				texto += '"optLaboral":"1", ';
				texto += '"txtOcupacion":"' + document.getElementById("txtOcupacion").value + '", ';
				texto += '"txnSalario":"' + document.getElementById("txnSalario").value + '", ';
			}
			else
			{
				texto += '"optLaboral":"0", ';
				texto += '"txtOcupacion":"", ';
				texto += '"txnSalario":"0", ';
			}

			texto += '"txtNombreMadre":"' + document.getElementById("txtNombreMadre").value + '", ';
			texto += '"txtNombrePadre":"' + document.getElementById("txtNombrePadre").value + '", ';
			texto += '"txtTelefonoMadre":"' + document.getElementById("txtTelefonoMadre").value + '", ';
			texto += '"txtTelefonoPadre":"' + document.getElementById("txtTelefonoPadre").value + '", ';
			texto += '"txtCelularMadre":"' + document.getElementById("txtCelularMadre").value + '", ';
			texto += '"txtCelularPadre":"' + document.getElementById("txtCelularPadre").value + '", ';

			if (document.getElementById("optTrabajaMadre1").checked)
			{
				texto += '"optTrabajaMadre":"1", ';
				texto += '"txtTrabajoMadre":"' + document.getElementById("txtTrabajoMadre").value + '", ';
				texto += '"txnSalarioMadre":"' + document.getElementById("txnSalarioMadre").value + '", ';
			}
			else
			{
				texto += '"optTrabajaMadre":"0", ';
				texto += '"txtTrabajoMadre":"", ';
				texto += '"txnSalarioMadre":"0", ';
			}

			if (document.getElementById("optTrabajaPadre1").checked)
			{
				texto += '"optTrabajaPadre":"1", ';
				texto += '"txtTrabajoPadre":"' + document.getElementById("txtTrabajoPadre").value + '", ';
				texto += '"txnSalarioPadre":"' + document.getElementById("txnSalarioPadre").value + '", ';
			}
			else
			{
				texto += '"optTrabajaPadre":"0", ';
				texto += '"txtTrabajoPadre":"", ';
				texto += '"txnSalarioPadre":"0", ';
			}

			texto += '"txnMenores":"' + document.getElementById("txnMenores").value + '", ';
			texto += '"txnMayores":"' + document.getElementById("txnMayores").value + '", ';
			texto += '"txnDependientes":"' + document.getElementById("txnDependientes").value + '", ';

			if (document.getElementById("optDiscapacidad1").checked)
				texto += '"optDiscapacidad":"1"}';
			else
				texto += '"optDiscapacidad":"0"}';
			
			datos = JSON.parse(texto);

			$.ajax({
				url:'catEstudiantes.php',
				type:'post',
				data:datos,
				beforeSend: function(){console.log(datos)}
			})
			.done(function(){location.href="gridEstudiantes.php"})
			.fail(function(){console.log('Error')});
			}
		}
	);
	
	function obtenerCedula()
	{
		mCedula = document.getElementById('txtCedula').value;
		parametros = '{"cedulaEstudiante":"' + document.getElementById("txtCedula").value + '", "codEstudiante":"' + document.getElementById("txtEstudiante").value + '"}';
		datosJson = JSON.parse(parametros);
	
		return $.ajax({
			url:'funciones/fxDatosEstudiantes.php',
			type:'post',
			async: false,
			data:datosJson,
			beforeSend: function(){console.log(datosJson)}
		})
	}

	function obtenerCarnet()
	{
		if (document.getElementById("txtEstudiante").value=="")
		{
			parametros = '{"fechaNac":"'+document.getElementById("dtpFechaNac").value+'", "codCarrera":"' +document.getElementById("cboCarrera").value+ '", "generacion":"'+document.getElementById("txnGeneracion").value+'"}';
			datosJson = JSON.parse(parametros);
		
			return $.ajax({
				url:'funciones/fxDatosEstudiantes.php',
				type:'post',
				async: false,
				data:datosJson,
				beforeSend: function(){console.log(datosJson)},
				success: function(response){document.getElementById('txtCarnet').value = response;}
			})
		}
	}
</script>