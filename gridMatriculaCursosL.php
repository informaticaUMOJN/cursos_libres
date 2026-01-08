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
	require_once ("funciones/fxMatriculaCursos.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("procMatCursosLibres", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
						echo('<label id="editar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
						echo('<label id="editarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');

						echo('<label id="imprimir" data-toggle="tooltip" data-placement="top" title="Imprimir"><img src="imagenes/btnLateralImprimir.png" height="80%" style="cursor:pointer" /></label>');
					?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="appendDis" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="editDis" type="button" class="btn btn-primary" disabled>Editar</button>');

							echo('<button id="print" type="button" class="btn btn-primary">Hoja de Matrícula</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true" style="font-size:small">
							<thead>
								<tr>
									<th data-column-id="MATCURSO_REL" data-order="desc" data-identifier="true" data-align="left" data-header-align="left" data-width="18%">Matrícula</th>
									<th data-column-id="ESTUDIANTE" data-order="desc" data-align="left" data-header-align="left" data-width="30%">Nombre del Estudiante</th>
									<th data-column-id="NOMBRE_200" data-align="left" data-header-align="left" data-width="20%">Curso libre</th>
									<th data-column-id="FECHA_210" data-align="center" data-header-align="center" data-width="15%">Fecha</th>
									<th data-column-id="ESTADO_210" data-align="center" data-header-align="center" data-width="15%">Estado</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveMatriculaCurso(1);

								while ($mFila = $mDatos->fetch())
								{
									$msNombre = trim($mFila["NOMBRES_200"]) . ", " . trim($mFila["APELLIDOS_200"]);
									//echo ("<td>" . $msNombre . " " . "</td>");
									echo ("<tr>");
									echo ("<td>" . $mFila["MATCURSO_REL"] . "</td>");
									echo ("<td>" . $msNombre . "</td>");
									echo ("<td>" . $mFila["NOMBRE_190"] . "</td>");
									$fecha = date_create_from_format('Y-m-d', $mFila["FECHA_210"]);
									echo ("<td>" . date_format($fecha, 'd-m-Y') . "</td>");
									if ($mFila["ESTADO_210"]==0)
										echo ("<td>Activo</td>");
									else{
										if ($mFila["ESTADO_210"]==1)
											echo ("<td>Inactivo</td>");
										else
											echo ("<td>Pre-matrícula</td>");
									}
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
<?php }?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/js/moderniz.2.8.1.js"></script>
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
			$.redirect("procMatCursosLibres.php", {mAccion: 0, mEstudiante: ''}, "POST");
		});

		$("#agregar").on("click", function() {
			$.redirect("procMatCursosLibres.php", {mAccion: 0, mEstudiante: ''}, "POST");
		});
		
		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procMatCursosLibres.php", {mAccion: 0, mCodigo: msCodigo}, "POST");
			}
		});

		$("#editar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procMatCursosLibres.php", {mAccion: 0, mCodigo: msCodigo}, "POST");
			}
		});

		$("#print").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repHojaMatCursosL.php", {UMOJN: msCodigo}, "POST", "_blank");
			}
		});

		$("#imprimir").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var msCodigo = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repHojaMatCursosL.php", {UMOJN: msCodigo}, "POST", "_blank");
			}
		});
	});
</script>
</body>
</html>