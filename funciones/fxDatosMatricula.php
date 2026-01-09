<?php
require_once ("fxGeneral.php");

/**********Llenar el combo del Plan de estudio**********/
if (isset($_POST["carreraPe"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["carreraPe"];
	$msConsulta = "Select PLANESTUDIO_REL, PERIODO_050 from UMO050A where CARRERA_REL = ? order by PERIODO_050";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msTexto = "Período " . trim($mFila["PERIODO_050"]);
			$msResultado .= "<option value='" . $mFila["PLANESTUDIO_REL"] . "'>" . $msTexto . "</option>";
		}
	}
	
	echo $msResultado;
}

/**********Llenar el combo de las Asignaturas**********/
if (isset($_POST["carreraAsg"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["carreraAsg"];
	$msConsulta = "select UMO060A.ASIGNATURA_REL, NOMBRE_060 from UMO060A where CARRERA_REL = ? order by NOMBRE_060";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera]);
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

/**********Llenar el año de ingreso del estudiante**********/
if (isset($_POST["estudianteGen"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["estudianteGen"];
	$msConsulta = "Select GENERACION_010 from UMO010A where ESTUDIANTE_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	
	if ($mnRegistros == 0)
		$mnResultado = 0;
	else{
		$mFila = $mDatos->fetch();
		$mnResultado = $mFila["GENERACION_010"];
	}
	
	echo $mnResultado;
}

/**********Llenar la carrera del estudiante**********/
if (isset($_POST["estudianteCrr"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["estudianteCrr"];
	$msConsulta = "Select CARRERA_REL from UMO010A where ESTUDIANTE_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	
	if ($mnRegistros == 0)
		$mnResultado = 0;
	else{
		$mFila = $mDatos->fetch();
		$mnResultado = $mFila["CARRERA_REL"];
	}
	
	echo $mnResultado;
}
?>