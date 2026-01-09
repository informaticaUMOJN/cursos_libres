<?php
function fxGuardarCobros($msCarrera, $msCurso, $msDescripcion, $mnTipo, $msMora ,$mnModalidad, $mnRegimen,$mnTurno,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo)
{ 
    try {
        $m_cnx_MySQL = fxAbrirConexion();

        $msConsulta = "SELECT IFNULL(MID(MAX(COBRO_REL), 7), 0) AS Ultimo FROM UMO130A";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
        $mFila = $mDatos->fetch();
        $mnNumero = intval($mFila["Ultimo"]) + 1; 

        $msCodigo = "CBR" . str_pad($mnNumero, 7, "0", STR_PAD_LEFT);

        $msConsulta = "INSERT INTO UMO130A 
            (COBRO_REL, CARRERA_REL, CURSOS_REL, DESC_130, TIPO_130, UMO_COBRO_REL ,MODALIDAD_130, REGIMEN_130, TURNO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130, ACTIVO_130)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msCarrera,$msCurso, $msDescripcion, $mnTipo, $msMora,$mnModalidad, $mnRegimen,$mnTurno ,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo]);

        return $msCodigo;

    } catch (PDOException $e) {
        echo "Error al guardar cobro: " . $e->getMessage();
        exit;
    }
}

	function fxModificarCobros( $msCodigo, $msCarrera, $msCurso, $msDescripcion, 
    $mnTipo, $msMora, $mnModalidad, $mnRegimen, $mnTurno, 
    $mfValor, $mnMoneda, $msFechaVenc, $mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO130A set CARRERA_REL = ?,  CURSOS_REL = ?, DESC_130 = ?,TIPO_130 = ?, UMO_COBRO_REL=?, MODALIDAD_130 =?, REGIMEN_130= ?,TURNO_130=? ,VALOR_130 = ?, MONEDA_130 = ?, VENCIMIENTO_130 = ?, ACTIVO_130 = ? where COBRO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera,$msCurso ,$msDescripcion, $mnTipo, $msMora, $mnModalidad, $mnRegimen,$mnTurno ,$mfValor, $mnMoneda, $msFechaVenc, $mbActivo, $msCodigo]);
	}

	function fxBorrarCobros($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO130A where COBRO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	/*
	function fxDevuelveCobros($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
		{

			$msConsulta = " select UMO130A.COBRO_REL, UMO040A.NOMBRE_040 as NOMBRE_CARRERA, UMO130A.DESC_130, UMO130A.VENCIMIENTO_130, 
			case  when UMO130A.ACTIVO_130 = 1 then 'Activo'
            	else 'Inactivo'
        			end as ACTIVO_130 
    			from UMO130A join UMO040A on UMO130A.CARRERA_REL = UMO040A.CARRERA_REL order by UMO130A.COBRO_REL";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select  COBRO_REL, CARRERA_REL,UMO_COBRO_REL, DESC_130 ,TIPO_130, MODALIDAD_130, REGIMEN_130,TURNO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130,ACTIVO_130  from UMO130A where COBRO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;

	}


	function fxDevuelveCobrosCursos($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		if ($mbLlenaGrid == 1)
		{

			$msConsulta = " select UMO130A.COBRO_REL, UMO190A.NOMBRE_190 as NOMBRE_CURSOS, UMO130A.DESC_130, UMO130A.VENCIMIENTO_130, 
			case  when UMO130A.ACTIVO_130 = 1 then 'Activo'
            	else 'Inactivo'
        			end as ACTIVO_130 
    			from UMO130A join UMO190A on UMO130A.CURSOS_REL = UMO190A.CURSOS_REL order by UMO130A.COBRO_REL";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select  COBRO_REL, CURSOS_REL,UMO_COBRO_REL, DESC_130 ,TIPO_130, MODALIDAD_130, REGIMEN_130,TURNO_130, VALOR_130, MONEDA_130, VENCIMIENTO_130,ACTIVO_130  from UMO130A where COBRO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;

	}

	*/
function fxDevuelveCobros($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1) {
       $msConsulta = " SELECT 
    UMO130A.COBRO_REL, 
    COALESCE(UMO040A.NOMBRE_040, UMO190A.NOMBRE_190, 'Sin nombre') AS NOMBRE_PROGRAMA,
    UMO130A.DESC_130, 
    UMO130A.VENCIMIENTO_130,
    -- Columnas con X según tipo de programa
    CASE WHEN UMO130A.CARRERA_REL IS NOT NULL AND (UMO040A.POSGRADO_040 IS NULL OR UMO040A.POSGRADO_040 = 0) THEN 'X' ELSE '' END AS Carrera,
    CASE WHEN UMO130A.CARRERA_REL IS NOT NULL AND UMO040A.POSGRADO_040 = 1 THEN 'X' ELSE '' END AS Posgrado,
    CASE WHEN UMO130A.CURSOS_REL IS NOT NULL THEN 'X' ELSE '' END AS Curso,
    CASE
        WHEN UMO130A.ACTIVO_130 = 1 THEN 'Activo'
        ELSE 'Inactivo'
    END AS ACTIVO_130
FROM UMO130A
LEFT JOIN UMO040A ON UMO130A.CARRERA_REL = UMO040A.CARRERA_REL
LEFT JOIN UMO190A ON UMO130A.CURSOS_REL = UMO190A.CURSOS_REL
ORDER BY UMO130A.COBRO_REL;
";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    } else {
        $msConsulta = "SELECT c.COBRO_REL, c.CARRERA_REL, c.CURSOS_REL, c.UMO_COBRO_REL, c.DESC_130, c.TIPO_130, c.MODALIDAD_130, c.REGIMEN_130, c.TURNO_130, c.VALOR_130,
                      c.MONEDA_130, c.VENCIMIENTO_130, c.ACTIVO_130, g.POSGRADO_040
               FROM UMO130A c
               LEFT JOIN UMO040A g ON c.CARRERA_REL = g.CARRERA_REL
               WHERE c.COBRO_REL = ?";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}
?>