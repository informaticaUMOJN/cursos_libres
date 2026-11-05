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
		$mbPermisoUsuario = fxPermisoUsuario("repAsistencia", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
                        <div class="degradado"><strong>Formato para asistencias</strong></div>
                    </div>
                </div>

                <div class="col-sm-12 offset-sm-none col-md-12 offset-md-1">
                    <div class = "form-group row">
                        <label for="dtpFecha" class="col-sm-12 col-md-2 form-label">Fecha</label>
                        <div class="col-sm-12 col-md-2">
                        <?php echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . date('Y-m-d') . '" />'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="cboCurso" class="col-sm-12 col-md-2 col-form-label">Curso</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                echo('<select class="form-control" id="cboCurso" name="cboCurso">');

                                $msConsulta = "select CURSOS_REL, NOMBRE_190, COHORTE_190, GRUPO_190 from UMO190A where ESTADO_190 = 1 order by NOMBRE_190";
                                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                $mDatos->execute();

                                while ($mFila = $mDatos->fetch())
                                {
                                    $mValor = trim($mFila["CURSOS_REL"]);
                                    $mTexto = trim($mFila["NOMBRE_190"]) . ' (' . trim($mFila["COHORTE_190"]) . '/G' . trim($mFila["GRUPO_190"]) . ')';

                                    echo("<option value='" . $mValor . "'>" . $mTexto . "</option>");
                                }
                                echo("</select>");
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-auto offset-sm-none col-md-8 offset-md-2">
                            <button id="print" type="button" class="btn btn-primary">Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
<?php }} ?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
    $("#print").on("click", function() {
        var fecha = $("#dtpFecha").val();
        var codCurso = $("#cboCurso").val();

        $.redirect("repAsistencia.php", {msCurso: codCurso, msFecha: fecha}, "POST", "_blank");
    });
</script>
</body>
</html>