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
	require_once ("funciones/fxCalificaciones.php");
	$mnRegistro = fxVerificaUsuario();
	$msDocente = $_SESSION["gsDocente"];
	
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
		$mbPermisoUsuario = fxPermisoUsuario("procCalificacion", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
		if (isset($_POST["UMOJN"]))
            {
                fxBorrarCalificacion($_POST["UMOJN"]);
				fxAgregarBitacora($_SESSION["gsUsuario"], "UMO160A", $_POST["UMOJN"], "", "Borrar", "");
            }
		?>
    	<div class="container">
        	<div id="DivContenido">
				<div id="lateral">
					<?php
						if ($mbAgregar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<label id="agregar" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="agregarDis" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregarDis.png" height="80%" style="cursor:default" /></label>');
							
						if ($mbModificar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<label id="modificar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');
						
						if ($mbBorrar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
							echo('<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="borrarDis" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrarDis.png" height="80%" style="cursor:default" /></label>');
					?>
				</div>

				<div class="row">
					<div class="col-md-12">
						<?php
							if ($mbAgregar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<button id="append" type="button" class="btn btn-primary">Agregar</button>');
							else
								echo('<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>');
								
							if ($mbModificar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<button id="edit" type="button" class="btn btn-primary">Editar</button>');
							else
								echo('<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>');
								
							if ($mbBorrar == 1 or $mbAdministrador == 1 or $mbSupervisor == 1)
								echo('<button id="remove" type="button" class="btn btn-primary">Borrar</button>');
							else
								echo('<button id="remove" type="button" class="btn btn-primary" disabled>Borrar</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true" style="font-size:small">
							<thead>
								<tr>
									<th data-column-id="CALIFICACION_REL" data-identifier="true" data-width="11%" data-align="left">Calificación</th>
									<th data-column-id="NOMBRE_100" data-width="20%" data-align="left">Docente</th>
									<th data-column-id="NOMBRE_060" data-width="25%" data-align="left">Asignatura</th>
									<th data-column-id="TURNO_060" data-width="10%" data-align="left">Turno</th>
									<th data-column-id="ANNO_160" data-width="8%" data-align="center" data-header-align="center">Año</th>
									<th data-column-id="SEMESTRE_160" data-width="10%" data-align="center" data-header-align="center">Semestre</th>
									<th data-column-id="PARCIAL_160" data-width="10%" data-align="left" data-header-align="left">Evaluación</th>
								</tr>
							</thead>
							<tbody>
							<?php
								if ($mbAdministrador == 1 or $mbSupervisor == 1)
									$mDatos = fxDevuelveCalificacion(1, "", "");
								else
									$mDatos = fxDevuelveCalificacion(1, $msDocente, "");
								
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["CALIFICACION_REL"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_100"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_060"] . "</td>");

									switch(intval($mFila["TURNO_160"]))
									{
										case 1:
											$msTurno = 'Diurno';
											break;
                                        case 2:
											$msTurno = 'Matutino';
											break;
                                        case 3:
											$msTurno = 'Vespertino';
											break;
                                        case 4:
											$msTurno = 'Nocturno';
											break;
                                        case 5:
											$msTurno = 'Sabatino';
											break;
                                        case 6:
											$msTurno = 'Dominical';
											break;
									}

									echo ("<td>" . $msTurno . "</td>");
									echo ("<td>" . $mFila["ANNO_160"] . "</td>");
									echo ("<td>" . $mFila["SEMESTRE_160"] . "</td>");

									switch(intval($mFila["PARCIAL_160"])){
										case 0:
											$mParcial = "1er. parcial";
											break;
										case 1:
											$mParcial = "2do. parcial";
											break;
										case 2:
											$mParcial = "3er. parcial";
											break;
										case 3:
											$mParcial = "Ex. Extraordinario";
											break;
										case 4:
											$mParcial = "Curso intersemestral";
											break;
										case 5:
											$mParcial = "Ex. Suficiencia";
											break;
										case 6:
											$mParcial = "Convalidación";
											break;
									}
									echo ("<td>" . $mParcial . "</td>");
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
                $.redirect("procCalificacion.php", "POST");
			});
  
			$("#remove").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCalificacion = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridCalificaciones.php", {UMOJN: codCalificacion}, "POST");
                }
			});
      			
            $("#edit").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCalificacion = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("procCalificacion.php", {UMOJN: codCalificacion}, "POST");
                }
            });

			$("#agregar").on("click", function() {
                $.redirect("procCalificacion.php", "POST");
			});
  
			$("#borrar").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCalificacion = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridCalificaciones.php", {UMOJN: codCalificacion}, "POST");
                }
			});
      			
            $("#modificar").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codCalificacion = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("procCalificacion.php", {UMOJN: codCalificacion}, "POST");
                }
            });
        });
    </script>
</body>
</html>