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
?>

	<div class="container">
		<div id="DivContenido">
			<div id="lateral">
				<?php
					echo('<label id="imprimir" data-toggle="tooltip" data-placement="top" title="Imprimir QR"><img src="imagenes/btnLateralImprimir.png" height="80%" style="cursor:pointer" /></label>');
				?>
			</div>

			<div class="row">
				<div class="col-md-12">
					<?php
						echo('<button id="print" type="button" class="btn btn-primary">Imprimir QR</button>');
					?>
					
					<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="true" data-row-select="true" data-keep-selection="true">
						<thead>
							<tr>
								<th data-column-id="DIPLOMA_REL" data-order="desc" data-identifier="true" data-align="left" data-width="15%">Diploma</th>
								<th data-column-id="FECHA_003" data-align="left" data-header-align="left" data-width="15%">Fecha</th>
								<th data-column-id="ESTUDIO_003" data-align="left" data-header-align="left" data-width="35%">Estudio realizado</th>
								<th data-column-id="NOMBRE_003" data-align="left" data-header-align="left" data-width="35%">Nombre del estudiante</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$mDatos = fxDevuelveDiploma(1);
							while ($mFila = $mDatos->fetch())
							{
								echo ("<tr>");
								echo ("<td>" . $mFila["DIPLOMA_REL"] . "</td>");
								echo ("<td>" . $mFila["FECHA_003"] . " " . "</td>");
								echo ("<td>" . $mFila["ESTUDIO_003"] . "</td>");
								echo ("<td>" . $mFila["NOMBRE_003"] . "</td>");
								echo ("</tr>");
							}
						?>
						</tbody>
					</table>
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

		$("#imprimir").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codDiploma = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repGenerarQR.php", {UMOJN: codDiploma}, "POST", "_blank");
			}
		});

		$("#print").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codDiploma = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repGenerarQR.php", {UMOJN: codDiploma}, "POST", "_blank");
			}
		});
	});
</script>
</body>
</html>