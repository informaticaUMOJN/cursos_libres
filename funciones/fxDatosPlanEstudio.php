<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de las Asignaturas**********/
if (isset($_POST["carrera"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["carrera"];
	$msConsulta = "Select ASIGNATURA_REL, NOMBRE_060 from UMO060A where CARRERA_REL = ? order by NOMBRE_060";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='>" . $mFila["ASIGNATURA_REL"] . "'>" . $mFila["NOMBRE_060"] . "</option>";
		}
	}
	
	echo $msResultado;
}
?>