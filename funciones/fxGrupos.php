<?php
	//*****GRUPOS************************************************************//
	function fxExisteGrupo($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select NOMBRE_003 from UMO003A where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$mnRegistros = $mDatos->rowCount();
		return $mnRegistros;
	}
	
	function fxGuardarGrupo($msCodigo, $msNombre)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO003A (GRUPO_REL, NOMBRE_003) values(?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msNombre]);
	}
	
	function fxModificarGrupo($msCodigo, $msNombre)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO003A set NOMBRE_003 = ? where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msNombre, $msCodigo]);
	}
	
	function fxBorrarGrupo($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO003A where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveGrupo($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select GRUPO_REL, NOMBRE_003 from UMO003A";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select GRUPO_REL, NOMBRE_003 from UMO003A where GRUPO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
	
	//*****PERMISOS DE LOS GRUPOS********************************************//
	
	function fxGuardarPermiso($msCodigo, $msPagina, $mbAgregar, $mbModificar, $mbBorrar, $mbAnular)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO005A (GRUPO_REL, PAGINA_REL, INCLUIR_005, MODIFICAR_005, BORRAR_005, ANULAR_005) values(?,?,?,?,?,?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msPagina, $mbAgregar, $mbModificar, $mbBorrar, $mbAnular]);
	}
	
	function fxBorrarPermiso($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO005A where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelvePermiso($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select GRUPO_REL, UMO005A.PAGINA_REL, DESC_004, INCLUIR_005, MODIFICAR_005, BORRAR_005, ANULAR_005 from UMO005A join UMO004A on UMO005A.PAGINA_REL = UMO004A.PAGINA_REL where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
	
	//*****USUARIOS DE LOS GRUPOS********************************************//
	
	function fxGuardarUsuarioGrupo($msCodigo, $msUsuario)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO006A (GRUPO_REL, USUARIO_REL) values(?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msUsuario]);
	}
	
	function fxBorrarUsuarioGrupo($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO006A where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveUsuarioGrupo($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select GRUPO_REL, UMO006A.USUARIO_REL, NOMBRE_002 from UMO006A join UMO002A on UMO006A.USUARIO_REL = UMO002A.USUARIO_REL where GRUPO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
?>