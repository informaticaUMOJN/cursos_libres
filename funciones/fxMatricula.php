<?php
	function fxGuardarMatricula($msEstudiante, $msCarrera, $msPlanEstudio, $mdFecha, $mbPrimerIngreso, $mAnnoIngreso, $mnAnnoAcademico, $mnAnnoLectivo, $mnSemestreAcademico, $msRecibo, $mnBeca, $mbDiploma, $mbNotas, $mbCedula, $mbActaNacimiento, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(MATRICULA_REL), 3), 0) as Ultimo from UMO030A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "MT" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO030A (MATRICULA_REL, ESTUDIANTE_REL, CARRERA_REL, PLANESTUDIO_REL, FECHA_030, PRIMERINGRESO_030, ANNOINGRESO_030, ANNOACADEMICO_030, ANNOLECTIVO_030, SEMESTREACADEMICO_030, RECIBO_030, BECA_030, DIPLOMA_030, NOTAS_030, CEDULA_030, ACTANACIMIENTO_030, ESTADO_030) ";
		$msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msEstudiante, $msCarrera, $msPlanEstudio, $mdFecha, $mbPrimerIngreso, $mAnnoIngreso, $mnAnnoAcademico, $mnAnnoLectivo, $mnSemestreAcademico, $msRecibo, $mnBeca, $mbDiploma, $mbNotas, $mbCedula, $mbActaNacimiento, $mnEstado]);
		return ($msCodigo);
	}
	
	function fxModificarMatricula($msCodigo, $msEstudiante, $msCarrera, $msPlanEstudio, $mdFecha, $mbPrimerIngreso, $mAnnoIngreso, $mnAnnoAcademico, $mnAnnoLectivo, $mnSemestreAcademico, $msRecibo, $mnBeca, $mbDiploma, $mbNotas, $mbCedula, $mbActaNacimiento, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "update UMO030A set ESTUDIANTE_REL = ?, CARRERA_REL = ?, PLANESTUDIO_REL = ?, FECHA_030 = ?, PRIMERINGRESO_030 = ?, ANNOINGRESO_030 = ?, ANNOACADEMICO_030 = ?, ANNOLECTIVO_030 = ?, SEMESTREACADEMICO_030 = ?, ";
		$msConsulta .= "RECIBO_030 = ?, BECA_030 = ?, DIPLOMA_030 = ?, NOTAS_030 = ?, CEDULA_030 = ?, ACTANACIMIENTO_030 = ?, ESTADO_030 = ? where MATRICULA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msEstudiante, $msCarrera, $msPlanEstudio, $mdFecha, $mbPrimerIngreso, $mAnnoIngreso, $mnAnnoAcademico, $mnAnnoLectivo, $mnSemestreAcademico, $msRecibo, $mnBeca, $mbDiploma, $mbNotas, $mbCedula, $mbActaNacimiento, $mnEstado, $msCodigo]);
	}
	
	function fxDevuelveMatricula($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select MATRICULA_REL, APELLIDO1_010, APELLIDO2_010, NOMBRE1_010, NOMBRE2_010, NOMBRE_040, FECHA_030, ESTADO_030 from UMO030A, UMO040A, UMO010A where UMO030A.CARRERA_REL = UMO040A.CARRERA_REL and UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL order by MATRICULA_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select MATRICULA_REL, ESTUDIANTE_REL, CARRERA_REL, PLANESTUDIO_REL, FECHA_030, PRIMERINGRESO_030, ANNOINGRESO_030, ANNOACADEMICO_030, ANNOLECTIVO_030, SEMESTREACADEMICO_030, RECIBO_030, BECA_030, DIPLOMA_030, NOTAS_030, CEDULA_030, ACTANACIMIENTO_030, ESTADO_030 from UMO030A where MATRICULA_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}

	function fxGuardarDetMatricula($msCodigo, $msAsignatura)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO031A (MATRICULA_REL, ASIGNATURA_REL) values (?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msAsignatura]);
	}

	function fxBorrarDetMatricula($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO031A where MATRICULA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
?> 