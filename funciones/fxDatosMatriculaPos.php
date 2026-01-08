<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de los Cursos**********/
if (isset($_POST["carrera"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCarrera = $_POST["carrera"];
	$msConsulta = "select CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A where CARRERA_REL = ? order by NOMBRE_240";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='" . $mFila["CURSOPOSGRADO_REL"] . "'>" . $mFila["NOMBRE_240"] . "</option>";
		}
	}
	
	echo $msResultado;
}

/**********Llenar la carrera del estudiante**********/
if (isset($_POST["estudianteCrr"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["estudianteCrr"];
	$msConsulta = "Select CARRERA_REL from UMO250A where ESTUDIANTEPOS_REL = ?";
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