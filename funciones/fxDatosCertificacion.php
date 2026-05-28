<?php
require_once ("fxGeneral.php");

/**********Llenar el grid de los Estudiantes**********/
if (isset($_POST["curso"]) and isset($_POST["certificados"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
	$msCurso = $_POST["curso"];
    $mbCertificados = $_POST["certificados"];

    if ($mbCertificados == 0)
        $msConsulta = "select MATCURSO_REL, concat_ws(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE from UMO210A join UMO200A on UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL where CURSOS_REL = ? and ESTADO_210 = 1";
    else
    {
        $msConsulta = "select UMO371A.MATCURSO_REL, PARTIDA_REL, concat_ws(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE, TOMO_371, FOLIO_371 from UMO210A, UMO200A, UMO371A ";
        $msConsulta .= "where UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL and UMO210A.MATCURSO_REL = UMO371A.MATCURSO_REL ";
        $msConsulta .= "and CURSOS_REL = ? and ESTADO_210 = 3";
    }
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCurso]);
    $mnRegistros = $mDatos->rowCount();

    $msResultado = "[";
    $i = 1;
    while ($mFila = $mDatos->fetch())
    {
        if ($mbCertificados == 0)
        {
            $msMatricula = trim($mFila["MATCURSO_REL"]);
            $msEstudiante = trim($mFila["ESTUDIANTE"]);
            $msResultado .= '{"matricula":"' . $msMatricula . '","estudiante":"' . $msEstudiante . '"}';;
        }
        else
        {
            $msMatricula = trim($mFila["MATCURSO_REL"]);
            $mnPartida = trim($mFila["PARTIDA_REL"]);
            $msEstudiante = trim($mFila["ESTUDIANTE"]);
            $mnTomo = trim($mFila["TOMO_371"]);
            $mnFolio = trim($mFila["FOLIO_371"]);
            $msResultado .= '{"matricula":"' . $msMatricula . '","estudiante":"' . $msEstudiante . '","tomo":"' . $mnTomo . '", "folio":"' . $mnFolio . '", "partida":"' . $mnPartida . '"}';;
        }

        if ($i != $mnRegistros)
            $msResultado .= ',';

        $i++;
    }
    
    $msResultado = "]";
}    
?>