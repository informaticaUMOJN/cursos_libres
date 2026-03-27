<?php
	function fxGuardarDocentes($msNombre, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "Select ifnull(mid(max(DOCENTECL_REL), 4), 0) as Ultimo from UMO340A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "DCL" . str_repeat("0", 6 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO340A (DOCENTECL_REL, NOMBRE_340, ACTIVO_340) ";
		$msConsulta .= "values(?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msNombre, $mbActivo]);
		return ($msCodigo);
	}
	
	function fxModificarDocentes($msCodigo, $msNombre, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO340A set NOMBRE_340 = ?, ACTIVO_340 = ? where DOCENTECL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msNombre, $mbActivo, $msCodigo]);
	}
	
	function fxBorrarDocentes($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO340A where DOCENTECL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveDocentes($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select DOCENTECL_REL, NOMBRE_340, (case ACTIVO_340 when 1 then 'x' else '' end) as ACTIVO_340 from UMO340A order by DOCENTECL_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select DOCENTECL_REL, NOMBRE_340, ACTIVO_340 from UMO340A where DOCENTECL_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}

		return $mDatos;
	}
?>