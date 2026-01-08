<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de las Asignaturas**********/
if (isset($_POST["carrera"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["carrera"];
	$msConsulta = "Select CURSOPOSGRADO_REL, NOMBRE_240 from UMO240A where CARRERA_REL = ? order by NOMBRE_240";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='>" . $mFila["CURSOPOSGRADO_REL"] . "'>" . $mFila["NOMBRE_240"] . "</option>";
		}
	}
	
	echo $msResultado;
}
?>