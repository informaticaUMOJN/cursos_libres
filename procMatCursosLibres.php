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
	require_once ("funciones/fxMatriculaCursos.php");
	require_once ("funciones/fxAlumnos.php");
	require_once ("funciones/fxModulos.php");

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
		$mbPermisoUsuario = fxPermisoUsuario("procMatCursosLibres");
		
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
				$msAlumno = $_POST["cboAlumno"];
				$msPlanEstudio = $_POST["cboPlanEstudio"];
				$mdFecha = $_POST["dtpFecha"];
				$msRecibo = $_POST["txtRecibo"];
				$mnBeca = $_POST["cboBeca"];
				$mnEstado = $_POST["cboEstado"];
				$msCursos = isset($_POST["cboCurso"]) ? $_POST["cboCurso"] : "";
				$mbDiploma = isset($_POST["chkDiploma"]) ? 1 : 0;
				$mbCedula = isset($_POST["chkCedula"]) ? 1 : 0;
				$mbActaNacimiento = isset($_POST["chkActaNac"]) ? 1 : 0;

				if ($msCodigo == "")
				{
					$msConsulta = "select MATCURSO_REL from UMO210A where ALUMNO_REL = ? and CURSOS_REL = ?";
					$mDatos = $m_cnx_MySQL->prepare($msConsulta);
					$mDatos->execute([$msAlumno, $msCursos]);
					
					if ($mDatos->rowCount() == 0)
					{
						$msCodigo = fxGuardarMatriculaCursos($msAlumno, $msCursos, $msPlanEstudio, $mdFecha, $msRecibo, $mnBeca, $mbDiploma, $mbCedula, $mbActaNacimiento, $mnEstado);
						$msBitacora = $msCodigo . "; " . $msAlumno . "; " . $msCursos . "; " . $msPlanEstudio . "; " . $mdFecha . "; " . $msRecibo . "; " . $mnBeca . "; " . $mbDiploma . "; "  . $mbCedula . "; " . $mbActaNacimiento . "; " . $mnEstado;
						fxAgregarBitacora($_SESSION["gsUsuario"], "UMO210A", $msCodigo, "", "Agregar", $msBitacora);

					}
					else
					{
						?><script>$.messager.alert('UMOJN', $('#cboAlumno option:selected').text() + ' ya fue matriculado en ' + $('#cboCurso option:selected').text(),'warning');</script><?php
					}
				}
				else
				{
					fxModificarMatricula($msCodigo, $msAlumno, $msCursos, $msPlanEstudio, $mdFecha, $msRecibo, $mnBeca, $mbDiploma,  $mbCedula, $mbActaNacimiento, $mnEstado);
					fxBorrarDetMatricula($msCodigo);
					$msBitacora = $msCodigo . "; " . $msAlumno . "; " . $msCursos . "; " . $msPlanEstudio . "; " . $mdFecha . "; " . $msRecibo . "; " . $mnBeca . "; " . $mbDiploma . "; " . $mbCedula . "; " . $mbActaNacimiento . "; " . $mnEstado;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO210A", $msCodigo, "", "Modificar", $msBitacora);
				}

				if (isset($_POST["gridAsignatura"]))
{
    $gridAsignatura = json_decode($_POST["gridAsignatura"], true);
    if (is_array($gridAsignatura))
    {
        foreach($gridAsignatura as $mRegistro)
        {
            $msModulo = $mRegistro['asignatura'];
            fxGuardarDetMatricula($msCodigo, $msModulo);
        }
    }
}

				?><meta http-equiv="Refresh" content="0;url=gridMatriculaCursosL.php"/><?php
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
				$RecordSet = fxDevuelveMatriculaCurso(0, $msCodigo);
				$mFila = $RecordSet->fetch();
				if ($msCodigo != "")
				{
					$msAlumno = $mFila["ALUMNO_REL"];
					$msCursos = $mFila["CURSOS_REL"];
					$msPlanEstudio = $mFila["PLANCURSO_REL"];
					$mdFecha = $mFila["FECHA_210"];
					$msRecibo = $mFila["RECIBO_210"];
					$mnBeca = $mFila["BECA_210"];
					$mbDiploma = $mFila["DIPLOMA_210"];
					$mbCedula = $mFila["CEDULA_210"];
					$mbActaNacimiento = $mFila["ACTANACIMIENTO_210"];
					$mnEstado = $mFila["ESTADO_210"];
				}
				else
				{
					if (isset($_POST["mAlumno"]))
						$msAlumno = $_POST["mAlumno"];
					else
						$msAlumno = "";
						$msCursos = "";
						$msPlanEstudio = "";
						$mdFecha = "";
						$msRecibo = "";
						$mnEstado = 0;
						$mbDiploma = 0;
						$mbCedula = 0;
						$mbActaNacimiento = 0;
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
				<form id="procMatCursosLibres" name="procMatCursosLibres" action="procMatCursosLibres.php" method="post" onsubmit="return prepararEnvio()">
	
				<div class = "form-group row">
							<label for="txtCodMatricula" class="col-sm-12 col-md-3 col-form-label">Código de la Matrícula</label>
							<div class="col-sm-12 col-md-3">
								<?php echo('<input type="text" class="form-control" id="txtCodMatricula" name="txtCodMatricula" value="' . $msCodigo . '" readonly />'); ?>
							</div>
						</div>
						
					<div class="form-group row">
							<label for="cboAlumno" class="col-sm-12 col-md-3 col-form-label">Estudiante</label>
							<div class="col-sm-12 col-md-7">
								<?php
								if ($msAlumno == "") {
									echo('<select class="form-control" id="cboAlumno" name="cboAlumno">');
									echo('<option value="">-- Seleccione un estudiante --</option>');
								} else {
									echo('<select class="form-control" id="cboAlumno" name="cboAlumno" disabled>');
								}

								$msConsulta = "SELECT ALUMNO_REL, NOMBRES_200, APELLIDOS_200 FROM UMO200A ORDER BY NOMBRES_200 DESC";
								$mDatos = $m_cnx_MySQL->prepare($msConsulta);
								$mDatos->execute();

								while ($mFila = $mDatos->fetch()) {
									$msValor = trim($mFila["ALUMNO_REL"]);
									$msTexto = trim($mFila["APELLIDOS_200"]) . ", " . trim($mFila["NOMBRES_200"]);

									if ($msAlumno != "" && $msAlumno == $msValor) {
										echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
									} elseif ($msAlumno == "") {
										echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
									}
								}

								echo('</select>');
								if ($msAlumno != "") {
									echo('<input type="hidden" name="cboAlumno" value="' . $msAlumno . '">');
								}
								?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboCurso" class="col-sm-12 col-md-3 col-form-label">Curso Libre</label>
							<div class="col-sm-12 col-md-7">
								<?php
									echo('<select class="form-control" id="cboCurso" name="cboCurso" onchange="llenaCombos(this.value)" >');

									$msConsulta = "SELECT CURSOS_REL, NOMBRE_190 FROM UMO190A ORDER BY NOMBRE_190";
									$mDatos = $m_cnx_MySQL->prepare($msConsulta);
									$mDatos->execute();
									while ($mFila = $mDatos->fetch())
									{
									$msValor = rtrim($mFila["CURSOS_REL"]);
									$msTexto = rtrim($mFila["NOMBRE_190"]);
										if ($msCursos == "")
										{
											echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											$msCursos = $msValor;
										}
										else
										{
											if ($msCursos == $msValor)
												echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
											else
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
										}
									}
									echo('</select>');
								?>
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
						
						<div class="form-group row">
							<label for="cboPlanEstudio" class="col-sm-12 col-md-3 col-form-label">Plan de estudio</label>
							<div class="col-sm-12 col-md-3">
								<select class="form-control" id="cboPlanEstudio" name="cboPlanEstudio">
									<?php
										$msConsulta = "select PLANCURSO_REL, PERIODO_220 from UMO220A where ACTIVO_220 = 1 order by PLANCURSO_REL";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["PLANCURSO_REL"]);
											$msTexto = "Período " . trim($mFila["PERIODO_220"]);
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

									if ($mbCedula == 1)
										echo('<input type="checkbox" name="chkCedula" id="chkCedula" checked > Cédula de identidad<br>');
									else
										echo('<input type="checkbox" name="chkCedula" id="chkCedula" > Cédula de identidad<br>');

									if ($mbActaNacimiento == 1)
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
							<label for="dgASG" class="col-sm-12 col-md-3 form-label">Modulo para inscripción</label>
							<div class="col-sm-auto col-md-7">
								<select class="form-control" id="cboModulo" name="cboModulo">
									<?php
										$mDatos = fxDevuelveModuloCurso($msCursos);
										while ($mFila = $mDatos->fetch())
										{
											$Valor = rtrim($mFila["MODULO_REL"]);
											$Texto = rtrim($mFila["NOMBRE_280"]);
											echo("<option value='" . $Valor . "'>" . $Texto . "</option>");
										}
									?>
								</select>
								<div id="dvASG">
									<table id="dgASG" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbASG', singleSelect:true, method:'get', onClickCell: onClickCell">
										<thead>
											<tr>
												<th data-options="field:'nombre',width:'100%',align:'left'"></th>
												<th data-options="field:'asignatura',hidden:'true'"></th>
											</tr>
										</thead>
										<?php
											$mDatos = fxDevuelveModuloMatricula($msCodigo);
											while ($mFila = $mDatos->fetch())
											{
												echo ("<tr>");
												echo ("<td>" . $mFila["NOMBRE_280"] . "</td>");
												echo ("<td>" . $mFila["MODULO_REL"] . "</td>");
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
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridMatriculaCursosL.php';"/>
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

		if(document.getElementById('txtRecibo').value=="" && document.getElementById('cboEstado').value!=2)
		{
			$.messager.alert('UMOJN','Falta el recibo.','warning');
			return false;
		}
		
		return true;
	}

	function cambiaEstudiante(estudiante)
	{
		llenaCurso (estudiante);
	}

	function llenaCombos(curso)
	{
		llenaPlanEstudio(curso);
		llenaModulo(curso);
	}

	function llenaCurso (estudiante)
	{
		var datos = new FormData();
		datos.append('alumnoCrl', estudiante);

		$.ajax({
			url: 'funciones/fxDatosMatCurso.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboCurso').value = response;
				llenaCombos(response);
			}
		})
	}

	function llenaPlanEstudio (curso)
	{
		var datos = new FormData();
		datos.append('cursoPe', curso);

		$.ajax({
			url: 'funciones/fxDatosMatCurso.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboPlanEstudio').innerHTML = response;
			}
		})
	}

	function llenaModulo (curso)
	{
		var datos = new FormData();
		datos.append('cursoAsg', curso);

		$.ajax({
			url: 'funciones/fxDatosMatCurso.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboModulo').innerHTML = response;
			}
		})
	}

	window.onload = function() 
	{
		var estudiante = $('#cboAlumno').val();
		var curso = $('#cboCurso').val();
		
		llenaPlanEstudio(curso);  
		llenaModulo(curso);    
		
		$('#dgASG').datagrid({striped: true});
		$('.datagrid-wrap').width('100%');
		$('.datagrid-view').height('200px');
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
    				if (datos.rows[i].asignatura == $('#cboModulo option:selected').val())
					existeAsignatura = true;
    			}
			}
			
			if (existeAsignatura == true)
			{
				$.messager.alert('UMOJN',$('#cboModulo option:selected').text() + ' ya fue incluido.','warning');
				$('#cboModulo').focus()
			}
			else
			{
				$('#dgASG').datagrid('appendRow',{asignatura:$('#cboModulo option:selected').val(), nombre:$('#cboModulo option:selected').text()});
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

	function prepararEnvio() {
    if (!verificarFormulario()) return false;

    $('#dgASG').datagrid('acceptChanges');
    var datosTabla = $('#dgASG').datagrid('getData').rows;
    var jsonTabla = JSON.stringify(datosTabla);
    if (document.getElementById('gridAsignaturaHidden')) {
        document.getElementById('gridAsignaturaHidden').value = jsonTabla;
    } else {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "gridAsignatura";
        input.id = "gridAsignaturaHidden";
        input.value = jsonTabla;
        document.getElementById("procMatCursosLibres").appendChild(input);
    }

    return true; 
}
</script>