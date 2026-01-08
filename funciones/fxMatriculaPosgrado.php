<?php
	//*****MATRICULA POSGRADO************************************************************//
	function fxGuardarMatriculaPos($msEstudiante, $msCarrera, $msCurso, $mdFecha, $mnAnnoIngreso, $msCohorte, $msRecibo,
	$mbTitulo, $mbNotas, $mbCedula, $mbCurriculum, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(MATRICULAPOS_REL), 3), 0) as Ultimo from UMO260A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "MP" . str_repeat("0", 3 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO260A (MATRICULAPOS_REL, ESTUDIANTEPOS_REL, CARRERA_REL, CURSOPOSGRADO_REL, FECHA_260, ";
		$msConsulta .= "ANNOINGRESO_260, COHORTE_260, RECIBO_260, TITULO_260, NOTAS_260, CEDULA_260, CURRICULUM_260, ";
		$msConsulta .= "ESTADO_260) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msEstudiante, $msCarrera, $msCurso, $mdFecha, $mnAnnoIngreso, $msCohorte, $msRecibo,
		$mbTitulo, $mbNotas, $mbCedula, $mbCurriculum, $mnEstado]);
		return $msCodigo;
	}
	
	function fxModificarMatriculaPos($msCodigo, $msEstudiante, $msCarrera, $msCurso, $mdFecha, $mnAnnoIngreso, $msCohorte, $msRecibo,
	$mbTitulo, $mbNotas, $mbCedula, $mbCurriculum, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO260A set ESTUDIANTEPOS_REL = ?, CARRERA_REL = ?, CURSOPOSGRADO_REL = ?, FECHA_260 = ?, ";
		$msConsulta .= "ANNOINGRESO_260 = ?, COHORTE_260 = ?, RECIBO_260 = ?, TITULO_260 = ?, NOTAS_260 = ?, CEDULA_260 = ?, ";
		$msConsulta .= "CURRICULUM_260 = ?, ESTADO_260 = ? where MATRICULAPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msEstudiante, $msCarrera, $msCurso, $mdFecha, $mnAnnoIngreso, $msCohorte, $msRecibo,
		$mbTitulo, $mbNotas, $mbCedula, $mbCurriculum, $mnEstado, $msCodigo]);
	}
	
	function fxBorrarMatriculaPos($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO260A where MATRICULAPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveMatriculaPos($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select MATRICULAPOS_REL, APELLIDO1_250, APELLIDO2_250, NOMBRE1_250, NOMBRE2_250, NOMBRE_040, FECHA_260, ";
			$msConsulta .= "ESTADO_260 from UMO260A, UMO040A, UMO250A where UMO260A.CARRERA_REL = UMO040A.CARRERA_REL and ";
			$msConsulta .= "UMO260A.ESTUDIANTEPOS_REL = UMO250A.ESTUDIANTEPOS_REL order by MATRICULAPOS_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select MATRICULAPOS_REL, ESTUDIANTEPOS_REL, CARRERA_REL, CURSOPOSGRADO_REL, FECHA_260, ";
			$msConsulta .= "ANNOINGRESO_260, COHORTE_260, RECIBO_260, TITULO_260, NOTAS_260, CEDULA_260, CURRICULUM_260, ";
			$msConsulta .= "ESTADO_260 from UMO260A where MATRICULAPOS_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
?>