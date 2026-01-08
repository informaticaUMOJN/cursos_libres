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
	require_once ("funciones/fxDiasClase.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("hrrDiasClase");
		
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
			if (isset($_POST["txtDiaClase"]))
			{
                $msCodigo = $_POST["txtDiaClase"];
                $msAsignatura = $_POST["cboAsignatura"];
				$mnAnnoLectivo = $_POST["txnAnnoLectivo"];
				$mnDiaSemana = $_POST["cboDiaSemana"];
				$msFechaIni = $_POST["dtpFechaIni"];
				$msFechaFin = $_POST["dtpFechaFin"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarDiasClase($msAsignatura, $msAnnoLectivo, $mnDiaSemana, $msFechaIni, $msFechaFin);
						$msBitacora = $msCodigo . "; " . $msAsignatura . "; " . $msAnnoLectivo . "; " . $mnDiaSemana . "; " . $msFechaIni . "; " . $msFechaFin;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO080A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarDiasClase($msCodigo, $msAsignatura, $msAnnoLectivo, $mnDiaSemana, $msFechaIni, $msFechaFin);
						$msBitacora = $msCodigo . "; " . $msAsignatura . "; " . $msAnnoLectivo . "; " . $mnDiaSemana . "; " . $msFechaIni . "; " . $msFechaFin;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO080A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridDiasClase.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveDiasClase(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msAsignatura = $mFila["CARRERA_REL"];
					$msAnnoLectivo = $mFila["ANNOLECTIVO_080"];
					$mnDiaSemana = $mFila["DIASEMANA_080"];
                    $msFechaIni = $mFila["FECHAINI_080"];
                    $msFechaFin = $mFila["FECHAFIN_080"];
				}
				else
				{
					$msAsignatura = "";
					$msAnnoLectivo = "";
					$mnDiaSemana = 1;
                    $msFechaIni = date('Y-m-d');
                    $msFechaFin = date('Y-m-d');
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Días de clase</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="hrrDiasClase" name="hrrDiasClase" action="hrrDiasClase.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtDiaClase" class="col-sm-12 col-md-3 col-form-label">Día de clase</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtDiaClase" name="txtDiaClase" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboCarrera" class="col-sm-12 col-md-3 col-form-label">Carrera</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaAsignaturas(this.value)">
									<?php
                                        $msConsulta = "select CARRERA_REL from UMO060A where ASIGNATURA_REL = ?";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                        $mDatos->execute([$msAsignatura]);
                                        $mFila = $mDatos->fetch();
                                        $msCarrera = $mFila["CARRERA_REL"];
                                        
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
									?>
								</select>
							</div>
						</div>

                        <div class="form-group row">
							<label for="cboAsignatura" class="col-sm-12 col-md-3 col-form-label">Asignatura</label>
							<div class="col-sm-12 col-md-7">
								<select class="form-control" id="cboAsignatura" name="cboAsignatura">
									<?php
										$msConsulta = "select ASIGNATURA_REL, NOMBRE_060 from UMO060A order by NOMBRE_060";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
										while ($mFila = $mDatos->fetch())
										{
											$msValor = rtrim($mFila["ASIGNATURA_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_060"]);
                                            if ($msAsignatura == "")
                                            {
                                                echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                                $msCarrera = $msValor;
                                            }
                                            else
                                            {
                                                if ($msAsignatura == $msValor)
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
							<label for="txtAnnoLectivo" class="col-sm-12 col-md-3 col-form-label">Año lectivo</label>
                            <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="text" class="form-control" id="txtAnnoLectivo" name="txtAnnoLectivo" value="' . $msAnnoLectivo . '" />');
                            ?>
							</div>
						</div>

						<div class="form-group row">
							<label for="cboDiaSemana" class="col-sm-12 col-md-3 col-form-label">Día de la semana</label>
                            <div class="col-sm-12 col-md-3">
                                <select class="form-control" id="cboDiaSemana" name="cboDiaSemana">
                                    <option value='0' selected>Domingo</option>
                                    <option value='1' >Lunes</option>
                                    <option value='2' >Martes</option>
                                    <option value='3' >Miércoles</option>
                                    <option value='4' >Jueves</option>
                                    <option value='5' >Viernes</option>
                                    <option value='6' >Sábado</option>
                                </select>
                            </div>
						</div>

                        <div class = "form-group row">
                            <label for="dtpFechaIni" class="col-sm-12 col-md-3 form-label">Fecha inicial</label>
                            <div class="col-sm-12 col-md-3">
                            <?php echo('<input type="date" class="form-control" id="dtpFechaIni" name="dtpFechaIni" value="' . $msFechaIni . '" />'); ?>
                            </div>
                        </div>

                        <div class = "form-group row">
                            <label for="dtpFechaFin" class="col-sm-12 col-md-3 form-label">Fecha final</label>
                            <div class="col-sm-12 col-md-3">
                            <?php echo('<input type="date" class="form-control" id="dtpFechaFin" name="dtpFechaFin" value="' . $msFechaFin . '" />'); ?>
                            </div>
                        </div>

						<div class="row" style="margin-top: 1%">
							<label for="dgDIA" class="col-sm-12 col-md-3 form-label">Días hábiles</label>
							<div class="col-md-7">
								<table id="dgDIA" class="easyui-datagrid" style="width:100%" data-options="iconCls:'icon-edit', toolbar:'#tbDIA', nowrap:'false', striped:'true', singleSelect:true, method:'get', onClickCell: onClickCell">
									<thead>
										<tr>
											<th data-options="field:'diaClase', hidden:true">Dia de clase</th>
											<th data-options="field:'fecha', hidden:true">FechaSQL</th>
											<th data-options="field:'semana', width:'20%', align:'center'">Semana</th>
											<th data-options="field:'fechaGrid', width:'30%', align:'center'">Fecha</th>
											<th data-options="field:'habil',width:'20%',align:'center',editor:{type:'checkbox',options:{on:'x',off:''}}">Hábil</th>
										</tr>
									</thead>

									<?php
										$mDatos = fxObtenerDetDiasClase($msCodigo);
										while ($mFila = $mDatos->fetch())
										{
											echo ("<tr>");
											echo ("<td>" . $mFila["DIACLASE_REL"] . "</td>");
											echo ("<td>" . $mFila["FECHA_081"] . "</td>");
											echo ("<td>" . $mFila["SEMANA_REL"] . "</td>");
											$mdFecha = date_create_from_format('Y-m-d', $Fila["FECHA_081"]);
											echo ("<td>" . date_format($mdFecha, 'd/m/Y') . "</td>");

											if ($mFila['HABIL_081'] == 1)
												echo ("<td>x</td>");
											else
												echo ("<td>&nbsp;</td>");

											echo ("</tr>");
										}
									?>
								</table>
							</div>

							<div id="tbDIA" style="height:auto">
								<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append()">Agregar el Calendario de fechas</a>
								<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptit()">Aceptar</a>
								<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="reject()">Deshacer</a>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridDiasClase.php';"/>
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
<script>
window.onload = function() 
{
	var dgDiaClase = $('#dgDIA');
	dgDiaClase.datagrid({striped: true});

	$('.datagrid-wrap').width('100%');
	$('.datagrid-view').height('200px');
}

function verificarFormulario()
{		
    if(document.getElementById('txtAnnoLectivo').value=="")
    {
        $.messager.alert('UMOJN','Falta el año lectivo.','warning');
        return false;
    }

    if(document.getElementById('dtpFechaIni').value > document.getElementById('dtpFechaFin').value)
    {
        $.messager.alert('UMOJN','La fecha final es menor que la inicial.','warning');
        return false;
    }


    return true;
}

function llenaAsignaturas (carrera)
{
    var datos = new FormData();
    datos.append('carrera', carrera);

    $.ajax({
        url: 'funciones/fxDatosPlanEstudio.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
            document.getElementById('cboRequisito').innerHTML = response;
        }
    })
}

var editIndex = undefined;
var lastIndex;

$('#dgGRP').datagrid({
	onClickRow:function(rowIndex){
		if (lastIndex != rowIndex){
			$(this).datagrid('endEdit', lastIndex);
			$(this).datagrid('beginEdit', rowIndex);
		}
		lastIndexGRP = rowIndex;
	}
});

function endEditing(){
	if (editIndex == undefined){return true}
	if ($('#dgDIA').datagrid('validateRow', editIndex)){
		$('#dgDIA').datagrid('endEdit', editIndex);
		editIndex = undefined;
		return true;
	} else {
		return false;
	}
}

function onClickCell(index, field){
	if (editIndex != index){
		if (endEditing()){
			$('#dgDIA').datagrid('selectRow', index)
					.datagrid('beginEdit', index);
			var ed = $('#dgDIA').datagrid('getEditor', {index:index,field:field});
			if (ed){
				($(ed.target).data('checkbox') ? $(ed.target).textbox('checkbox') : $(ed.target)).focus();
			}
			editIndex = index;
		} else {
			setTimeout(function(){
				$('#dgDIA').datagrid('selectRow', editIndex);
			},0);
		}
	}
}

function append(){
	insertarNuevo();
}

function insertarNuevo() {
	var diaSemana = document.getElementById('cboDiaSemana').value;
	var fecha = new Date($('#dtpFechaIni').val());
	var fechaFin = new Date($('#dtpFechaFin').val());
	var numeroDia;
	var semana = 1;

	while(fecha <= fechaFin)
	{
		numeroDia = fecha.getDay();
		if (numeroDia == diaSemana)
		{
			fechaGrid = fecha.getDate() + '-' + (fecha.getMonth() + 1) + '-' + fecha.getFullYear();
			$('#dgDIA').datagrid('appendRow', {
				diaClase: $('#txtDiaClase').val(),
				fecha: fecha,
				semana: semana,
				fechaGrid: fechaGrid,
				habil: 'x'
			});
			semana++;
		}

		fecha.setDate(fecha.getDate() + 1);
	}
}
</script>