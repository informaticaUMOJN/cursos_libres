<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxDiplomas.php");
	require_once ("funciones/fxCursoslibres.php");
?>

	<div class="container">
		<div id="DivContenido">
			<div id="lateral">
				<?php
					echo('<label id="imprimirQR" data-toggle="tooltip" data-placement="top" title="Imprimir QR"><img src="imagenes/btnLateralQR.png" height="80%" style="cursor:pointer" /></label>');
					echo('<label id="imprimirCT" data-toggle="tooltip" data-placement="top" title="Datos del certificado"><img src="imagenes/btnLateralImprimir.png" height="80%" style="cursor:pointer" /></label>');
					echo('<label id="imprimirLST" data-toggle="tooltip" data-placement="top" title="Lista de certificados"><img src="imagenes/btnLateralMatricular.png" height="80%" style="cursor:pointer" /></label>');
				?>
			</div>

			<div class="row">
				<div class="col-md-12">
					<?php
						echo('<button id="printQR" type="button" class="btn btn-primary">Imprimir QR</button>');
						echo('<button id="printCT" type="button" class="btn btn-primary">Datos del certificado</button>');
						echo('<button id="printLST" type="button" class="btn btn-primary">Lista de certificados</button>');
					?>
					
					<div class="table-responsive">
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
							<thead>
								<tr>
									<th data-column-id="CURSOS_REL" data-identifier="true" data-width="10%" data-align="left">Curso</th>
									<th data-column-id="TIPOC_190" data-align="left" data-width="10%">Tipo</th>
									<th data-column-id="NOMBRE_190" data-align="left" data-width="30%">Nombre</th>
									<th data-column-id="NOMBRE_DOCENTE" data-align="left" data-width="30%">Docente</th>
									<th data-column-id="TURNO_190" data-align="left" data-width="10%">Turno</th>
									<th data-column-id="ESTADO_190" data-align="left" data-width="10%">Estado</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveCursosLibres(1);
								
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["CURSOS_REL"] . "</td>");
									echo ("<td>" . $mFila["TIPOC_190"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_190"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_DOCENTE"] . "</td>");
									echo ("<td>" . $mFila["TURNO_190"] . "</td>");
									
									echo ("<td>" . $mFila["ESTADO_190"] . "</td>");
									echo ("</tr>");
								}
							?> 
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

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

		$("#imprimirCT").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCurso = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("imgDatosCertificado.php", {UMOJN: codCurso}, "POST", "_blank");
			}
		});

		$("#imprimirQR").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCurso = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repGenerarQR.php", {UMOJN: codCurso}, "POST", "_blank");
			}
		});

		$("#imprimirLST").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCurso = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repCertificados.php", {UMOJN: codCurso}, "POST", "_blank");
			}
		});

		$("#printCT").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCurso = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("imgDatosCertificado.php", {UMOJN: codCurso}, "POST", "_blank");
			}
		});

		$("#printQR").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCurso = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repGenerarQR.php", {UMOJN: codCurso}, "POST", "_blank");
			}
		});

		$("#printLST").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCurso = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repCertificados.php", {UMOJN: codCurso}, "POST", "_blank");
			}
		});
	});
</script>
</body>
</html>