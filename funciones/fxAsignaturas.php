<?php
	//*****CARRERAS************************************************************//
	function fxGuardarAsignatura($msCarrera, $msCodAcademico, $msNombre, $msDesc, $mnParciales)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(ASIGNATURA_REL), 3), 0) as Ultimo from UMO060A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "AS" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO060A (ASIGNATURA_REL, CARRERA_REL, CODIGO_060, NOMBRE_060, DESCGRAL_060, PARCIALES_060) values(?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCarrera, $msCodAcademico, $msNombre, $msDesc, $mnParciales]);
		return $msCodigo;
	}
	
	function fxModificarAsignatura($msCodigo, $msCarrera, $msCodAcademico, $msNombre, $msDesc, $mnParciales)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO060A set CARRERA_REL = ?, CODIGO_060 = ?, NOMBRE_060 = ?, DESCGRAL_060 = ?, PARCIALES_060 = ? where ASIGNATURA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera, $msCodAcademico, $msNombre, $msDesc, $mnParciales, $msCodigo]);
	}
	
	function fxBorrarAsignatura($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO060A where ASIGNATURA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveAsignatura($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select ASIGNATURA_REL, NOMBRE_040, CODIGO_060, NOMBRE_060 from UMO060A, UMO040A where UMO060A.CARRERA_REL = UMO040A.CARRERA_REL order by ASIGNATURA_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select ASIGNATURA_REL, CARRERA_REL, CODIGO_060, NOMBRE_060, DESCGRAL_060, PARCIALES_060 from UMO060A where ASIGNATURA_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}

	function fxDevuelveAsignaturaCarrera($msCarrera)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select ASIGNATURA_REL, NOMBRE_060 from UMO060A where CARRERA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera]);

		return $mDatos;
	}

	function fxDevuelveAsignaturaMatricula($msMatricula)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select UMO031A.ASIGNATURA_REL, NOMBRE_060, MATRICULA_REL from UMO031A join UMO060A on UMO031A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL where MATRICULA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMatricula]);

		return $mDatos;
	}
?>