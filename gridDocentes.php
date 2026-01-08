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
	require_once ("funciones/fxDocentes.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catDocentes", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
		
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
                                <th data-column-id="DOCENTE_REL" data-identifier="true" data-align="left" data-header-align="left" data-width="10%">Docente</th>
                                <th data-column-id="NOMBRE_100" data-order="asc" data-align="left" data-header-align="left" data-width="80%">Nombre del Docente</th>
                                <th data-column-id="ACTIVO_100" data-order="asc" data-align="center" data-header-align="center" data-width="10%">Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
							$mDatos = fxDevuelveDocentes(1);
							
                            while ($mFila = $mDatos->fetch())
							{
								echo ("<tr>");
								echo ("<td>" . $mFila["DOCENTE_REL"] . "</td>");
								echo ("<td>" . $mFila["NOMBRE_100"] . "</td>");
								echo ("<td>" . $mFila["ACTIVO_100"] . "</td>");
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
                $.redirect("catDocentes.php", "POST");
			});
      			
            $("#edit").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codDocente = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("catDocentes.php", {UMOJN: codDocente}, "POST");
                }
		    });
			  
			$("#remove").on("click", function() {
                if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
                {
                    var codDocente = $.trim($("#grid").bootgrid("getSelectedRows"));
                    $.redirect("gridDocentes.php", {UMOJN: codDocente}, "POST");
                }
			});
        });
    </script>
</body>
</html>
