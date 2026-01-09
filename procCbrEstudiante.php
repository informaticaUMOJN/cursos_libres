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
    require_once ("funciones/fxCbrEstudiantes.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$Registro = fxVerificaUsuario();
	if ($Registro == 0)
	{
?>
<div class="container text-center">
    <div id="DivContenido">
        <img src="imagenes/errordeacceso.png" />
    </div>
</div>

<?php 
}
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("procCbrEstudiante");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{?>
		<div class="container text-center">
			<div id="DivContenido">
				<img src="imagenes/errordeacceso.png" />
			</div>
		</div>
		<?php }
		else
		{
			if (isset($_POST["txtCobros"]))
			{
				$msCodigo = $_POST["txtCobros"];
				$msCarrera = $_POST["lstCarrera"];
                $msCobro =$_POST["lstCobros"];
                if (isset($_POST["gridDetalle"]))
                    $gridDetalle = $_POST["gridDetalle"];
                
                    if ($msCodigo == "")
                    {
                        $msCodigo = fxGuardarCobrosEstudiantes ( $mfAdeudado, $mfAbonado, $mnMoneda, $mfDescuento, $mbAnulado, $msMatricula);
                        $msBitacora = $msCodigo . "; " . $mfAdeudado . "; " . "; " . $mfAbonado.";".";".$mnMoneda.";" .";".$mfDescuento . ";" . ";". $mbAnulado .";".";".$msMatricula;
                        fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $msCodigo, "", "Agregar", $msBitacora);
                    }
                    else
                    {
                        $msBitacora = $msCodigo . "; " . $mfAdeudado . "; " . "; " . $mfAbonado.";".";".$mnMoneda.";" .";".$mfDescuento . ";" . ";". $mbAnulado .";".";".$msMatricula;
                        fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $Codigo, "", "Modificar", $msBitacora);
                    }
           
                    ?><meta http-equiv="Refresh" content="0;url=gridCbrEstudiante.php" /><?php
				}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";
                if ($msCodigo != "")
                {   $objRecordSet = fxDevuelveEncabezado(0, $msCodigo);
                    $mFila = $objRecordSet->fetch();
                    $msCarrera = $mFila["CARRERA_REL"];
                    $msEstudiante = $mFila["ESTUDIANTE_REL"];
                }
                else
                {
					$msCarrera ="";
					$msEstudiante = "";
                }
	?>
    <div class="container text-left">
        <div id="DivContenido">
            <div class="row">
                <div class="col-xs-12 col-md-11">
                    <div class="degradado">
                        <strong>Cobros de los estudiantes</strong>
                    </div>
                </div>
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                    <input type="button" id="Regresar" name="Regresar" value="Regresar" class="btn btn-primary"  onclick="location.href='gridCbrEstudiante.php';" />
                </div>
            </div><br>
             <div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                    <form id="procCbrEstudiante" name="procCbrEstudiante" action="procCbrEstudiante.php" onsubmit="return verificarFormulario()" method="post">
                        <div class = "form-group row">
                            <label for="txtCobros" class="col-sm-12 col-md-3 form-label">Matricula</label>
                            <div class="col-sm-12 col-md-3">
                                <?php
                                echo('<input type="text" class="form-control" id="txtCobros" name="txtCobros" value="' . $msCodigo . '" readonly />'); 
                                ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cboEstudiante" class="col-sm-12 col-md-3 col-form-label">Estudiante</label>
                                <div class="col-sm-12 col-md-7">
                                        <?php
                                            if ($msEstudiante == "")
                                                echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante"disabled>');
                                            else
                                                echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" disabled>');
                                                $msConsulta = "select ESTUDIANTE_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 from UMO010A order by APELLIDO1_010, NOMBRE1_010 desc"; 
                                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                                $mDatos->execute();
                                            while ($mFila = $mDatos->fetch())
                                                {
                                            $msValor = trim($mFila["ESTUDIANTE_REL"]); 
                                            $msTexto = trim($mFila["NOMBRE1_010"]);

										    if (trim($mFila["NOMBRE2_010"]) != "")
											    $msTexto .= " " . $mFila["NOMBRE2_010"];
										        $msTexto .= ", " . $mFila["APELLIDO1_010"];
										    if (trim($mFila["APELLIDO2_010"]) != "")
											    $msTexto .= " " . $mFila["APELLIDO2_010"];
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
                                <label for="lstCarrera" class="col-sm-12 col-md-3 col-form-label" >Carreras</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control" id="lstCarrera" name="lstCarrera" id="cobroSelect" disabled>
                                        <?php
                                        try {
                                            $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A order by NOMBRE_040";
                                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                            $mDatos->execute();
                                            while ($mFila = $mDatos->fetch()) {
                                                $msValor = rtrim($mFila["CARRERA_REL"]);
                                                $msTexto = htmlspecialchars(rtrim($mFila["NOMBRE_040"]));
                                                if ($msCarrera == $msValor) 
                                                {
                                                    echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
                                                } else 
                                                {
                                                    echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                                }
                                                }
                                            } catch (PDOException $e) {
                                                echo "Error: " . $e->getMessage();
                                            }
                                            ?>
                                    </select>
                                </div> 
                            </div>

                            <div class="form-group row">
                                <label for="lstCobros" class="col-sm-12 col-md-3 col-form-label">Cobros</label>
                                <div class="col-sm-12 col-md-7">
                                    <select id="lstCobros" name="lstCobros" class="form-control">
                                        <?php
                                        if (!empty($msCarrera))
                                        {
                                            $msConsulta = "
                                            select u.COBRO_REL, u.DESC_130 AS DESCRIPCION,  case 
                                            when u.ACTIVO_130 = 1 then 'Activo' else 'Inactivo'
                                            end as ACTIVO_130 from UMO130A u
                                            where u.CARRERA_REL = ? and u.ACTIVO_130 = 1
                                            and u.TIPO_130 != 0 and u.TIPO_130 != 1 order by u.COBRO_REL;";
                                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                            $mDatos->execute([$msCarrera]);
                                            while ($mFila = $mDatos->fetch())
                                            {
                                                $msValor = rtrim($mFila["COBRO_REL"]);
                                                $msTexto = htmlspecialchars(rtrim($mFila["DESCRIPCION"]));
                                                echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="col-xs-12 col-md-10">
                                <div id="dvCBR">
                                    <table id="dgCBR" data-options="" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbASG', footer:'#ftASG', singleSelect:true, method:'get', onClickCell: onClickCellASG, fitColumns:true, height:200, width:'100%'">
                                        <thead>
                                            <tr>
                                                <th data-options="field:'cobro',width:'12%',align:'left'">Cobro</th>
                                                <th data-options="field:'descripcion',width:'36%',align:'left'">Descripcion</th>
                                                <th data-options="field:'adeudado', width:'10%',align:'left'">Adeudado</th>
                                                <th data-options="field:'abonado',width:'10%',align:'left'">Abonado</th>
                                                <th data-options="field:'descuento', width:'10%',align:'left'">Descuento</th>
                                                <th data-options="field:'moneda', width:'12%',align:'left'">Moneda</th>
                                                <th data-options="field:'anulado', width:'10%',align:'left'">Anulado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $mDatos = fxObtenerMostrarC($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr data-cobro='" . $mFila["COBRO_REL"] . "'>");
                                            echo ("<td>" . $mFila["COBRO_REL"] . "</td>");
                                            echo ("<td>" . $mFila["DESC_130"] . "</td>");
                                            echo ("<td>" . $mFila["ADEUDADO_131"] . "</td>");
                                            echo ("<td>" . $mFila["ABONADO_131"] . "</td>");
                                            echo ("<td>" . $mFila["DESCUENTO_131"] . "</td>");
                                            echo ("<td>" . $mFila["MONEDA_131"] . "</td>");
                                            echo ("<td>" . $mFila["ANULADO_131"] . "</td>");
                                            echo ("</tr>");
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="ftCBR" style="height:auto">
                                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append()">Agregar</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeit()">Borrar</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-cancel',plain:true" onclick="cancelarCobro()">Anular</a> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}}}
?>
<script type="text/javascript">
var editIndex = undefined;
var lastIndex; 

window.onload = function() {
    $('#dgCBR').datagrid({
        striped: true,
        footer: '#ftCBR',
        singleSelect: true,
        method: 'get',
        onClickRow: onClickRow,  
        onClickCell: onClickCellASG  
        
    });
};

$(document).ready(function () {
            $('#dgCBR').datagrid('hideColumn', 'cobro');
        });
function onClickRow(index, row) {
    lastIndex = index;
    console.log('Fila seleccionada:', row);
}

function onClickCellASG(index, field) {
    console.log('onClickCellASG:', index, field);  

    if (editIndex != index) {
        if (endEditing()) {
            $('#dgCBR').datagrid('selectRow', index).datagrid('beginEdit', index);
            editIndex = index;
        } else { 
            setTimeout(function() {
                $('#dgCBR').datagrid('selectRow', editIndex);
            }, 0);
        }
    }
}
function endEditing() {
    if (editIndex == undefined) return true;
    if ($('#dgCBR').datagrid('validateRow', editIndex)) {
        $('#dgCBR').datagrid('endEdit', editIndex);
        editIndex = undefined;
        return true;
    } else {
        return false;
    }
}
function append() {
    if (endEditing()) {
        var cobroId = $('#lstCobros').val();
        var cobroTexto = $('#lstCobros option:selected').text();
        var matriculaRel = $('#txtCobros').val();
        if (!cobroId) {
            $.messager.alert('UMOJN', 'Por favor selecciona un cobro.', 'warning');
            return;
        }
        var existeCobro = false;
        var datos = $('#dgCBR').datagrid('getData');
        var registros = $('#dgCBR').datagrid('getRows').length;
        if (registros > 0) {
            for (var i = 0; i < registros; i++) {
                if (datos.rows[i].descripcion == cobroTexto) {
                    existeCobro = true;
                }
            }
        }
        if (existeCobro) {
            $.messager.alert('UMOJN', cobroTexto + ' ya fue incluido.', 'warning');
            return;
        }
        $.ajax({
            url: 'funciones/fxDatosCobros.php',
            type: 'POST',
            data: {
                action: 'getCobroDetails',
                cobroId: cobroId,
                matriculaRel: matriculaRel 
            },
            success: function(response) {
                console.log('Respuesta del servidor:', response);  
                try {
                    if (response.success) {
                        var monedaTexto = (response.moneda == 0) ? 'Córdobas' : 'Dólares';
                        $('#dgCBR').datagrid('appendRow', {
                            id: cobroId,
                            cobro: cobroId,
                            descripcion: response.descripcion,
                            adeudado: response.adeudado,
                            abonado: response.abonado,
                            descuento: response.descuento,
                            moneda: monedaTexto,
                            anulado: response.anulado
                        });
                        $('#dgCBR').datagrid('reload');  // Recarga el datagrid
                    } else {
                        alert('Error: No se pudo obtener la información del cobro.');
                    }
                } catch (e) {
                    console.error('Error al analizar la respuesta JSON:', e);
                    alert('Error al procesar la respuesta del servidor.');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error AJAX:', error); 
                alert('Error en la solicitud AJAX');
            }
        });
    }
}

function removeit() {
    if (lastIndex != null) {
        var row = $('#dgCBR').datagrid('getRows')[lastIndex];
        var cobro = row.cobro || $(row).data('cobro');  
        var matricula_rel = $('#txtCobros').val();  
        console.log('Fila seleccionada:', row);
        console.log('Cobro:', cobro);
        console.log('Matrícula Rel:', matricula_rel);
        if (cobro && matricula_rel) {
            $.messager.confirm('UMOJN', '¿Estás seguro de que deseas eliminar esta fila?', function(r) {
                if (r) {
                    $.ajax({
                        url: 'funciones/fxDatosCobros.php',
                        type: 'POST',
                        data: {
                            action: 'eliminarCobro',
                            COBRO_REL: cobro,  
                            MATRICULA_REL: matricula_rel  
                        },
                        success: function(response) {
                            console.log('Respuesta del servidor:', response);

                            try {
                                if (response.success) {
                                    $('#dgCBR').datagrid('deleteRow', lastIndex);
                                    $.messager.alert('UMOJN', 'Fila eliminada correctamente.', 'info');
                                    lastIndex = null;
                                } else {
                                    $.messager.alert('UMOJN', 'No se pudo eliminar la fila: ' + response.error, 'error');
                                }
                            } catch (e) {
                                console.error('Error al analizar la respuesta JSON:', e);
                                $.messager.alert('UMOJN', 'Error en la respuesta del servidor.', 'error');
                            }
                        },

                        error: function(xhr, status, error) {
                            console.log('Error AJAX:', error);
                            $.messager.alert('UMOJN', 'Error en la solicitud AJAX.', 'error');
                        }
                    });
                }
            });
        } else {
            $.messager.alert('UMOJN', 'No se encontraron los datos necesarios para eliminar.', 'error');
        }
    } else {
        $.messager.alert('UMOJN', 'Por favor selecciona una fila para eliminar.', 'warning');
    }
}

function cancelarCobro() {
    if (lastIndex != null) {
        var row = $('#dgCBR').datagrid('getRows')[lastIndex];
        var cobro = row.cobro || $(row).data('cobro');  
        var matricula_rel = $('#txtCobros').val();  

        if (cobro && matricula_rel) {
            $.messager.confirm('UMOJN', '¿Estás seguro de que deseas anular este cobro?', function(r) {
                if (r) {
                    $.ajax({
                        url: 'funciones/fxDatosCobros.php', 
                        type: 'POST',
                        data: {
                            action: 'anularCobro',
                            COBRO_REL: cobro,  
                            MATRICULA_REL: matricula_rel  
                        },
                        success: function(response) {
                            console.log('Respuesta del servidor:', response);

                            try {
                                if (response.success) {
                                    $('#dgCBR').datagrid('getRows')[lastIndex].anulado = 'Sí';  
                                    $('#dgCBR').datagrid('refreshRow', lastIndex);  
                                    $.messager.alert('UMOJN', 'Cobro anulado correctamente.', 'info');
                                    lastIndex = null;  
                                } else {
                                    $.messager.alert('UMOJN', 'No se pudo anular el cobro: ' + response.error, 'error');
                                }
                            } catch (e) {
                                console.error('Error al analizar la respuesta JSON:', e);
                                $.messager.alert('UMOJN', 'Error en la respuesta del servidor.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error AJAX:', error);
                            $.messager.alert('UMOJN', 'Error en la solicitud AJAX.', 'error');
                        }
                    });
                }
            });
        } else {
            $.messager.alert('UMOJN', 'No se encontraron los datos necesarios para anular.', 'error');
        }
    } else {
        $.messager.alert('UMOJN', 'Por favor selecciona una fila para anular.', 'warning');
    }
}
</script>