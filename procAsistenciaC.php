<?php
session_start();
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1) {
    echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
    exit('');
}

include("masterApp.php");
require_once("funciones/fxGeneral.php");
require_once("funciones/fxUsuarios.php");
require_once("funciones/fxAsistenciasCl.php");

$m_cnx_MySQL = fxAbrirConexion();
$Registro = fxVerificaUsuario();
$msDocenteSesion = $_SESSION["gsDocente"] ?? "";

if ($Registro == 0) {
    echo '<div class="container text-center"><img src="imagenes/errordeacceso.png" /></div>';
    exit;
}

$mbAdministrador = fxVerificaAdministrador();
$mbSupervisor = fxVerificaSupervisor();
$PermisoUsuario = fxPermisoUsuario("procAsistenciaC");

if ($mbAdministrador == 0 && $PermisoUsuario == 0 && $mbSupervisor == 0) {
    echo '<div class="container text-center"><img src="imagenes/errordeacceso.png" /></div>';
    exit;
}

if (isset($_POST["txtCodAsistencia"])) {

    $msCodigo = trim($_POST["txtCodAsistencia"]);
    $mnOperacion = ($msCodigo == "") ? 0 : 1;

    $msDocente = $_POST["cboDocente"];
    $msCurso   = $_POST["cboCurso"];
    $mdFecha   = $_POST["dtpFechaClase"];
    $mnTurno   = $_POST["optTurno"];
    $mnAnno    = $_POST["txnAnno"];

    $gridEstudiantes = json_decode($_POST["gridEstudiantes"], true);

    if ($mnOperacion == 0) {
        $msCodigo = fxGuardarAsistencia($msDocente, $msCurso, $mdFecha, $mnTurno, $mnAnno);
        fxAgregarBitacora($_SESSION["gsUsuario"], "UMO320A", $msCodigo, "", "Agregar",
            "$msCodigo;$msCurso;$mdFecha;$mnTurno;$mnAnno");
    } else {
        fxModificarAsistencia($msCodigo, $msDocente, $msCurso, $mdFecha, $mnTurno, $mnAnno);
        fxAgregarBitacora($_SESSION["gsUsuario"], "UMO320A", $msCodigo, "", "Modificar",
            "$msCodigo;$msCurso;$mdFecha;$mnTurno;$mnAnno");
    }
 
    foreach ($gridEstudiantes as $mRegistro) {
        $mMatricula = $mRegistro["matricula"];
        $mnEstado   = intval($mRegistro["estado"]);
        fxGuardarDetAsistencia($msCodigo, $mMatricula, $mnEstado);
    }

    echo '<meta http-equiv="Refresh" content="0;url=gridAsistenciaCL.php" />';
    exit;
}
 
$msCodigo = $_POST["UMOJN"] ?? "";
$esEdicion = ($msCodigo != "");

$mRecordSet = fxDevuelveAsistencia(0, "", $msCodigo);

if ($mRecordSet->rowCount() > 0) {
    $mFila = $mRecordSet->fetch();
    $msDocente = $mFila["DOCENTE_REL"];
    $msCurso = $mFila["CURSOS_REL"];
    $mdFechaClase = $mFila["FECHA_320"];
    $mnTurno = $mFila["TURNO_320"];
    $mnAnno = $mFila["ANNO_320"];
} else {
    $msDocente = "";
    $msCurso = "";
    $mdFechaClase = date("Y-m-d");
    $mnTurno = 1;
    $mnAnno = date("Y");
}
?>

<div class="container text-left">
    <div id="DivContenido">
        <div class="row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Asistencia</strong></div>
            </div>
        </div>
        
        <form id="procAsistenciaC" method="post">
            <input type="hidden" name="txtCodAsistencia" id="txtCodAsistencia" value="<?= $msCodigo ?>">
            <?php if ($esEdicion) { ?>
            <input type="hidden" name="cboDocente" value="<?= $msDocente ?>">
            <input type="hidden" name="cboCurso" value="<?= $msCurso ?>">
            <input type="hidden" name="optTurno" value="<?= $mnTurno ?>">
            <?php } ?>
            
             <div class="form-group row">
                <label class="col-sm-12 col-md-2 col-form-label">Docente</label>
                <div class="col-sm-12 col-md-6">
                    <select class="form-control" id="cboDocente" name="cboDocente" <?= $esEdicion ? "disabled" : "" ?> onchange="llenaCursosEstudiantes()">
                        <?php
                            $mDatos = $m_cnx_MySQL->prepare("SELECT DOCENTE_REL, NOMBRE_100 FROM UMO100A WHERE ACTIVO_100=1 ORDER BY NOMBRE_100");
                            $mDatos->execute();
                            while ($mFila = $mDatos->fetch()) {
                                $msValor = ($msDocente == $mFila["DOCENTE_REL"]) ? "selected" : "";
                                echo "<option value='{$mFila["DOCENTE_REL"]}' $msValor>{$mFila["NOMBRE_100"]}</option>";
                            }
                        ?>    
                    </select>
                </div>
            </div>
            
             <div class="form-group row">
                <label class="col-sm-12 col-md-2 col-form-label">Curso</label>
                <div class="col-sm-12 col-md-6">
                    <select class="form-control" id="cboCurso" name="cboCurso" <?= $esEdicion ? "disabled" : "" ?> onchange="llenaEstudiantes()"></select>
                </div>
            </div>
            
             <div class="form-group row">
                <label class="col-sm-12 col-md-2 col-form-label">Fecha</label>
                <div class="col-sm-12 col-md-2">
                    <input type="date" class="form-control" name="dtpFechaClase" value="<?= $mdFechaClase ?>" <?= $esEdicion ? "readonly" : "" ?>>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-12 col-md-2 col-form-label">Año</label>
                <div class="col-sm-12 col-md-2">
                    <input type="number" class="form-control" name="txnAnno" value="<?= $mnAnno ?>" <?= $esEdicion ? "readonly" : "" ?>>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-auto col-md-2 form-label">Turno</label>
                <div class="col-sm-12 col-md-7">
                    <?php
                        $turnos = ["Diurno","Matutino","Vespertino","Nocturno","Sabatino","Dominical"];
                        for ($i=1; $i<=6; $i++) {
                            $checked = ($mnTurno == $i) ? "checked" : "";
                            $disabled = $esEdicion ? "disabled" : "";
                            echo "<input type='radio' name='optTurno' value='$i' $checked $disabled onclick='llenaCursosEstudiantes()'> {$turnos[$i-1]} &emsp;";
                        }
                    ?>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-12 col-md-2 form-label">Estudiantes</label>
                <div class="col-sm-12 col-md-8">
                    <table id="dgEST" class="easyui-datagrid table" data-options=" iconCls:'icon-edit', singleSelect:true,method:'get', onClickCell:onClickCellEST, fitColumns:true">
                        <thead>
                            <tr>
                                <th data-options="field:'matricula',hidden:true">Matrícula</th>
                                <th data-options="field:'estudiante',width:'60%'">Estudiante</th>
                                <th data-options="field:'estado', width:'20%',align:'center',formatter:function(value,row){ if(value==0) return 'Presente';if(value==1) return 'Ausente';if(value==2) return 'Justificado';return '';},
                                    editor:{type:'combobox',options:{ panelHeight:'auto',  valueField:'value',  textField:'text', editable:false, data:[ {value:0,text:'Presente'}, {value:1,text:'Ausente'}, {value:2,text:'Justificado'}]}}">Asistencia</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-auto offset-md-2">
                    <input type="submit" class="btn btn-primary" value="Guardar">
                    <input type="button" class="btn btn-primary" value="Cancelar" onclick="location.href='gridAsistenciaCL.php'">
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<script>
var editIndex;

function onClickCellEST(index)
{
    if(editIndex!=index){
        if(endEditing()){
            $('#dgEST').datagrid('selectRow',index).datagrid('beginEdit',index);
            editIndex=index;
        }
    }
}

function endEditing()
{
    if(editIndex==undefined) return true;
    $('#dgEST').datagrid('endEdit',editIndex);
    editIndex=undefined;
    return true;
}

$('#procAsistenciaC').submit(function(e)
{
    e.preventDefault();
    endEditing();
    var gridData=$('#dgEST').datagrid('getRows');
    var formData=$(this).serializeArray();
    formData.push({name:'gridEstudiantes',value:JSON.stringify(gridData)});
    $.post('procAsistenciaC.php',formData,function(){
        location.href='gridAsistenciaCL.php';
    });
});

function llenaCursosEstudiantes()
{
    $.post('funciones/fxDatosASCL.php',
    {
        docenteSelect:$('#cboDocente').val(),
        turnoSelect:$('input[name=optTurno]:checked').val(),
        asistencia:$('#txtCodAsistencia').val()
    },function(r){
        let d=JSON.parse(r);
        $('#cboCurso').html(d.cursos);
        $('#dgEST').datagrid('loadData',d.estudiantes);
    });
}

function llenaEstudiantes()
{
    $.post('funciones/fxDatosASCL.php',
    {
        cursoAsg:$('#cboCurso').val()
    },function(r){
        $('#dgEST').datagrid('loadData',JSON.parse(r));
    });
}

$(document).ready(function(){ llenaCursosEstudiantes(); });
</script>