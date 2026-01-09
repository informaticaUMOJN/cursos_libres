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
	require_once ("funciones/fxMatricula.php");
	require_once ("funciones/fxEstudiantes.php");
	require_once ("funciones/fxAsignaturas.php");

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
		$mbPermisoUsuario = fxPermisoUsuario("procMatricula");
		
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
				$msPlanEstudio = $_POST["cboPlanEstudio"];
				$mdFecha = $_POST["dtpFecha"];
				$mbPrimerIngreso = $_POST["optPrimerIngreso"];
				$mnAnnoIngreso = $_POST["txnAnnoIngreso"];
				$mnAnnoAcademico = $_POST["txnAnnoAcademico"];
				$msRecibo = $_POST["txtRecibo"];
				$mnBeca = $_POST["cboBeca"];
				$mbDiploma = $_POST["chkDiploma"];
				$mbNotas = $_POST["chkNotas"];
				$mbCedula = $_POST["chkCedula"];
				$mbActaNac = $_POST["chkActaNac"];
				$mnEstado = $_POST["cboEstado"];

				if ($msCodigo == "")
				{
					$msConsulta = "select MATRICULA_REL from UMO030A where ESTUDIANTE_REL = ? and CARRERA_REL = ?";
					$mDatos = $m_cnx_MySQL->prepare($msConsulta);
					$mDatos->execute([$msEstudiante, $msCarrera]);
					
					if ($mDatos->rowCount() == 0)
					{
						$msCodigo = fxGuardarMatricula ($msEstudiante, $msCarrera, $msPlanEstudio, $mdFecha, $mbPrimerIngreso, $mnAnnoIngreso, $mnAnnoAcademico, $msRecibo, $mnBeca, $mbDiploma, $mbNotas, $mbCedula, $mbActaNac, $mnEstado);
						$msBitacora = $msCodigo . "; " . $msEstudiante . "; " . $msCarrera . "; " . $msPlanEstudio . "; " . $mdFecha . "; " . $mbPrimerIngreso . "; " . $mnAnnoIngreso . "; " . $mnAnnoAcademico . "; " . $msRecibo . "; " . $mnBeca . "; " . $mbDiploma . "; " . $mbNotas . "; " . $mbCedula . "; " . $mbActaNac . "; " .  $mnEstado;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO030A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						?><script>$.messager.alert('UMOJN', $('#cboEstudiante option:selected').text() + ' ya fue matriculado en ' + $('#cboCarrera option:selected').text(),'warning');</script><?php
					}
				}
				else
				{
					fxModificarMatricula ($msCodigo, $msEstudiante, $msCarrera, $msPlanEstudio, $mdFecha, $mbPrimerIngreso, $mnAnnoIngreso, $mnAnnoAcademico, $msRecibo, $mnBeca, $mbDiploma, $mbNotas, $mbCedula, $mbActaNac, $mnEstado);
					fxBorrarDetMatricula ($msCodigo);
					$msBitacora = $msCodigo . "; " . $msEstudiante . "; " . $msCarrera . "; " . $msPlanEstudio . "; " . $mdFecha . "; " . $mbPrimerIngreso . "; " . $mnAnnoIngreso . "; " . $mnAnnoAcademico . "; " . $msRecibo . "; " . $mnBeca . "; " . $mnEstado;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO030A", $msCodigo, "", "Modificar", $msBitacora);
				}

				if (isset($_POST["gridAsignatura"]))
				{
					$gridAsignatura = $_POST["gridAsignatura"];
					foreach($gridAsignatura as $mRegistro)
					{
						$msAsignatura = $mRegistro['asignatura'];
						fxGuardarDetMatricula ($msCodigo, $msAsignatura);
					}
				}
				
				?><meta http-equiv="Refresh" content="0;url=gridMatricula.php"/><?php
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

				$RecordSet = fxDevuelveMatricula(0, $msCodigo);
				$mFila = $RecordSet->fetch();
				if ($msCodigo != "")
				{
					$msEstudiante = $mFila["ESTUDIANTE_REL"];
					$msCarrera = $mFila["CARRERA_REL"];
					$msPlanEstudio = $mFila["PLANESTUDIO_REL"];
					$mdFecha = $mFila["FECHA_030"];
					$mbPrimerIngreso = $mFila["PRIMERINGRESO_030"];
					$mnAnnoIngreso = $mFila["ANNOINGRESO_030"];
					$mnAnnoAcademico = $mFila["ANNOACADEMICO_030"];
					$msRecibo = $mFila["RECIBO_030"];
					$mnBeca = $mFila["BECA_030"];
					$mbDiploma = $mFila["DIPLOMA_030"];
					$mbNotas = $mFila["NOTAS_030"];
					$mbCedula = $mFila["CEDULA_030"];
					$mbActaNac = $mFila["ACTANACIMIENTO_030"];
					$mnEstado = $mFila["ESTADO_030"];
				}
				else
				{
					if (isset($_POST["mEstudiante"]))
						$msEstudiante = $_POST["mEstudiante"];
					else
						$msEstudiante = "";
					$msCarrera = "";
					$msPlanEstudio = "";
					$mdFecha = "";
					$mbPrimerIngreso = 0;
					$mnAnnoIngreso = 0;
					$mnAnnoAcademico = date("Y");
					$msRecibo = "";
					$mnBeca = 0;
					$mnEstado = 0;
					$mbDiploma = 0;
					$mbNotas = 0;
					$mbCedula = 0;
					$mbActaNac = 0;
					$mnEstado = 2; //Pre-matriculado
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Matrícula de estudiantes</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-xs-12 offset-sm-none col-md-10 offset-md-1">
					<form id="procMatricula" name="procMatricula" action="procMatricula.php" method="post" onsubmit="return verificarFormulario()">
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
										echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" disabled>');

									$msConsulta = "select ESTUDIANTE_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 from UMO010A order by APELLIDO1_010, NOMBRE1_010 desc";
									$mDatos = $m_cnx_MySQL->prepare($msConsulta);
									$mDatos->execute();

									while ($mFila = $mDatos->fetch())
									{
										$msValor = trim($mFila["ESTUDIANTE_REL"]);
										$msTexto = trim($mFila["APELLIDO1_010"]);
										if (trim($mFila["APELLIDO2_010"]) != "")
											$msTexto .= " " . $mFila["APELLIDO2_010"];
										$msTexto .= ", " . $mFila["NOMBRE1_010"];
										if (trim($mFila["NOMBRE2_010"]) != "")
											$msTexto .= " " . $mFila["NOMBRE2_010"];

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
							<label for="cboCurso" class="col-sm-12 col-md-3 col-form-label">Carrera</label>
							<div class="col-sm-12 col-md-7">
								<?php
									echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaCombos(this.value)" disabled>');

									$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A order by NOMBRE_040";
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
							<label for="cboPlanEstudio" class="col-sm-12 col-md-3 col-form-label">Plan de estudio</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboPlanEstudio" name="cboPlanEstudio">
									<?php
										$msConsulta = "select PLANESTUDIO_REL, PERIODO_050 from UMO050A where ACTIVO_050 = 1 order by PLANESTUDIO_REL";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["PLANESTUDIO_REL"]);
											$msTexto = "Período " . trim($mFila["PERIODO_050"]);
											if ($msPlanEstudio == "")
											{
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
												$msPlanEstudio = $msValor;
											}
											else
											{
												if ($msPlanEstudio == $msValor)
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
										echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . date("Y-m-d") . '" disabled />');
									else
										echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" disabled />');
								?>
							</div>
						</div>

						<div class = "form-group row">
							<label for="optPrimerIngreso" class="col-sm-12 col-md-3 form-label">Primera vez en UMOJN</label>
							<div class="col-sm-12 col-md-4">
								<div class = "radio">
									<?php
										if ($mbPrimerIngreso == 1)
											echo('<input type="radio" id="OptPrimero1" name="optPrimerIngreso" value="0" /> No <input type="radio" id="OptPrimero2" name="optPrimerIngreso" value="1" checked="checked" /> Si');
										else
											echo('<input type="radio" id="OptPrimero1" name="optPrimerIngreso" value="0" checked="checked" /> No <input type="radio" id="OptPrimero2" name="optPrimerIngreso" value="1" /> Si');
									?>
								</div>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txnAnnoIngreso" class="col-sm-12 col-md-3 col-form-label">Año de ingreso</label>
							<div class="col-sm-12 col-md-3">
								<?php
									echo('<input type="number" style="text-align:right" class="form-control" id="txnAnnoIngreso" name="txnAnnoIngreso" value="' . $mnAnnoIngreso . '" disabled />');
								?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txnAnnoAcademico" class="col-sm-12 col-md-3 col-form-label">Año académico</label>
							<div class="col-sm-12 col-md-3">
								<?php
									echo('<input type="number" style="text-align:right" class="form-control" id="txnAnnoAcademico" name="txnAnnoAcademico" value="' . $mnAnnoAcademico . '" />');
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
							<label for="cboBeca" class="col-sm-12 col-md-3 form-label">Beca</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboBeca" name="cboBeca">
									<?php
										if ($mnBeca == 0)
											echo("<option value='0' selected >Sin beca</option>");
										else
											echo("<option value='0' >Sin beca</option>");

										if ($mnBeca == 1)
											echo("<option value='1' selected >Beca 50%</option>");
										else
											echo("<option value='1' >Beca 50%</option>");

										if ($mnBeca == 2)
											echo("<option value='2' selected >Beca 25%</option>");
										else
											echo("<option value='2' >Beca 25%</option>");

											if ($mnBeca == 3)
											echo("<option value='3' selected >Beca 16%</option>");
										else
											echo("<option value='3' >Beca 16%</option>");
									?>
								</select>
							</div>
						</div>

						<div class = "form-group row">
							<label class="col-sm-12 col-md-3 form-label">Documentos entregados</label>
							<div class="col-sm-12 col-md-8">
								<?php
									if ($mbDiploma == 1)
										echo('<input type="checkbox" name="chkDiploma" id="chkDiploma" checked > Diploma de bachiller<br>');
									else
										echo('<input type="checkbox" name="chkDiploma" id="chkDiploma" > Diploma de bachiller<br>');

									if ($mbNotas == 1)
										echo('<input type="checkbox" name="chkNotas" id="chkNotas" checked > Notas de secundaria<br>');
									else
										echo('<input type="checkbox" name="chkNotas" id="chkNotas" > Notas de secundaria<br>');

									if ($mbCedula == 1)
										echo('<input type="checkbox" name="chkCedula" id="chkCedula" checked > Cédula de identidad<br>');
									else
										echo('<input type="checkbox" name="chkCedula" id="chkCedula" > Cédula de identidad<br>');

									if ($mbActaNac == 1)
										echo('<input type="checkbox" name="chkActaNac" id="chkActaNac" checked > Acta de nacimiento');
									else
										echo('<input type="checkbox" name="chkActaNac" id="chkActaNac" > Acta de nacimiento');
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

						<div class = "form-group row">
							<label for="dgASG" class="col-sm-12 col-md-3 form-label">Asignatura para inscripción</label>
							<div class="col-sm-auto col-md-7">
								<select class="form-control" id="cboAsignatura" name="cboAsignatura">
									<?php
										$mDatos = fxDevuelveAsignaturaCarrera($msCarrera);
										while ($mFila = $mDatos->fetch())
										{
											$Valor = rtrim($mFila["ASIGNATURA_REL"]);
											$Texto = rtrim($mFila["NOMBRE_060"]);
											echo("<option value='" . $Valor . "'>" . $Texto . "</option>");
										}
									?>
								</select>
								<div id="dvASG">
									<table id="dgASG" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbASG', singleSelect:true, method:'get', onClickCell: onClickCell">
										<thead>
											<tr>
												<th data-options="field:'nombre',width:'100%',align:'left'">Asignatura</th>
												<th data-options="field:'asignatura',hidden:'true'"></th>
											</tr>
										</thead>
										<?php
											$mDatos = fxDevuelveAsignaturaMatricula($msCodigo);
											while ($mFila = $mDatos->fetch())
											{
												echo ("<tr>");
												echo ("<td>" . $mFila["NOMBRE_060"] . "</td>");
												echo ("<td>" . $mFila["ASIGNATURA_REL"] . "</td>");
												echo ("</tr>");
											}
										?>
									</table>
								</div>
							</div>
						</div>
						
						<div id="tbASG" style="height:auto">
							<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append()">Agregar</a>
							<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeit()">Borrar</a>
							<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptit()">Aceptar</a>
							<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="reject()">Deshacer</a>
						</div>
						<div class = "row">
							<div class="col-auto offset-sm-none col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridMatricula.php';"/>
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
		
		if(document.getElementById('txnAnnoAcademico').value<=0)
		{
			$.messager.alert('UMOJN','Falta el año académico.','warning');
			return false;
		}

		if(document.getElementById('txtRecibo').value=="" && document.getElementById('cboEstado').value!=2)
		{
			$.messager.alert('UMOJN','Falta el recibo.','warning');
			return false;
		}
		
		return true;
	}

	function cambiaEstudiante(estudiante)
	{
		llenaGeneracion (estudiante);
		llenaCarrera (estudiante);
	}

	function llenaCombos(carrera)
	{
		llenaPlanEstudio(carrera);
		llenaAsignaturas(carrera);
	}

	function llenaCarrera (estudiante)
	{
		var datos = new FormData();
		datos.append('estudianteCrr', estudiante);

		$.ajax({
			url: 'funciones/fxDatosMatricula.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboCarrera').value = response;
				llenaCombos(response);
			}
		})
	}

	function llenaGeneracion (estudiante)
	{
		var datos = new FormData();
		datos.append('estudianteGen', estudiante);

		$.ajax({
			url: 'funciones/fxDatosMatricula.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('txnAnnoIngreso').value = response;
			}
		})
	}

	function llenaPlanEstudio (carrera)
	{
		var datos = new FormData();
		datos.append('carreraPe', carrera);

		$.ajax({
			url: 'funciones/fxDatosMatricula.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboPlanEstudio').innerHTML = response;
			}
		})
	}

	function llenaAsignaturas (carrera)
	{
		var datos = new FormData();
		datos.append('carreraAsg', carrera);

		$.ajax({
			url: 'funciones/fxDatosMatricula.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboAsignatura').innerHTML = response;
			}
		})
	}

	window.onload = function() 
	{
		var carrera = $('#cboCarrera').val();
		var estudiante = $('#cboEstudiante').val();
        var dgAsignatura = $('#dgASG');
		dgAsignatura.datagrid({striped: true});

		$('.datagrid-wrap').width('100%');
		$('.datagrid-view').height('200px');

		llenaCarrera(estudiante)
		llenaGeneracion(estudiante);
	}

	var editIndex = undefined;
	var lastIndex;
	
	$('#dgASG').datagrid({
		onClickRow:function(rowIndex){
			if (lastIndex != rowIndex){
				$(this).datagrid('endEdit', lastIndex);
				$(this).datagrid('beginEdit', rowIndex);
			}
			lastIndex = rowIndex;
		}
	});
	
	function endEditing(){
		if (editIndex == undefined){return true}
		if ($('#dgASG').datagrid('validateRow', editIndex)){
			$('#dgASG').datagrid('endEdit', editIndex);
			editIndex = undefined;
			return true;
		} else {
			return false;
		}
	}
	
	function onClickCell(index, field){
		if (editIndex != index){
			if (endEditing()){
				$('#dgASG').datagrid('selectRow', index)
						.datagrid('beginEdit', index);
				editIndex = index;
			} else {
				setTimeout(function(){
					$('#dgASG').datagrid('selectRow', editIndex);
				},0);
			}
		}
	}

	function append(){
		if (endEditing()){
			var i;
			var codigo;
			var existeAsignatura = false;
			var datos = $('#dgASG').datagrid('getData');
			var registros = $('#dgASG').datagrid('getRows').length;
			
			if (registros > 0)
            {
    			for (i=0; i<registros; i++)
    			{
    				if (datos.rows[i].asignatura == $('#cboAsignatura option:selected').val())
					existeAsignatura = true;
    			}
			}
			
			if (existeAsignatura == true)
			{
				$.messager.alert('UMOJN',$('#cboAsignatura option:selected').text() + ' ya fue incluido.','warning');
				$('#cboAsignatura').focus()
			}
			else
			{
				$('#dgASG').datagrid('appendRow',{asignatura:$('#cboAsignatura option:selected').val(), nombre:$('#cboAsignatura option:selected').text()});
				editIndex = $('#dgASG').datagrid('getRows').length;
				$('#dgASG').datagrid('selectRow', editIndex).datagrid('beginEdit', editIndex);
			}
		}
	}
		
	function removeit(){
		if (editIndex == undefined){return}
		$('#dgASG').datagrid('cancelEdit', editIndex)
				.datagrid('deleteRow', editIndex);
		editIndex = undefined;
	}
	
	function acceptit(){
		if (endEditing()){
			$('#dgASG').datagrid('acceptChanges');
		}
	}
	
	function reject(){
		$('#dgASG').datagrid('rejectChanges');
		editIndex = undefined;
	}

	$('form').submit(function(e){
		e.preventDefault();

		if (verificarFormulario() == true)
		{
			var texto;
			var datos;
			var registros;
			var i;
			var gridAsignatura = $('#dgASG').datagrid('getData');
			
			texto = '{"Guardar":"1", ';
			texto += '"txtCodMatricula":"' + document.getElementById("txtCodMatricula").value + '", ';
			texto += '"cboEstudiante":"' + document.getElementById("cboEstudiante").value + '", ';
			texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
			texto += '"cboPlanEstudio":"' + document.getElementById("cboPlanEstudio").value + '", ';
			texto += '"dtpFecha":"' + document.getElementById("dtpFecha").value + '", ';
			
			if (document.getElementById("OptPrimero2").checked)
				texto += '"optPrimerIngreso":"1", ';
			else
				texto += '"optPrimerIngreso":"0", ';
			
			texto += '"txnAnnoIngreso":"' + document.getElementById("txnAnnoIngreso").value + '", ';
			texto += '"txnAnnoAcademico":"' + document.getElementById("txnAnnoAcademico").value + '", ';
			texto += '"txtRecibo":"' + document.getElementById("txtRecibo").value + '", ';
			texto += '"cboBeca":"' + document.getElementById("cboBeca").value + '", ';
			
			if (document.getElementById("chkDiploma").checked)
				texto += '"chkDiploma":"1", ';
			else
				texto += '"chkDiploma":"0", ';
			
			if (document.getElementById("chkNotas").checked)
				texto += '"chkNotas":"1", ';
			else
				texto += '"chkNotas":"0", ';

			if (document.getElementById("chkCedula").checked)
				texto += '"chkCedula":"1", ';
			else
				texto += '"chkCedula":"0", ';
			
			if (document.getElementById("chkActaNac").checked)
				texto += '"chkActaNac":"1", ';
			else
				texto += '"chkActaNac":"0", ';
			
			texto += '"cboEstado":"' + document.getElementById("cboEstado").value + '", ';

			registros = $('#dgASG').datagrid('getRows').length - 1;
			
			if (registros >= 0)
			{
				texto += '"gridAsignatura": [';
				for (i=0; i<=registros; i++)
				{
					texto += '{"nombre":"' + gridAsignatura.rows[i].nombre + '", "asignatura":"' + gridAsignatura.rows[i].asignatura;
					if (i==registros)
						texto += '"}]}';
					else
						texto += '"},';
				}
			}
			else
				texto += '"gridAsignatura": []}';
			
			datos = JSON.parse(texto);

			$.ajax({
				url:'procMatricula.php',
				type:'post',
				data:datos,
				beforeSend: function(){console.log(datos)}	
			})
			.done(function(){location.href="gridMatricula.php";})
			.fail(function(){console.log('Error')});
		}	
	});
</script>