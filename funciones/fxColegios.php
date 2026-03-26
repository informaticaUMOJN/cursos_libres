<?php
	//*****COLEGIOS************************************************************//
	function fxGuardarColegio($msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(COLEGIO_REL), 4), 0) as Ultimo from UMO020A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "COL" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO020A (COLEGIO_REL, MUNICIPIO_REL, NOMBRE_020, TIPO_020) values(?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msMunicipio, $msNombre, $mnTipo]);
		return $msCodigo;
	}
	
	function fxModificarColegio($msCodigo, $msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO020A set MUNICIPIO_REL = ?, NOMBRE_020 = ?, TIPO_020 = ? where COLEGIO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMunicipio, $msNombre, $mnTipo, $msCodigo]);
	}
	
	function fxBorrarColegio($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO020A where COLEGIO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveColegio($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select COLEGIO_REL, NOMBRE_120, NOMBRE_020, (case TIPO_020 when 0 then 'Privado' when 1 then 'Público' when 2 then 'Subvencionado' else 'Otro' end) as TIPO_020 from UMO020A join UMO120A on UMO020A.MUNICIPIO_REL = UMO120A.MUNICIPIO_REL order by COLEGIO_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select COLEGIO_REL, MUNICIPIO_REL, NOMBRE_020, TIPO_020 from UMO020A where COLEGIO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>