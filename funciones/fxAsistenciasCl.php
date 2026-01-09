<?php
	function fxGuardarAsistencia($msDocente, $msModulo, $msCursos, $mdFecha, $mnTurno, $mnAnno, $mnModulosLectivos)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(ASISTENCIACL_REL), 4), 0) as Ultimo from UMO320A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "ASC" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO320A (ASISTENCIACL_REL, DOCENTE_REL, MODULO_REL, CURSOS_REL, FECHA_320, TURNO_320, ANNO_320, MODULOLECTIVO_320) values(?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msDocente, $msModulo, $msCursos, $mdFecha, $mnTurno, $mnAnno, $mnModulosLectivos]);
		return $msCodigo;
	}
	function fxModificarAsistencia($msCodigo, $msDocente, $msModulo, $msCursos, $mdFecha, $mnTurno, $mnAnno, $mnModulosLectivos)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO320A set DOCENTE_REL = ?, MODULO_REL = ?, CURSOS_REL = ?, FECHA_320 = ?, TURNO_320 = ?, ANNO_320 = ?, MODULOLECTIVO_320 = ? where ASISTENCIACL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msDocente, $msModulo, $msCursos, $mdFecha, $mnTurno, $mnAnno, $mnModulosLectivos, $msCodigo]);
	}
function fxBorrarAsistencia($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO321A where ASISTENCIACL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		$msConsulta = "delete from UMO320A where ASISTENCIACL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	function fxDevuelveAsistencia($mbLlenaGrid, $msDocente, $msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1) 
			{
				if ($msDocente == "")
					{
						$msConsulta = "SELECT A.ASISTENCIACL_REL, D.NOMBRE_100, M.NOMBRE_280, C.NOMBRE_190, A.FECHA_320, A.TURNO_320, A.ANNO_320, A.MODULOLECTIVO_320 FROM UMO320A A
						INNER JOIN UMO100A D ON A.DOCENTE_REL = D.DOCENTE_REL INNER JOIN UMO280A M ON A.MODULO_REL = M.MODULO_REL INNER JOIN UMO190A C ON A.CURSOS_REL = C.CURSOS_REL
						ORDER BY A.ASISTENCIACL_REL DESC";
						$mDatos = $m_cnx_MySQL->prepare($msConsulta);
						$mDatos->execute();
					} 
					else 
					{
						 $msConsulta = "SELECT A.ASISTENCIACL_REL, D.NOMBRE_100,  M.NOMBRE_280, C.NOMBRE_190, A.FECHA_320, A.TURNO_320, A.ANNO_320, A.MODULOLECTIVO_320
						 FROM UMO320A A INNER JOIN UMO100A D ON A.DOCENTE_REL = D.DOCENTE_REL
						 INNER JOIN UMO280A M ON A.MODULO_REL = M.MODULO_REL INNER JOIN UMO190A C ON A.CURSOS_REL = C.CURSOS_REL
						 WHERE A.DOCENTE_REL = ? ORDER BY A.ASISTENCIACL_REL DESC";
						 $mDatos = $m_cnx_MySQL->prepare($msConsulta);
						 $mDatos->execute([$msDocente]);
					}
			} 
			else
				{   $msConsulta = " SELECT A.ASISTENCIACL_REL, A.DOCENTE_REL, A.CURSOS_REL,A.MODULO_REL,A.FECHA_320, A.TURNO_320,A.ANNO_320,
					A.MODULOLECTIVO_320,D.NOMBRE_100,M.NOMBRE_280,C.NOMBRE_190 FROM UMO320A A INNER JOIN UMO100A D ON D.DOCENTE_REL = A.DOCENTE_REL INNER JOIN UMO280A M ON M.MODULO_REL = A.MODULO_REL
					INNER JOIN UMO190A C ON C.CURSOS_REL = A.CURSOS_REL WHERE A.ASISTENCIACL_REL = ?";
					$mDatos = $m_cnx_MySQL->prepare($msConsulta);
					$mDatos->execute([$msCodigo]);
				}
			return $mDatos;
		}
		function fxGuardarDetAsistencia($msCodigo, $msMatricula, $mnEstado)
		{
			$m_cnx_MySQL = fxAbrirConexion();
			$msConsulta = "insert into UMO321A (ASISTENCIACL_REL, MATCURSO_REL, ESTADO_321) values (?, ?, ?)";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo, $msMatricula, $mnEstado]);
		}
		function fxDevuelveDetAsistencia($msCodigo, $msModulo = "", $mnTurno = 0, $mnAnno = 0, $mnModuloLectivo = 0)
		{
			$m_cnx_MySQL = fxAbrirConexion();
			if ($msCodigo != "") 
			{ 
				$msConsulta = "SELECT UMO321A.ASISTENCIACL_REL, UMO210A.MATCURSO_REL,UMO200A.NOMBRES_200, UMO200A.APELLIDOS_200,UMO321A.ESTADO_321 FROM UMO321A
                INNER JOIN UMO210A ON UMO321A.MATCURSO_REL = UMO210A.MATCURSO_REL
                INNER JOIN UMO200A ON UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL WHERE UMO321A.ASISTENCIACL_REL = ?";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute([$msCodigo]);
    		} else {
				$msConsulta = " SELECT '' AS ASISTENCIACL_REL,UMO210A.MATCURSO_REL,UMO200A.NOMBRES_200, UMO200A.APELLIDOS_200,
						1 AS ESTADO_321 FROM UMO210A INNER JOIN UMO200A ON UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL INNER JOIN UMO220A ON UMO210A.PLANCURSO_REL = UMO220A.PLANCURSO_REL
						INNER JOIN UMO221A ON UMO210A.PLANCURSO_REL = UMO221A.PLANCURSO_REL WHERE UMO221A.MODULO_REL = ? AND UMO220A.TURNO_220 = 1 AND UMO210A.ESTADO_210 = 0 ORDER BY UMO200A.APELLIDOS_200, UMO200A.NOMBRES_200";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute([$msModulo]);
    		}return $mDatos;
		}
function fxBorrarDetAsistencia($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO321A where ASISTENCIACL_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
?>