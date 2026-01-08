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
	require_once ("funciones/fxSyllabus.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("procSyllabus", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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

		if (isset($_POST["UMOJN"]))
		{
			$msCodigo = $_POST["UMOJN"];
			fxBorrarSyllabus($msCodigo);
			$msBitacora = $msCodigo;
            fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO070A", $msCodigo, "", "Borrar", $msBitacora);
		}
		?>
    	<div class="container">
        	<div id="DivContenido">
        	<div class="row">
            	<div class="col-md-12">
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
							
							if ($mbBorrar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>');
							else
								echo('<label id="borrarDis" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrarDis.png" height="80%" style="cursor:default" /></label>');
								
							echo('<label id="imprimir" data-toggle="tooltip" data-placement="top" title="Imprimir"><img src="imagenes/btnLateralImprimir.png" height="80%" style="cursor:pointer" /></label>');
						?>
					</div>
					
                    <?php
						if ($mbAgregar == 1 or $mbAdministrador == 1)
							echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
						else
							echo('<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>');
							
						if ($mbModificar == 1 or $mbAdministrador == 1)
							echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
						else
							echo('<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>');
							
						if ($mbBorrar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<button id="remove" type="button" class="btn btn-primary">Borrar</button>');
						else
							echo('<button id="remove" type="button" class="btn btn-primary" disabled>Borrar</button>');	
							
						echo('<button id="print" type="button" class="btn btn-primary">Imprimir</button>');
					?>
                    
                    <table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
                    	<thead>
                            <tr>
                                <th data-column-id="SYLLABUS_REL" data-order="desc" data-identifier="true" data-align="left" data-header-align="left" data-width="12%">Syllabus</th>
                                <th data-column-id="NOMBRE_100" data-align="left" data-header-align="left" data-width="16%">Docente</th>
                                <th data-column-id="NOMBRE_060" data-align="left" data-header-align="left" data-width="20%">Asignatura</th>
								<th data-column-id="FECHA_070" data-align="center" data-header-align="center" data-width="12%">Fecha</th>
								<th data-column-id="ANNO_070" data-align="center" data-header-align="center" data-width="10%">Año</th>
								<th data-column-id="TURNO_070" data-align="center" data-header-align="center" data-width="10%">Turno</th>
								<th data-column-id="GRUPO_070" data-align="center" data-header-align="center" data-width="10%">Grupo</th>
								<th data-column-id="ACTIVO_070" data-align="center" data-header-align="center" data-width="10%">Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
							$mDatos = fxDevuelveSyllabus(1);
							
                            while ($mFila = $mDatos->fetch())
							{
								echo ("<tr>");
								echo ("<td>" . $mFila["SYLLABUS_REL"] . "</td>");
								echo ("<td>" . $mFila["NOMBRE_100"] . "</td>");
								echo ("<td>" . $mFila["NOMBRE_060"] . "</td>");
								$fecha = date_create_from_format('Y-m-d', $mFila["FECHA_070"]);
								echo ("<td>" . date_format($fecha, 'd-m-Y') . "</td>");
								echo ("<td>" . $mFila["ANNO_070"] . "</td>");
								switch(intval($mFila["TURNO_070"])){
									case 0:
										echo ("<td>Regular</td>");
										break;
									case 1:
										echo ("<td>Sabatino</td>");
										break;
									default:
										echo ("<td>Dominical</td>");
								}
								echo ("<td>" . $mFila["GRUPO_070"] . "</td>");
								if ($mFila["ACTIVO_070"] == 0)
									echo ("<td></td>");
								else
									echo ("<td>X</td>");
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
			$.redirect("procSyllabus.php", "POST");
		});

		$("#agregar").on("click", function() {
			$.redirect("procSyllabus.php", "POST");
		});
			
		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codSyllabus = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procSyllabus.php", {UMOJN: codSyllabus}, "POST");
			}
		});

		$("#modificar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codSyllabus = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("procSyllabus.php", {UMOJN: codSyllabus}, "POST");
			}
		});
			
		$("#remove").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codSyllabus = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("gridSyllabus.php", {UMOJN: codSyllabus}, "POST");
			}
		});

		$("#borrar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codSyllabus = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("gridSyllabus.php", {UMOJN: codSyllabus}, "POST");
			}
		});

		$("#print").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codSyllabus = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repSyllabus.php", {UMOJN: codSyllabus}, "POST", "_blank");
			}
		});

		$("#imprimir").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codSyllabus = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("repSyllabus.php", {UMOJN: codSyllabus}, "POST", "_blank");
			}
		});
	});
</script>
</body>
</html>