<?php
require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion();

if (isset($_POST["carrera"]) && isset($_POST["tipo"])) {
    $valor = $_POST["carrera"];
    $tipo = $_POST["tipo"];
    echo "<option value=''></option>";

    if ($tipo == "curso") {
        $consulta = "select COBRO_REL, DESC_130 from UMO130A where CURSOS_REL = ? and ACTIVO_130 = 1 and TIPO_130 not in (0, 2) order by COBRO_REL";
    } else {
        $consulta = "select COBRO_REL, DESC_130 from UMO130A where CARRERA_REL = ? and ACTIVO_130 = 1 and TIPO_130 not in (0, 2) order by COBRO_REL";
    }

    $mDatos = $m_cnx_MySQL->prepare($consulta);
    $mDatos->execute([$valor]);
    while ($fila = $mDatos->fetch()) {
        echo "<option value='" . $fila["COBRO_REL"] . "'>" . $fila["DESC_130"] . "</option>";
    }
}
?> 