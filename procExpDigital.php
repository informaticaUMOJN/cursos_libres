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
	require_once ("funciones/fxExpDigital.php");

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
		$mbPermisoUsuario = fxPermisoUsuario("procExpDigital");
		
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
			if (isset($_POST["txtExpDigital"]))
			{
				$msCodigo = $_POST["txtExpDigital"];
				$mdFecha = $_POST["dtpFecha"];
				$msCarrera = $_POST["txtCarrera"];

				if (isset($_POST["gridEstudiantes"]))
				    $gridEstudiantes = $_POST["gridEstudiantes"];
					
				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarExpDigital($mdFecha, $msCarrera);
					$msBitacora = $msCodigo . "; " . $mdFecha . "; " . "; " . $msCarrera;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO001B", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarExpDigital ($msCodigo, $mdFecha, $msCarrera);
					$msBitacora = $msCodigo . "; " . $mdFecha . "; " . "; " . $msCarrera;
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO001B", $msCodigo, "", "Modificar", $msBitacora);
				}

				foreach($gridEstudiantes as $mFila)
				{
					$mFolder = $mFila['folder'];
					$mCarnet = $mFila['carnet'];
					$mEstudiante = $mFila['estudiante'];
					$mRegistro = $mFila['registro'];
					$mTomo = $mFila['tomo'];
					$mFolio = $mFila['folio'];

                    fxGuardarDetExpDigital($msCodigo, $mCarnet, $mFolder, $mEstudiante, $mRegistro, $mTomo, $mFolio);
				}
				
				?><meta http-equiv="Refresh" content="0;url=gridExpDigital.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";

				$mnGeneracion = date('Y');
				
				if ($msCodigo != "")
				{
					$objRecordSet = fxDevuelveExpDigital(0, $msCodigo);
					$mFila = $objRecordSet->fetch();
					$mdFecha = $mFila["FECHA_001"];
					$msCarrera = $mFila["CARRERA_001"];
				}
				else
				{
					$msColegio = "";
					$mdFecha = date('Y-m-d');
					$msCarrera = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
                <div class="col-xs-12 col-md-12">
					<form name="procExpDigital" id="procExpDigital">
						<div class = "row">
							<div class="col-auto col-md-11">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridExpDigital.php';"/>
							</div>
						</div>

						<div id="tbTabs" class="easyui-tabs tabs-narrow" style="width:100%;height:auto">
							<!--Inicio del DIV de Tabs-->
							<div title="Estudiantes" style="padding-left: 20px; padding-top: 10px">
								<div class="col-sm-auto col-md-11 offset-md-1">
									<div class = "form-group row">
										<label for="txtExpDigital" class="col-sm-12 col-md-2 form-label">Expediente</label>
										<div class="col-sm-12 col-md-2">
										<?php
											echo('<input type="text" class="form-control" id="txtExpDigital" name="txtExpDigital" value="' . $msCodigo . '" readonly />'); 
										?>
										</div>
									</div>
									
									<div class = "form-group row">
										<label for="dtpFecha" class="col-sm-12 col-md-2 form-label">Fecha de registro</label>
										<div class="col-sm-12 col-md-2">
										<?php echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />'); ?>
										</div>
									</div>

									<div class = "form-group row">
										<label for="txtCarrera" class="col-sm-12 col-md-2 form-label">Carrera</label>
										<div class="col-sm-12 col-md-6">
										<?php 
											echo('<input type="text" class="form-control" id="txtCarrera" name="txtCarrera" value="' . $msCarrera . '" />');
										?>
										</div>
									</div>

									<div class="form-group row">
										<label for="dgEST" class="col-sm-12 col-md-2 form-label">Estudiantes</label>
										<div class="col-sm-12 col-md-8">
											<div id="dvEST">
												<table id="dgEST" class="easyui-datagrid table", data-options="iconCls:'icon-edit', toolbar:'#tbEST', singleSelect:true, method:'get', onClickCell: onClickCellEST">
													<thead>
														<tr>
															<th data-options="field:'expediente', hidden:'true'">Expediente</th>
															<th data-options="field:'folder', hidden:'true'">Folder</th>
															<th data-options="field:'carnet', width:'20%', align:'left'">Carnet</th>
															<th data-options="field:'estudiante', width:'50%', align:'left'">Estudiante</th>
															<th data-options="field:'registro', width:'10%', align:'center', editor:'text'">Registro</th>
															<th data-options="field:'tomo', width:'10%', align:'center', editor:'text'">Tomo</th>
															<th data-options="field:'folio', width:'10%', align:'center', editor:'text'">Folio</th>
														</tr>
													</thead>
													<tbody>
													<?php
														$mDatos = fxDevuelveDetExpDigital($msCodigo);

														while ($mFila = $mDatos->fetch())
														{
															echo('<tr>');
															echo('<td>' . rtrim($mFila['EXPDIGITAL_REL']) . '</td>');
															echo('<td>' . rtrim($mFila['FOLDER_002']) . '</td>');
															echo('<td>' . rtrim($mFila['CARNET_REL']) . '</td>');
															echo('<td>' . rtrim($mFila['NOMBRE_002']) . '</td>');
															echo('<td>' . rtrim($mFila['REGISTRO_002']) . '</td>');
															echo('<td>' . rtrim($mFila['TOMO_002']) . '</td>');
															echo('<td>' . rtrim($mFila['FOLIO_002']) . '</td>');
															echo('</tr>');
														}
													?>
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<div id="tbEST" style="height:auto">
										<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove'" onclick="removeitEST()">Borrar</a>
										<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" onclick="acceptitEST()">Salir del Modo de Edición</a>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab ESTUDIANTES-->

							<!--Inicio del DIV de Tab AGREGAR-->
							<div title="Agregar estudiantes" style="padding-left: 20px; padding-top: 10px">
								<div class="row">
									<div class="col-auto offset-sm-none col-md-10 offset-md-1">
										<div class="form-group row">
											<label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
											<div class="col-sm-12 col-md-7">
												<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaGrid()">
													<?php
														$msPrimerValor = "";
														$msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A order by NOMBRE_040";
														$mDatos = $m_cnx_MySQL->prepare($msConsulta);
														$mDatos->execute();
														while ($mFila = $mDatos->fetch())
														{
															$msValor = rtrim($mFila["CARRERA_REL"]);
															$msTexto = rtrim($mFila["NOMBRE_040"]);

															if ($msPrimerValor == "")
															{
																$msPrimerValor = $msValor;
																echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
															}
															else
																echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
														}
													?>
												</select>
											</div>
										</div>

										<div class = "form-group row">
											<label for="txnGeneracion" class="col-sm-12 col-md-2 form-label">Generación</label>
											<div class="col-sm-12 col-md-2">
											<?php 
												echo('<input type="number" class="form-control" id="txnGeneracion" name="txnGeneracion" value="' . $mnGeneracion . '" onchange="llenaGrid()" />');
											?>
											</div>
										</div>

										<div class = "form-group row">
											<label for="dgADD" class="col-sm-12 col-md-2 form-label">Agregar Estudiantes</label>
											<div class="col-sm-12 col-md-9">
												<div id="dvADD">
													<table id="dgADD" class="easyui-datagrid table", data-options="iconCls:'icon-edit', toolbar:'#tbADD', singleSelect:false, method:'get', onClickCell: onClickCellADD">
														<thead>
															<tr>
																<th data-options="field:'codigo', width:'20%', align:'left'">Código</th>
																<th data-options="field:'carnet', width:'20%', align:'left'">Carnet</th>
																<th data-options="field:'generacion', width:'10%', align:'left'">Generación</th>
																<th data-options="field:'estudiante', width:'50%', align:'left'">Estudiante</th>
															</tr>
														</thead>
														<tbody>
														<?php
															$msConsulta = "SELECT ESTUDIANTE_REL, GENERACION_010, CARNET_010, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 FROM UMO010A WHERE CARRERA_REL = ? AND GENERACION_010 = ?";
															$mDatos = $m_cnx_MySQL->prepare($msConsulta);
															$mDatos->execute([$msPrimerValor, $mnGeneracion]);

															while ($mFila = $mDatos->fetch())
															{
																echo('<tr>');
																echo('<td>' . rtrim($mFila['ESTUDIANTE_REL']) . '</td>');
																echo('<td>' . rtrim($mFila['CARNET_010']) . '</td>');
																echo('<td>' . rtrim($mFila['GENERACION_010']) . '</td>');
																if (trim($mFila["NOMBRE2_010"])!="")
																	$msNombre = trim($mFila["NOMBRE1_010"]) . " " . $mFila["NOMBRE2_010"] . " ";
																else
																	$msNombre = trim($mFila["NOMBRE1_010"]) . " ";

																if (trim($mFila["APELLIDO2_010"])!="")
																	$msNombre .= trim($mFila["APELLIDO1_010"]) . " " . trim($mFila["APELLIDO2_010"]);
																else
																	$msNombre .= trim($mFila["APELLIDO1_010"]);

																echo ("<td>" . $msNombre . " " . "</td>");
																echo('</tr>');
															}
														?>
														</tbody>
													</table>
												</div>
											</div>
										</div>									
									</div>

									<div id="tbADD" style="height:auto">
										<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add'" onclick="appendADD()">Agregar al expediente</a>
									</div>
								</div>
							</div>
							<!--Fin del DIV de Tab AGREGAR-->
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
	function verificarFormulario()
	{
		if(document.getElementById('txtCarrera').value=="")
		{
			document.getElementById('txtCarrera').focus();
			$.messager.alert('UMOJN','Falta la carrera.','warning');
			return false;
		}

		return true;
	}

	function llenaGrid()
	{
		var datos = new FormData();
		var carrera = document.getElementById('cboCarrera').value;
		var generacion = document.getElementById('txnGeneracion').value
		var response;
		datos.append('carrera', carrera);
		datos.append('generacion', generacion);

		$.ajax({
			url: 'funciones/fxDatosExpediente.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				datos = JSON.parse(response);
                $('#dgADD').datagrid({data: datos});
                $('#dgADD').datagrid('reload');
				}
			}
		)
	}

	/*Grid de Estudiantes*/
	var editIndexEST = undefined;
	var lastIndexEST;

	$('#dgEST').datagrid({
		onClickRow: function(rowIndex) {
			if (lastIndexEST != rowIndex) {
				$(this).datagrid('endEdit', lastIndexEST);
				$(this).datagrid('beginEdit', rowIndex);
			}
			lastIndexEST = rowIndex;
		}
	});

	function endEditingEST() {
		if (editIndexEST == undefined) {
			return true
		}
		if ($('#dgEST').datagrid('validateRow', editIndexEST)) {
			$('#dgEST').datagrid('endEdit', editIndexEST);
			editIndexEST = undefined;
			return true;
		} else {
			return false;
		}
	}

	function onClickCellEST(index, field) {
		if (editIndexEST != index) {
			if (endEditingEST()) {
				$('#dgEST').datagrid('selectRow', index)
					.datagrid('beginEdit', index);
				editIndexEST = index;
			} else {
				setTimeout(function() {
					$('#dgEST').datagrid('selectRow', editIndexEST);
				}, 0);
			}
		}
	}

	function appendEST(pCarnet, pEstudiante, pFolder){
		if (endEditingEST()){
			var i;
			var codigo;
			var existeEstudiante = false;
			var datos = $('#dgEST').datagrid('getData');
			var registros = $('#dgEST').datagrid('getRows').length;
			var mExpediente = $('#txtExpDigital').val();
			var mCarrera = $('#cboCarrera option:selected').text();
			
			if (registros > 0)
			{
				for (i=0; i<registros; i++)
				{
					if (datos.rows[i].carnet == pCarnet)
					existeEstudiante = true;
				}
			}
			
			if (existeEstudiante == true)
			{
				$.messager.alert('UMOJN','El estudiante ' + pEstudiante + ' ya fue incluido.','warning');
				return false;
			}
			else
			{
				$('#txtCarrera').val(mCarrera);
				$('#dgEST').datagrid('appendRow',{expediente: mExpediente, folder:pFolder, carnet:pCarnet, estudiante:pEstudiante, registro:'', tomo:'', folio:''});
				editIndexEST = $('#dgEST').datagrid('getRows').length;
				$('#dgEST').datagrid('selectRow', editIndexEST).datagrid('beginEdit', editIndexEST);
				$('#dgEST').datagrid('reload');
			}
		}
	}
		
	function removeitEST(){
		if (editIndexEST == undefined){return}
		$('#dgEST').datagrid('cancelEdit', editIndexEST)
				.datagrid('deleteRow', editIndexEST);
		editIndexEST = undefined;
	}

	function acceptitEST() {
		if (endEditingEST()) {
			$('#dgEST').datagrid('acceptChanges');
		}
	}

	/*Grid de Agregar*/
	var editIndexADD = undefined;
	var lastIndexADD;

	$('#dgADD').datagrid({
		onClickRow: function(rowIndex) {
			if (lastIndexADD != rowIndex) {
				$(this).datagrid('endEdit', lastIndexADD);
				$(this).datagrid('beginEdit', rowIndex);
			}
			lastIndexADD = rowIndex;
		}
	});

	function endEditingADD() {
		if (editIndexADD == undefined) {
			return true
		}
		if ($('#dgADD').datagrid('validateRow', editIndexADD)) {
			$('#dgADD').datagrid('endEdit', editIndexADD);
			editIndexADD = undefined;
			return true;
		} else {
			return false;
		}
	}

	function onClickCellADD(index, field) {
		if (editIndexADD != index) {
			if (endEditingADD()) {
				$('#dgADD').datagrid('selectRow', index)
					.datagrid('beginEdit', index);
				editIndexADD = index;
			} else {
				setTimeout(function() {
					$('#dgADD').datagrid('selectRow', editIndexADD);
				}, 0);
			}
		}
	}

	function appendADD(){
		if (endEditingADD()){
			var i;
			var carnet;
			var estudiante;
			var expediente = $('#txtExpDigital').val();
			var rows = $('#dgADD').datagrid('getSelections');
			var cuenta = rows.length;

			for (i=0; i<cuenta; i++)
			{
				folder = rows[i].codigo;
				carnet = rows[i].carnet;
				estudiante = rows[i].estudiante;

				$('#tbTabs').tabs('select', 'Estudiantes')	
				appendEST(carnet, estudiante, folder);
			}
		}
	}

	function acceptitADD() {
		if (endEditingADD()) {
			$('#dgADD').datagrid('acceptChanges');
		}
	}

	$('form').submit(function(e){
		e.preventDefault();

		if (verificarFormulario() == true)
		{
			var texto;
			var datos;
			var gridEstudiantes = $('#dgEST').datagrid('getData');
			
			texto = '{"txtExpDigital":"' + document.getElementById("txtExpDigital").value + '", ';
			texto += '"dtpFecha":"' + document.getElementById("dtpFecha").value + '", ';
			texto += '"txtCarrera":"' + document.getElementById("txtCarrera").value + '", ';
			
			registros = $('#dgEST').datagrid('getRows').length - 1;

			texto += '"gridEstudiantes": [';
			for (i = 0; i <= registros; i++) {
				texto += '{"expediente":"' + gridEstudiantes.rows[i].expediente;
				texto += '","folder":"' + gridEstudiantes.rows[i].folder;
				texto += '","carnet":"' + gridEstudiantes.rows[i].carnet;
				texto += '","estudiante":"' + gridEstudiantes.rows[i].estudiante;
				texto += '","registro":"' + gridEstudiantes.rows[i].registro;
				texto += '","tomo":"' + gridEstudiantes.rows[i].tomo;
				texto += '","folio":"' + gridEstudiantes.rows[i].folio;

				if (i == registros)
					texto += '"}]}';
				else
					texto += '"},';
			}

			datos = JSON.parse(texto);

			$.ajax({
				url:'procExpDigital.php',
				type:'post',
				data:datos,
				beforeSend: function(){console.log(datos)}
			})
			.done(function(){location.href="gridExpDigital.php"})
			.fail(function(){console.log('Error')});
			}
		}
	);
</script>