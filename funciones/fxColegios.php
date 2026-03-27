<?php
	//*****COLEGIOS************************************************************//
	function fxGuardarColegio($msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(COLEGIOCL_REL), 4), 0) as Ultimo from UMO350A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "COL" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO350A (COLEGIOCL_REL, MUNICIPIO_REL, NOMBRE_350, TIPO_350) values(?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msMunicipio, $msNombre, $mnTipo]);
		return $msCodigo;
	}
	
	function fxModificarColegio($msCodigo, $msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO350A set MUNICIPIO_REL = ?, NOMBRE_350 = ?, TIPO_350 = ? where COLEGIOCL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMunicipio, $msNombre, $mnTipo, $msCodigo]);
	}
	
	function fxBorrarColegio($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO350A where COLEGIOCL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveColegio($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select COLEGIOCL_REL, NOMBRE_120, NOMBRE_350, (case TIPO_350 when 0 then 'Privado' when 1 then 'Público' when 2 then 'Subvencionado' else 'Otro' end) as TIPO_350 from UMO350A join UMO120A on UMO350A.MUNICIPIO_REL = UMO120A.MUNICIPIO_REL order by COLEGIOCL_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select COLEGIOCL_REL, MUNICIPIO_REL, NOMBRE_350, TIPO_350 from UMO350A where COLEGIOCL_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>