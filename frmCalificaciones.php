<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$mnRegistro = fxVerificaUsuario();
	
	if ($mnRegistro == 0)
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
        $mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("repCalificacion", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0 and $mbSupervisor == 0)
		{ ?>
            <div class="container text-center">
                <div id="DivContenido">
                    <img src="imagenes/errordeacceso.png"/>
                </div>
            </div>
		<?php }
		else
		{
		?>
    	<div class="container">
        	<div id="DivContenido">
                <div class = "row">
                    <div class="col-xs-12 col-md-11">
                        <div class="degradado"><strong>Calificaciones</strong></div>
                    </div>
                </div>

                <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                    <div class = "form-group row">
                        <label for="txnAnnoAcademico" class="col-sm-12 col-md-2 col-form-label">Año académico</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="number" style="text-align:right" class="form-control" id="txnAnnoAcademico" name="txnAnnoAcademico" value="' . date('Y') . '" />');
                            ?>
                        </div>
                    </div>

                    <div class = "form-group row">
                        <label for="txnSemestreAcademico" class="col-sm-12 col-md-2 col-form-label">Semestre académico</label>
                        <div class="col-sm-12 col-md-2">
                            <?php
                                echo('<input type="number" style="text-align:right" class="form-control" id="txnSemestreAcademico" name="txnSemestreAcademico" value="1" />');
                            ?>
                        </div>
                    </div>
                        
                    <div class="form-group row">
                        <label for="cboDocente" class="col-sm-12 col-md-2 col-form-label">Docente</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboDocente" name="cboDocente" onchange="llenaAsignaturas()">');

                                if ($mbAdministrador == 1 or $mbSupervisor == 1)
                                {
                                    $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A where ACTIVO_100 = 1 order by NOMBRE_100";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute();
                                }
                                else
                                {
                                    $msConsulta = "select DOCENTE_REL, NOMBRE_100 from UMO100A where DOCENTE_REL = ?";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute([$_SESSION["gsDocente"]]);
                                }

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["DOCENTE_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_100"]);
                                    
                                    echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                }

                                echo('</select>');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboCarrera" class="col-sm-12 col-md-2 col-form-label">Carrera</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboCarrera" name="cboCarrera" onchange="llenaAsignaturas()">');

                                $msConsulta = "select CARRERA_REL, NOMBRE_040 from UMO040A";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["CARRERA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_040"]);

                                    echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboAsignatura" class="col-sm-12 col-md-2 col-form-label">Asignatura</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboAsignatura" name="cboAsignatura">');
                                    
                                $msConsulta = "select distinct UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO070A where UMO060A.ASIGNATURA_REL = UMO070A.ASIGNATURA_REL and CARRERA_REL = ? and DOCENTE_REL = ? order by NOMBRE_060";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute([$msCarrera, $msDocente]);

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = rtrim($mFila["ASIGNATURA_REL"]);
                                    $mTexto = rtrim($mFila["NOMBRE_060"]);

                                    echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="optTurno" class="col-sm-auto col-md-2 form-label">Turno</label>
                        <div class="col-sm-12 col-md-7">
                            <div class="radio">
                                <input type="radio" id="optTurno1" name="optTurno" value="1" checked/> Diurno
                                &emsp;<input type="radio" id="optTurno2" name="optTurno" value="2" /> Matutino
                                &emsp;<input type="radio" id="optTurno3" name="optTurno" value="3" /> Vespertino
                                &emsp;<input type="radio" id="optTurno4" name="optTurno" value="4" /> Nocturno
                                &emsp;<input type="radio" id="optTurno5" name="optTurno" value="5" /> Sabatino
                                &emsp;<input type="radio" id="optTurno6" name="optTurno" value="6" /> Dominical
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <button id="print" type="button" class="btn btn-primary" >Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
<?php }} ?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
    window.onload = function() 
    {
        llenaAsignaturas();
    }

    $("#print").on("click", function() {
        var codAsignatura = $("#cboAsignatura").val();
        var codDocente = $("#cboDocente").val();
        var anno = $("#txnAnnoAcademico").val();
        var semestre = $("#txnSemestreAcademico").val();
        var turno;
        
        if (document.getElementById('optTurno1').checked)
            turno = 1;
        if (document.getElementById('optTurno2').checked)
            turno = 2;
        if (document.getElementById('optTurno3').checked)
            turno = 3;
        if (document.getElementById('optTurno4').checked)
            turno = 4;
        if (document.getElementById('optTurno5').checked)
            turno = 5;
        if (document.getElementById('optTurno6').checked)
            turno = 6;

        $.redirect("repCalificaciones.php", {msAsignatura: codAsignatura, msDocente: codDocente, mnTurno: turno, mnAnno: anno, mnSemestre: semestre}, "POST", "_blank");
    });

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
            }
        })
    }
</script>
</body>
</html>