<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1) {
    echo('<meta http-equiv="Refresh" content="0;url=index.php">');
    exit('');
}

include("masterApp.php");
require_once("funciones/fxGeneral.php");
require_once("funciones/fxUsuarios.php");
require_once("funciones/fxModulos.php");

$Registro = fxVerificaUsuario();

if ($Registro == 0) {
?>
<div class="container text-center">
    <div id="DivContenido">
        <img src="imagenes/errordeacceso.png"/>
    </div>
</div>
<?php
} else {
    $mbAdministrador = fxVerificaAdministrador();
    $mbPermisoUsuario = fxPermisoUsuario("catModulos.php", $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);

    if ($mbAdministrador == 0 and $mbPermisoUsuario == 0) { ?>
        <div class="container text-center">
            <div id="DivContenido">
                <img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
    <?php
    } else {

        // Borrar módulo si se envió
        if (isset($_POST["UMOJN"])) {
            fxBorrarModulo($_POST["UMOJN"]);
            fxAgregarBitacora($_SESSION["gsUsuario"], "UMO190A", $_POST["UMOJN"], "", "Borrar", "");
        }
?>
<div class="container">
    <div id="DivContenido">
        <div id="lateral">
            <?php
            echo ($mbAgregar == 1 or $mbAdministrador == 1)
                ? '<label id="agregar" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregar.png" height="80%" style="cursor:pointer" /></label>'
                : '<label id="agregarDis" data-toggle="tooltip" data-placement="top" title="Agregar"><img src="imagenes/btnLateralAgregarDis.png" height="80%" style="cursor:default" /></label>';

            echo ($mbModificar == 1 or $mbAdministrador == 1)
                ? '<label id="modificar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>'
                : '<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>';

            echo ($mbBorrar == 1 or $mbAdministrador == 1)
                ? '<label id="borrar" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrar.png" height="80%" style="cursor:pointer" /></label>'
                : '<label id="borrarDis" data-toggle="tooltip" data-placement="top" title="Borrar"><img src="imagenes/btnLateralBorrarDis.png" height="80%" style="cursor:default" /></label>';
            ?>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php
                echo ($mbAgregar == 1 or $mbAdministrador == 1)
                    ? '<button id="append" type="button" class="btn btn-primary">Agregar</button>'
                    : '<button id="append" type="button" class="btn btn-primary" disabled>Agregar</button>';

                echo ($mbModificar == 1 or $mbAdministrador == 1)
                    ? '<button id="edit" type="button" class="btn btn-primary">Editar</button>'
                    : '<button id="edit" type="button" class="btn btn-primary" disabled>Editar</button>';

                echo ($mbBorrar == 1 or $mbAdministrador == 1)
                    ? '<button id="remove" type="button" class="btn btn-primary">Borrar</button>'
                    : '<button id="remove" type="button" class="btn btn-primary" disabled>Borrar</button>';
                ?>

                <table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
                    <thead>
                        <tr>
                            <th data-column-id="MODULO_REL" data-identifier="true" data-width="23%" data-align="left">Modulo</th>
                            <th data-column-id="NOMBRE_190" data-width="30%" data-align="left">Curso</th>
                            <th data-column-id="NOMBRE_280" data-width="40%" data-align="left">Nombre</th>
                       </tr>
                    </thead>
                    <tbody>
                    <?php
                    $mDatos = fxDevuelveModulo(1);

                    while ($mFila = $mDatos->fetch()) {
                        echo "<tr>";
                        echo "<td>" . $mFila["MODULO_REL"] . "</td>";
                        echo "<td>" . $mFila["NOMBRE_190"] . "</td>";
                        echo "<td>" . $mFila["NOMBRE_280"] . "</td>";
                       echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
    }
}
?>
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
			$.redirect("catModulos.php", "POST");
		});

		$("#remove").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCursos = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("gridModulos.php", {UMOJN: codCursos}, "POST");
			}
		});
			
		$("#edit").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCursos = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("catModulos.php", {UMOJN: codCursos}, "POST");
			}
		});

		$("#agregar").on("click", function() {
			$.redirect("catModulos.php", "POST");
		});

		$("#borrar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCursos = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("gridModulos.php", {UMOJN: codCursos}, "POST");
			}
		});
			
		$("#modificar").on("click", function() {
			if ($.trim($("#grid").bootgrid("getSelectedRows")) != "")
			{
				var codCursos = $.trim($("#grid").bootgrid("getSelectedRows"));
				$.redirect("catModulos.php", {UMOJN: codCursos}, "POST");
			}
		});
	});
</script>
</body>
</html>