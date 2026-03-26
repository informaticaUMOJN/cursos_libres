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
	$Registro = fxVerificaUsuario();
	
	if ($Registro == 0)
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
		$mbPermisoUsuario = fxPermisoUsuario("hrrUsuarios", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
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
				<div id="lateral">
					<?php
						if ($mbModificar == 1 or $mbAdministrador == 1)
							echo('<label id="modificar" data-toggle="tooltip" data-placement="top" title="Cambiar clave"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Cambiar clave"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');
					?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbModificar == 1 or $mbAdministrador == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Cambiar clave</button>');
							else
								echo('<button id="edit" type="button" class="btn btn-primary" disabled>Cambiar clave</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
							<thead>
								<tr>
									<th data-column-id="USUARIO_REL" data-identifier="true" data-align="left" data-width="15%">Usuario</th>
									<th data-column-id="NOMBRE_002" data-align="left" data-header-align="left" data-width="35%">Nombre completo</th>
									<th data-column-id="SUPERVISOR_002" data-type="boolean" data-align="center" data-header-align="center" data-width="10%">Académico</th>
									<th data-column-id="ARCHIVOS_002" data-type="boolean" data-align="center" data-header-align="center" data-width="10%">Archivos</th>
									<th data-column-id="ESTUDIANTE_002" data-type="boolean" data-align="center" data-header-align="center" data-width="10%">Estudiante</th>
									<th data-column-id="ADMINISTRADOR_002" data-type="boolean" data-align="center" data-header-align="center" data-width="10%">Administrador</th>
									<th data-column-id="ACTIVO_002" data-visible="boolean" data-align="center" data-header-align="center" data-width="10%">Activo</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveUsuario(1);
								
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["USUARIO_REL"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_002"] . "</td>");
									echo ("<td>" . $mFila["SUPERVISOR_002"] . "</td>");
									echo ("<td>" . $mFila["ARCHIVOS_002"] . "</td>");
									echo ("<td>" . $mFila["ESTUDIANTE_002"] . "</td>");
									echo ("<td>" . $mFila["ADMINISTRADOR_002"] . "</td>");
									echo ("<td>" . $mFila["ACTIVO_002"] . "</td>");
									echo ("</tr>");
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
            </div>
    	</div>
	</div>
<?php }?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.fa.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
	$(function() {
		$(window).scroll(function() {
			var scroll = $(window).scrollTop();
			if (scroll >= 100) {
			$("#lateral").addClass("entra");
			} else {
			$("#lateral").removeClass("entra");
			}
		});
	});

	$(function() {
		function init() {
			$("#grid").bootgrid({
				formatters: {
					"link": function(column, row) {
						return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
					}
				},
				rowCount: [-1, 10, 50, 75]
			});
		}

		init();

		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codUsuario = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("hrrCambiaClave.php", {UMO: codUsuario}, "POST");
			}
		});

		$("#modificar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codUsuario = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("hrrCambiaClave.php", {UMO: codUsuario}, "POST");
			}
		});
	});
    </script>
</body>
</html>
