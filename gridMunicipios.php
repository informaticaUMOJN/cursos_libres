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
	require_once ("funciones/fxMunicipios.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catMunicipio", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
                fxBorrarCarrera($_POST["UMOJN"]);
				fxAgregarBitacora($_SESSION["gsUsuario"], "UMO120A", $_POST["UMOJN"], "", "Borrar", "");
            }
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
						
						if ($mbBorrar == 1 or $mbAdministrador == 1)
							echo('<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>');
						else
							echo('<label id="borrarDis" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrarDis.png" height="80%" style="cursor:default" /></label>');
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
								
							if ($mbBorrar == 1 or $mbAdministrador == 1)
								echo('<button id="remove" type="button" class="btn btn-primary">Borrar</button>');
							else
								echo('<button id="remove" type="button" class="btn btn-primary" disabled>Borrar</button>');
						?>
						
						<table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
							<thead>
								<tr>
									<th data-column-id="MUNICIPIO_REL" data-identifier="true" data-align="left">Municipio</th>
									<th data-column-id="NOMBRE_110" data-align="left">Departamento</th>
									<th data-column-id="NOMBRE_120" data-align="left">Nombre</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mDatos = fxDevuelveMunicipio(1);
								
								while ($mFila = $mDatos->fetch())
								{
									echo ("<tr>");
									echo ("<td>" . $mFila["MUNICIPIO_REL"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_110"] . "</td>");
									echo ("<td>" . $mFila["NOMBRE_120"] . "</td>");
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
                $.redirect("catMunicipio.php", "POST");
			});
  
			$("#remove").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codDepartamento = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridMunicipios.php", {UMOJN: codDepartamento}, "POST");
                }
			});
      			
            $("#edit").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codDepartamento = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("catMunicipios.php", {UMOJN: codDepartamento}, "POST");
                }
            });

			$("#agregar").on("click", function() {
                $.redirect("catMunicipios.php", "POST");
			});
  
			$("#borrar").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codDepartamento = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridMunicipios.php", {UMOJN: codDepartamento}, "POST");
                }
			});
      			
            $("#modificar").on("click", function() {
				if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codDepartamento = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("catMunicipios.php", {UMOJN: codDepartamento}, "POST");
                }
            });
        });
    </script>
</body>
</html>