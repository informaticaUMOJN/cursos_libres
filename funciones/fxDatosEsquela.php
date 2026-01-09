<?php
require_once ("fxGeneral.php");

if (isset($_POST["carrera"]) and isset($_POST["turno"]) and isset($_POST["anno"]) and isset($_POST["semestre"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCarrera = $_POST["carrera"];
    $mnTurno = $_POST["turno"];
    $mnAnno = $_POST["anno"];
    $mnSemestre = $_POST["semestre"];
    $msConsulta = "select UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 from UMO030A, UMO010A, UMO050A ";
    $msConsulta .= "where UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL ";
    $msConsulta .= "and UMO030A.CARRERA_REL = ? and TURNO_050 = ? and ANNOLECTIVO_030 = ? and SEMESTREACADEMICO_030 = ? ";
    $msConsulta .= "order by APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCarrera, $mnTurno, $mnAnno, $mnSemestre]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

    while ($mFila = $mDatos->fetch())
    {
        $msEstudiante = trim($mFila["APELLIDO1_010"]);
        if (trim($mFila["APELLIDO2_010"]) != "")
            $msEstudiante .= " " . $mFila["APELLIDO2_010"];

        $msEstudiante .= ", " . $mFila["NOMBRE1_010"];

        if (trim($mFila["NOMBRE2_010"]) != "")
            $msEstudiante .= " " . $mFila["NOMBRE2_010"];
                    
        $msResultado .= '{"matricula":"' . $mFila["MATRICULA_REL"] . '","estudiante":"' . $msEstudiante . '"}';
        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    $msResultado .= ']';

	echo ($msResultado);
}
?>