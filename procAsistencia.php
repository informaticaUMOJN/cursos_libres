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
	require_once ("funciones/fxAsistencias.php");
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
		$PermisoUsuario = fxPermisoUsuario("procAsistencia");
		
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
                $msAsignatura = $_POST["cboAsignatura"];
                $msCarrera = $_POST["cboCarrera"];
                $mdFecha = $_POST["dtpFechaClase"];
                $mnTurno = $_POST["optTurno"];
                $mnAnno = $_POST["txnAnno"];
                $mnSemestre = $_POST["txnSemestre"];
				$gridEstudiantes = $_POST["gridEstudiantes"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarAsistencia ($msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnTurno, $mnAnno, $mnSemestre);
                    $msBitacora = $msCodigo . "; " . $msAsignatura . "; " . $msCarrera . "; " . $mdFecha . "; " . $mnTurno . "; " . $mnAnno . "; " . $mnSemestre;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO150A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarAsistencia ($msCodigo, $msDocente, $msAsignatura, $msCarrera, $mdFecha, $mnTurno, $mnAnno, $mnSemestre);
                    fxBorrarDetAsistencia($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msAsignatura . "; " . $msCarrera . "; " . $mdFecha . "; " . $mnTurno . "; " . $mnAnno . "; " . $mnSemestre;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO150A", $msCodigo, "", "Modificar", $msBitacora);
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

				?><meta http-equiv="Refresh" content="0;url=gridAsistencias.php" /><?php
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
                    $msCarrera = $mFila["CARRERA_REL"];
                    $msAsignatura = $mFila["ASIGNATURA_REL"];
                    $mdFechaClase = $mFila["FECHA_150"];
                    $mnTurno = $mFila["TURNO_150"];
                    $mnAnno = $mFila["ANNO_150"];
                    $mnSemestre = $mFila["SEMESTRE_150"];
                }
                else 
                {
                    $msDocente = "";
                    $msCarrera = "";
                    $msAsignatura = "";
                    $mdFechaClase = date("Y-m-d");
                    $mnTurno = 1;
                    $mnAnno = date('Y');
                    $mnSemestre = 1;
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
                <form id="procAsistencia" name="procAsistencia">
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
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente">');
                                    
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
                        <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCarrera == "")
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaAsignaturas()">');
                                else
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" disabled>');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["CARRERA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_040"]);

                                    if ($msCarrera == "")
                                    {
                                        $msCarrera = $mValor;
                                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                    }
                                    else
                                    {
                                        if ($msCarrera == $mValor)
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
                        <label for="cboAsignatura" class="col-sm-12 col-md-2 col-form-label">Asignatura</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" onchange="llenaEstudiantes()">');
                                else
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" disabled>');
                                    
                                $msConsulta = "select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO070A where UMO060A.ASIGNATURA_REL = UMO070A.ASIGNATURA_REL and CARRERA_REL = ? and DOCENTE_REL = ? order by NOMBRE_060";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msCarrera, $msDocente]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["ASIGNATURA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_060"]);

                                    if ($msAsignatura == "")
                                    {
                                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                                        $msAsignatura = $mValor;
                                    }
                                    else
                                    {
                                        if ($msAsignatura == $mValor)
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
                        <label for="txnSemestre" class="col-sm-12 col-md-2 col-form-label">Semestre lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnSemestre . '" readonly />');
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
                                        $mDatos = fxDevuelveDetAsistencia($msCodigo, $msAsignatura, $mnTurno, $mnAnno, $mnSemestre);

                                        while ($mFila = $mDatos->fetch())
                                        {
                                            $msEstudiante = trim($mFila["APELLIDO1_010"]);
                                            if (trim($mFila["APELLIDO2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["APELLIDO2_010"];

                                            $msEstudiante .= ", " . $mFila["NOMBRE1_010"];

                                            if (trim($mFila["NOMBRE2_010"]) != "")
                                                $msEstudiante .= " " . $mFila["NOMBRE2_010"];
                                                
                                            echo('<tr>');
                                            echo('<td>' . rtrim($mFila['MATRICULA_REL']) . '</td>');
                                            echo('<td>' . rtrim($msEstudiante) . '</td>');
                                            switch ($mFila['ESTADO_151'])
                                            {
                                                case 0:
                                                    echo('<td>Presente</td>');
                                                    break;

                                                case 1:
                                                    echo('<td>Ausente</td>');
                                                    break;

                                                default:
                                                    echo('<td>Justificado</td>');
                                            }
                                            echo('</tr>');
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
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridAsistencias.php';" />
                        </div>
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
window.onload = function() 
{
    llenaAsignaturas();
    verificaAsistencia();

    if ($('#txtCodAsistencia').val() == "")
    {
        var asignatura = $('#cboAsignatura').val();
        llenaEstudiantes();
    }
}

function verificarFormulario() {
    var semestre = $('#txnSemestre').val();
    var gridEstudiantes = $('#dgEST').datagrid('getData');
    var registros = $('#dgEST').datagrid('getRows').length - 1;

    if (semestre < 1 || semestre > 2)
    {
        $.messager.alert('UMOJN', 'El valor del semestre sólo puede ser 1 ó 2.', 'warning');
        return false;
    }
    
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

function llenaAsignaturas()
{
    var carrera = $('#cboCarrera').val();
    var docente = $('#cboDocente').val();
    var datos = new FormData();
    datos.append('carreraAsg', carrera);
    datos.append('docenteAsg', docente);

    $.ajax({
        url: 'funciones/fxDatosAsistencia.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
            llenaEstudiantes();
        }
    })
}

function llenaEstudiantes()
{
    var asistencia = document.getElementById('txtCodAsistencia').value;
    var datos = new FormData();
    var asignatura = document.getElementById('cboAsignatura').value;
    var anno = document.getElementById('txnAnno').value;
    var semestre = document.getElementById('txnSemestre').value;
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
            url: 'funciones/fxDatosAsistencia.php',
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
    var asignatura = document.getElementById('cboAsignatura').value;
    var fecha = document.getElementById('dtpFechaClase').value;
    datos.append('asignatura2', asignatura);
    datos.append('fecha', fecha);

    $.ajax({
        url: 'funciones/fxDatosAsistencia.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('txnExisteAsistencia').value = response;
        }
    })
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
        texto += '"cboAsignatura":"' + document.getElementById("cboAsignatura").value + '", ';
        texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
        texto += '"dtpFechaClase":"' + document.getElementById("dtpFechaClase").value + '", ';
        texto += '"txnSemestre":"' + document.getElementById("txnSemestre").value + '", ';
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
                url: 'procAsistencia.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridAsistencias.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
});
</script>