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
	require_once ("funciones/fxAsistenciasCl.php");
    $m_cnx_MySQL = fxAbrirConexion();
    $Registro = fxVerificaUsuario();
    if (isset($_SESSION["gsDocente"]))
        $msDocente = $_SESSION["gsDocente"];
    else
        $msDocente = "";
	
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
        $mbSupervisor = fxVerificaSupervisor();
		$PermisoUsuario = fxPermisoUsuario("procAsistenciaC");
		if ($mbAdministrador == 0 and $PermisoUsuario == 0 and $mbSupervisor == 0)
		{?>
<div class="container text-center">
    <div id="DivContenido">
        <img src="imagenes/errordeacceso.png" />
    </div>
</div>
<?php }
		else
		{
			if (isset($_POST["Operacion"]))
			{
                $mnOperacion = $_POST["Operacion"];
				$msCodigo = $_POST["txtCodAsistencia"];
                $msDocente = $_POST["cboDocente"];
                $msModulo = $_POST["cboModulo"];
                $msCurso = $_POST["cboCurso"];
                $mdFecha = $_POST["dtpFechaClase"];
                $mnTurno = $_POST["optTurno"];
                $mnAnno = $_POST["txnAnno"];
                $mnModuloLectivo = $_POST["txnModuloLectivo"];
				$gridEstudiantes = $_POST["gridEstudiantes"];
                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarAsistencia ($msDocente, $msModulo, $msCurso, $mdFecha, $mnTurno, $mnAnno, $mnModuloLectivo);
                    $msBitacora = $msCodigo . "; " . $msModulo . "; " . $msCurso . "; " . $mdFecha . "; " . $mnTurno . "; " . $mnAnno . "; " . $mnModuloLectivo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO230A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarAsistencia ($msCodigo, $msDocente, $msModulo, $msCurso, $mdFecha, $mnTurno, $mnAnno, $mnModuloLectivo);
                    fxBorrarDetAsistencia($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msModulo . "; " . $msCurso . "; " . $mdFecha . "; " . $mnTurno . "; " . $mnAnno . "; " . $mnModuloLectivo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO230A", $msCodigo, "", "Modificar", $msBitacora);
                }
				foreach($gridEstudiantes as $mRegistro)
				{
                    $mMatricula = $mRegistro['matricula'];
                    $mEstado = $mRegistro['estado'];
                    switch ($mEstado)
                    {
                        case "Presente":
                            $mnEstado = 0;
                            break;
                        case "Ausente":
                            $mnEstado = 1;
                            break;
                        default:
                            $mnEstado = 2;
                    }
                    fxGuardarDetAsistencia ($msCodigo, $mMatricula, $mnEstado);
                    $itemId++;
				}
				?><meta http-equiv="Refresh" content="0;url=gridAsistenciaCL.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = trim($_POST["UMOJN"]);
                else
                    $msCodigo = "";
                $mRecordSet = fxDevuelveAsistencia (0, "", $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msCurso = $mFila["CURSOS_REL"];
                    $msModulo = $mFila["MODULO_REL"];
                    $mdFechaClase = $mFila["FECHA_320"];
                    $mnTurno = $mFila["TURNO_320"];
                    $mnAnno = $mFila["ANNO_320"];
                    $mnModuloLectivo = $mFila["MODULOLECTIVO_320"];
                }
                else 
                {
                    $msDocente = "";
                    $msCurso = "";
                    $msModulo = "";
                    $mdFechaClase = date("Y-m-d");
                    $mnTurno = 1;
                    $mnAnno = date('Y');
                    $mnModuloLectivo = 1;
                }
	?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Asistencia</strong></div>
            </div>
        </div>
         <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procAsistenciaC" name="procAsistenciaC">
                    <div class="form-group row">
                        <label for="txtCodAsistencia" class="col-sm-12 col-md-2 col-form-label">Código de Asistencia</label>
                        <div class="col-sm-12 col-md-2">
                            <?php echo('<input type="text" class="form-control" id="txtCodAsistencia" name="txtCodAsistencia" value="' . $msCodigo . '" readonly />'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente" onchange="llenaCursoModuloEstudiantes()">');
                                    
                                    if (trim($_SESSION["gsDocente"]) != "" and $mbAdministrador == 0 and $mbSupervisor == 0)
                                    {
                                        $mDocente = $_SESSION["gsDocente"];
                                        $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A where ACTIVO_100 = 1 and DOCENTE_REL = ? order by NOMBRE_100";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
					                    $mDatos->execute([$mDocente]);
                                    }
                                    else
                                    {
                                        $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A where ACTIVO_100 = 1 order by NOMBRE_100";
                                        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
					                    $mDatos->execute();
                                    }
                                }
                                else
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente" disabled>');

                                    $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A order by NOMBRE_100 desc";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
					                $mDatos->execute();
                                }
                                while ($mFila = $mDatos->fetch())
                                {
                                    $Docente = rtrim($mFila["DOCENTE_REL"]);
                                    $Texto = rtrim($mFila["NOMBRE_100"]);
                                    
                                    if ($msDocente == "")
                                        $msDocente = $Docente;
                                    
                                    if ($msDocente == $Docente)
                                        echo("<option value='" . $Docente . "' selected>" . $Texto . "</option>");
                                    else
                                        echo("<option value='" . $Docente . "'>" . $Texto . "</option>");
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                   <div class="form-group row">
                        <label for="cboCurso" class="col-sm-12 col-md-2 col-form-label">Curso</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                            if ($msCurso == "")
                                echo('<select class="form-control" id="cboCurso" name="cboCurso" onchange="llenaModulos()">');
                            else
                                echo('<select class="form-control" id="cboCurso" name="cboCurso" disabled>');
                           $msConsulta = "SELECT DISTINCT c.CURSOS_REL, c.NOMBRE_190
                                            FROM UMO190A AS c
                                            INNER JOIN UMO280A AS m ON c.CURSOS_REL = m.CURSOS_REL
                                            WHERE m.DOCENTE_REL = ?";

                                $params = [$msDocente];
                                if ($msCurso != "") {
                                    $msConsulta .= " AND c.CURSOS_REL = ?";
                                    $params[] = $msCurso;
                                }

                                $msConsulta .= " ORDER BY c.NOMBRE_190";

                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute($params);

                                while ($mFila = $mDatos->fetch(PDO::FETCH_ASSOC)) {
                                    $mValor = rtrim($mFila["CURSOS_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_190"]);
                                    echo ($msCurso == $mValor ? "<option value='$mValor' selected>$mTexto</option>" : "<option value='$mValor'>$mTexto</option>");
                                }
                            echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboModulo" class="col-sm-12 col-md-2 col-form-label">Modulo</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboModulo" name="cboModulo" onchange="llenaEstudiantes()">');
                                else
                                    echo('<select class="form-control" id="cboModulo" name="cboModulo" disabled>');
                                    
                                $msConsulta = "select MODULO_REL, NOMBRE_280 from UMO280A where DOCENTE_REL = ? and CURSOS_REL = ? order by NOMBRE_280";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msDocente,$msCurso]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["MODULO_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_280"]);

                                    if ($msModulo == "")
                                    {
                                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        $msModulo = $mValor;
                                    }
                                    else
                                    {
                                        if ($msModulo == $mValor)
                                            echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        else
                                            echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                    }
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFechaClase" class="col-sm-12 col-md-2 col-form-label">Fecha de la clase</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="date" class="form-control" id="dtpFechaClase" name="dtpFechaClase" value="' . $mdFechaClase . '" onchange="verificaAsistencia()" />');
                                else
                                    echo('<input type="date" class="form-control" id="dtpFechaClase" name="dtpFechaClase" value="' . $mdFechaClase . '" readonly />');
                                echo('<input type="hidden" class="form-control" id="txnExisteAsistencia" name="txnExisteAsistencia" value="0" />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" readonly />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnModuloLectivo" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnModuloLectivo" name="txnModuloLectivo" value="' . $mnModuloLectivo . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnModuloLectivo" name="txnModuloLectivo" value="' . $mnModuloLectivo . '" readonly />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                        <div class="col-sm-12 col-md-7">
                            <div class="radio">
                                <?php
                                    if ($msCodigo == "")
                                    {
                                        if ($mnTurno == 1)
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" onclick="llenaEstudiantes()" checked/> Diurno');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" onclick="llenaEstudiantes()" /> Diurno');

                                        if ($mnTurno == 2)
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" onclick="llenaEstudiantes()" checked /> Matutino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" onclick="llenaEstudiantes()" /> Matutino');

                                        if ($mnTurno == 3)
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" onclick="llenaEstudiantes()" checked /> Vespertino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" onclick="llenaEstudiantes()" /> Vespertino');

                                        if ($mnTurno == 4)
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" onclick="llenaEstudiantes()" checked /> Nocturno');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" onclick="llenaEstudiantes()" /> Nocturno');

                                        if ($mnTurno == 5)
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" onclick="llenaEstudiantes()" checked /> Sabatino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" onclick="llenaEstudiantes()" /> Sabatino');

                                        if ($mnTurno == 6)
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" onclick="llenaEstudiantes()" checked /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" onclick="llenaEstudiantes()" /> Dominical');
                                    }
                                    else
                                    {
                                        if ($mnTurno == 1)
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" checked disabled /> Diurno');
                                        else
                                            echo('<input type="radio" id="optTurno1" name="optTurno" value="1" disabled /> Diurno');

                                        if ($mnTurno == 2)
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" checked disabled /> Matutino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" disabled /> Matutino');

                                        if ($mnTurno == 3)
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" checked disabled /> Vespertino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" disabled /> Vespertino');

                                        if ($mnTurno == 4)
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" checked disabled /> Nocturno');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" disabled /> Nocturno');

                                        if ($mnTurno == 5)
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" checked disabled /> Sabatino');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" disabled /> Sabatino');

                                        if ($mnTurno == 6)
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" checked disabled /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" disabled /> Dominical');
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dgEST" class="col-sm-12 col-md-2 form-label">Estudiantes</label>
                        <div class="col-sm-12 col-md-8">
                            <div id="dvEST">
                                <table id="dgEST" class="easyui-datagrid table", data-options="iconCls:'icon-edit', toolbar:'#tbEST', singleSelect:true, method:'get', onClickCell: onClickCellEST">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'matricula', hidden:'true'">Matrícula</th>
                                            <th data-options="field:'estudiante', width:'60%', align:'left'">Estudiante</th>
                                            <th data-options="field:'estado', width:'20%', align:'center',
                                                editor: {type:'combobox',
                                                options:{panelHeight:'auto', data:[{value:'Presente',text:'Presente'}, {value:'Ausente',text:'Ausente'}, {value:'Justificado',text:'Justificado'}]}}">Asistencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                   <?php
                                        $mDatos = fxDevuelveDetAsistencia($msCodigo, $msModulo, $mnTurno, $mnAnno, $mnModuloLectivo);
                                        while ($mFila = $mDatos->fetch()) {
                                            $msEstudiante = trim($mFila["APELLIDOS_200"]) . ", " . trim($mFila["NOMBRES_200"]);
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($mFila['MATCURSO_REL']) . '</td>';
                                            echo '<td>' . htmlspecialchars($msEstudiante) . '</td>';
                                            switch ($mFila['ESTADO_321']) {
                                                case 0:  echo '<td>Presente</td>'; break;
                                                case 1:  echo '<td>Ausente</td>'; break;
                                                default: echo '<td>Justificado</td>';
                                            }
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tbEST" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-ok'" onclick="acceptitEST()">Salir del Modo de Edición</a>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                        <?php
                            $mdFechaHoy = date('Y-m-d');
                            $HoraHoy = date('H:i:s');

                            if ($mbAdministrador == 1 or $_SESSION["gsDocente"] == "" or $mdFechaClase == $mdFechaHoy)
                                echo('<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />');
                            else
                                echo('<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" disabled />');
                        ?>
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAsistenciaCL.php';" />
                        </div>
                    </div>
                </form>
            </div>
            <?php	}
		}
	}
?></div>
    </div>
</div>
</body>
</html>
<script>
window.onload = function() 
{
    llenaModulos();
    verificaAsistencia();

    if ($('#txtCodAsistencia').val() == "")
    {
        var asignatura = $('#cboModulo').val();
        llenaEstudiantes();
    }
}
function verificarFormulario() {
    var semestre = $('#txnModuloLectivo').val();
    var gridEstudiantes = $('#dgEST').datagrid('getData');
    var registros = $('#dgEST').datagrid('getRows').length - 1;
    
    if (document.getElementById('txnExisteAsistencia').value == 1 && document.getElementById('txtCodAsistencia').value == "")
    {
        $.messager.alert('UMOJN', 'La asistencia de esta asignatura en esta fecha ya fue ingresada', 'warning');
        return false;
    }
    
    if (registros < 0)
    {
        $.messager.alert('UMOJN', 'Faltan los Estudiantes', 'warning');
        return false;
    }

    for (i = 0; i <= registros; i++) {
        if (gridEstudiantes.rows[i].estado == "")
        {
            $.messager.alert('UMOJN', 'Falta la asistencia de ' + gridEstudiantes.rows[i].estudiante, 'warning');
            return false;
        }
    }

    return true;
}

function llenaModulos()
{
    var curso = $('#cboCurso').val();
    var docente = $('#cboDocente').val();
    var datos = new FormData();
    datos.append('cursoAsg', curso);
    datos.append('docenteAsg', docente);

    $.ajax({
        url: 'funciones/fxDatosASCL.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboModulo').innerHTML = response;
            llenaEstudiantes();
        }
    })
}

function llenaEstudiantes()
{
    var asistencia = document.getElementById('txtCodAsistencia').value;
    var datos = new FormData();
    var asignatura = document.getElementById('cboModulo').value;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnModuloLectivo').value;
    var turno;

    if (asistencia == "")
    {
        if (document.getElementById("optTurno1").checked)
            turno = 1;
        if (document.getElementById("optTurno2").checked)
            turno = 2;
        if (document.getElementById("optTurno3").checked)
            turno = 3;
        if (document.getElementById("optTurno4").checked)
            turno = 4;
        if (document.getElementById("optTurno5").checked)
            turno = 5;
        if (document.getElementById("optTurno6").checked)
            turno = 6;
        
        datos.append('asignatura', asignatura);
        datos.append('turno', turno);
        datos.append('anno', anno);
        datos.append('semestre', semestre);

        $.ajax({
            url: 'funciones/fxDatosASCL.php',
            type: 'post',
            data: datos,
            contentType: false,
            processData: false,
            success: function(response){
                datos = JSON.parse(response);
                $('#dgEST').datagrid({data: datos});
                $('#dgEST').datagrid('reload');
                verificaAsistencia();
            }
        })
    }
}

function verificaAsistencia()
{
    var datos = new FormData();
    var asignatura = document.getElementById('cboModulo').value;
    var fecha = document.getElementById('dtpFechaClase').value;
    datos.append('asignatura2', asignatura);
    datos.append('fecha', fecha);

    $.ajax({
        url: 'funciones/fxDatosASCL.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('txnExisteAsistencia').value = response;
        }
    })
}
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
function acceptitEST() {
    if (endEditingEST()) {
        $('#dgEST').datagrid('acceptChanges');
    }
}
$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario()) {
        var texto;
        var datos;
        var registros;
        var i;
        var gridEstudiantes = $('#dgEST').datagrid('getData');

        texto = '{"txtCodAsistencia":"' + document.getElementById("txtCodAsistencia").value + '", ';
        if (document.getElementById("txtCodAsistencia").value == "")
            texto += '"Operacion":"0", ';
        else
            texto += '"Operacion":"1", ';
        texto += '"cboDocente":"' + document.getElementById("cboDocente").value + '", ';
        texto += '"cboModulo":"' + document.getElementById("cboModulo").value + '", ';
        texto += '"cboCurso":"' + document.getElementById("cboCurso").value + '", ';
        texto += '"dtpFechaClase":"' + document.getElementById("dtpFechaClase").value + '", ';
        texto += '"txnModuloLectivo":"' + document.getElementById("txnModuloLectivo").value + '", ';
        texto += '"txnAnno":"' + document.getElementById("txnAnno").value + '", ';

        if (document.getElementById("optTurno1").checked)
            texto += '"optTurno":"1", ';
        if (document.getElementById("optTurno2").checked)
            texto += '"optTurno":"2", ';
        if (document.getElementById("optTurno3").checked)
            texto += '"optTurno":"3", ';
        if (document.getElementById("optTurno4").checked)
            texto += '"optTurno":"4", ';
        if (document.getElementById("optTurno5").checked)
            texto += '"optTurno":"5", ';
        if (document.getElementById("optTurno6").checked)
            texto += '"optTurno":"6", ';

        registros = $('#dgEST').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridEstudiantes": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"matricula":"' + gridEstudiantes.rows[i].matricula;
                texto += '","estudiante":"' + gridEstudiantes.rows[i].estudiante;
                texto += '","estado":"' + gridEstudiantes.rows[i].estado;
                if (i == registros)
                    texto += '"}]}';
                else
                    texto += '"},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
                url: 'procAsistenciaC.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridAsistenciaCL.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
});
function llenaCursoModuloEstudiantes() {
    var docente = $('#cboDocente').val();
    var datos = new FormData();
    datos.append('docenteSelect', docente);

    $.ajax({
        url: 'funciones/fxDatosASCL.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            var datos = JSON.parse(response);
            if (datos.cursos) {
                $('#cboCurso').html(datos.cursos);
            }
            if (datos.modulos) {
                $('#cboModulo').html(datos.modulos);
            }
            if (datos.estudiantes) {
                $('#dgEST').datagrid({data: datos.estudiantes});
                $('#dgEST').datagrid('reload');
            }
        }
    });
}
</script>