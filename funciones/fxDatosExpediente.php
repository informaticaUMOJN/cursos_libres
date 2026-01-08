<?php
require_once ("fxGeneral.php");

if (isset($_POST["carrera"]) and isset($_POST["generacion"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCarrera = $_POST["carrera"];
    $mnGeneracion = $_POST["generacion"];
    $msConsulta = "SELECT ESTUDIANTE_REL, GENERACION_010, CARNET_010, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 FROM UMO010A WHERE CARRERA_REL = ? AND GENERACION_010 = ?";;
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCarrera, $mnGeneracion]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msEstudiante = trim($mFila["NOMBRE1_010"]);
        if (trim($mFila["NOMBRE2_010"]) != "")
            $msEstudiante .= " " . $mFila["NOMBRE2_010"];

        $msEstudiante .= " " . $mFila["APELLIDO1_010"];

        if (trim($mFila["APELLIDO2_010"]) != "")
            $msEstudiante .= " " . $mFila["APELLIDO2_010"];
            
        

        $msResultado .= '{"codigo":"' . $mFila["ESTUDIANTE_REL"] . '","carnet":"' . $mFila["CARNET_010"] . '","generacion":"' . $mFila["GENERACION_010"] . '","estudiante":"' . $msEstudiante . '"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}
?>