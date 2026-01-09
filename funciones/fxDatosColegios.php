<?php
require_once ("fxGeneral.php");

/**********Llenar el combo de los Municipios**********/
if (isset($_POST["departamento"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCodigo = $_POST["departamento"];
	$msConsulta = "Select MUNICIPIO_REL, NOMBRE_120 from UMO120A where DEPARTAMENTO_REL = ? order by NOMBRE_120";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	$msResultado = "";

	if ($mnRegistros > 0)
	{
		while ($mFila = $mDatos->fetch())
		{
			$msResultado .= "<option value='" . $mFila["MUNICIPIO_REL"] . "'>" . $mFila["NOMBRE_120"] . "</option>";
		}
	}
	
	echo $msResultado;
}
?>