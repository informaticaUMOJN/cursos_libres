<?php
	function fxGuardarAsistenciaPos($msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(ASISTENCIAPOS_REL), 4), 0) as Ultimo from UMO300A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "ASP" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO300A (ASISTENCIAPOS_REL, DOCENTE_REL, CURSOPOSGRADO_REL, FECHA_300, COHORTE_300, TURNO_300, REGIMEN_300) values(?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen]);
		return $msCodigo;
	}
	
	function fxModificarAsistenciaPos($msCodigo, $msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO300A set DOCENTE_REL = ?, CURSOPOSGRADO_REL = ?, FECHA_300 = ?, COHORTE_300 = ?, TURNO_300 = ?, REGIMEN_300 = ? where ASISTENCIAPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msDocente, $msCurso, $mdFecha, $msCohorte, $mnTurno, $mnRegimen, $msCodigo]);
	}
	
	function fxBorrarAsistenciaPos($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO300A where ASISTENCIAPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveAsistenciaPos($mbLlenaGrid, $msDocente, $msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			if ($msDocente == "")
			{
				$msConsulta = "select ASISTENCIAPOS_REL, NOMBRE_100, NOMBRE_240, NOMBRE_040, FECHA_300, TURNO_300, COHORTE_300, REGIMEN_300 from UMO300A, UMO100A, UMO240A, UMO040A ";
				$msConsulta .= "where UMO300A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO300A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL and ";
				$msConsulta .= "UMO240A.CARRERA_REL = UMO040A.CARRERA_REL order by ASISTENCIAPOS_REL desc";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute();
			}
			else
			{
				$msConsulta = "select ASISTENCIAPOS_REL, NOMBRE_100, NOMBRE_240, NOMBRE_040, FECHA_300, TURNO_300, COHORTE_300, REGIMEN_300 from UMO300A, UMO100A, UMO240A, UMO040A ";
				$msConsulta .= "where UMO300A.DOCENTE_REL = UMO100A.DOCENTE_REL and UMO300A.CURSOPOSGRADO_REL = UMO240A.CURSOPOSGRADO_REL and ";
				$msConsulta .= "UMO240A.CARRERA_REL = UMO040A.CARRERA_REL and UMO300A.DOCENTE_REL = ? order by ASISTENCIAPOS_REL desc";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute([$msDocente]);
			}
		}
		else
		{
			$msConsulta = "select ASISTENCIAPOS_REL, DOCENTE_REL, CURSOPOSGRADO_REL, FECHA_300, COHORTE_300, TURNO_300, REGIMEN_300 from UMO300A where ASISTENCIAPOS_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}

	function fxGuardarDetAsistenciaPos($msCodigo, $msMatricula, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO301A (ASISTENCIAPOS_REL, MATRICULAPOS_REL, ESTADO_301) values (?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msMatricula, $mnEstado]);
	}

	function fxDevuelveDetAsistenciaPos($msCodigo, $msCurso="", $msCohorte = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($msCodigo != "")
		{
			$msConsulta = "select ASISTENCIAPOS_REL, UMO260A.MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, ESTADO_301 ";
			$msConsulta .= "from UMO301A, UMO260A, UMO250A where UMO301A.MATRICULAPOS_REL = UMO260A.MATRICULAPOS_REL and ";
			$msConsulta .= "UMO250A.ESTUDIANTEPOS_REL = UMO260A.ESTUDIANTEPOS_REL and UMO301A.ASISTENCIAPOS_REL = ? ";
			$msConsulta .= "order by APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		else
		{
			$msConsulta = "select '' as ASISTENCIAPOS_REL, UMO260A.MATRICULAPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, 1 as ESTADO_301 ";
			$msConsulta .= "from UMO260A, UMO250A where UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL ";
			$msConsulta .= "and UMO260A.CURSOPOSGRADO_REL = ? and ESTADO_260 = 0 and COHORTE_260 = ? ";
			$msConsulta .= "order by APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCurso, $msCohorte]);
		}

		return $mDatos;
	}

	function fxBorrarDetAsistenciaPos($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO301A where ASISTENCIAPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
?>