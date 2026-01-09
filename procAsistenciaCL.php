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
                $mnModulo = $_POST["cboModulo"];
                $msCursos = $_POST["cboCurso"];
                $mdFecha = $_POST["dtpFechaClase"];
                $mnTurno = $_POST["optTurno"];
                $mnAnno = $_POST["txnAnno"];
                $mnModulosLectivos = $_POST["txnSemestre"];
				$gridEstudiantes = $_POST["gridEstudiantes"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarAsistencia ($msDocente, $mnModulo, $msCursos, $mdFecha, $mnTurno, $mnAnno, $mnModulosLectivos);
                    $msBitacora = $msCodigo . "; " . $mnModulo . "; " . $msCursos . "; " . $mdFecha . "; " . $mnTurno . "; " . $mnAnno . "; " . $mnModulosLectivos;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO320A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarAsistencia ($msCodigo, $msDocente, $mnModulo, $msCursos, $mdFecha, $mnTurno, $mnAnno, $mnModulosLectivos);
                    fxBorrarDetAsistencia($msCodigo);
                    $msBitacora = $msCodigo . "; " . $mnModulo . "; " . $msCursos . "; " . $mdFecha . "; " . $mnTurno . "; " . $mnAnno . "; " . $mnModulosLectivos;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO320A", $msCodigo, "", "Modificar", $msBitacora);
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
                    $msCursos = $mFila["CURSOS_REL"];
                    $mnModulo = $mFila["MODULO_REL"];
                    $mdFechaClase = $mFila["FECHA_320"];
                    $mnTurno = $mFila["TURNO_320"];
                    $mnAnno = $mFila["ANNO_320"];
                    $mnModulosLectivos = $mFila["MODULOLECTIVO_320"];
                }
                else 
                {
                    $msDocente = "";
                    $msCursos = "";
                    $mnModulo = "";
                    $mdFechaClase = date("Y-m-d");
                    $mnTurno = 1;
                    $mnAnno = date('Y');
                    $mnModulosLectivos = 1;
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
                                   echo('<select class="form-control" id="cboDocente" name="cboDocente" onchange="actualizaCursos()">');

                                    
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
        <select class="form-control" id="cboCurso" name="cboCurso" onchange="llenaAsignaturas()" <?php echo($msCursos != "" ? 'disabled' : ''); ?>>
            <?php
                $msConsulta = "select CURSOS_REL, NOMBRE_190 from UMO190A order by NOMBRE_190";
                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                $mDatos->execute();

                while ($mFila = $mDatos->fetch()) {
                    $mValor = rtrim($mFila["CURSOS_REL"]);
                    $mTexto = rtrim($mFila["NOMBRE_190"]);

                    if ($msCursos == $mValor)
                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                    else
                        echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                }
            ?>
        </select>
    </div>
</div>

                 <div class="form-group row">
    <label for="cboModulo" class="col-sm-12 col-md-2 col-form-label">Modulo</label>
    <div class="col-sm-12 col-md-6">
        <select class="form-control" id="cboModulo" name="cboModulo" onchange="llenaEstudiantes()" <?php echo($msCodigo != "" ? 'disabled' : ''); ?>>
            <?php
                $msConsulta = "select UMO280A.MODULO_REL, NOMBRE_280 
                               from UMO280A 
                               where CURSOS_REL = ? and DOCENTE_REL = ? 
                               order by NOMBRE_280";
                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                $mDatos->execute([$msCursos, $msDocente]);

                while ($mFila = $mDatos->fetch()) {
                    $mValor = rtrim($mFila["MODULO_REL"]);
                    $mTexto = rtrim($mFila["NOMBRE_280"]);

                    if ($mnModulo == $mValor)
                        echo("<option value='" . $mValor . "' selected>" . $mTexto . "</option>");
                    else
                        echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                }
            ?>
        </select>
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
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnModulosLectivos . '" onchange="llenaEstudiantes()" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnSemestre" name="txnSemestre" value="' . $mnModulosLectivos . '" readonly />');
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
                                      $mDatos = fxDevuelveDetAsistencia($msCodigo, $mnModulo, $mnTurno, $mnAnno, $mnModulosLectivos);
                                        while ($mFila = $mDatos->fetch()) {
                                            $msEstudiante = trim($mFila["APELLIDOS_200"]) . ", " . trim($mFila["NOMBRES_200"]);
                                            echo '<tr>';
                                            echo '<td>' . rtrim($mFila['MATCURSO_REL']) . '</td>';
                                            echo '<td>' . rtrim($msEstudiante) . '</td>';

                                            switch ($mFila['ESTADO_321']) {
                                                case 0:
                                                    echo '<td>Presente</td>';
                                                    break;
                                                case 1:
                                                    echo '<td>Ausente</td>';
                                                    break;
                                                default:
                                                    echo '<td>Justificado</td>';
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
        var asignatura = $('#cboModulo').val();
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

function llenaAsignaturas() {
    var carrera = $('#cboCurso').val();
    var docente = $('#cboDocente').val();
    var datos = new FormData();
    datos.append('carreraAsg', carrera);
    datos.append('docenteAsg', docente);

    $.ajax({
        url: 'funciones/fxDatosAsistenciaCL.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            // Rellenar el combo de módulos con lo que devuelva PHP
            document.getElementById('cboModulo').innerHTML = response;
        }
    });
}

function llenaCursos()
{
    var docente = $('#cboDocente').val();
    var datos = new FormData();
    datos.append('docenteCursos', docente);

    $.ajax({
        url: 'funciones/fxDatosAsistenciaCL.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboCurso').innerHTML = response;
            llenaAsignaturas(); // Llenar módulos según nuevo curso
        }
    })
}

function actualizaCursos() {
    var docente = $('#cboDocente').val();
    var datos = new FormData();
    datos.append('docenteCursos', docente);

    $.ajax({
        url: 'funciones/fxDatosASCL.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response) {
            // response será el HTML con <option> de cursos
            $('#cboCarrera').html(response);
            // Llenar módulos del primer curso
            llenaAsignaturas();
        }
    });
}



function llenaEstudiantes()
{
    var asistencia = document.getElementById('txtCodAsistencia').value;
    var datos = new FormData();
    var asignatura = document.getElementById('cboModulo').value;
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
            url: 'funciones/fxDatosAsistenciaCL.php',
            type: 'post',
            data: datos,
            contentType: false,
            processData: false,
         success: function(response){
    if (response) {
        try {
            datos = JSON.parse(response);
            $('#dgEST').datagrid({data: datos});
            $('#dgEST').datagrid('reload');
            verificaAsistencia();
        } catch(e) {
            console.error("Error parseando JSON:", e, response);
        }
    } else {
        console.warn("No hay datos devueltos por fxDatosAsistenciaCL.php");
    }
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
        texto += '"cboModulo":"' + document.getElementById("cboModulo").value + '", ';
        texto += '"cboCurso":"' + document.getElementById("cboCurso").value + '", ';
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