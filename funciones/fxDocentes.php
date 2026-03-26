<?php
	function fxGuardarDocentes($msUsuario, $msNombre, $mnTipo, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		/*Verifica la existencia del usuario*/
		$msConsulta = "select NOMBRE_002 from UMO002A where USUARIO_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msUsuario]);
		$mnRegistros = $mResultado->rowCount();

		if ($mnRegistros == 0)
		{
			/*Crea el usuario del docente en UMO002A*/
			$msCorreo = $msUsuario . "@umojn.edu.ni";
			$msEncriptado = crypt($msUsuario, '_appUMOJN'); //La clave es el mismo nombre de usuario
			$msConsulta = "insert into UMO002A (USUARIO_REL, NOMBRE_002, CORREO_002, CLAVE_002, SUPERVISOR_002, ARCHIVOS_002, ESTUDIANTE_002, ADMINISTRADOR_002, ACTIVO_002) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$mResultado = $m_cnx_MySQL->prepare($msConsulta);
			$mResultado->execute([$msUsuario, $msNombre, $msCorreo, $msEncriptado, 0, 0, 0, 0, 1]);

			
			/*Se agrega al grupo DOCENTES en UMO006A*/
			$msConsulta = "insert into UMO006A (USUARIO_REL, GRUPO_REL) values (?, ?)";
			$mResultado = $m_cnx_MySQL->prepare($msConsulta);
			$mResultado->execute([$msUsuario, "DOCENTES"]); //Grupo pre-establecido
		}

		$msConsulta = "Select ifnull(mid(max(DOCENTE_REL), 3), 0) as Ultimo from UMO100A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "DC" . str_repeat("0", 6 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO100A (DOCENTE_REL, USUARIO_REL, NOMBRE_100, TIPO_100, ACTIVO_100) ";
		$msConsulta .= "values(?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msUsuario, $msNombre, $mnTipo, $mbActivo]);
		return ($msCodigo);
	}
	
	function fxModificarDocentes($msCodigo, $msUsuario, $msNombre, $mnTipo, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO100A set USUARIO_REL = ?, NOMBRE_100 = ?, TIPO_100 = ?";
		$msConsulta .= ", ACTIVO_100 = ? where DOCENTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUsuario, $msNombre, $mnTipo, $mbActivo, $msCodigo]);
	}
	
	function fxBorrarDocentes($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO100A where DOCENTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveDocentes($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select DOCENTE_REL, NOMBRE_100, (case ACTIVO_100 when 1 then 'x' else '' end) as ACTIVO_100 from UMO100A order by DOCENTE_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select DOCENTE_REL, USUARIO_REL, NOMBRE_100, TIPO_100, ACTIVO_100 from UMO100A where DOCENTE_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}

		return $mDatos;
	}
?>