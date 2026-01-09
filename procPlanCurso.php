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
    require_once ("funciones/fxPlanCurso.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("procPlanCurso");
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
			if (isset($_POST["txtPlanCurso"])) {
                $msCodigo = $_POST["txtPlanCurso"];
                $msCurso = $_POST["cboCurso"];
                $msPeriodo = $_POST["txtPeriodo"];
                $mnHoras = $_POST["txnHoras"];
                $mnTurno = $_POST["optTurno"];
                $mnRegimen = $_POST["optRegimen"];
                $mnModalidad = $_POST["optModalidad"];
                $mnEncuentros = $_POST["txnEncuentrosTotal"];
                $mbActivo = $_POST["optActivo"];
				
				 if ($msCodigo == "") {
                    $msCodigo = fxGuardarPlanCurso($msCurso, $msPeriodo, $mnHoras, $mnTurno, $mnRegimen, $mnModalidad, $mnEncuentros, $mbActivo);
                    $msBitacora = "$msCodigo; $msCurso; $msPeriodo; $mnHoras; $mnEncuentros; $mnTurno; $mnRegimen; $mnModalidad; $mbActivo";
                    fxAgregarBitacora($_SESSION["gsUsuario"], "UMO220A", $msCodigo, "", "Agregar", $msBitacora);
                } else {
                    fxModificarPlanCurso($msCodigo, $msCurso, $msPeriodo, $mnHoras, $mnTurno, $mnRegimen, $mnModalidad, $mnEncuentros, $mbActivo);
                    fxBorrarDetPlanCurso($msCodigo);
                    $msBitacora = "$msCodigo; $msCurso; $msPeriodo; $mnHoras; $mnEncuentros; $mnTurno; $mnRegimen; $mnModalidad; $mbActivo";
                    fxAgregarBitacora($_SESSION["gsUsuario"], "UMO220A", $msCodigo, "", "Modificar", $msBitacora);
                }
                if (!empty($_POST['gridDetalle'])) {
                    $gridDetalle = json_decode($_POST['gridDetalle'], true);
                    foreach ($gridDetalle as $row) {
                        fxGuardarDetPlanCurso(
                            $msCodigo,           // Plan del curso
                            $row['modulo'], 
                            $row['hPresenciales'], 
                            $row['hAutoestudio'], 
                            $row['hTotales'], 
                            $row['encuentros'],
                            $row['periodo']
                        );
                    }  } ?><meta http-equiv="Refresh" content="0;url=gridPlanCurso.php"/><?php
						}
						else
							{
								if (isset($_POST["UMOJN"]))
									$msCodigo = $_POST["UMOJN"];
								else
									$msCodigo = "";
								if ($msCodigo != "")
									{
                                        $mRecordSet = fxDevuelvePlanCurso(0, $msCodigo);
                                        $mFila = $mRecordSet->fetch();
                                        $msCurso = $mFila["CURSOS_REL"];
                                        $msPeriodo = $mFila["PERIODO_220"];
                                        $mnHoras = $mFila["HORAS_220"];
                                        $mnTurno = $mFila["TURNO_220"];
                                        $mnRegimen = $mFila["REGIMEN_220"];
                                        $mnModalidad = $mFila["MODALIDAD_220"];
                                        $mnEncuentros = $mFila["ENCUENTRO_220"];
                                        $mbActivo = $mFila["ACTIVO_220"];

									}
									else
										{
										$msCurso = "";
                                        $msPeriodo = 0;
                                        $mnHoras = 0;
                                        $mnTurno = 1;
                                        $mnRegimen = 1;
                                        $mnModalidad = 1;
                                        $mnEncuentros = 0;
                                        $mbActivo = 0;
										}
						?>
                        
<div class="container text-left">
   <div class="container text-left">
    <div id="DivContenido">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <form id="procPlanCurso" name="procPlanCurso" method="post" action="procPlanCurso.php">
                    <input type="hidden" id="gridDetalle" name="gridDetalle" value="" />
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary"  onclick="location.href='gridPlanCurso.php';" />
                        </div>
                    </div>
                    <div class="easyui-tabs tabs-narrow" style="width:100%;height:auto">
                        <div title="Generales" style="padding:10px">
                            <div class="col-sm-auto offset-sm-0 col-md-11 offset-md-1">
                                <div class="form-group row">
                                    <label for="txtPlanCurso" class="col-sm-auto col-md-2 col-form-label">Código del Plan</label>
                                    <div class="col-sm-12 col-md-3">
                                        <input type="text" class="form-control" id="txtPlanCurso" name="txtPlanCurso" value="<?php echo $msCodigo; ?>" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="cboCurso" class="col-sm-12 col-md-2 col-form-label">Curso</label>
                                    <div class="col-sm-12 col-md-6">
                                        <select class="form-control" id="cboCurso" name="cboCurso" onchange="llenaModulos(this.value)">
                                            <option value="">Seleccione un curso</option>
                                            <?php
                                            $msConsulta = "SELECT CURSOS_REL, NOMBRE_190 FROM UMO190A ORDER BY NOMBRE_190";
                                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                            $mDatos->execute();
                                            while ($mFila = $mDatos->fetch())
                                            {
                                                $msValor = rtrim($mFila["CURSOS_REL"]);
                                                $msTexto = rtrim($mFila["NOMBRE_190"]);
                                                $selected = (isset($msCurso) && $msCurso == $msValor) ? "selected" : "";
                                                echo "<option value='$msValor' $selected>$msTexto</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                        <label for="txtPeriodo" class="col-sm-auto col-md-2 form-label">Periodo</label>
                                        <div class="col-sm-12 col-md-2">
                                            <?php echo('<input type="text" class="form-control" id="txtPeriodo" name="txtPeriodo" value="' . $msPeriodo . '"  />'); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="txnHoras" class="col-sm-auto col-md-2 form-label">Horas</label>
                                        <div class="col-sm-12 col-md-2">
                                            <?php echo('<input type="number" class="form-control" id="txnHoras" name="txnHoras" value="' . $mnHoras . '" readonly />'); ?>
                                        </div>
                                    </div>
                                
                                    <div class="form-group row">
                                        <label for="txnEncuentrosTotal" class="col-sm-auto col-md-2 form-label">Encuentros</label>
                                        <div class="col-sm-12 col-md-2">
                                            <?php echo('<input type="number" class="form-control" id="txnEncuentrosTotal" name="txnEncuentrosTotal" value="' . $mnEncuentros . '" readonly />'); ?>
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
                                                        echo('<input type="radio" id="optModalidad1" name="optModalidad" value="1" /> Presencial');

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
                                        <label for="optRegimen" class="col-sm-auto col-md-2 form-label">Régimen</label>
                                        <div class="col-sm-12 col-md-8">
                                            <div class="radio">
                                                <input type="radio" id="optRegimen1" name="optRegimen" value="1" <?php if ($mnRegimen==1) echo "checked"; ?> onclick="mostrarRegimen(this)"> Mensual
                                                &emsp;<input type="radio" id="optRegimen2" name="optRegimen" value="2" <?php if ($mnRegimen==2) echo "checked"; ?> onclick="mostrarRegimen(this)"> Bimestral
                                                &emsp;<input type="radio" id="optRegimen3" name="optRegimen" value="3" <?php if ($mnRegimen==3) echo "checked"; ?> onclick="mostrarRegimen(this)"> Trimestral
                                                &emsp;<input type="radio" id="optRegimen4" name="optRegimen" value="4" <?php if ($mnRegimen==4) echo "checked"; ?> onclick="mostrarRegimen(this)"> Cuatrimestral
                                                &emsp;<input type="radio" id="optRegimen5" name="optRegimen" value="5" <?php if ($mnRegimen==5) echo "checked"; ?> onclick="mostrarRegimen(this)"> Semestral
                                                &emsp;<input type="radio" id="optRegimen6" name="optRegimen" value="6" <?php if ($mnRegimen==6) echo "checked"; ?> onclick="mostrarRegimen(this)"> Intensivo
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                                        <div class="col-sm-12 col-md-8">
                                            <div class="radio">
                                                <?php
                                                    if ($mnTurno == 1)
                                                        echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked /> Diurno');
                                                    else
                                                        echo('<input type="radio" id="optTurno1" name="optTurno" value="1" /> Diurno');

                                                    if ($mnTurno == 2)
                                                        echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" checked /> Matutino');
                                                    else
                                                        echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino');

                                                    if ($mnTurno == 3)
                                                        echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" checked /> Vespertino');
                                                    else
                                                        echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" /> Vespertino');

                                                    if ($mnTurno == 4)
                                                        echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" checked /> Nocturno');
                                                    else
                                                        echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" /> Nocturno');

                                                    if ($mnTurno == 5)
                                                        echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" checked /> Sabatino');
                                                    else
                                                        echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" /> Sabatino');

                                                    if ($mnTurno == 6)
                                                        echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" checked /> Dominical');
                                                    else
                                                        echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" /> Dominical');
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="optActivo" class="col-sm-auto col-md-2 form-label">Activo</label>
                                        <div class="col-sm-12 col-md-7">
                                            <div class="radio">
                                                <?php
                                                    if ($mbActivo == 0)
                                                        echo('<input type="radio" id="optActivo1" name="optActivo" value="0" checked/> No');
                                                    else
                                                        echo('<input type="radio" id="optActivo1" name="optActivo" value="0" /> No');

                                                    if ($mbActivo == 1)
                                                        echo('&emsp;<input type="radio" id="optActivo2" name="optActivo" value="1" checked/> Si');
                                                    else
                                                        echo('&emsp; <input type="radio" id="optActivo2" name="optActivo" value="1" /> Si');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!--Final del DIV de Tab GENERALES-->		
                            <div title="Detalles del curso" style="padding:10px">
                            <!--Inicio del DIV de Tab CURSOS-->
                            <div class="col-xs-auto col-md-12">
                                <div class="form-group row">
                                    <div class="col-sm-auto col-md-12">
                                        <div id="dvCUR">
                                            <table id="dgCUR" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbCUR', footer:'#ftCUR', singleSelect:true, method:'get', onClickCell: onClickCellCUR">
                                                <thead>
                                                    <tr>
                                                        <th data-options="field:'codConsecutivo', hidden:'true'">codConsecutivo</th>
                                                        <th id="thPeriodo" data-options="field:'periodo',width:'18%',align:'left'">Periodo</th>
                                                        <th data-options="field:'modulo',width:'18%',align:'left'">Módulo</th>
                                                        <th data-options="field:'encuentros',width:'18%',align:'left'">Encuentros</th>
                                                        <th data-options="field:'hPresenciales',width:'18%',align:'left'">Presenciales/Teóricas</th>
                                                        <th data-options="field:'hAutoestudio',width:'12%',align:'left'">Prácticas</th>
                                                        <th data-options="field:'hTotales',width:'12%',align:'left'">Horas totales</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                   $mDatos = fxObtenerDetPlanCurso($msCodigo);
                                                       while ($mFila = $mDatos->fetch())
                                                            {
                                                                $msModuloNombre = "";
                                                                if (!empty($mFila["MODULO_REL"])) {
                                                                    $msConsultaModulo = "SELECT NOMBRE_280 FROM UMO280A WHERE MODULO_REL = ?";
                                                                    $mMod = $m_cnx_MySQL->prepare($msConsultaModulo);
                                                                    $mMod->execute([$mFila["MODULO_REL"]]);
                                                                    $mFilaModulo = $mMod->fetch();
                                                                    if ($mFilaModulo) {
                                                                        $msModuloNombre = $mFilaModulo["NOMBRE_280"];
                                                                    } else {
                                                                        $msModuloNombre = $mFila["MODULO_REL"];
                                                                    }
                                                                }
                                                                echo ("<tr>");
                                                                echo ("<td>" . $mFila["CONSECUTIVOC_REL"] . "</td>");
                                                                echo ("<td>" . $mFila["PERIODO_221"] . "</td>");
                                                                echo ("<td>" . htmlspecialchars($msModuloNombre) . "</td>");
                                                                echo ("<td>" . $mFila["ENCUENTROS_221"] . "</td>");
                                                                echo ("<td>" . $mFila["HRSPRESENCIALES_221"] . "</td>");
                                                                echo ("<td>" . $mFila["HRSPRACTICA_221"] . "</td>");
                                                                echo ("<td>" . $mFila["HRSTOTAL_221"] . "</td>");
                                                                echo ("</tr>");
                                                            }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="tbCUR" style="height:auto; padding-top:1%; padding-bottom:2%">
                                    <table width="100%">
                                        <tr>
                                            <td width="15%"><label id="txtRegimenSeleccionado" style="padding: 0.375rem 0.75rem; display:inline-block; width:100%; cursor:default;"></label></td>
                                            <td width="40%"><input type="number" id="txnPeriodo" class="form-control" style="width:25%" value="0"></td>
                                            <td width="15%">Horas prácticas</td>
                                            <td width="30%"><input type="number" id="txnHAutoestudio" class="form-control" style="width:30%" value="0" onchange="sumaHoras()"></td>
                                        </tr>
                                        <tr></tr>
                                         <tr>
                                            <td width="15%">Módulo</td>
                                            <td width="40%">
                                               <div class="form-group row">
                                                    <div class="col-sm-12 col-md-7">
                                                        <select class="form-control" id="cboModulo" name="cboModulo"></select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td width="15%">Horas presenciales</td> 
                                            <td width="40%"><input type="number" id="txnHPresenciales" class="form-control" style="width:25%" value="0" onchange="sumaHoras()"></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Numero de encuentros</td>
                                            <td width="40%"><input type="number" id="txnEncuentrosTotalDetalle" class="form-control" style="width:25%" value="0" ></td>
                                            <td width="15%">Horas totales</td>
                                            <td width="30%"><input type="number" id="txnHTotales" class="form-control" style="width:30%" value="0" readonly></td>
                                       </tr>
                                    </table>
                                </div>

                                <div id="ftCUR" style="height:auto">
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="appendCUR()">Agregar</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitCUR()">Borrar</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitCUR()">Aceptar</a>
                                    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectCUR()">Deshacer</a>
                                </div>
                            </div>
                        </div>
                        <!--Fin del DIV de Tab CURSOS-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
}}} 
?>
</body>
</html><script>
$(function () {
    const $dgCUR = $('#dgCUR');
    const regimenMap = {
        1: "Mensual",
        2: "Bimestral",
        3: "Trimestral",
        4: "Cuatrimestral",
        5: "Semestral",
        6: "Intensivo"
    };
    const regimenTexto = regimenMap[<?php echo (int)$mnRegimen; ?>] || "";
    $('#txtRegimenSeleccionado').text(regimenTexto);
    actualizarCabeceraPeriodo(regimenTexto);
    $dgCUR.datagrid({
        striped: true,
        onClickRow(index, row) {
            $("#txnPeriodo").val(row.periodo);
        }
    });
    $('.datagrid-wrap').width('100%');
    $('.datagrid-view').height('200px');
    $('#procPlanCurso').on('submit', function () {
        const gridData = $dgCUR.datagrid('getRows');
        $('#gridDetalle').val(JSON.stringify(gridData));
        return true;
    });
    $('#txnHPresenciales, #txnHAutoestudio').on('change', sumaHoras);
});

function actualizarCabeceraPeriodo(regimenTexto) {
    const dg = $('#dgCUR');
    const col = dg.datagrid('getColumnOption', 'periodo');
    if (col) col.title = regimenTexto;
    dg.datagrid('getPanel').find('th[field="periodo"] div.datagrid-cell').text(regimenTexto);
}

function mostrarRegimen(radio) {
    const regimenTexto = radio.nextSibling.textContent.trim();
    $("#txtRegimenSeleccionado").text(regimenTexto);
    actualizarCabeceraPeriodo(regimenTexto);
}

function llenaModulos(curso) {
    const data = new FormData();
    data.append('curso', curso);

    $.ajax({
        url: 'funciones/fxDatosModulos.php',
        type: 'POST',
        data: data,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#cboModulo').html(response);
        }
    });
}

function sumaHoras() {
    const p = parseInt($("#txnHPresenciales").val()) || 0;
    const a = parseInt($("#txnHAutoestudio").val()) || 0;
    $("#txnHTotales").val(p + a);
}

function sumaHorasCreditos() {
    const filas = $('#dgCUR').datagrid('getRows');
    let totalPres = 0, totalAuto = 0, totalEncuentros = 0;

    filas.forEach(fila => {
        totalPres += parseInt(fila.hPresenciales) || 0;
        totalAuto += parseInt(fila.hAutoestudio) || 0;
        totalEncuentros += parseInt(fila.encuentros) || 0;
    });

    $('#txnHoras').val(totalPres + totalAuto);
    $('#txnEncuentrosTotal').val(totalEncuentros);
}

let editIndexCUR, lastIndexCUR;
function endEditingCUR() {
    if (editIndexCUR === undefined) return true;
    const dg = $('#dgCUR');
    if (dg.datagrid('validateRow', editIndexCUR)) {
        dg.datagrid('endEdit', editIndexCUR);
        editIndexCUR = undefined;
        return true;
    }
    return false;
}
$('#dgCUR').datagrid({
    onClickRow(rowIndex) {
        if (lastIndexCUR !== rowIndex) {
            $(this).datagrid('endEdit', lastIndexCUR).datagrid('beginEdit', rowIndex);
            lastIndexCUR = rowIndex;
        }
    }
});
function onClickCellCUR(index) {
    if (editIndexCUR !== index) {
        if (endEditingCUR()) {
            $('#dgCUR').datagrid('selectRow', index).datagrid('beginEdit', index);
            editIndexCUR = index;
        } else {
            setTimeout(() => $('#dgCUR').datagrid('selectRow', editIndexCUR), 0);
        }
    }
}

function appendCUR() {
    const periodo = parseInt($('#txnPeriodo').val()) || 0;
    const modulo = $('#cboModulo').val(); // <-- cambiar text() por val()
    const hPres = parseInt($('#txnHPresenciales').val()) || 0;
    const hAuto = parseInt($('#txnHAutoestudio').val()) || 0;
    const hTotales = parseInt($('#txnHTotales').val()) || 0;
    const encuentros = parseInt($('#txnEncuentrosTotalDetalle').val()) || 0;

    if (periodo === 0) return $.messager.alert('UMOJN', 'Ingrese el periodo correspondiente.', 'warning');

    const filas = $('#dgCUR').datagrid('getRows');
    if (filas.some(f => f.modulo === modulo))
        return $.messager.alert('UMOJN', 'El módulo ya existe en la lista.', 'warning');

    $('#dgCUR').datagrid('appendRow', {
        periodo, modulo, encuentros,
        hPresenciales: hPres,
        hAutoestudio: hAuto,
        hTotales
    });

    sumaHorasCreditos();
    $('#txnPeriodo, #txnHPresenciales, #txnHAutoestudio, #txnHTotales, #txnEncuentrosTotalDetalle').val(0);
}
function removeitCUR() {
    const row = $('#dgCUR').datagrid('getSelected');
    if (!row) return $.messager.alert('UMOJN', 'Seleccione la fila a borrar.', 'warning');
    const index = $('#dgCUR').datagrid('getRowIndex', row);
    $('#dgCUR').datagrid('deleteRow', index);
    sumaHorasCreditos();
}
function acceptitCUR() {
    $('#dgCUR').datagrid('endEdit', lastIndexCUR);
    sumaHorasCreditos();
}
function rejectCUR() {
    $('#dgCUR').datagrid('rejectChanges');
    sumaHorasCreditos();
}
$(document).ready(function() {
    const cursoSeleccionado = $('#cboCurso').val();
    if (cursoSeleccionado !== '') {
        llenaModulos(cursoSeleccionado);
    }
});
</script>