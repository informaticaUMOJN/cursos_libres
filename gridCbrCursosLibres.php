<?php
session_start();
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1) {
    echo('<meta http-equiv="Refresh" content="0;url=index.php">');
    exit();
}
include("masterApp.php");
require_once("funciones/fxGeneral.php");
require_once("funciones/fxUsuarios.php");
require_once("funciones/fxCbrCursosLibres.php");

$Registro = fxVerificaUsuario();
if ($Registro == 0) {
    echo '<div class="container text-center"><div id="DivContenido"><img src="imagenes/errordeacceso.png"/></div></div>';
    exit();
}

$mbAdministrador = fxVerificaAdministrador();
$mbPermisoUsuario = fxPermisoUsuario("procCbrCursosLibres", $mbRefrescar, $mbModificar, $mbMora);

if ($mbAdministrador == 0 && $mbPermisoUsuario == 0) {
    echo '<div class="container text-center"><div id="DivContenido"><img src="imagenes/errordeacceso.png"/></div></div>';
    exit();
}

if (isset($_POST["UMOJN"])) {
    fxAgregarBitacora($_SESSION["gsUsuario"], "UMO132A", $_POST["UMOJN"], "", "Borrar", "");
}

$m_cnx_MySQL = fxAbrirConexion();
?>

<div class="container">
    <div id="DivContenido">
        <div id="lateral">
            <?php 
            if ($mbModificar == 1 || $mbAdministrador == 1)
                echo('<label id="modificar" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditar.png" height="80%" style="cursor:pointer" /></label>');
            else
                echo('<label id="modificarDis" data-toggle="tooltip" data-placement="top" title="Editar"><img src="imagenes/btnLateralEditarDis.png" height="80%" style="cursor:default" /></label>');
            ?>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php 
                if ($mbModificar == 1 || $mbAdministrador == 1)
                    echo('<button id="edit" type="button" class="btn btn-primary">Detalle</button>');
                else
                    echo('<button id="edit" type="button" class="btn btn-primary" disabled>Detalle</button>');

                if ($mbRefrescar == 1 || $mbAdministrador == 1)
                    echo('<button id="refresch" type="button" class="btn btn-primary">Generar Cobros</button>');
                else
                    echo('<button id="refresch" type="button" class="btn btn-primary" disabled>Generar Cobros</button>');
                 
                if (isset($_POST["CBRUMOJN"])) {
                    obtener();
                }

                if (isset($_POST["mora"])) {
                    mora(); 
                }
                ?>
                
                <table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
                    <thead>
                        <tr>
                            <th data-column-id="MATCURSO_REL" data-identifier="true" data-align="left">Matrícula</th>
                            <th data-column-id="ALUMNOS_REL" data-header-align="left" data-width="30%">Estudiante</th>
                            <th data-column-id="NOMBRE_040" data-header-align="left" data-width="36%">Curso</th>
                            <th data-column-id="ESTADO_210" data-align="left" data-width="9%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mDatos = fxDevuelveCursos(1);
                        while ($mFila = $mDatos->fetch()) {
                            echo "<tr>";
                            echo "<td>" . $mFila["MATCURSO_REL"] . "</td>";

                            $msNombre = "";
                            if (trim($mFila["NOMBRES_200"]) != "")
                                $msNombre .= trim($mFila["NOMBRES_200"]) . " ";
                            if (trim($mFila["APELLIDOS_200"]) != "")
                                $msNombre .= trim($mFila["APELLIDOS_200"]);

                            echo "<td>" . $msNombre . "</td>";
                            echo "<td>" . $mFila["NOMBRE_190"] . "</td>";
                            echo "<td>" . $mFila["ESTADO_210"] . "</td>";
                            echo "</tr>";
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

    $("#edit").on("click", function() {
        if ($.trim($("#grid").bootgrid("getSelectedRows")) != "") {
            var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
            $.redirect("procCbrCursosLibres.php", {UMOJN: codCobros}, "POST");
        }
    });

    $("#modificar").on("click", function() {
        if ($.trim($("#grid").bootgrid("getSelectedRows")) != "") {
            var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
            $.redirect("procCbrCursosLibres.php", {UMOJN: codCobros}, "POST");
        }
    });

    $("#refresch").on("click", function() {
        $.redirect("gridCbrCursosLibres.php", {CBRUMOJN: 1}, "POST");
    });
});
</script>

<?php
function obtener()  
{
    global $m_cnx_MySQL;

    // Solo cursos libres (estado = 0, por ejemplo)
    $msConsulta = "select * from UMO210A where ESTADO_210 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([0]);
    
    while ($mEstudiante = $mDatos->fetch()) 
    {
        $msCurso = $mEstudiante['CURSOS_REL'];
        $msPlanCurso = $mEstudiante['PLANCURSO_REL'];

        // Obtener cobros activos del curso
        $consultaCobros = "select * from UMO130A where CURSOS_REL = ? and ACTIVO_130 = ?";
        $mnCobros = $m_cnx_MySQL->prepare($consultaCobros);
        $mnCobros->execute([$msCurso, 1]);   

        while ($filaCobro = $mnCobros->fetch())
        {
            $mnCobroRel = $filaCobro['COBRO_REL'];
            $mnValor130 = $filaCobro['VALOR_130'];
            $mnMoneda130 = $filaCobro['MONEDA_130'];
            $tipoCobro = $filaCobro['TIPO_130'];

            // Solo aplica a tipo 0 o 1
            if ($tipoCobro != 0 && $tipoCobro != 1)
                continue;

            // Calcular descuento si es mensualidad
            $mnDescuento = 0;
            if ($tipoCobro == 1) 
            { 
                switch ($mEstudiante['BECA_210']) 
                {
                    case 1: $mnDescuento = $mnValor130 * 0.50; break;
                    case 2: $mnDescuento = $mnValor130 * 0.25; break;
                    case 3: $mnDescuento = $mnValor130 * 0.16; break;
                    default: $mnDescuento = 0; break;
                }
            }

            // Verificar si ya existe en UMO132A
            $verificarRegistro = "select * from UMO132A where COBRO_REL = ? and MATCURSO_REL = ?";
            $msConsulta = $m_cnx_MySQL->prepare($verificarRegistro);
            $msConsulta->execute([$mnCobroRel, $mEstudiante['MATCURSO_REL']]);                
            $registro = $msConsulta->fetch();

            if ($registro)
            {
                // Si ya abonó, no modificar
                if ($registro['ABONADO_132'] > 0) continue;

                // Si no ha abonado, actualizar
                $msConsulta = "update UMO132A set ADEUDADO_132 = ?, ABONADO_132 = ?, MONEDA_132 = ?, DESCUENTO_132 = ? where COBRO_REL = ? and MATCURSO_REL = ?";
                $msConsulta = $m_cnx_MySQL->prepare($msConsulta);
                $msConsulta->execute([$mnValor130, 0, $mnMoneda130, $mnDescuento, $mnCobroRel, $mEstudiante['MATCURSO_REL']]);
            } 
            else 
            {
                // Insertar nuevo registro
                $msConsulta = "insert into UMO132A (COBRO_REL, MATCURSO_REL, ADEUDADO_132, ABONADO_132, MONEDA_132, DESCUENTO_132, ANULADO_132)
                               values (?, ?, ?, ?, ?, ?, 0)";
                $msConsulta = $m_cnx_MySQL->prepare($msConsulta);
                $msConsulta->execute([$mnCobroRel, $mEstudiante['MATCURSO_REL'], $mnValor130, 0, $mnMoneda130, $mnDescuento]);
            }
        }
    }
}
?>
