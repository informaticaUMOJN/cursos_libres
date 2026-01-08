<?php
	function fxVerificaUsuario()
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msUsuario = $_SESSION["gsUsuario"];
		$msClave = $_SESSION["gsClave"];
		
		$msConsulta = "Select * from UMO002A where USUARIO_REL =? and CLAVE_002 =?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario, $msClave]);
		$mnRegistros = $mDatos->rowCount();
		return $mnRegistros;
	}
	
	function fxVerificaAdministrador()
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msUsuario = $_SESSION["gsUsuario"];
		
		$msConsulta = "Select * from UMO002A where USUARIO_REL =? and ADMINISTRADOR_002 = 1";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario]);
		$mnRegistros = $mDatos->rowCount();
		return $mnRegistros;
	}

	function fxVerificaSupervisor()
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msUsuario = $_SESSION["gsUsuario"];
		
		$msConsulta = "Select * from UMO002A where USUARIO_REL =? and SUPERVISOR_002 = 1";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario]);
		$mnRegistros = $mDatos->rowCount();
		return $mnRegistros;
	}
	
	function fxVerificaArchivos()
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msUsuario = $_SESSION["gsUsuario"];
		
		$msConsulta = "Select * from UMO002A where USUARIO_REL =? and ARCHIVOS_002 = 1";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario]);
		$mnRegistros = $mDatos->rowCount();
		return $mnRegistros;
	}

	function fxExisteUsuario($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		$msConsulta = "select NOMBRE_002 from UMO002A where USUARIO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$mnRegistros = $mDatos->rowCount();
		return $mnRegistros;
	}
	
	function fxGuardarUsuario($msCodigo, $msNombre, $msCorreo, $msClave, $mbSupervisor, $mbArchivos, $mbEstudiante, $mbAdministrador)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msEncriptado = crypt($msClave, '_appUMOJN');
		$msConsulta = "insert into UMO002A (USUARIO_REL, NOMBRE_002, CORREO_002, CLAVE_002, SUPERVISOR_002, ARCHIVOS_002, ESTUDIANTE_002, ADMINISTRADOR_002, ACTIVO_002) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msCodigo, $msNombre, $msCorreo, $msEncriptado, $mbSupervisor, $mbArchivos, $mbEstudiante, $mbAdministrador, 1]);
	}
	
	function fxModificarUsuario($msCodigo, $msNombre, $msCorreo, $mbSupervisor, $mbArchivos, $mbEstudiante, $mbAdministrador, $mnActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		$msConsulta = "update UMO002A set NOMBRE_002=?, CORREO_002=?, SUPERVISOR_002=?, ARCHIVOS_002=?, ESTUDIANTE_002=?, ADMINISTRADOR_002=?, ACTIVO_002=? where USUARIO_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msNombre, $msCorreo, $mbSupervisor, $mbArchivos, $mbEstudiante, $mbAdministrador, $mnActivo, $msCodigo]);
	}
	
	function fxClaveUsuario($msCodigo, $msClave)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		$msEncriptado = crypt($msClave, '_appUMOJN');
		$msConsulta = "update UMO002A set CLAVE_002=? where USUARIO_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msEncriptado, $msCodigo]);
	}
	
	function fxDesactivarUsuario($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		$msConsulta = "update UMO002A set ACTIVO_002 = 0 where USUARIO_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msCodigo]);
	}
	
	function fxDevuelveUsuario($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select USUARIO_REL, NOMBRE_002, CORREO_002, SUPERVISOR_002, ARCHIVOS_002, ESTUDIANTE_002, ADMINISTRADOR_002, ACTIVO_002 from UMO002A order by USUARIO_REL";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select USUARIO_REL, NOMBRE_002, CORREO_002, CLAVE_002, SUPERVISOR_002, ARCHIVOS_002, ADMINISTRADOR_002, ESTUDIANTE_002, ACTIVO_002 from UMO002A where USUARIO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}
	
	function fxPermisoUsuario($msPagina, &$mbAgregar = 0, &$mbModificar = 0, &$mbBorrar = 0, &$mbAnular = 0)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msUsuario = $_SESSION["gsUsuario"];
		
		$msConsulta = "select UMO005A.PAGINA_REL, UMO005A.GRUPO_REL, INCLUIR_005, MODIFICAR_005, BORRAR_005, ANULAR_005 ";
		$msConsulta .= "from UMO005A, UMO006A where UMO006A.GRUPO_REL = UMO005A.GRUPO_REL and UMO006A.USUARIO_REL = ? and PAGINA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario, $msPagina]);
		$mnRegistros = $mDatos->rowCount();
		if ($mnRegistros > 0)
		{
			while($mFila = $mDatos->fetch())
			{
				if ($mbAgregar == 0)
					$mbAgregar = $mFila["INCLUIR_005"];

				if ($mbModificar == 0)
					$mbModificar = $mFila["MODIFICAR_005"];

				if ($mbBorrar == 0)
					$mbBorrar = $mFila["BORRAR_005"];

				if ($mbAnular == 0)
					$mbAnular = $mFila["ANULAR_005"];
			}
		}
		return $mnRegistros;
	}
?>