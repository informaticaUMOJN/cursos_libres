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
    require_once ("funciones/fxCbrCursosLibres.php");
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
				$msCursos = $_POST["lstCarrera"];
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
           
                    ?><meta http-equiv="Refresh" content="0;url=gridCbrCursosLibres.php" /><?php
				}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";
                if ($msCodigo != "")
                {   $objRecordSet = fxDevuelveCursos(0, $msCodigo);
                    $mFila = $objRecordSet->fetch();
                    $msCursos = $mFila["CURSOS_REL"];
                    $msAlumno = $mFila["ALUMNO_REL"];
                }
                else
                {
					$msCursos ="";
					$msAlumno = "";
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
                    <input type="button" id="Regresar" name="Regresar" value="Regresar" class="btn btn-primary"  onclick="location.href='gridCbrCursosLibres.php';" />
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
                                            if ($msAlumno == "")
                                                echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante"disabled>');
                                            else
                                                echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" disabled>');
                                                $msConsulta = "select ALUMNO_REL, NOMBRES_200, APELLIDOS_200 from UMO200A order by APELLIDOS_200, NOMBRES_200 desc"; 
                                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                                $mDatos->execute();
                                         while ($mFila = $mDatos->fetch()) {
                                                    $msValor = trim($mFila["ALUMNO_REL"]);
                                                    $msTexto = trim($mFila["NOMBRES_200"]);

                                                    if (trim($mFila["NOMBRE2_200"]) != "")
                                                        $msTexto .= " " . trim($mFila["NOMBRE2_200"]);

                                                    if (trim($mFila["APELLIDOS_200"]) != "")
                                                        $msTexto .= " " . trim($mFila["APELLIDOS_200"]);

                                                    if ($msAlumno == "")
                                                        echo("<option value='$msValor'>$msTexto</option>");
                                                    else {
                                                        if ($msAlumno == $msValor)
                                                            echo("<option value='$msValor' selected>$msTexto</option>");
                                                        else
                                                            echo("<option value='$msValor'>$msTexto</option>");
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
                                            $msConsulta = "select CURSOS_REL, NOMBRE_190 from UMO190A order by NOMBRE_190";
                                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                            $mDatos->execute();
                                            while ($mFila = $mDatos->fetch()) {
                                                $msValor = rtrim($mFila["CURSOS_REL"]);
                                                $msTexto = htmlspecialchars(rtrim($mFila["NOMBRE_190"]));
                                                if ($msCursos == $msValor) 
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
                                        if (!empty($msCursos))
                                        {
                                            $msConsulta = "
                                            select u.COBRO_REL, u.DESC_130 AS DESCRIPCION,  case 
                                            when u.ACTIVO_130 = 1 then 'Activo' else 'Inactivo'
                                            end as ACTIVO_130 from UMO130A u
                                            where u.CURSOS_REL = ? and u.ACTIVO_130 = 1
                                            and u.TIPO_130 != 0 and u.TIPO_130 != 1 order by u.COBRO_REL;";
                                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                            $mDatos->execute([$msCursos]);
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
                                            echo ("<td>" . $mFila["ADEUDADO_132"] . "</td>");
                                            echo ("<td>" . $mFila["ABONADO_132"] . "</td>");
                                            echo ("<td>" . $mFila["DESCUENTO_132"] . "</td>");
                                            echo ("<td>" . $mFila["MONEDA_132"] . "</td>");
                                            echo ("<td>" . $mFila["ANULADO_132"] . "</td>");
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
            url: 'funciones/fxDatosCbrCursosLibres.php',
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
        var matcurso_rel = $('#txtCobros').val();  
        console.log('Fila seleccionada:', row);
        console.log('Cobro:', cobro);
        console.log('Matrícula Rel:', matcurso_rel);
        if (cobro && matcurso_rel) {
            $.messager.confirm('UMOJN', '¿Estás seguro de que deseas eliminar esta fila?', function(r) {
                if (r) {
                    $.ajax({
                        url: 'funciones/fxDatosCbrCursosLibres.php',
                        type: 'POST',
                        data: {
                            action: 'eliminarCobro',
                            COBRO_REL: cobro,  
                            MATCURSO_REL: matcurso_rel  
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
        var matcurso_rel = $('#txtCobros').val();  

        if (cobro && matcurso_rel) {
            $.messager.confirm('UMOJN', '¿Estás seguro de que deseas anular este cobro?', function(r) {
                if (r) {
                    $.ajax({
                        url: 'funciones/fxDatosCbrCursosLibres.php', 
                        type: 'POST',
                        data: {
                            action: 'anularCobro',
                            COBRO_REL: cobro,  
                            MATCURSO_REL: matcurso_rel  
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