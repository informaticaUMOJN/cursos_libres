<?php
session_start();
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1) {
    echo('<meta http-equiv="Refresh" content="0;url=index.php">');
    exit();
}
include("masterApp.php");
require_once("funciones/fxGeneral.php");
require_once("funciones/fxUsuarios.php");
require_once("funciones/fxCbrEstudiantes.php");
$Registro = fxVerificaUsuario();
if ($Registro == 0) {
    echo '<div class="container text-center"><div id="DivContenido"><img src="imagenes/errordeacceso.png"/></div></div>';
    exit();
} 
$mbAdministrador = fxVerificaAdministrador();
$mbPermisoUsuario = fxPermisoUsuario("procCbrEstudiante", $mbRefrescar, $mbModificar, $mbMora);

if ($mbAdministrador == 0 && $mbPermisoUsuario == 0) {
    echo '<div class="container text-center"><div id="DivContenido"><img src="imagenes/errordeacceso.png"/></div></div>';
    exit();
} 
if (isset($_POST["UMOJN"])) {
    fxAgregarBitacora($_SESSION["gsUsuario"], "UMO131A", $_POST["UMOJN"], "", "Borrar", "");
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
                  if ($mbMora == 1 || $mbAdministrador == 1)
                 echo('<button id="mora" type="button" class="btn btn-primary">Mora</button>');
                else
                  echo('<button id="mora" type="button" class="btn btn-primary" disabled>Mora</button>');
             
                if (isset($_POST["CBRUMOJN"]) ) {
                    obtener();
                }
                
                if (isset($_POST["mora"])) {
                    mora(); 
                }?>
                
                <table id="grid" class="table table-condensed table-hover table-striped" data-selection="true" data-multi-select="false" data-row-select="true" data-keep-selection="true">
                    <thead>
                    <tr>
                        <th data-column-id="MATRICULA_REL" data-identifier="true" data-align="left">Matricula</th>
                        <th data-column-id="ESTUDIANTE" data-header-align="left" data-width="30%">Estudiante</th>
                        <th data-column-id="NOMBRE_040" data-header-align="left" data-width="36%">Carrera</th>
                        <th data-column-id="ESTADO_030" data-align="left" data-width="9%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $mDatos = fxDevuelveEncabezado(1);
                    while ($mFila = $mDatos->fetch())
                    {
                        echo ("<tr>");
                        echo ("<td>" . $mFila["MATRICULA_REL"] . "</td>");
                        if (trim($mFila["NOMBRE2_010"])!="")
                            $msNombre = trim($mFila["NOMBRE1_010"]) . " " . $mFila["NOMBRE2_010"] . ", ";
                        else
                            $msNombre = trim($mFila["NOMBRE1_010"]) . ", ";
                        if (trim($mFila["APELLIDO2_010"])!="")
                            $msNombre .= trim($mFila["APELLIDO1_010"]) . " " . trim($mFila["APELLIDO2_010"]);
                        else
                            $msNombre .= trim($mFila["APELLIDO1_010"]);
                        echo ("<td>" . $msNombre . " " . "</td>");
                        echo ("<td>" . $mFila["NOMBRE_040"] . "</td>");
                        echo ("<td>" . $mFila["ESTADO_030"] . "</td>");
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
            $.redirect("procCbrEstudiante.php", {UMOJN: codCobros}, "POST");
        }
    });

    $("#modificar").on("click", function() {
        if ($.trim($("#grid").bootgrid("getSelectedRows")) != "") {
            var codCobros = $.trim($("#grid").bootgrid("getSelectedRows"));
            $.redirect("procCbrEstudiante.php", {UMOJN: codCobros}, "POST");
        }
    });

    $("#refresch").on("click", function() {
        $.redirect("gridCbrEstudiante.php", {CBRUMOJN: 1}, "POST");
    });

    $("#mora").on("click", function() {
    $.redirect("gridCbrEstudiante.php", {mora: 1}, "POST");
});
});
</script>
<?php
function obtener()  
{
    global $m_cnx_MySQL;
    $msConsulta = "select * from UMO030A where ESTADO_030 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([0]);
    
    while ($mEstudiante = $mDatos->fetch()) 
    {
        $msCarrera = $mEstudiante['CARRERA_REL'];
        $msPlanEstudio = $mEstudiante['PLANESTUDIO_REL'];

        // Obtener turno y régimen del estudiante
        $consultaTurnoRegimen = "select TURNO_050, REGIMEN_050 from UMO050A where PLANESTUDIO_REL = ? and CARRERA_REL = ?";
        $mnTurnoRegimen = $m_cnx_MySQL->prepare($consultaTurnoRegimen);
        $mnTurnoRegimen->execute([$msPlanEstudio, $msCarrera]);
        $filaTurnoRegimen = $mnTurnoRegimen->fetch();
        
        if ($filaTurnoRegimen) 
        {
            $turnoEstudiante = $filaTurnoRegimen['TURNO_050'];
            $regimenEstudiante = $filaTurnoRegimen['REGIMEN_050'];
            
            // Obtener cobros activos para la carrera considerando turno y régimen
            $consultaCobros = "select * from UMO130A where CARRERA_REL = ? and ACTIVO_130 = ? and TURNO_130 = ? and REGIMEN_130 = ?";
            $mnCobros = $m_cnx_MySQL->prepare($consultaCobros);
            $mnCobros->execute([$msCarrera, 1, $turnoEstudiante, $regimenEstudiante]);   
            
            while ($filaCobro = $mnCobros->fetch())
            {
                $mnCobroRel = $filaCobro['COBRO_REL'];
                $mnValor130 = $filaCobro['VALOR_130'];
                $mnMoneda130 = $filaCobro['MONEDA_130'];
                $tipoCobro = $filaCobro['TIPO_130'];

                // Saltar cobros que no sean de tipo 0 o 1
                if ($tipoCobro != 0 && $tipoCobro != 1)
                {
                    continue;
                }

                // Calcular descuento si es una mensualidad
                $mnDescuento = 0;
                if ($tipoCobro == 1) 
                { 
                    switch ($mEstudiante['BECA_030']) 
                    {
                        case 1: 
                            $mnDescuento = $mnValor130 * 0.50; 
                            break;
                        case 2: 
                            $mnDescuento = $mnValor130 * 0.25; 
                            break;
                        case 3:  
                            $mnDescuento = $mnValor130 * 0.16; 
                            break;
                        default:
                            $mnDescuento = 0;
                            break;
                    }
                }
                // Verificar si ya existe un registro para el cobro y la matrícula
                $verificarRegistro = "select * from UMO131A where COBRO_REL = ? and MATRICULA_REL = ?";
                $msConsulta = $m_cnx_MySQL->prepare($verificarRegistro);
                $msConsulta->execute([$mnCobroRel, $mEstudiante['MATRICULA_REL']]);                
                $registro = $msConsulta->fetch();
                
                if ($registro)
                {
                    // Si el cobro ya ha sido abonado, no hacer nada
                    if ($registro['ABONADO_131'] > 0) 
                    {
                        continue;
                    } 
                    else 
                    { 
                        // Si el cobro no ha sido abonado, actualizar el registro 
                        $msConsulta = "update UMO131A set ADEUDADO_131 = ?, ABONADO_131 = ?, MONEDA_131 = ?, DESCUENTO_131 = ? where COBRO_REL = ? and MATRICULA_REL = ?";
                        $msConsulta = $m_cnx_MySQL->prepare($msConsulta);
                        $msConsulta->execute([$mnValor130, 0, $mnMoneda130, $mnDescuento, $mnCobroRel, $mEstudiante['MATRICULA_REL']]);
                    }
                } 
                else 
                {
                    // Si no existe el registro, insertar el cobro
                    $msConsulta = "insert into UMO131A (COBRO_REL, MATRICULA_REL, ADEUDADO_131, ABONADO_131, MONEDA_131, DESCUENTO_131, ANULADO_131) values (?, ?, ?, ?, ?, ?, 0)";
                    $msConsulta = $m_cnx_MySQL->prepare($msConsulta);
                    $msConsulta->execute([$mnCobroRel, $mEstudiante['MATRICULA_REL'], $mnValor130, 0, $mnMoneda130, $mnDescuento]);
                }
            }
        }
    }
}
function esDiaNoHabil($fecha)
{
    global $m_cnx_MySQL;
    $consulta = "select FECHA_007 FROM UMO007A where FECHA_007 = ?";
    $mDatos = $m_cnx_MySQL->prepare($consulta);
    $mDatos->execute([$fecha->format('Y-m-d')]);
    return $mDatos->rowCount() > 0;
}
function mora() 
{
    global $m_cnx_MySQL;
    $fechaActual = new DateTime();
    $fechaMora = new DateTime($fechaActual->format('Y-m-10'));
    
    while ($fechaMora->format('w') == 0 || esDiaNoHabil($fechaMora)) // Avanzar al siguiente día si es domingo o un día no hábil
    {
        $fechaMora->modify('+1 day');
    }
    if ($fechaActual > $fechaMora) // Si la fecha actual es posterior a la fecha de mora, se aplica la mora
    {
        $msConsulta = "select * from UMO030A where ESTADO_030 = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([0]);
        
        while ($mEstudiante = $mDatos->fetch()) 
        {
            $msCarrera = $mEstudiante['CARRERA_REL'];  
            $consultaCobros = "select * from UMO130A where CARRERA_REL = ? and ACTIVO_130 = ?";
            $mnCobros = $m_cnx_MySQL->prepare($consultaCobros);
            $mnCobros->execute([$msCarrera, 1]);
            
            while ($filaCobro = $mnCobros->fetch()) 
            {
                $mnCobroRel = $filaCobro['COBRO_REL'];
                $mnValor130 = $filaCobro['VALOR_130'];
                $mnMoneda130 = $filaCobro['MONEDA_130'];  
                $tipoCobro = $filaCobro['TIPO_130'];
                
                if ( $tipoCobro != 2) {
                    continue; 
                }
                // Verificar si ya existe un registro para el cobro y la matrícula
                $verificarRegistro = "select * from UMO131A where COBRO_REL = ? and MATRICULA_REL = ?";
                $msConsulta = $m_cnx_MySQL->prepare($verificarRegistro);
                $msConsulta->execute([$mnCobroRel, $mEstudiante['MATRICULA_REL']]);  
                $registro = $msConsulta->fetch();
                
                if (!$registro) // Si no existe el registro, insertar el cobro
                {
                    $msConsulta = "insert into UMO131A (COBRO_REL, MATRICULA_REL, ADEUDADO_131, ABONADO_131, MONEDA_131, DESCUENTO_131, ANULADO_131) values (?, ?, ?, ?, ?, ?, 0)";
                    $msConsulta = $m_cnx_MySQL->prepare($msConsulta);
                    $msConsulta->execute([$mnCobroRel, $mEstudiante['MATRICULA_REL'], $mnValor130, 0, $mnMoneda130, 0]);
                }
            }
        }
    }
}
?>