<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de las Asignaturas**********/
if (isset($_POST["planEstudio"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["planEstudio"];
	$msConsulta = "Select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO051A where UMO051A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and PLANESTUDIO_REL = ? order by NOMBRE_060";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='" . $mFila["ASIGNATURA_REL"] . "'>" . $mFila["NOMBRE_060"] . "</option>";
		}
	}
	
	echo $msResultado;
}

/**********Llenar el combo del Plan de estudios**********/
if (isset($_POST["carreraPE"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["carreraPE"];
	$msConsulta = "Select PLANESTUDIO_REL, PERIODO_050, ACTIVO_050 from UMO050A where CARRERA_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msPlanEstudio = $mFila["PLANESTUDIO_REL"];
			$msPeriodo = $mFila["PERIODO_050"];

			if ($msSyllabus == "")
			{
				if (intval($mFila["ACTIVO_050"])==1)
					$msResultado .= "<option value='" . $msPlanEstudio . "'>Período " . $msPeriodo . "</option>";
			}
			else
				$msResultado .= "<option value='" . $msPlanEstudio . "'>Período " . $msPeriodo . "</option>";
		}
	}
	
	$msRespuesta = array('resultado'=>$msResultado, 'planEstudio'=>$msPlanEstudio);
	echo json_encode($msRespuesta);
}

/**********Verificar la existencia del Avance programático cuando borra**********/
if (isset($_POST["syllabus"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msSyllabus = $_POST["syllabus"];
	$msConsulta = "Select AVANCE_REL from UMO170A where SYLLABUS_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msSyllabus]);
	$mnRegistros = $mDatos->rowCount();

	echo $mnRegistros;
}
?>