<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de las Asignaturas**********/
if (isset($_POST["carreraAsg"]) and isset($_POST["docenteAsg"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["carreraAsg"];
	$msDocente = $_POST["docenteAsg"];
	$msConsulta = "select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A, UMO070A where UMO060A.ASIGNATURA_REL = UMO070A.ASIGNATURA_REL and CARRERA_REL = ? and DOCENTE_REL = ? and ACTIVO_070 = ? order by NOMBRE_060";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera, $msDocente, 1]);
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

/************Llenar el grid de los Avances************/
if (isset($_POST["asignatura"]) and isset($_POST["anno"]) and isset($_POST["semestre"]) and isset($_POST["turno"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msAsignatura = $_POST["asignatura"];
	$mnAnno = $_POST["anno"];
	$mnSemestre = $_POST["semestre"];
	$mnTurno = $_POST["turno"];
	$msConsulta = "select FECHA_073 as FechaP, UNIDAD_073 as UnidadP, CONTENIDO_073 as ContenidoP, DATE(NOW()) as FechaE, '' as UnidadE, '' as ContenidoE, ";
	$msConsulta .= "'' as OBSERVACIONES_171 from UMO073A join UMO070A on UMO073A.SYLLABUS_REL = UMO070A.SYLLABUS_REL where ASIGNATURA_REL = ? and ANNO_070 = ? and SEMESTRE_070 = ? and TURNO_070 = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msAsignatura, $mnAnno, $mnSemestre, $mnTurno]);
	$mnRegistros = $mDatos->rowCount();
    $msResultado = "[";
    $i = 1;

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$fecha = date_create_from_format('Y-m-d', $mFila["FechaP"]);
			$mdFechaP = date_format($fecha, 'd/m/Y');
			$fecha = date_create_from_format('Y-m-d', $mFila["FechaE"]);
			$mdFechaE = date_format($fecha, 'd/m/Y');
			$msResultado .= '{"FechaP":"' . $mdFechaP . '","UnidadP":"' . $mFila["UnidadP"] . '","ContenidoP":"' . $mFila["ContenidoP"] . '","FechaE":"' . $mdFechaE . '","UnidadE":"' . $mFila["UnidadE"] . '","ContenidoE":"' . $mFila["ContenidoE"] . '","OBSERVACIONES_171":"' . $mFila["OBSERVACIONES_171"] . '"}';
			if ($i != $mnRegistros)
            	$msResultado .= ',';

        	$i++;
		}
	}
	$msResultado .= ']';

	//Verifica que no exista el registro para evitar duplicidad
	$msConsulta = "select AVANCE_REL from UMO170A where ASIGNATURA_REL = ? and ANNO_170 = ? and SEMESTRE_170 = ? and TURNO_170 = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msAsignatura, $mnAnno, $mnSemestre, $mnTurno]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado .= '%' . $mnRegistros . '#';

	echo $msResultado;
}
?>