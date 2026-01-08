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
    require_once ("funciones/fxCarreras.php");
    require_once ("funciones/fxSyllabus.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$mnRegistro = fxVerificaUsuario();
	
	if ($mnRegistro == 0)
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
		$mbPermisoUsuario = fxPermisoUsuario("procSyllabus");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
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
			if (isset($_POST["txtCodSyllabus"]))
			{
				$mnOperacion = $_POST["Operacion"];
				$msCodigo = $_POST["txtCodSyllabus"];
                $msPlanEstudio = $_POST["cboPlanEstudio"];
                $msDocente = $_POST["cboDocente"];
                $msAsignatura = $_POST["cboAsignatura"];
                $mdFecha = $_POST["dtpFecha"];
                $mnTurno = $_POST["optTurno"];
                $mnAnno = $_POST["txnAnno"];
                $msGrupo = $_POST["txtGrupo"];
                $msMediacion = $_POST["txtMediacion"];
                $msEjesValores = $_POST["txtEjesValores"];

                $gridObjetivoGrl = $_POST["gridObjGral"];
                $gridObjetivoUnd = $_POST["gridObjUnd"];
                $gridDetalle = $_POST["gridDetalle"];

                if (isset($_POST["gridObsDocente"]))
                    $gridObsDocente = $_POST["gridObsDocente"];

                if (isset($_POST["gridObsAcademica"]))
                    $gridObsAcademica = $_POST["gridObsAcademica"];

                if ($mnOperacion == 0)
                {
                    $msCodigo = fxGuardarSyllabus($msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnTurno, $msGrupo, $msMediacion, $msEjesValores);
                    $msBitacora = $msCodigo . "; " . $msPlanEstudio . "; " . $msDocente . "; " . $msAsignatura . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnTurno . "; " . $msGrupo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO070A", $msCodigo, "", "Agregar", $msBitacora);
                }
                else
                {
                    fxModificarSyllabus($msCodigo, $msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnTurno, $msGrupo, $msMediacion, $msEjesValores);
                    fxBorrarDetObjGral($msCodigo);
                    fxBorrarDetObjUnd($msCodigo);
                    fxBorrarDetObsAcademica($msCodigo);
                    fxBorrarDetObsDocente($msCodigo);
                    fxBorrarDetSyllabus($msCodigo);
                    $msBitacora = $msCodigo . "; " . $msPlanEstudio . "; " . $msDocente . "; " . $msAsignatura . "; " . $mdFecha . "; " . $mnAnno . "; " . $mnTurno . "; " . $msGrupo;
                    fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO070A", $msCodigo, "", "Modificar", $msBitacora);
                }
                
                $itemId = 1;
				foreach($gridObjetivoGrl as $Registro)
				{
					$msObjetivo = $Registro['objGral'];
                    fxGuardarDetObjGral($msCodigo, $itemId, $msObjetivo);
                    $itemId++;
				}
				
				$itemId = 1;
				foreach($gridObjetivoUnd as $Registro)
				{
                    $msObjetivo = $Registro['objUnd'];
                    $msUnidad = $Registro['unidad'];
					fxGuardarDetObjUnd($msCodigo, $itemId, $msUnidad, $msObjetivo);
					$itemId++;
                }
                
                if (isset($_POST["gridObsDocente"]))
                {
                    $itemId = 1;
                    foreach($gridObsDocente as $Registro)
                    {
                        $msObservacion = $Registro['obsDocente'];
                        fxGuardarDetObsDocente($msCodigo, $itemId, $msObservacion);
                        $itemId++;
                    }
                }

                if (isset($_POST["gridObsAcademica"]))
                {
                    $itemId = 1;
                    foreach($gridObsAcademica as $Registro)
                    {
                        $msObservacion = $Registro['obsAcademica'];
                        fxGuardarDetObsAcademica($msCodigo, $itemId, $msObservacion);
                        $itemId++;
                    }
                }

                $itemId = 1;
				foreach($gridDetalle as $Registro)
				{
                    $mdFecha = $Registro['fecha'];
                    $msUnidad = $Registro['unidad'];
                    $msContenido = $Registro['contenido'];
                    $msObjetivoEsp = $Registro['objEsp'];
                    $msForma = $Registro['forma'];
                    $msMedios = $Registro['medios'];
                    $msEvaluacion = $Registro['evaluacion'];
					fxGuardarDetSyllabus($msCodigo, $itemId, $mdFecha, $msUnidad, $msContenido, $msObjetivoEsp, $msForma, $msMedios, $msEvaluacion);
					$itemId++;
				}
				?><meta http-equiv="Refresh" content="0;url=gridSyllabus.php" /><?php
			}
			else
			{
                if (isset($_POST["UMOJN"]))
				    $msCodigo = $_POST["UMOJN"];
                else
                    $msCodigo = "";

				$mRecordSet = fxDevuelveSyllabus (0, $msCodigo);
                $mnRegistros = $mRecordSet->rowCount();
                if ($mnRegistros > 0)
                {
                    $mFila = $mRecordSet->fetch();
                    $msPlanEstudio = $mFila["PLANESTUDIO_REL"];
                    $msDocente = $mFila["DOCENTE_REL"];
                    $msAsignatura = $mFila["ASIGNATURA_REL"];
                    $mdFecha = $mFila["FECHA_070"];
                    $mnAnno = $mFila["ANNO_070"];
                    $mnTurno = $mFila["TURNO_070"];
                    $msGrupo = $mFila["GRUPO_070"];
                    $msMediacion = $mFila["RECOMENDACIONES_070"];
                    $msEjesValores = $mFila["EJESVALORES_070"];
                }
                else 
                {
                    $msPlanEstudio = "";
                    $msDocente = "";
                    $msCarrera = "";
                    $msAsignatura = "";
                    $mdFecha = date('Y-m-d');
                    $mnAnno = date('Y');
                    $mnTurno = 0;
                    $msGrupo = "";
                    $msMediacion = "";
                    $msEjesValores = "";
                }
	?>

<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
			<div class="col-xs-12 col-md-11">
				<div class="degradado"><strong>Syllabus</strong></div>
			</div>
		</div>

        <div class="row">
            <div class="col-xs-12 offset-sm-none col-md-12 offset-md-1">
                <form id="procSyllabus" name="procSyllabus" action="procSyllabus.php">
                    <div class="form-group row">
                        <label for="txtCodSyllabus" class="col-sm-12 col-md-2 col-form-label">Código del Syllabus</label>
                        <div class="col-sm-12 col-md-2">
                            <?php echo('<input type="text" class="form-control" id="txtCodSyllabus" name="txtCodSyllabus" value="' . $msCodigo . '" readonly />'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFecha" class="col-sm-12 col-md-2 col-form-label">Fecha</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
								echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />');
						    ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                {
                                    echo('<select class="form-control" id="cboDocente" name="cboDocente">');
                                    
                                    if (trim($_SESSION["gsDocente"]) != "" and $mbAdministrador == 0)
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
                        <label for="cboCarrera" class="col-sm-auto col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaPlanEstudio()">');
                                else
                                    echo('<select class="form-control" id="cboCarrera" name="cboCarrera" disabled>');

                                if ($msAsignatura != ""){
                                    $msConsulta = "select CARRERA_REL from UMO060A where ASIGNATURA_REL = ?";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute([$msAsignatura]);
                                    $mFila = $mDatos->fetch();
                                    $msCarrera = $mFila["CARRERA_REL"];
                                }

                                $mDatos = fxDevuelveCarrera(1);
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
                        <label for="cboPlanEstudio" class="col-sm-auto col-md-2 col-form-label">Plan de estudio</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboPlanEstudio" name="cboPlanEstudio" onchange="llenaAsignatura()">');
                                else
                                    echo('<select class="form-control" id="cboPlanEstudio" name="cboPlanEstudio" disabled>');
                                
                                $msConsulta = "Select PLANESTUDIO_REL, PERIODO_050, ACTIVO_050 from UMO050A where CARRERA_REL = ?";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msCarrera]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = trim($mFila["PLANESTUDIO_REL"]);
                                    $msTexto = "Período " . trim($mFila["PERIODO_050"]);
                                    $mbActivo = $mFila["ACTIVO_050"];

                                    if ($msPlanEstudio == "")
                                    {
                                        if ($mbActivo == 1)
                                        {
                                            echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                            $msPlanEstudio = $msValor;
                                        }
                                    }
                                    else
                                    {
                                        if ($msPlanEstudio == $msValor)
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
                        <label for="cboAsignatura" class="col-sm-auto col-md-2 col-form-label">Asignatura</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msCodigo == "")
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura">');
                                else
                                    echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura" disabled>');
                                
                                $msConsulta = "Select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO051A where UMO051A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and PLANESTUDIO_REL = ? order by NOMBRE_060";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msPlanEstudio]);
                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = rtrim($mFila["ASIGNATURA_REL"]);
                                    $msTexto = rtrim($mFila["NOMBRE_060"]);
                                    if ($msAsignatura == "")
                                    {
                                        echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                        $msAsignatura = $msValor;
                                    }
                                    else
                                    {
                                        if ($msAsignatura == $msValor)
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
                        <label for="txtGrupo" class="col-sm-12 col-md-2 col-form-label">Grupo</label>
                        <div class="col-sm-12 col-md-3">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="text" class="form-control" id="txtGrupo" name="txtGrupo" value="' . $msGrupo . '" />');
                                else
                                    echo('<input type="text" class="form-control" id="txtGrupo" name="txtGrupo" value="' . $msGrupo . '" disabled />');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txnAnno" class="col-sm-12 col-md-2 col-form-label">Año lectivo</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                if ($msCodigo == "")
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" />');
                                else
                                    echo('<input type="number" class="form-control" id="txnAnno" name="txnAnno" value="' . $mnAnno . '" disabled />');
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
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" disabled checked /> Dominical');
                                        else
                                            echo('&emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" disabled /> Dominical');
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtMediacion" class="col-sm-12 col-md-2 col-form-label">Mediación pedagógica</label>
                        <div class="col-sm-12 col-md-8">
                            <?php echo('<textarea class="form-control" id="txtMediacion" name="txtMediacion" rows="3" maxlength="300">' . $msMediacion . '</textarea>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtEjesValores" class="col-sm-12 col-md-2 col-form-label">Ejes transversales y valores</label>
                        <div class="col-sm-12 col-md-8">
                            <?php echo('<textarea class="form-control" id="txtEjesValores" name="txtEjesValores" rows="3" maxlength="300">' . $msEjesValores . '</textarea>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObjGral" class="col-sm-12 col-md-8 form-label">Objetivos generales de la asignatura</label>
                        </div>
                        <div class="col-sm-12 col-md-10">
                            <div id="dvObjGral">
                                <table id="dgObjGral" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObjGral', hidden:'true'">codObjGral</th>
                                            <th data-options="field:'objGral',width:'100%',align:'left'">Objetivo general</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObjGral($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBJETIVOG_REL"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_071"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObjGral" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObjGral').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObjGral()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObjGral()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObjGral()">Deshacer</a>
                    </div>

                    <div id="dlgObjGral" class="easyui-dialog" title="Objetivo general" data-options="closed:true, modal:true, buttons: '#btnObjGral'" style="top:10%; height:15%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtObjGral" class="col-sm-12 col-md-3 form-label">Objetivo general</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObjGral" name="txtObjGral" rows="5" maxlength="400"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObjGral">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObjGral()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObjGral').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObjUnd" class="col-sm-12 col-md-4 form-label">Objetivos por unidad</label>
                        </div>
                        <div class="col-sm-auto col-md-10">
                            <div id="dvObjUnd">
                                <table id="dgObjUnd">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObjUnd', hidden:'true'">codObjUnd</th>
                                            <th data-options="field:'unidad',width:'25%',align:'left'">Unidad</th>
                                            <th data-options="field:'objUnd',width:'75%',align:'left'">Objetivo de unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObjUnd($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBJETIVOU_REL"] . "</td>");
                                            echo ("<td>" . $mFila["UNIDAD_072"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_072"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObjUnd" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObjUnd').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObjUnd()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObjUnd()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObjUnd()">Deshacer</a>
                    </div>

                    <div id="dlgObjUnd" class="easyui-dialog" title="Objetivos por unidades" data-options="closed:true, modal:true, buttons: '#btnObjUnd'" style="top:10%; height:20%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtUnidad" class="col-sm-12 col-md-3 form-label">Unidad</label>
                            <div class="col-sm-12 col-md-8">
                                <td><input id="txtUnidad" name="txtUnidad" class="form-control" value="" maxlength="100"></td>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtObjUnd" class="col-sm-12 col-md-3 form-label">Objetivo de la Unidad</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObjUnd" name="txtObjUnd" rows="5" maxlength="500"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObjUnd">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObjUnd()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObjUnd').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label class="col-sm-12 col-md-3 form-label">Detalle del syllabus</label>
                        </div>
                        <div class="col-sm-auto col-md-10">
                            <div id="dvDetalle">
                                <table id="dgDetalle" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codDetalle', hidden:'true'">codDetalle</th>
                                            <th data-options="field:'fecha', align:'left'">Fecha</th>
                                            <th data-options="field:'unidad', editor:'text', align:'left'">Unidad</th>
                                            <th data-options="field:'contenido', editor:'text', align:'left'">Contenido</th>
                                            <th data-options="field:'objEsp', editor:'text', align:'left'">Objetivo específico</th>
                                            <th data-options="field:'forma', editor:'text', align:'left'">Mediación pedagógica y ejes</th>
                                            <th data-options="field:'medios', editor:'text', align:'left'">Recursos didácticos</th>
                                            <th data-options="field:'evaluacion', editor:'text', align:'left'">Evaluación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetSyllabus($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["DETSYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["FECHA_073"] . "</td>");
                                            echo ("<td>" . $mFila["UNIDAD_073"] . "</td>");
                                            echo ("<td>" . $mFila["CONTENIDO_073"] . "</td>");
                                            echo ("<td>" . $mFila["OBJETIVOESP_073"] . "</td>");
                                            echo ("<td>" . $mFila["FORMA_073"] . "</td>");
                                            echo ("<td>" . $mFila["MEDIOS_073"] . "</td>");
                                            echo ("<td>" . $mFila["EVALUACION_073"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftDetalle" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgDetalle').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitDetalle()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitDetalle()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectDetalle()">Deshacer</a>
                    </div>

                    <div id="dlgDetalle" class="easyui-dialog" title="Detalle del syllabus" data-options="closed:true, modal:true, buttons: '#btnDetalle'" style="top:10%; height:45%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="dtpFechaDet" class="col-sm-12 col-md-3 form-label">Fecha</label>
                            <div class="col-sm-12 col-md-4">
                                <?php echo('<input type="date" class="form-control" id="dtpFechaDet" name="dtpFechaDet" value="' . date('Y-m-d') . '" />'); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtUnidadDet" class="col-sm-12 col-md-3 form-label">Unidad</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtUnidadDet" name="txtUnidadDet" rows="2" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtContenidoDet" class="col-sm-12 col-md-3 form-label">Contenido o temas</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtContenidoDet" name="txtContenidoDet" rows="2" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtObjEspDet" class="col-sm-12 col-md-3 form-label">Objetivo específico</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObjEspDet" name="txtObjEspDet" rows="2" maxlength="200"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtFormaDet" class="col-sm-12 col-md-3 form-label">Mediación pedagógica y ejes</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtFormaDet" name="txtFormaDet" rows="2" maxlength="100"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtMediosDet" class="col-sm-12 col-md-3 form-label">Recursos didácticos</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtMediosDet" name="txtMediosDet" rows="2" maxlength="100"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="txtEvaluacionDet" class="col-sm-12 col-md-3 form-label">Evaluación</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtEvaluacionDet" name="txtEvaluacionDet" rows="2" maxlength="100"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnDetalle">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarDetalle()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgDetalle').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObsDocente" class="col-sm-12 col-md-8 form-label">Observaciones docentes</label>
                        </div>
                        <div class="col-sm-12 col-md-10">
                            <div id="dvObsDocente">
                                <table id="dgObsDocente" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObsDocente', hidden:'true'">codObsDocente</th>
                                            <th data-options="field:'obsDocente',width:'100%',align:'left'">Observación docente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObsDocente($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBSDOCENTE_REL"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_074"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObsDocente" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObsDocente').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObsDocente()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObsDocente()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObsDocente()">Deshacer</a>
                    </div>

                    <div id="dlgObsDocente" class="easyui-dialog" title="Observación docente" data-options="closed:true, modal:true, buttons: '#btnObsDocente'" style="top:10%; height:15%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtObsDocente" class="col-sm-12 col-md-3 form-label">Observación docente</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObsDocente" name="txtObsDocente" rows="5" maxlength="300"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObsDocente">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObsDocente()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObsDocente').dialog('close')">Cerrar</a>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgObsAcademica" class="col-sm-12 col-md-8 form-label">Observaciones de dirección académica</label>
                        </div>
                        <div class="col-sm-12 col-md-10">
                            <div id="dvObsAcademica">
                                <table id="dgObsAcademica" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'codSyllabus', hidden:'true'">codSyllabus</th>
                                            <th data-options="field:'codObsAcademica', hidden:'true'">codObsAcademica</th>
                                            <th data-options="field:'obsAcademica',width:'100%',align:'left'">Observación dirección académica</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $mDatos = fxObtenerDetObsAcademica($msCodigo);
                                        while ($mFila = $mDatos->fetch())
                                        {
                                            echo ("<tr>");
                                            echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
                                            echo ("<td>" . $mFila["OBSACADEMICA_REL"] . "</td>");
                                            echo ("<td>" . $mFila["TEXTO_075"] . "</td>");
                                            echo ("</tr>");
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="ftObsAcademica" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="$('#dlgObsAcademica').dialog('open')">Agregar</a>
						<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitObsAcademica()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitObsAcademica()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectObsAcademica()">Deshacer</a>
                    </div>

                    <div id="dlgObsAcademica" class="easyui-dialog" title="Observación dirección académica" data-options="closed:true, modal:true, buttons: '#btnObsAcademica'" style="top:10%; height:17%; width:50%; padding:1%">
                        <div class="form-group row">
                            <label for="txtObsAcademica" class="col-sm-12 col-md-3 form-label">Observación dirección académica</label>
                            <div class="col-sm-12 col-md-8">
                                <textarea class="form-control" id="txtObsAcademica" name="txtObsAcademica" rows="5" maxlength="300"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="btnObsAcademica">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="agregarObsAcademica()">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#dlgObsAcademica').dialog('close')">Cerrar</a>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8">
                            <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridSyllabus.php';" />
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
window.onload = function() {
    $('#txtObjGral').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('#txtObjUnd').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('#txtObsDocente').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    $('#txtObsAcademica').keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });

    $('#dgObjGral').datagrid({
        striped: true,
        footer: '#ftObjGral',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObjGral
    });

    $('#dgObjUnd').datagrid({
        striped: true,
        footer: '#ftObjUnd',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObjUnd
    });

    $('#dgDetalle').datagrid({
        striped: true,
        footer: '#ftDetalle',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellDetalle
    });

    $('#dgObsDocente').datagrid({
        striped: true,
        footer: '#ftObsDocente',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObsDocente
    });

    $('#dgObsAcademica').datagrid({
        striped: true,
        footer: '#ftObsAcademica',
        singleSelect: true,
        method: 'get',
        onClickCell: onClickCellObsAcademica
    });
}

function verificarFormulario() {
    var regObjGral = $('#dgObjGral').datagrid('getRows').length;
    var regObjUnd = $('#dgObjUnd').datagrid('getRows').length;
    var regObjDetalle = $('#dgDetalle').datagrid('getRows').length;
    var administrador = <?php echo($mbAdministrador) ?>;

    if (regObjGral == 0) {
        $.messager.alert('UMOJN', 'Faltan los Objetivos generales.', 'warning');
        return false;
    }

    if (regObjUnd == 0) {
        $.messager.alert('UMOJN', 'Faltan los Objetivos por unidad.', 'warning');
        return false;
    }

    if (regObjDetalle == 0) {
        $.messager.alert('UMOJN', 'Falta el detalle del syllabus.', 'warning');
        return false;
    }
    return true;
}

function llenaAsignaturas()
{
    var planEstudio = document.getElementById('cboPlanEstudio').value;
    var datos = new FormData();
    datos.append('planEstudio', planEstudio);

    $.ajax({
        url: 'funciones/fxDatosSyllabus.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            document.getElementById('cboAsignatura').innerHTML = response;
        }
    })
}

function llenaPlanEstudio()
{
    var syllabus = document.getElementById('txtCodSyllabus').value;
    var carrera = document.getElementById('cboCarrera').value;
    var datos = new FormData();
    datos.append('carreraPE', carrera);
    datos.append('syllabus', syllabus);

    $.ajax({
        url: 'funciones/fxDatosSyllabus.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response){
            var data = JSON.parse(response)
            document.getElementById('cboPlanEstudio').innerHTML = data.resultado;
            llenaAsignaturas(data.planEstudio);
        }
    })
}

function agregarObjGral(){
    if ($('#txtObjGral').val() == "")
        $.messager.alert('UMOJN', 'Falta el Objetivo general.', 'warning');
    else
    {
        appendObjGral();
        $.messager.alert('UMOJN', 'Objetivo general agregado.', 'info');
    }
}

function agregarObjUnd(){
    if ($('#txtUnidad').val() == "")
        $.messager.alert('UMOJN', 'Falta la unidad.', 'warning');
    else{
        if ($('#txtObjUnd').val() == "")
            $.messager.alert('UMOJN', 'Falta el Objetivo de la unidad.', 'warning');
        else
        {
            appendObjUnd();
            $.messager.alert('UMOJN', 'Objetivo de la unidad agregado.', 'info');
        }
    }
}

function agregarObsDocente(){
    if ($('#txtObsDocente').val() == "")
        $.messager.alert('UMOJN', 'Falta la observación docente.', 'warning');
    else
    {
        appendObsDocente();
        $.messager.alert('UMOJN', 'Observación docente agregada.', 'info');
    }
}

function agregarObsAcademica(){
    if ($('#txtObsAcademica').val() == "")
        $.messager.alert('UMOJN', 'Falta la observación académica.', 'warning');
    else
    {
        appendObsAcademica();
        $.messager.alert('UMOJN', 'Observación académica agregado.', 'info');
    }
}

function agregarDetalle(){
    if ($('#txtUnidadDet').val() == "")
        $.messager.alert('UMOJN', 'Falta la unidad.', 'warning');
    else{
        if ($('#txtContenidoDet').val() == "")
            $.messager.alert('UMOJN', 'Falta el contenido.', 'warning');
        else{
            if ($('#txtObjEspDet').val() == "")
                $.messager.alert('UMOJN', 'Falta el objetivo específico.', 'warning');
            else{
                if ($('#txtFormaDet').val() == "")
                    $.messager.alert('UMOJN', 'Falta la forma de enseñanza.', 'warning');
                else{
                    if ($('#txtMediosDet').val() == "")
                        $.messager.alert('UMOJN', 'Faltan los medios o recursos.', 'warning');
                    else{
                        if ($('#txtEvaluacionDet').val() == "")
                            $.messager.alert('UMOJN', 'Falta la evaluación.', 'warning');
                        else
                            {
                                appendDetalle();
                                $.messager.alert('UMOJN', 'Registro agregado.', 'info');
                            }
                    }
                }
            }
        }
    }
}

var editIndexObjGral = undefined;
var lastIndexObjGral;
var editIndexObjUnd = undefined;
var lastIndexObjUnd;
var editIndexDetalle = undefined;
var lastIndexDetalle;
var editIndexObsDocente = undefined;
var lastIndexObsDocente;
var editIndexObsAcademica = undefined;
var lastIndexObsAcademica;

/*Grid de Objetivo general*/
$('#dgObjGral').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObjGral != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObjGral);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObjGral = rowIndex;
    }
});

function endEditingObjGral() {
    if (editIndexObjGral == undefined) {
        return true
    }
    if ($('#dgObjGral').datagrid('validateRow', editIndexObjGral)) {
        $('#dgObjGral').datagrid('endEdit', editIndexObjGral);
        editIndexObjGral = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellObjGral(index, field) {
    if (editIndexObjGral != index) {
        if (endEditingObjGral()) {
            $('#dgObjGral').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObjGral = index;
        } else {
            setTimeout(function() {
                $('#dgObjGral').datagrid('selectRow', editIndexObjGral);
            }, 0);
        }
    }
}

function appendObjGral() {
    if (endEditingObjGral()) {
        $('#dgObjGral').datagrid('appendRow', {
            objGral: $('#txtObjGral').val()
        });
        editIndexObjGral = $('#dgObjGral').datagrid('getRows').length;
        $('#dgObjGral').datagrid('selectRow', editIndexObjGral).datagrid('beginEdit', editIndexObjGral);
    }
}

function removeitObjGral() {
    if (editIndexObjGral == undefined) {
        return
    }
    $('#dgObjGral').datagrid('cancelEdit', editIndexObjGral)
        .datagrid('deleteRow', editIndexObjGral);
    editIndexObjGral = undefined;
}

function acceptitObjGral() {
    if (endEditingObjGral()) {
        $('#dgObjGral').datagrid('acceptChanges');
    }
}

function rejectObjGral() {
    $('#dgObjGral').datagrid('rejectChanges');
    editIndexObjGral = undefined;
}

/*Grid de Objetivo por unidad*/

$('#dgObjUnd').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObjUnd != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObjUnd);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObjUnd = rowIndex;
    }
});

function endEditingObjUnd() {
    if (editIndexObjUnd == undefined) {
        return true
    }
    if ($('#dgObjUnd').datagrid('validateRow', editIndexObjUnd)) {
        $('#dgObjUnd').datagrid('endEdit', editIndexObjUnd);
        editIndexObjUnd = undefined;
        return true;
    } else {
        return false;
    }
}

function appendObjUnd() {
    if (endEditingObjUnd()) {
        $('#dgObjUnd').datagrid('appendRow', {
            unidad: $('#txtUnidad').val(),
            objUnd: $('#txtObjUnd').val()
        });
        editIndexObjUnd = $('#dgObjUnd').datagrid('getRows').length;
        $('#dgObjUnd').datagrid('selectRow', editIndexObjUnd).datagrid('beginEdit', editIndexObjUnd);
    }
}

function onClickCellObjUnd(index, field) {
    if (editIndexObjUnd != index) {
        if (endEditingObjUnd()) {
            $('#dgObjUnd').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObjUnd = index;
        } else {
            setTimeout(function() {
                $('#dgObjUnd').datagrid('selectRow', editIndexObjUnd);
            }, 0);
        }
    }
}

function removeitObjUnd() {
    if (editIndexObjUnd == undefined) {
        return
    }
    $('#dgObjUnd').datagrid('cancelEdit', editIndexObjUnd)
        .datagrid('deleteRow', editIndexObjUnd);
    editIndexObjUnd = undefined;
}

function acceptitObjUnd() {
    if (endEditingObjUnd()) {
        $('#dgObjUnd').datagrid('acceptChanges');
    }
}

function rejectObjUnd() {
    $('#dgObjUnd').datagrid('rejectChanges');
    editIndexObjUnd = undefined;
}

/*Grid de Detalle*/
$('#dgDetalle').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexDetalle != rowIndex) {
            $(this).datagrid('endEdit', lastIndexDetalle);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexDetalle = rowIndex;
    }
});

function endEditingDetalle() {
    if (editIndexDetalle == undefined) {
        return true
    }
    if ($('#dgDetalle').datagrid('validateRow', editIndexDetalle)) {
        $('#dgDetalle').datagrid('endEdit', editIndexDetalle);
        editIndexDetalle = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellDetalle(index, field) {
    if (editIndexDetalle != index) {
        if (endEditingDetalle()) {
            $('#dgDetalle').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexDetalle = index;
        } else {
            setTimeout(function() {
                $('#dgDetalle').datagrid('selectRow', editIndexDetalle);
            }, 0);
        }
    }
}

function appendDetalle() {
    if (endEditingDetalle()) {
        $('#dgDetalle').datagrid('appendRow', {
            fecha: $('#dtpFechaDet').val(),
            unidad: $('#txtUnidadDet').val(),
            contenido: $('#txtContenidoDet').val(),
            objEsp: $('#txtObjEspDet').val(),
            forma: $('#txtFormaDet').val(),
            medios: $('#txtMediosDet').val(),
            evaluacion: $('#txtEvaluacionDet').val()
        });
        editIndexDetalle = $('#dgDetalle').datagrid('getRows').length;
        $('#dgDetalle').datagrid('selectRow', editIndexDetalle).datagrid('beginEdit', editIndexDetalle);
    }
}

function removeitDetalle() {
    if (editIndexDetalle == undefined) {
        return
    }
    $('#dgDetalle').datagrid('cancelEdit', editIndexDetalle)
        .datagrid('deleteRow', editIndexDetalle);
    editIndexDetalle = undefined;
}

function acceptitDetalle() {
    if (endEditingDetalle()) {
        $('#dgDetalle').datagrid('acceptChanges');
    }
}

function rejectDetalle() {
    $('#dgDetalle').datagrid('rejectChanges');
    editIndexDetalle = undefined;
}

/*Grid de Observación docente*/
$('#dgObsDocente').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObsDocente != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObsDocente);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObsDocente = rowIndex;
    }
});

function endEditingObsDocente() {
    if (editIndexObsDocente == undefined) {
        return true
    }
    if ($('#dgObsDocente').datagrid('validateRow', editIndexObsDocente)) {
        $('#dgObsDocente').datagrid('endEdit', editIndexObsDocente);
        editIndexObsDocente = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellObsDocente(index, field) {
    if (editIndexObsDocente != index) {
        if (endEditingObsDocente()) {
            $('#dgObsDocente').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObsDocente = index;
        } else {
            setTimeout(function() {
                $('#dgObsDocente').datagrid('selectRow', editIndexObsDocente);
            }, 0);
        }
    }
}

function appendObsDocente() {
    if (endEditingObsDocente()) {
        $('#dgObsDocente').datagrid('appendRow', {
            obsDocente: $('#txtObsDocente').val()
        });
        editIndexObsDocente = $('#dgObsDocente').datagrid('getRows').length;
        $('#dgObsDocente').datagrid('selectRow', editIndexObsDocente).datagrid('beginEdit', editIndexObsDocente);
    }
}

function removeitObsDocente() {
    if (editIndexObsDocente == undefined) {
        return
    }
    $('#dgObsDocente').datagrid('cancelEdit', editIndexObsDocente)
        .datagrid('deleteRow', editIndexObsDocente);
    editIndexObsDocente = undefined;
}

function acceptitObsDocente() {
    if (endEditingObsDocente()) {
        $('#dgObsDocente').datagrid('acceptChanges');
    }
}

function rejectObsDocente() {
    $('#dgObsDocente').datagrid('rejectChanges');
    editIndexObsDocente = undefined;
}

/*Grid de Observación dirección académica*/
$('#dgObsAcademica').datagrid({
    onClickRow: function(rowIndex) {
        if (lastIndexObsAcademica != rowIndex) {
            $(this).datagrid('endEdit', lastIndexObsAcademica);
            $(this).datagrid('beginEdit', rowIndex);
        }
        lastIndexObsAcademica = rowIndex;
    }
});

function endEditingObsAcademica() {
    if (editIndexObsAcademica == undefined) {
        return true
    }
    if ($('#dgObsAcademica').datagrid('validateRow', editIndexObsAcademica)) {
        $('#dgObsAcademica').datagrid('endEdit', editIndexObsAcademica);
        editIndexObsAcademica = undefined;
        return true;
    } else {
        return false;
    }
}

function onClickCellObsAcademica(index, field) {
    if (editIndexObsAcademica != index) {
        if (endEditingObsAcademica()) {
            $('#dgObsAcademica').datagrid('selectRow', index)
                .datagrid('beginEdit', index);
                editIndexObsAcademica = index;
        } else {
            setTimeout(function() {
                $('#dgObsAcademica').datagrid('selectRow', editIndexObsAcademica);
            }, 0);
        }
    }
}

function appendObsAcademica() {
    if (endEditingObsAcademica()) {
        $('#dgObsAcademica').datagrid('appendRow', {
            obsAcademica: $('#txtObsAcademica').val()
        });
        editIndexObsAcademica = $('#dgObsAcademica').datagrid('getRows').length;
        $('#dgObsAcademica').datagrid('selectRow', editIndexObsAcademica).datagrid('beginEdit', editIndexObsAcademica);
    }
}

function removeitObsAcademica() {
    if (editIndexObsAcademica == undefined) {
        return
    }
    $('#dgObsAcademica').datagrid('cancelEdit', editIndexObsAcademica)
        .datagrid('deleteRow', editIndexObsAcademica);
    editIndexObsAcademica = undefined;
}

function acceptitObsAcademica() {
    if (endEditingObsAcademica()) {
        $('#dgObsAcademica').datagrid('acceptChanges');
    }
}

function rejectObsAcademica() {
    $('#dgObsAcademica').datagrid('rejectChanges');
    editIndexObsAcademica = undefined;
}

$('form').submit(function(e) {
    e.preventDefault();

    if (verificarFormulario()) {
        var texto;
        var datos;
        var registros;
        var i;
        var gridObjGral = $('#dgObjGral').datagrid('getData');
        var gridObjUnd = $('#dgObjUnd').datagrid('getData');
        var gridDetalle = $('#dgDetalle').datagrid('getData');

        texto = '{"txtCodSyllabus":"' + document.getElementById("txtCodSyllabus").value + '", ';
        if (document.getElementById("txtCodSyllabus").value == "")
            texto += '"Operacion":"0", ';
        else
            texto += '"Operacion":"1", ';
        texto += '"dtpFecha":"' + document.getElementById("dtpFecha").value + '", ';
        texto += '"cboDocente":"' + document.getElementById("cboDocente").value + '", ';
        texto += '"cboCarrera":"' + document.getElementById("cboCarrera").value + '", ';
        texto += '"cboPlanEstudio":"' + document.getElementById("cboPlanEstudio").value + '", ';
        texto += '"cboAsignatura":"' + document.getElementById("cboAsignatura").value + '", ';
        texto += '"txtGrupo":"' + document.getElementById("txtGrupo").value + '", ';
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

        texto += '"txtMediacion":"' + document.getElementById("txtMediacion").value + '", ';
        texto += '"txtEjesValores":"' + document.getElementById("txtEjesValores").value + '", ';

        registros = $('#dgObjGral').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridObjGral": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"objGral":"' + gridObjGral.rows[i].objGral;
                if (i == registros)
                    texto += '"}],';
                else
                    texto += '"},';
            }
        }

        registros = $('#dgObjUnd').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridObjUnd": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"unidad":"' + gridObjUnd.rows[i].unidad + '","objUnd":"' + gridObjUnd.rows[i].objUnd;
                if (i == registros)
                    texto += '"}],';
                else
                    texto += '"},';
            }
        }

        registros = $('#dgDetalle').datagrid('getRows').length - 1;

        if (registros >= 0) {
            texto += '"gridDetalle": [';
            for (i = 0; i <= registros; i++) {
                texto += '{"fecha":"' + gridDetalle.rows[i].fecha + '","unidad":"' + gridDetalle.rows[i].unidad + '","contenido":"' + gridDetalle.rows[i].contenido + '","objEsp":"' + gridDetalle.rows[i].objEsp + '","forma":"' + gridDetalle.rows[i].forma + '","medios":"' + gridDetalle.rows[i].medios + '","evaluacion":"' + gridDetalle.rows[i].evaluacion;
                if (i == registros)
                    texto += '"}]}';
                else
                    texto += '"},';
            }
        }

        datos = JSON.parse(texto);

        $.ajax({
                url: 'procSyllabus.php',
                type: 'post',
                data: datos,
            })
            .done(function() {
                location.href = "gridSyllabus.php";
            })
            .fail(function() {
                console.log('Error')
            });
    }
})
</script>