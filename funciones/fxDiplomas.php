<?php
	require_once ("fxGeneral.php");

	function fxAgregarDiploma($msEstudio, $msNombre)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(DIPLOMA_REL), 3), 0) as Ultimo from UMO003B";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "DP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
		$mdFechaHoy = date('Y-m-d H:i:s');
		$msConsulta = "insert into UMO003B (DIPLOMA_REL, FECHA_003, ESTUDIO_003, NOMBRE_003, VERIFICACION_003, RUTA_003) values (?,?,?,?,?,?)";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msCodigo, $mdFechaHoy, $msEstudio, $msNombre, "", ""]);
		return $msCodigo;
	}

	function fxModificarDiploma($msCodigo, $msEstudio, $msNombre)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO003B set ESTUDIO_003 = ?, NOMBRE_003 = ? where DIPLOMA_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msEstudio, $msNombre, $msCodigo]);
	}

	function fxModificarRuta($msCodigo, $msRuta)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO003B set RUTA_003 = ? where DIPLOMA_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msRuta, $msCodigo]);
	}

	function fxBorrarDiploma($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select RUTA_003 from UMO003B where DIPLOMA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$mRow->fetch();
		$msRuta = $mRow["RUTA_003"];
		array_map('unlink', glob($msRuta));
		
		$msConsulta = "delete from UMO003B where DIPLOMA_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msCodigo]);
	}

	function fxDevuelveRuta($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select RUTA_003 from UMO003B where DIPLOMA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$mRow = $mDatos->fetch();
		$msRuta = $mRow["RUTA_003"];
		return $msRuta;
	}

	function fxDevuelveDiploma($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select DIPLOMA_REL, FECHA_003, ESTUDIO_003, NOMBRE_003 from UMO003B";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select DIPLOMA_REL, FECHA_003, ESTUDIO_003, NOMBRE_003, RUTA_003 from UMO003B where DIPLOMA_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}
?>