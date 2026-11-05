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
	require_once ("funciones/fxCursoslibres.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catCursosLibres");
		
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
				$msCodigo = $_POST["txtCursos"];
				$msDocente = $_POST ["cboDocente"];
				$mnTipoC = $_POST["cboTipoCurso"];
				$msNombre = $_POST["txtNombre"];
				$mnTurno = $_POST["optTurno"];
				$mnHoraInicio = $_POST["dtpHoraIni"];
				$mnHoraFin = $_POST["dtpHoraFin"];
				$mnTotalHoras = $_POST["txnTotalHoras"];
				$mnFechaInicio = $_POST["dtpFechaIni"];
				$mnFechaFin = $_POST ["dtpFechaFin"];
				$mnDia = isset($_POST["dias"]) ? implode(",", $_POST["dias"]) : null;
				$mnModalidad = $_POST["optModalidad"];
				$mnAcreditable = $_POST["optAcreditable"];
				$mnEstado = $_POST["optEstado"];
				$msCohorte = $_POST["txtCohorte"];
				$msPrograma = $_POST["txtPrograma"];
				
				if ($msCodigo == "") 
				{
					$msCodigo = fxGuardarCursosLibres($msDocente, $mnTipoC, $msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnTotalHoras, $mnFechaInicio, $mnFechaFin, $mnDia, $mnModalidad, $mnAcreditable, $mnEstado, $msCohorte, $msPrograma );
					$msBitacora = $msCodigo . "; ".$mnTipoC.";" . $msNombre.  ";". $mnTurno . ";". $mnHoraInicio. ";".$mnHoraFin.";".$mnTotalHoras.";". $mnFechaInicio.";". $mnFechaFin.";". $msDocente. ";" . $mnDia. ";" .$mnModalidad. ";" . $mnAcreditable . ";" . $mnEstado;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO190A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarCursosLibres($msCodigo, $msDocente, $mnTipoC, $msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnTotalHoras, $mnFechaInicio, $mnFechaFin, $mnDia, $mnModalidad, $mnAcreditable, $mnEstado, $msCohorte, $msPrograma );
					$msBitacora = $msCodigo . "; " .$mnTipoC.";". $msNombre.  ";". $mnTurno . ";". $mnHoraInicio. ";".$mnHoraFin.";". $mnTotalHoras.";".$mnFechaInicio.";". $mnFechaFin.";". $msDocente. ";" . $mnDia. ";" .$mnModalidad. ";". $mnAcreditable . ";" . $mnEstado;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO190A", $msCodigo, "", "Modificar", $msBitacora);
				}
				?><meta http-equiv="Refresh" content="0;url=gridCursosLibres.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveCursosLibres(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msDocente = $mFila["DOCENTECL_REL"];
					$mnTipoC = $mFila["TIPOC_190"];
					$msNombre = $mFila["NOMBRE_190"];
					$mnTurno = $mFila["TURNO_190"];
					$mnHoraInicio = $mFila["HRSINICIO_190"];
					$mnHoraFin = $mFila["HRSFIN_190"];
					$mnFechaInicio = $mFila["FECHAINICIO_190"];
					$mnFechaFin = $mFila["FECHAFIN_190"];
					$mnDia = $mFila["DIACLASES_190"];
					$mnModalidad = $mFila["MODALIDAD_190"];
					$mnTotalHoras=$mFila["HRSTOTAL_190"];
					$mnAcreditable = $mFila["ACREDITABLE_190"];
					$mnEstado = $mFila["ESTADO_190"];
					$msPrograma = $mFila["PROGRAMA_190"];
					$msCohorte = $mFila["COHORTE_190"];
				}
				else
				{
					$mnTipoC =0;
					$msNombre = "";
					$mnTurno = 1;
					$mnHoraInicio = date("H:i");
					$mnHoraFin    = date("H:i");
					$mnTotalHoras = "";
					$mnFechaInicio = date("Y-m-d");
					$mnFechaFin    = date("Y-m-d");
					$msDocente ="";
					$mnDia="";
					$mnModalidad=1;
					$mnAcreditable = 0;
					$mnEstado=0;
					$msPrograma = "";
					$msCohorte = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de Cursos Libres</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catCursosLibres" name="catCursosLibres" action="catCursosLibres.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtCursos" class="col-sm-12 col-md-2 col-form-label">Cursos</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtCursos" name="txtCursos" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>

							<div class = "form-group row">
							<label for="cboTipoCurso" class="col-sm-12 col-md-2 form-label">Tipo de curso</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboTipoCurso" name="cboTipoCurso">
									<?php

										if ($mnTipoC == 0)
											echo("<option value='0' selected >Curso de extensión</option>");
										else
											echo("<option value='0' >Curso de extensión</option>");

										if ($mnTipoC == 1)
											echo("<option value='1' selected >Conferencia</option>");
										else
											echo("<option value='1' >Conferencia</option>");

										if ($mnTipoC == 2)
											echo("<option value='2' selected >Taller</option>");
										else
											echo("<option value='2' >Taller</option>");

										if ($mnTipoC == 3)
											echo("<option value='3' selected >Diplomado de extensión</option>");
										else
											echo("<option value='3' >Diplomado de extensión</option>");

										if ($mnTipoC == 4)
											echo("<option value='4' selected >Seminario</option>");
										else
											echo("<option value='4' >Seminario</option>");

										if ($mnTipoC == 5)
											echo("<option value='5' selected >Curso de posgrado</option>");
										else
											echo("<option value='5' >Curso de posgrado</option>");
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
							<label for="txtPrograma" class="col-sm-12 col-md-2 col-form-label">Código Programa</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="text" class="form-control" id="txtPrograma" name="txtPrograma" value="' . $msPrograma . '" />'); ?>
							</div>
						</div>

						

						 <div class="form-group row">
							<label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
							<div class="col-sm-12 col-md-7">
								<?php
								$disabled = ($msCodigo != "") ? "" : "";
								echo "<select class='form-control' id='cboDocente' name='cboDocente' $>";

								try {
									if (!isset($m_cnx_MySQL)) {
										throw new Exception("Error: conexión no inicializada.");
									}

									if ($msCodigo == "") {
										if (trim($_SESSION["gsDocente"]) != "" && $mbAdministrador == 0 && $mbSupervisor == 0) {
											$msDocente = $_SESSION["gsDocente"];
											$msConsulta = "SELECT DOCENTECL_REL, NOMBRE_340 FROM UMO340A WHERE ACTIVO_340 = 1 AND DOCENTECL_REL = ? ORDER BY NOMBRE_340";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute([$msDocente]);
										} else {
											$msConsulta = "SELECT DOCENTECL_REL, NOMBRE_340 FROM UMO340A WHERE ACTIVO_340 = 1 ORDER BY NOMBRE_340";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute();
										}
									} else {
										$msConsulta = "SELECT DOCENTECL_REL, NOMBRE_340 FROM UMO340A ORDER BY NOMBRE_340 DESC";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
									}

									if ($mDatos->rowCount() == 0) {
										echo "<option value=''>-- No hay docentes --</option>";
									} else {
										while ($mFila = $mDatos->fetch(PDO::FETCH_ASSOC)) {
											$Docente = trim($mFila["DOCENTECL_REL"]);
											$Texto = trim($mFila["NOMBRE_340"]);
											$selected = ($msDocente == "" || $msDocente == $Docente) ? "selected" : "";
											echo "<option value='$Docente' $selected>$Texto</option>";
										}
									}

								} catch (Exception $e) {
									echo "<option value=''>Error: " . $e->getMessage() . "</option>";
								}

								echo "</select>";
								?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="dtpFechaIni" class="col-sm-12 col-md-2 col-form-label">Fecha inicio del curso</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="date" class="form-control" id="dtpFechaIni" name="dtpFechaIni" value="' . $mnFechaInicio . '" onchange="fxCohorte()" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="dtpFechaFin" class="col-sm-12 col-md-2 col-form-label">Fecha final del curso</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="date" class="form-control" id="dtpFechaFin" name="dtpFechaFin" value="' . $mnFechaFin . '" />'); ?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="dtpHoraIni" class="col-sm-12 col-md-2 col-form-label">Hora Inicio</label>
							<div class="col-sm-12 col-md-3">
							<input type="time" class="form-control" id="dtpHoraIni" name="dtpHoraIni" value="<?php echo $mnHoraInicio; ?>" step="60" />
							</div>
						</div>

						<div class = "form-group row">
							<label for="dtpHoraFin" class="col-sm-12 col-md-2 col-form-label">Hora Final</label>
							<div class="col-sm-12 col-md-3">
								<input type="time" class="form-control" id="dtpHoraFin" name="dtpHoraFin" value="<?php echo $mnHoraFin; ?>" step="60" />
							</div>
						</div>

						<div class = "form-group row">
							<label for="txnTotalHoras" class="col-sm-12 col-md-2 col-form-label">Total de horas</label>
							<div class="col-sm-12 col-md-3">
								<input type="number" class="form-control" id="txnTotalHoras" name="txnTotalHoras" value="<?php echo $mnTotalHoras; ?>" />
							</div>
						</div>

						<div class = "form-group row">
							<label for="txtCohorte" class="col-sm-12 col-md-2 col-form-label">Cohorte</label>
							<div class="col-sm-12 col-md-3">
							<?php echo('<input type="text" class="form-control" id="txtCohorte" name="txtCohorte" value="' . $msCohorte . '" />'); ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="chkDiaClases" class="col-sm-12 col-md-2 col-form-label">Día de clase</label>
							<div class="col-sm-12 col-md-10">
								<?php 
									$diasSeleccionados = explode(",", $mnDia);
									$todosDias = ["Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo"];
									foreach ($todosDias as $dia) {
										$checked = in_array($dia, $diasSeleccionados) ? "checked" : "";
										echo '<div class="form-check form-check-inline">
												<input class="form-check-input" type="checkbox" name="dias[]" value="'.$dia.'" id="'.$dia.'" '.$checked.'>
												<label class="form-check-label" for="'.$dia.'">'.$dia.'</label>
											</div>';
									}
								?>
							</div>
						</div>

						<div class="form-group row">
							<label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
							<div class="col-sm-12 col-md-10">
								<div class="radio">
								    <?php
										if ($mnTurno == 1)
											echo('<input type="radio" id="optTurno" name="optTurno" value="1" checked/>Diurno &nbsp');
										else
											echo('<input type="radio" id="optTurno" name="optTurno" value="1" />Diurno &nbsp');

										if ($mnTurno == 2)
											echo('<input type="radio" id="optTurno" name="optTurno" value="2"  checked/>Matutino');
										else
											echo(' <input type="radio" id="optTurno" name="optTurno" value="2"  />Matutino &nbsp ');

										if ($mnTurno == 3)
											echo('<input type="radio" id="optTurno" name="optTurno" value="3"  checked/>Vespertino');
										else
											echo(' <input type="radio" id="optTurno" name="optTurno" value="3"  />Vespertino &nbsp ');

										if ($mnTurno == 4)
											echo('<input type="radio" id="optTurno" name="optTurno" value="4"  checked/>Nocturno');
										else
											echo(' <input type="radio" id="optTurno" name="optTurno" value="4"  />Nocturno &nbsp ');

										if ($mnTurno == 5)
											echo('<input type="radio" id="optTurno" name="optTurno" value="5"  checked/>Sabatino');
										else
											echo(' <input type="radio" id="optTurno" name="optTurno" value="5"  />Sabatino &nbsp ');

										if ($mnTurno == 6)
											echo('<input type="radio" id="optTurno" name="optTurno" value="6"  checked/>Dominical ');
										else
											echo(' <input type="radio" id="optTurno" name="optTurno" value="6"  />Dominical  &nbsp ');
									?>	
								</div>
							</div>
						</div>

                      	<div class="form-group row">
							<label for="optModalidad" class="col-sm-auto col-md-2 form-label">Modalidad</label>
							<div class="col-sm-12 col-md-8">
								<div class="radio">
									<?php
										if($mnModalidad==1) 
											echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" checked /> Presencial');
										else
											echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" checked/> Presencial');

										if($mnModalidad==2)
											echo('&emsp;<input type="radio" id="optModalidad2" name="optModalidad" value="2" checked /> Por encuentro');
										else
											echo('&emsp;<input type="radio" id="optModalidad2" name="optModalidad" value="2" /> Por encuentro');

										if($mnModalidad==3)
											echo('&emsp;<input type="radio" id="optModalidad3" name="optModalidad" value="3" checked /> Virtual');
										else
											echo('&emsp;<input type="radio" id="optModalidad3" name="optModalidad" value="3" /> Virtual');

										if($mnModalidad==4)
											echo('&emsp;<input type="radio" id="optModalidad4" name="optModalidad" value="4" checked /> Mixta');
										else
											echo('&emsp;<input type="radio" id="optModalidad4" name="optModalidad" value="4" /> Mixta');
									?>
								</div>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="optAcreditable" class="col-sm-auto col-md-2 form-label">Acreditable</label>
							<div class="col-sm-12 col-md-3">
								<div class="radio">
									<?php
										if ($mnAcreditable == 1)
											echo('<input type="radio" id="optAcreditable1" name="optAcreditable" value="0" /> No &nbsp <input type="radio" id="optAcreditable2" name="optAcreditable" value="1" checked/> Si &nbsp');
										else
											echo('<input type="radio" id="optAcreditable1" name="optAcreditable" value="0" checked/> No  &nbsp <input type="radio" id="optAcreditable2" name="optAcreditable" value="1" /> Si &nbsp');
									?>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<label for="optEstado" class="col-sm-auto col-md-2 form-label">Activo</label>
							<div class="col-sm-12 col-md-3">
								<div class="radio">
									<?php
										if ($mnEstado == 1)
											echo('<input type="radio" id="optEstado1" name="optEstado" value="0" /> No &nbsp <input type="radio" id="optEstado2" name="optEstado" value="1" checked/> Si &nbsp');
										else
											echo('<input type="radio" id="optEstado1" name="optEstado" value="0" checked/> No  &nbsp <input type="radio" id="optEstado2" name="optEstado" value="1" /> Si &nbsp');
									?>
								</div>
							</div>
						</div>
						
						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-2">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridCursosLibres.php';"/>
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
function verificarFormulario() {
    if(document.getElementById('txtNombre').value=="") {
        $.messager.alert('UMOJN','Falta el Nombre.','warning');
        return false;  
    }

    if(document.getElementById('dtpHoraIni').value=="") {
        $.messager.alert('UMOJN','Falta la hora de inicio.','warning');
        return false;
    }

    if(document.getElementById('dtpHoraFin').value=="") {
        $.messager.alert('UMOJN','Falta la hora de finalización.','warning');
        return false;
    }

    if(document.getElementById('dtpFechaIni').value == "") {
        $.messager.alert('UMOJN','Falta la fecha de inicio.','warning');
        return false;
    }

    if(document.getElementById('dtpFechaFin').value == "") {
        $.messager.alert('UMOJN','Falta la fecha final.','warning');
        return false;
    }
/*
    // Validar horas
    var horaInicio = document.getElementById('dtpHoraIni').value;
    var horaFin = document.getElementById('dtpHoraFin').value;
    var hInicio = new Date("2000-01-01T" + horaInicio + ":00");
    var hFin = new Date("2000-01-01T" + horaFin + ":00");

    if (hInicio.getTime() === hFin.getTime()) {
        $.messager.alert('UMOJN','La hora inicial y la final no pueden ser iguales.','error');
        return false;
    }

    if (hInicio > hFin) {
        $.messager.alert('UMOJN','La hora inicial no puede ser mayor que la hora final.','error');
        return false;
    }

    // Validar fechas
    var fechaInicio = new Date(document.getElementById('dtpFechaIni').value);
    var fechaFin = new Date(document.getElementById('dtpFechaFin').value);

    if (fechaInicio.getTime() === fechaFin.getTime()) {
        $.messager.alert('UMOJN','La fecha inicial y final no pueden ser iguales.','error');
        return false;
    }

    if (fechaInicio > fechaFin) {
        $.messager.alert('UMOJN','La fecha inicial no puede ser mayor que la fecha final.','error');
        return false;
    }
*/
    return true;
}

function fxCohorte() {
    var fechaIni = document.getElementById("dtpFechaIni").value;
    var annoFecha = fechaIni.substr(0, 4);
    var mesFecha = parseInt(fechaIni.substr(5, 2));
    var resultado;

    resultado = mesFecha + "-" + annoFecha;
    document.getElementById("txtCohorte").value = resultado;
}

window.onload = function() {fxCohorte();}
</script>