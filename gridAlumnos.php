<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxAlumnos.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catAlumnos", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
						if ($mbAgregar == 1 or $mbAdministrador == 1)
							echo('<label id="agregar" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="agregarDis" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregarDis.png" height="80%" style="cursor:default" /></label>');
							
						if ($mbModificar == 1 or $mbAdministrador == 1)
							echo('<label id="modificar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');
						
						//echo('<label id="matricular" data-toggle="tooltip" data-placement="top" title="Matricular"><img src="imagenes/btnLateralMatricular.png" height="80%" style="cursor:pointer" /></label>');
					?>
				</div>

				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>');
							
						//	echo('<button id="matricula" type="button" class="btn btn-primary">Matricular</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
							<thead>
								<tr>
									<th data-column-id="ALUMNO_REL" data-order="desc" data-identifier="true">Estudiante</th>
									<th data-column-id="NOMBRES_200" data-header-align="left" data-width="53%">Nombre completo</th>
									<th data-column-id="APELLIDOS_200" data-align="left" data-header-align="left" data-width="15%">Celular</th>
										</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveAlumnos(1);
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["ALUMNO_REL"] . "</td>");
									$msNombre = trim($mFila["NOMBRES_200"]) . ", " . trim($mFila["APELLIDOS_200"]);
									echo ("<td>" . $msNombre . " " . "</td>");
									echo ("<td>" . $mFila["CELULAR_200"] . "</td>");
								//echo ("<td>" . $mFila["CURSOS_REL"] . "</td>");
								}
							}
							?>
							</tbody>
						</table>
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

		$("#append").on("click", function() {
			$.redirect("catAlumnos.php", "POST");
		});

		$("#agregar").on("click", function() {
			$.redirect("catAlumnos.php", "POST");
		});
			
		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codEstudiante = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("catAlumnos.php", {UMOJN: codEstudiante}, "POST");
			}
		});

		$("#modificar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codEstudiante = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("catAlumnos.php", {UMOJN: codEstudiante}, "POST");
			}
		});

	/*	$("#matricula").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msEstudiante = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procMatricula.php", {mAccion: 1, mEstudiante: msEstudiante}, "POST");
			}
		});

		$("#matricular").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msEstudiante = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procMatricula.php", {mAccion: 1, mEstudiante: msEstudiante}, "POST");
			}
		});
	*/
	});
</script>
</body>
</html>