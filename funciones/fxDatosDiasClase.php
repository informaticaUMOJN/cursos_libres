<?php
require_once ("fxGeneral.php");

/**********Llenar el detalle de los días de clase**********/
if (isset($_POST["diaSemana"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$mnDiaSemana = $_POST["diaSemana"];

	$msConsulta = "select DOMINGO_020, LUNES_020, MARTES_020, MIERCOLES_020, JUEVES_020, VIERNES_020, SABADO_020, FECHAINI_021, FECHAFIN_021 from KDSA021A, KDSA020A where KDSA021A.CURSO_REL = KDSA020A.CURSO_REL and KDSA021A.MODULO_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$modulo]);
	$Fila = $mDatos->fetch();
	$domingo = $Fila["DOMINGO_020"];
	$lunes = $Fila["LUNES_020"];
	$martes = $Fila["MARTES_020"];
	$miercoles = $Fila["MIERCOLES_020"];
	$jueves = $Fila["JUEVES_020"];
	$viernes = $Fila["VIERNES_020"];
	$sabado = $Fila["SABADO_020"];
	$fecha = trim($Fila["FECHAINI_021"]);
	$fechaFin = trim($Fila["FECHAFIN_021"]);
	$msResultado = "";

	while ($fecha <= $fechaFin)
	{
		$escribirFecha = false;
		$diaSemana = date("l", strtotime($fecha));

		if ($diaSemana == "Sunday" and $domingo == 1)
			$escribirFecha = true;

		if ($diaSemana == "Monday" and $lunes == 1)
			$escribirFecha = true;
		
		if ($diaSemana == "Tuesday" and $martes == 1)
			$escribirFecha = true;

		if ($diaSemana == "Wednesday" and $miercoles == 1)
			$escribirFecha = true;

		if ($diaSemana == "Thursday" and $jueves == 1)
			$escribirFecha = true;

		if ($diaSemana == "Friday" and $viernes == 1)
			$escribirFecha = true;

		if ($diaSemana == "Saturday" and $sabado == 1)
			$escribirFecha = true;

		if ($escribirFecha)
		{
			//Verifica que no sea un día Feriado
			$msConsulta = "select DETFECHA_REL from KDSA021A, KDSA022A where KDSA021A.CURSO_REL = KDSA022A.CURSO_REL and KDSA021A.MODULO_REL = ? and FECHA_022 = ?";
			$mAuxiliar = $m_cnx_MySQL->prepare($msConsulta);
			$mAuxiliar->execute([$modulo, $fecha]);
			$mnAuxiliar = $mAuxiliar->rowCount();
			
			if ($mnAuxiliar == 0)
			{
				$fechaDividida = explode("-", $fecha);
				$anno = $fechaDividida[0];
				$mes = $fechaDividida[1];
				$dia = $fechaDividida[2];
				$msResultado .= $anno . "%" . $mes . "@" . $dia . "#";
			}
		}
		
		$fecha = date("Y-m-d", strtotime($fecha . "+ 1 days"));
	}
	echo $msResultado;
}
?>