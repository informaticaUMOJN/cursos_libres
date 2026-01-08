<?php
    session_start();
    if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
    {
        echo('<meta http-equiv="Refresh" content="0;url=index.php">');
        exit('');
    }
    include("masterApp.php");
    require_once("funciones/fxGeneral.php");
    require_once("funciones/fxUsuarios.php");
    require_once("funciones/fxPagosCL.php");
    $Registro = fxVerificaUsuario();
    if ($Registro == 0)
    {
?>
<div class="container text-center">
    <div id="DivContenido">
        <img src="imagenes/errordeacceso.png"/>
    </div>
</div>
<?php
}   else 
    {
        $mbAdministrador = fxVerificaAdministrador();
        $mbPermisoUsuario = fxPermisoUsuario("procPagosCL", $mbModificar,$mbModificar2, $mbAnular);
        if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
        {
            ?>
            <div class="container text-center">
                <div id="DivContenido">
                    <img src="imagenes/errordeacceso.png"/>
                </div>
            </div>
            <?php
            }
            else
            {
                if (isset($_POST["UMOJN"])) {
                    $codPagos = $_POST["UMOJN"];
                    fxAnularPagos($codPagos); 
                    fxAgregarBitacora($_SESSION["gsUsuario"], "UMO140A", $codPagos, "", "Anular", ""); 
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
                    ?>    
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="easyui-tabs" style="width:100%; height:auto;">
                            <div title="Alumnos de Cursos Libres" style="padding:10px">
                            <?php
                                echo '<br>';
                                if ($mbModificar == 1 or $mbAdministrador == 1)
                                echo('<button id="edit" type="button" class="btn btn-primary">Agregar Pagos</button>');
                                else
                                echo('<button id="edit" type="button" class="btn btn-primary" disabled>Agregar Pagos</button>');
                            ?>
                            <table id="dgPG" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
                                <thead>
                                    <tr>
                                        <th data-column-id="MATCURSO_REL" data-identifier="true" data-align="left">Matricula</th>
                                        <th data-column-id="ALUMNO_REL" data-header-align="left" data-width="30%">Estudiante</th>
                                        <th data-column-id="NOMBRE_190" data-header-align="left" data-width="36%">Carrera</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $mDatos = fxDevuelveEncabezadoP(1); //GENERAR PAGOS GRID - 1
                                    while ($mFila = $mDatos->fetch())
                                {
                                    $msNombre = trim($mFila["NOMBRES_200"]);
                                    $msApellido = trim($mFila["APELLIDOS_200"]);
                                    $msNombreCompleto = $msNombre;
                                    if ($msApellido != "") {
                                        $msNombreCompleto .= " " . $msApellido;
                                    }
                                    echo "<tr>";
                                    echo "<td>" . $mFila["MATCURSO_REL"] . "</td>";
                                    echo "<td>" . htmlspecialchars($msNombreCompleto, ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . $mFila["NOMBRE_190"] . "</td>";
                                    echo "</tr>";
                                }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div title="Pagos realizados" style="padding:10px">
                            <?php 
                            echo '<br>';
                            if ($mbModificar2 == 1 or $mbAdministrador == 1)
                                echo('<button id="detallePago" type="button" class="btn btn-primary">Detalle de pago</button>');
                            else
                               echo('<button id="detallePago" type="button" class="btn btn-primary" disabled>Detalle de pago</button>');
                          
                                if ($mbModificar2 == 1 or $mbAdministrador == 1)
                                echo('<button id="imprimir" type="button" class="btn btn-primary">Imprimir</button>');
                            else
                                echo('<button id="imprimir" type="button" class="btn btn-primary" disabled>Imprimir</button>');

                                if ($mbModificar2 == 1 or $mbAdministrador == 1)
                                echo('<button id="anulado" type="button" class="btn btn-primary">Anular</button>');
                            else
                                echo('<button id="anulado" type="button" class="btn btn-primary" disabled>Anular</button>');

                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="dgPG2" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
                                        <thead>
                                            <tr>
                                            <th data-column-id="PAGO_REL" data-identifier="true" data-align="left" >Pago</th>
                                            <th data-column-id="MATCURSO_REL" data-visible="false">Matrícula</th>
                                            <th data-column-id="RECIBO_140" data-header-align="left" data-width="12%">Recibo</th>
                                            <th data-column-id="ALUMNO_REL" data-header-align="left" data-width="28%">Estudiante</th>
                                            <th data-column-id="CONCEPTO_140" data-header-align="left" data-width="30%">Concepto</th>
                                            <th data-column-id="FECHA_140" data-header-align="left" data-width="14%">Fecha</th>
                                            <th data-column-id="ANULADO_142" data-header-align="left" data-width="14%">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $mDatos = fxPagosRealizados(1); // consulta de los detalles de los pagos GRID - 2
                                            while ($mFila = $mDatos->fetch()) 
                                            {
                                                echo ("<tr>");
                                                echo ('<td >' . $mFila["PAGO_REL"] . '</td>');
                                               // echo ('<td hidden>' . $mFila["COBRO_REL"] . '</td>');
                                                echo ('<td hidden>' . $mFila["MATCURSO_REL"] . '</td>');
                                                echo ("<td>" . $mFila["RECIBO_140"] . "</td>");
                                                $msNombre = trim($mFila["ALUMNO_REL"]);
                                                echo ("<td>" . htmlspecialchars($msNombre, ENT_QUOTES, 'UTF-8') . "</td>");
                                                echo ("<td>" . $mFila["CONCEPTO_140"] . "</td>");
                                                echo ("<td>" . $mFila["FECHA_140"] . "</td>");
                                                echo ("<td>" . $mFila["ANULADO_142"] . "</td>");
                                                echo ("</tr>");
                                            }
                                            ?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>  <!--Fin del DIV de Tab Detalle de los pagos-->
                    </div><!--Fin del DIV de Tabs-->
                </div>
            </div>
        </div>
    </div>
<?php }}?>
<script src="bootstrap/lib/jquery-1.11.1.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.js"></script>
<script src="bootstrap/dist/jquery.bootgrid.fa.js"></script>
<script src="js/jquery.redirect.js"></script>
<script>
    $(document).ready(function () {
        function initBootgrid(selectores) {
            $(selectores).bootgrid({
                formatters: {
                    "link": function (column, row) {
                        return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
                    }
                },
                rowCount: [-1, 10, 50, 75]
            });
        }

        // Inicializar datagrids
        initBootgrid("#dgPG, #dgPG2");

        // Estilos
        $('.datagrid-wrap').width('100%');
        $('.datagrid-view').height('200px');

        // Funciones reutilizables
        function getSelected(id) {
            return $.trim($(id).bootgrid("getSelectedRows"));
        }

        function redirectIfSelected(id, url, param) {
            var selected = getSelected(id);
            if (selected !== "") {
                var data = {};
                data[param] = selected;
                $.redirect(url, data, "POST");
            }
        }

        // Eventos
      /*  $("#append, #agregar").on("click", function () {
            $.redirect("procPagosCL.php", "POST");
        });
*/
        $("#edit").on("click", function () {
            redirectIfSelected("#dgPG", "procPagosCL.php", "UMOJN");
        });

        $("#agregarPago").on("click", function () {
            redirectIfSelected("#dgPG", "procPagosCL.php", "UMOJN");
        });

        $("#detallePago").on("click", function () {
            redirectIfSelected("#dgPG2", "procDetPagosCl.php", "UMOJN");
        });

        $("#anulado").on("click", function () {
            redirectIfSelected("#dgPG2", "gridPagoCL.php", "UMOJN");
        });

        $("#imprimir").on("click", function () {
            var selectedRows = $("#dgPG2").bootgrid("getSelectedRows");
            if (selectedRows.length > 0) {
                var selectedRowsString = selectedRows.join(',');
                window.location.href = "repReciboCL.php?selectedRows=" + encodeURIComponent(selectedRowsString);
            }
        });

        // Ocultar columnas específicas
        $("#dgPG2 th[data-column-id='MATCURSO_REL'], #dgPG2 td:nth-child(2)").hide();
        $("#dgPG2 th[data-column-id='CANTIDAD_140'], #dgPG2 td:nth-child(7)").hide();
    });
</script>