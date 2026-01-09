
<?php
/*datos de la asistencia de los cursos libres (ASCL)*/
require_once ("fxGeneral.php");

if (isset($_POST["asignatura"]) and isset($_POST["turno"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
    $msCodigo = $_POST["asignatura"];
    $mnTurno = $_POST["turno"];
       $msConsulta = "select '' as ASISTENCIACL_REL, UMO210A.MATCURSO_REL, NOMBRES_200,  APELLIDOS_200, 1 as ESTADO_321 from UMO211A, UMO210A, UMO200A, UMO220A where UMO211A.MATCURSO_REL = UMO210A.MATCURSO_REL and UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL  and UMO210A.PLANCURSO_REL = UMO220A.PLANCURSO_REL and UMO211A.MODULO_REL = ? and TURNO_220 = ? and ESTADO_210 = 0  order by APELLIDOS_200, NOMBRES_200";
         $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnTurno]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

     while ($mFila = $mDatos->fetch()) {
        $msEstudiante = trim($mFila["APELLIDOS_200"]) . ", " . trim($mFila["NOMBRES_200"]);
        switch ($mFila['ESTADO_321']) {
            case 0:
                $msEstado = "Presente";
                break;
            case 1:
                $msEstado = "Ausente";
                break;
            default:
                $msEstado = "Justificado";
        }$msResultado .= '{"matricula":"' . $mFila["MATCURSO_REL"] . '","estudiante":"' . $msEstudiante . '","estado":"' . $msEstado . '"}';
        if ($i != $mnRegistros) {
            $msResultado .= ',';
        }
        $i++;
    }
    $msResultado .= ']';
    echo($msResultado);
}

if (isset($_POST["asignatura2"]) and isset($_POST["fecha"]))
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msAsignatura = $_POST["asignatura2"];
    $msFecha = $_POST["fecha"];
    $msConsulta = "select * from UMO320A where FECHA_320 = ? and MODULO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msFecha, $msAsignatura]);
    $mnRegistros = $mDatos->rowCount();
    echo($mnRegistros);
}

/**********Llenar el combo de las Modulo**********/
if (isset($_POST["cursoAsg"]) and isset($_POST["docenteAsg"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["cursoAsg"];
	$msDocente = $_POST["docenteAsg"];
	$msConsulta = "SELECT MODULO_REL, NOMBRE_280 FROM UMO280A WHERE CURSOS_REL = ? AND DOCENTE_REL = ? ORDER BY MODULO_REL";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera, $msDocente]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='" . $mFila["MODULO_REL"] . "'>" . $mFila["NOMBRE_280"] . "</option>";
		}
	}
	
	echo $msResultado;
}

if (isset($_POST["docenteSelect"])) {
    $m_cnx_MySQL = fxAbrirConexion();
    $docente = $_POST["docenteSelect"];
    
    // Obtener cursos asignados al docente
    $msConsultaCurso = "SELECT DISTINCT C.CURSOS_REL, C.NOMBRE_190 
                        FROM UMO280A M
                        INNER JOIN UMO190A C ON M.CURSOS_REL = C.CURSOS_REL
                        WHERE M.DOCENTE_REL = ?
                        ORDER BY C.NOMBRE_190";
    $mCursos = $m_cnx_MySQL->prepare($msConsultaCurso);
    $mCursos->execute([$docente]);
    $htmlCursos = "";
    while ($fila = $mCursos->fetch()) {
        $htmlCursos .= "<option value='" . $fila['CURSOS_REL'] . "'>" . $fila['NOMBRE_190'] . "</option>";
    }

    // Obtener módulos del primer curso del docente
    $primerCurso = "";
    $mCursos = $m_cnx_MySQL->prepare($msConsultaCurso);
    $mCursos->execute([$docente]);
    if ($fila = $mCursos->fetch()) {
        $primerCurso = $fila['CURSOS_REL'];
    }

    $msConsultaModulo = "SELECT MODULO_REL, NOMBRE_280 
                         FROM UMO280A 
                         WHERE DOCENTE_REL = ? AND CURSOS_REL = ?
                         ORDER BY NOMBRE_280";
    $mModulos = $m_cnx_MySQL->prepare($msConsultaModulo);
    $mModulos->execute([$docente, $primerCurso]);
    $htmlModulos = "";
    while ($fila = $mModulos->fetch()) {
        $htmlModulos .= "<option value='" . $fila['MODULO_REL'] . "'>" . $fila['NOMBRE_280'] . "</option>";
    }

    // Tomar primer módulo y traer estudiantes de ese módulo
    $primerModulo = "";
    $mModulos = $m_cnx_MySQL->prepare($msConsultaModulo);
    $mModulos->execute([$docente, $primerCurso]);
    if ($fila = $mModulos->fetch()) {
        $primerModulo = $fila['MODULO_REL'];
    }

    $msConsultaEst = "SELECT UMO210A.MATCURSO_REL, APELLIDOS_200, NOMBRES_200
                      FROM UMO211A
                      INNER JOIN UMO210A ON UMO211A.MATCURSO_REL = UMO210A.MATCURSO_REL
                      INNER JOIN UMO200A ON UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
                      WHERE UMO211A.MODULO_REL = ?
                      ORDER BY APELLIDOS_200, NOMBRES_200";
    $mEst = $m_cnx_MySQL->prepare($msConsultaEst);
    $mEst->execute([$primerModulo]);
    $estudiantes = [];
    while ($fila = $mEst->fetch()) {
        $estudiantes[] = [
            "matricula" => $fila["MATCURSO_REL"],
            "estudiante" => $fila["APELLIDOS_200"] . ", " . $fila["NOMBRES_200"],
            "estado" => "Presente"
        ];
    }

    echo json_encode([
        "cursos" => $htmlCursos,
        "modulos" => $htmlModulos,
        "estudiantes" => $estudiantes
    ]);
}

?>