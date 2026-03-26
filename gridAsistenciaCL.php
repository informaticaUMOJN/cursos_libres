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
	require_once ("funciones/fxAsistenciasCl.php");
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
	{$mbAdministrador = fxVerificaAdministrador();
		$mbSupervisor = fxVerificaSupervisor();
		$mbPermisoUsuario = fxPermisoUsuario("procAsistencia", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
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
                fxBorrarAsistencia($_POST["UMOJN"]);
				fxAgregarBitacora($_SESSION["gsUsuario"], "UMO320A", $_POST["UMOJN"], "", "Borrar", "");
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
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
							<thead>
								<tr>
									<th data-column-id="ASISTENCIA_REL" data-identifier="true" data-width="15%" data-align="left">Asistencia</th>
									<th data-column-id="NOMBRE_100" data-width="20%" data-align="left">Docente</th>
									<th data-column-id="NOMBRE_190" data-width="15%" data-align="left">Curso</th>
									<th data-column-id="FECHA_320" data-width="15%" data-align="left">Fecha</th>
									<th data-column-id="TURNO_320" data-width="15%" data-align="left">Turno</th>
								</tr>
							</thead>
							<tbody>
							<?php
								if ($mbAdministrador == 1 or $mbSupervisor == 1)
									$mDatos = fxDevuelveAsistencia(1, "", "");
								else
									$mDatos = fxDevuelveAsistencia(1, $msDocente, "");
								
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["ASISTENCIACL_REL"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_100"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_190"] . "</td>");
									$fecha = date_create_from_format('Y-m-d', $mFila["FECHA_320"]);
									echo ("<td>" . date_format($fecha, 'd-m-Y') . "</td>");
									switch(intval($mFila["TURNO_320"]))
									{
										case 1: echo ("<td>Diurno</td>"); break;
										case 2: echo ("<td>Matutino</td>"); break;
										case 3: echo ("<td>Vespertino</td>"); break;
										case 4: echo ("<td>Nocturno</td>"); break;
										case 5: echo ("<td>Sabatino</td>"); break;
										case 6: echo ("<td>Dominical</td>"); break;
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
                    }, rowCount: [-1, 10, 50, 75]
                });
            }  init(); 
			$("#append").on("click", function() {
                $.redirect("procAsistenciaC.php", "POST");});
			$("#remove").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                { var codAsistencia = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridAsistenciaCL.php", {UMOJN: codAsistencia}, "POST");
                }
			});  $("#edit").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAsistencia = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("procAsistenciaC.php", {UMOJN: codAsistencia}, "POST");
                }
            });
			$("#agregar").on("click", function() {
                $.redirect("procAsistenciaC.php", "POST");
			});
			$("#borrar").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAsistencia = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridAsistenciaCL.php", {UMOJN: codAsistencia}, "POST");
                }
			});
            $("#modificar").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codAsistencia = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("procAsistenciaC.php", {UMOJN: codAsistencia}, "POST");
                } });  });</script></body></html>