<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
			<img src="imagenes/errordeacceso.png"/>
		</div>
	</div>
<?php 
	}
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("repLibroActas");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{?>
        <div class="container text-center">
        	<div id="DivContenido">
				<img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
		{
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Libros de actas</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-xs-12 offset-sm-none col-md-10 offset-md-1">
					<form id="frmLibroActas" name="frmLibroActas" method="post">
						<div class="form-group row">
							<label for="cboLibro" class="col-sm-12 col-md-2 col-form-label">Libro</label>
							<div class="col-sm-12 col-md-7">
								<?php
									echo('<select class="form-control" id="cboLibro" name="cboLibro">');

									$msConsulta = "SELECT LIBRO_REL, concat(DESC_330, ' (Tomo ', TOMO_330, ')') as DESC_330  FROM UMO330A";
									$mDatos = $m_cnx_MySQL->prepare($msConsulta);
									$mDatos->execute();
									while ($mFila = $mDatos->fetch())
									{
										$msValor = rtrim($mFila["LIBRO_REL"]);
										$msTexto = rtrim($mFila["DESC_330"]);
										echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
									}
									echo('</select>');
								?>
							</div>
						</div>
						<div class = "row">
							<div class="col-auto offset-sm-none col-md-12 offset-md-2">
								<input type="submit" id="Aceptar" name="Aceptar" value="Aceptar" class="btn btn-primary" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php }} ?>
</body>
</html>
<script>
	$("#Aceptar").on("click", function() {
        var codLibro = $("#cboLibro").val();
        $.redirect("repLibroActas.php", {UMOJN: codLibro}, "POST", "_blank");
    });
</script>