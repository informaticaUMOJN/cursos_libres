<?php
	//*****Universidad************************************************************//
	function fxGuardarUniversidad($msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(UNIVERSIDAD_REL), 4), 0) as Ultimo from UMO180A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "UNI" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO180A (UNIVERSIDAD_REL, MUNICIPIO_REL, NOMBRE_180, TIPO_180) values(?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msMunicipio, $msNombre, $mnTipo]);
		return $msCodigo;
	}
	
	function fxModificarUniversidad($msCodigo, $msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO180A set MUNICIPIO_REL = ?, NOMBRE_180 = ?, TIPO_180 = ? where UNIVERSIDAD_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMunicipio, $msNombre, $mnTipo, $msCodigo]);
	}
	
	function fxBorrarUniversidad($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO180A where UNIVERSIDAD_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveUniversidad($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select UNIVERSIDAD_REL, NOMBRE_120, NOMBRE_180, (case TIPO_180 when 0 then 'Privado' when 1 then 'Público' when 2 then 'Subvencionado' else 'Otro' end) as TIPO_180 from UMO180A join UMO120A on UMO180A.MUNICIPIO_REL = UMO120A.MUNICIPIO_REL order by UNIVERSIDAD_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select UNIVERSIDAD_REL, MUNICIPIO_REL, NOMBRE_180, TIPO_180 from UMO180A where UNIVERSIDAD_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>