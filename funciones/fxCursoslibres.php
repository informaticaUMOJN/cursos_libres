<?php
	//*****CURSOS LIBRES************************************************************//
	function fxGuardarCursosLibres($mnTipoC,$msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin,$mnTotalHoras, $mnFechaInicio, $mnFechaFin, $mDocente, $mnDia, $mnModalidad, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "SELECT IFNULL(MID(MAX(CURSOS_REL), 4), 0) AS Ultimo FROM UMO190A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]) + 1;
		$msCodigo = "CLS" . str_pad($mnNumero, 7, "0", STR_PAD_LEFT);
		$msConsulta = "INSERT INTO UMO190A 
					(CURSOS_REL, TIPOC_190,NOMBRE_190, TURNO_190, HRSINICIO_190, HRSFIN_190, HRSTOTAL_190,FECHAINICIO_190, FECHAFIN_190, DOCENTECL_REL, DIACLASES_190, ASISTENCIA_190, ESTADO_190) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $mnTipoC,$msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnTotalHoras, $mnFechaInicio, $mnFechaFin, $mDocente, $mnDia, $mnModalidad, $mnEstado]);

		$msUpdate = "UPDATE UMO130A SET ACTIVO_130 = ? WHERE CURSOS_REL = ?";
		$mDatosUpdate = $m_cnx_MySQL->prepare($msUpdate);
		$mDatosUpdate->execute([$mnEstado, $msCodigo]);

		return $msCodigo;
	}
	
	function fxModificarCursosLibres($msCodigo, $mnTipoC,$msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin,$mnTotalHoras, $mnFechaInicio, $mnFechaFin, $mDocente, $mnDia, $mnModalidad, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "UPDATE UMO190A 
					SET TIPOC_190 = ?, NOMBRE_190 = ?, TURNO_190 = ?, HRSINICIO_190 = ?, HRSFIN_190 = ?,HRSTOTAL_190=?, FECHAINICIO_190 = ?, FECHAFIN_190 = ?, DOCENTECL_REL = ?, DIACLASES_190 = ?, ASISTENCIA_190 = ?, ESTADO_190 = ? 
					WHERE CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$mnTipoC, $msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin,$mnTotalHoras, $mnFechaInicio, $mnFechaFin, $mDocente, $mnDia, $mnModalidad, $mnEstado, $msCodigo]);
		$msUpdate = "UPDATE UMO130A SET ACTIVO_130 = ? WHERE CURSOS_REL = ?";
		$mDatosUpdate = $m_cnx_MySQL->prepare($msUpdate);
		$mDatosUpdate->execute([$mnEstado, $msCodigo]);
	}
	
	function fxBorrarCursosLibres($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsultaCobros = "DELETE FROM UMO130A WHERE CURSOS_REL = ?";
		$mDatosCobros = $m_cnx_MySQL->prepare($msConsultaCobros);
		$mDatosCobros->execute([$msCodigo]);

        $msConsultaCobros = "DELETE FROM UMO210A WHERE CURSOS_REL = ?";
		$mDatosCobros = $m_cnx_MySQL->prepare($msConsultaCobros);
		$mDatosCobros->execute([$msCodigo]);

		$msConsultaCurso = "DELETE FROM UMO190A WHERE CURSOS_REL = ?";
		$mDatosCurso = $m_cnx_MySQL->prepare($msConsultaCurso);
		$mDatosCurso->execute([$msCodigo]);
	}
	
	function fxDevuelveCursosLibres($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "
            SELECT c.CURSOS_REL, c.ESTADO_190, c.NOMBRE_190,
                CASE c.TURNO_190
                    WHEN 1 THEN 'DIURNO'
                    WHEN 2 THEN 'MATUTINO'
                    WHEN 3 THEN 'VESPERTINO'
                    WHEN 4 THEN 'NOCTURNO'
                    WHEN 5 THEN 'SABATINO'
                    WHEN 6 THEN 'DOMINICAL'
                    ELSE 'SIN DEFINIR'
                END AS TURNO_190,
                CASE TIPOC_190
                    WHEN 0 THEN 'CURSO'
                    WHEN 1 THEN 'CONFERENCIA'
                    WHEN 2 THEN 'TALLER'
                    WHEN 3 THEN 'DIPLOMADO'
                    WHEN 4 THEN 'SEMINARIO'
                    ELSE 'SIN DEFINIR'
                END AS TIPOC_190,
                CASE c.ESTADO_190 WHEN 0 THEN 'Inactivo' ELSE 'Activo' END AS ESTADO_190,
                d.NOMBRE_340 AS NOMBRE_DOCENTE
            FROM UMO190A c
            LEFT JOIN UMO340A d ON c.DOCENTECL_REL = d.DOCENTECL_REL
            ORDER BY c.CURSOS_REL DESC
        ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "
            SELECT c.CURSOS_REL,c.TIPOC_190, c.NOMBRE_190, c.TURNO_190, c.HRSINICIO_190, c.HRSFIN_190, c.HRSTOTAL_190,
                   c.FECHAINICIO_190, c.FECHAFIN_190, c.DOCENTECL_REL, d.NOMBRE_340 AS NOMBRE_DOCENTE,
                   c.DIACLASES_190, c.ASISTENCIA_190, c.ESTADO_190
            FROM UMO190A c
            LEFT JOIN UMO340A d ON c.DOCENTECL_REL = d.DOCENTECL_REL
            WHERE c.CURSOS_REL = ?
        ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}

	
	function fxGenerarCobrosCurso($msCodigo, $valorCertificado, $matricula, $mensualidad, $turno, $mnModalidad, $mnFechaInicio, $mnMoneda, $mnEstado)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "SELECT IFNULL(MID(MAX(COBRO_REL), 4), 0) AS Ultimo FROM UMO130A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnUltimoNumero = intval($mFila["Ultimo"]);

    $cobros = [
        ["MATRICULA", 0, $matricula],
        ["MENSUALIDAD", 1, $mensualidad],
        ["CERTIFICADO", 3, $valorCertificado],
    ];

    foreach ($cobros as $c) {
        $mnUltimoNumero++; 
        $msCobroRel = "CBR" . str_pad($mnUltimoNumero, 4, "0", STR_PAD_LEFT);

        try {
            $msInsert = "INSERT INTO UMO130A 
                        (COBRO_REL, CURSOS_REL, DESC_130, TIPO_130, VALOR_130, MONEDA_130, ACTIVO_130, TURNO_130, MODALIDAD_130, VENCIMIENTO_130) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $mDatosInsert = $m_cnx_MySQL->prepare($msInsert);
            $mDatosInsert->execute([$msCobroRel, $msCodigo, $c[0], $c[1], $c[2], $mnMoneda, $mnEstado, $turno, $mnModalidad, $mnFechaInicio]);
        } catch (PDOException $e) {
            error_log("Error en cobro: " . $e->getMessage());
        }
    }
}
	
	function fxDevuelveCobrosCurso($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "SELECT DESC_130, VALOR_130 FROM UMO130A WHERE CURSOS_REL = ? ";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
			
		$cobros = [
			"MATRICULA"   => 0,
			"MENSUALIDAD" => 0,
			"CERTIFICADO" => 0
		];
		while ($fila = $mDatos->fetch()) {
			$desc = strtoupper($fila["DESC_130"]);
			if (array_key_exists($desc, $cobros)) {
				$cobros[$desc] = floatval($fila["VALOR_130"]);
			}
		}
		return $cobros;
	}
	
	function fxModificarCobrosCurso($msCodigo, $valorCertificado, $matricula, $mensualidad, $turno = 1, $mnModalidad, $mnFechaInicio)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $cobros = [ 
        "MATRICULA"   => $matricula, 
        "MENSUALIDAD" => $mensualidad, 
        "CERTIFICADO" => $valorCertificado 
    ];

    foreach ($cobros as $desc => $valor) {
        $msConsulta = "UPDATE UMO130A 
                       SET VALOR_130 = ?, TURNO_130 = ?, MODALIDAD_130 = ?, VENCIMIENTO_130 = ? 
                       WHERE CURSOS_REL = ? AND UPPER(DESC_130) = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$valor, $turno, $mnModalidad, $mnFechaInicio, $msCodigo, strtoupper($desc)]);
    }
}

?>