<?php
	//*****CURSOS LIBRES************************************************************//
	function fxGuardarCursosLibres($msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnFechaInicio, $mnFechaFin, $mnGrupo, $mnDia, $mnModalidad, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "SELECT IFNULL(MID(MAX(CURSOS_REL), 4), 0) AS Ultimo FROM UMO190A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]) + 1;
		$msCodigo = "CLS" . str_pad($mnNumero, 7, "0", STR_PAD_LEFT);
		$msConsulta = "INSERT INTO UMO190A 
					(CURSOS_REL, NOMBRE_190, TURNO_190, HRSINICIO_190, HRSFIN_190, FECHAINICIO_190, FECHAFIN_190, GRUPO_190, DIACLASES_190, ASISTENCIA_190, ESTADO_190) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnFechaInicio, $mnFechaFin, $mnGrupo, $mnDia, $mnModalidad, $mnEstado]);

		$msUpdate = "UPDATE UMO130A SET ACTIVO_130 = ? WHERE CURSOS_REL = ?";
		$mDatosUpdate = $m_cnx_MySQL->prepare($msUpdate);
		$mDatosUpdate->execute([$mnEstado, $msCodigo]);

		return $msCodigo;
	}
	
	function fxModificarCursosLibres($msCodigo, $msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnFechaInicio, $mnFechaFin, $mnGrupo, $mnDia, $mnModalidad, $mnEstado)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "UPDATE UMO190A 
					SET NOMBRE_190 = ?, TURNO_190 = ?, HRSINICIO_190 = ?, HRSFIN_190 = ?, FECHAINICIO_190 = ?, FECHAFIN_190 = ?, GRUPO_190 = ?, DIACLASES_190 = ?, ASISTENCIA_190 = ?, ESTADO_190 = ? 
					WHERE CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msNombre, $mnTurno, $mnHoraInicio, $mnHoraFin, $mnFechaInicio, $mnFechaFin, $mnGrupo, $mnDia, $mnModalidad, $mnEstado, $msCodigo]);
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

		$msConsultaCurso = "DELETE FROM UMO190A WHERE CURSOS_REL = ?";
		$mDatosCurso = $m_cnx_MySQL->prepare($msConsultaCurso);
		$mDatosCurso->execute([$msCodigo]);
	}
	
	function fxDevuelveCursosLibres($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
			{
				$msConsulta = "SELECT  CURSOS_REL,  NOMBRE_190,  CASE TURNO_190
				WHEN 1 THEN 'DIURNO'
				WHEN 2 THEN 'MATUTINO'
				WHEN 3 THEN 'VESPERTINO'
				WHEN 4 THEN 'NOCTURNO'
				WHEN 5 THEN 'SABATINO'
				WHEN 6 THEN 'DOMINICAL'
				ELSE 'SIN DEFINIR'
				END AS TURNO_190 FROM UMO190A";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute();
			}
			else
			{
				$msConsulta = "select CURSOS_REL, NOMBRE_190, TURNO_190, HRSINICIO_190,HRSFIN_190, FECHAINICIO_190, FECHAFIN_190, GRUPO_190, DIACLASES_190, ASISTENCIA_190, ESTADO_190 from UMO190A where CURSOS_REL = ?";
				$mDatos = $m_cnx_MySQL->prepare($msConsulta);
				$mDatos->execute([$msCodigo]);
			}
			return $mDatos;
	}
	
	function fxGenerarCobrosCurso($msCodigo, $valorCertificado, $matricula, $mensualidad, $turno, $mnModalidad, $mnFechaInicio, $mnMoneda, $mnEstado)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $cobros = [
        ["MATRICULA", 0, $matricula],
        ["MENSUALIDAD", 1, $mensualidad],
        ["CERTIFICADO", 3, $valorCertificado],
    ];

    foreach ($cobros as $c) {
        try {
            $msConsulta = "SELECT IFNULL(MID(MAX(COBRO_REL), 4), 0) AS Ultimo FROM UMO130A";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute();
            $mFila = $mDatos->fetch();
            $mnNumero = intval($mFila["Ultimo"]) + 1;
            $msCobroRel = "CBR" . str_pad($mnNumero, 4, "0", STR_PAD_LEFT);

            $msInsert = "INSERT INTO UMO130A 
                        (COBRO_REL, CURSOS_REL, DESC_130, TIPO_130, VALOR_130, MONEDA_130, ACTIVO_130, TURNO_130, MODALIDAD_130, VENCIMIENTO_130) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $mDatosInsert = $m_cnx_MySQL->prepare($msInsert);
            $mDatosInsert->execute([$msCobroRel, $msCodigo, $c[0], $c[1], $c[2], $mnMoneda, $mnEstado, $turno, $mnModalidad, $mnFechaInicio]);
        } catch (PDOException $e) {
            echo "Error al generar cobro: " . $e->getMessage();
        }
    }
}


	
	function fxDevuelveCobrosCurso($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "SELECT DESC_130, VALOR_130 FROM UMO130A WHERE CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
			
		$cobros = [
			"MATRICULA"   => 0,
			"MENSUALIDAD" => 0,
		//	"MORA"        => 0,
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
	
	function fxModificarCobrosCurso($msCodigo, $valorCertificado, /*$valorMora,*/ $matricula, $mensualidad, $turno = 1, $mnModalidad, $mnFechaInicio)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $cobros = [ 
        "MATRICULA"   => $matricula, 
        "MENSUALIDAD" => $mensualidad, 
       // "MORA"        => $valorMora,
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