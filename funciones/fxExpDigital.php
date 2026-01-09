<?php
	function fxGuardarExpDigital($msFecha, $msCodCarrera, $msCarrera)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(EXPDIGITAL_REL), 3), 0) as Ultimo from UMO001B";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "ED" . str_repeat("0", 6 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO001B (EXPDIGITAL_REL, CARRERA_REL, FECHA_001, CARRERA_001) values (?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCodCarrera, $msFecha, $msCarrera]);
		return $msCodigo;
	}

	function fxModificarExpDigital($msCodigo, $msCodCarrera, $msFecha, $msCarrera)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO001B set FECHA_001 = ?, CARRERA_REL = ?, CARRERA_001 = ? where EXPDIGITAL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msFecha, $msCodCarrera, $msCarrera, $msCodigo]);
	}

	function fxBorrarExpDigital($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO002B where EXPDIGITAL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$msConsulta = "delete from UMO001B where EXPDIGITAL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveExpDigital($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "SELECT EXPDIGITAL_REL, FECHA_001, CARRERA_001 FROM UMO001B JOIN UMO040A ON UMO001B.CARRERA_REL = UMO040A.CARRERA_REL WHERE POSGRADO_040 = 0 ORDER BY EXPDIGITAL_REL DESC";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select EXPDIGITAL_REL, CARRERA_REL, FECHA_001, CARRERA_001 FROM UMO001B where EXPDIGITAL_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}

	function fxGuardarDetExpDigital($msCodigo, $msCarnet, $msFolder, $msNombre, $msRegistro, $msTomo, $msFolio)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO002B (EXPDIGITAL_REL, CARNET_REL, FOLDER_002, NOMBRE_002, REGISTRO_002, TOMO_002, FOLIO_002) values (?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCarnet, $msFolder, $msNombre, $msRegistro, $msTomo, $msFolio]);
		return $msCodigo;
	}

	function fxBorrarDetExpDigital($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO002B where EXPDIGITAL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $msCodigo;
	}

	function fxDevuelveDetExpDigital($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select EXPDIGITAL_REL, CARNET_REL, FOLDER_002, NOMBRE_002, REGISTRO_002, TOMO_002, FOLIO_002 FROM UMO002B where EXPDIGITAL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);

		return $mDatos;
	}
?>
