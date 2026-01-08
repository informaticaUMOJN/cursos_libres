<?php
require_once ("fxGeneral.php");

if (isset($_POST["recibo"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msConsulta = "SELECT RECIBO_140 FROM UMO140A ORDER BY RECIBO_140 DESC LIMIT 1";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute();
	$mFila = $mDatos->fetch();

	if ($mFila) {
		$mnRecibo = $mFila["RECIBO_140"];
	} else {
		$mnRecibo = 1; 
	}

	$msConsulta = "SELECT COUNT(PAGO_REL) as CONTEO FROM UMO140A ";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$mnRecibo]);
	$mFila = $mDatos->fetch();
	$mnConteo = intval($mFila["CONTEO"]) + 1;

	$msResultado = $mnRecibo . "-" . $mnConteo;
	echo $msResultado;
}
?>