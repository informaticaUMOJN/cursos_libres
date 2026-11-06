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
        $mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("repMatriculadosCL");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
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
            $msCurso = "";
            $mdFechaIni = date("Y-m-d");
            $mdFechaFin = date("Y-m-d");
            $mnTipo = 0;
        }
    }
?>
<div class="container text-left">
    <div id="DivContenido">
        <div class = "row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Estudiantes matriculados</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 offset-sm-none col-md-12 offset-md-2">
                <form id="frmMatriculados" name="frmMatriculados">
                    <div class="form-group row">
                        <label for="optTipo" class="col-sm-auto col-md-2 form-label">Tipo de reporte</label>
                        <div class="col-sm-12 col-md-7">
                            <div class="radio">
                                <?php
                                    if ($mnTipo == 0)
                                        echo('<input type="radio" id="optTipo1" name="optTipo" value="1" onchange="cambiaTipo()" checked/> Cursos activos');
                                    else
                                        echo('<input type="radio" id="optTipo1" name="optTipo" value="1" onchange="cambiaTipo()" /> Cursos activos');

                                    if ($mnTipo == 1)
                                        echo('&emsp;<input type="radio" id="optTipo2" name="optTipo" value="2" onchange="cambiaTipo()" checked /> Por fecha de apertura');
                                    else
                                        echo('&emsp;<input type="radio" id="optTipo2" name="optTipo" value="2" onchange="cambiaTipo()" /> Por fecha de apertura');
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFechaIni" class="col-sm-12 col-md-2 col-form-label">Desde</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="date" class="form-control" id="dtpFechaIni" name="dtpFechaIni" value="' . $mdFechaIni . '" readonly/>');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpFechaFin" class="col-sm-12 col-md-2 col-form-label">Hasta</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="date" class="form-control" id="dtpFechaFin" name="dtpFechaFin" value="' . $mdFechaFin . '" readonly/>');
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <input type="button" id="Aceptar" name="Aceptar" value="Aceptar" class="btn btn-primary" onclick="imprimir()"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script>
function verificarFormulario() {
    var fechaIni = $('#dtpFechaIni').val();
    var fechaFin = $('#dtpFechaFin').val();
    
    if (fechaIni > fechaFin)
    {
        $.messager.alert('UMOJN', 'La fecha inicial el mayor que la final', 'warning');
        return false;
    }
}

function cambiaTipo(){
    if (document.getElementById('optTipo1').checked)
    {
        document.getElementById('dtpFechaIni').readOnly = true;
        document.getElementById('dtpFechaFin').readOnly = true;
    }
    else
    {
        document.getElementById('dtpFechaIni').readOnly = false;
        document.getElementById('dtpFechaFin').readOnly = false;
    }
}

function imprimir() {
    var tipo;
    var fechaIni = document.getElementById('dtpFechaIni').value;
    var fechaFin = document.getElementById('dtpFechaFin').value;
    
    if (document.getElementById('optTipo1').checked)
        tipo = 0;
    else
        tipo = 1;

    $.redirect("repMatriculadosCL.php", {mbTipo: tipo, mdFechaIni: fechaIni, mdFechaFin: fechaFin}, "POST", "_blank");
}
</script>