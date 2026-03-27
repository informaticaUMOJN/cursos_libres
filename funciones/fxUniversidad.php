<?php
	//*****Universidad************************************************************//
	function fxGuardarUniversidad($msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(UNIVERSIDADCL_REL), 4), 0) as Ultimo from UMO360A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "UNI" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO360A (UNIVERSIDADCL_REL, MUNICIPIO_REL, NOMBRE_360, TIPO_360) values(?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msMunicipio, $msNombre, $mnTipo]);
		return $msCodigo;
	}
	
	function fxModificarUniversidad($msCodigo, $msMunicipio, $msNombre, $mnTipo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO360A set MUNICIPIO_REL = ?, NOMBRE_360 = ?, TIPO_360 = ? where UNIVERSIDADCL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMunicipio, $msNombre, $mnTipo, $msCodigo]);
	}
	
	function fxBorrarUniversidad($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO360A where UNIVERSIDADCL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveUniversidad($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select UNIVERSIDADCL_REL, NOMBRE_120, NOMBRE_360, (case TIPO_360 when 0 then 'Privado' when 1 then 'Público' when 2 then 'Subvencionado' else 'Otro' end) as TIPO_360 from UMO360A join UMO120A on UMO360A.MUNICIPIO_REL = UMO120A.MUNICIPIO_REL order by UNIVERSIDADCL_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select UNIVERSIDADCL_REL, MUNICIPIO_REL, NOMBRE_360, TIPO_360 from UMO360A where UNIVERSIDADCL_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>