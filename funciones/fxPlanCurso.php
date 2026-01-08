<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    function fxGuardarPlanCurso($msCurso, $msPeriodo,  $mnHoras,  $mnTurno, $mnRegimen, $mnModalidad, $mnEncuentros,$mbActivo)
    {
        $m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select ifnull(MAX(CAST(SUBSTRING(PLANCURSO_REL, 4) as UNSIGNED)),0) as Ultimo from UMO220A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]) + 1;
		$msCodigo = "PCL" . str_pad($mnNumero, 8, "0", STR_PAD_LEFT);
        $msConsulta = "insert into UMO220A (PLANCURSO_REL, CURSOS_REL, PERIODO_220,  HORAS_220,  TURNO_220, REGIMEN_220, MODALIDAD_220, ENCUENTRO_220, ACTIVO_220) ";
        $msConsulta .= "values(?, ?, ?, ?, ?, ?, ?,  ?,?)";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msCurso, $msPeriodo, $mnHoras,  $mnTurno, $mnRegimen, $mnModalidad, $mnEncuentros,$mbActivo]);
        return ($msCodigo);
    }

    function fxModificarPlanCurso($msCodigo, $msCurso, $msPeriodo,  $mnHoras,  $mnTurno, $mnRegimen, $mnModalidad, $mnEncuentros,$mbActivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "update UMO220A set CURSOS_REL = ?, PERIODO_220 = ?,  HORAS_220 = ?,  TURNO_220 = ?, REGIMEN_220 = ?, MODALIDAD_220 = ?, ACTIVO_220 = ?, ENCUENTRO_220 =? ";
		$msConsulta .= "where PLANCURSO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCurso, $msPeriodo,  $mnHoras,  $mnTurno, $mnRegimen, $mnModalidad, $mbActivo, $mnEncuentros,$msCodigo]);
	}
	
	function fxDevuelvePlanCurso($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select PLANCURSO_REL, NOMBRE_190, PERIODO_220, ACTIVO_220 from UMO220A join UMO190A on UMO220A.CURSOS_REL = UMO190A.CURSOS_REL order by PLANCURSO_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select PLANCURSO_REL, CURSOS_REL, PERIODO_220, HORAS_220, TURNO_220, REGIMEN_220, MODALIDAD_220, ACTIVO_220, ENCUENTRO_220 from UMO220A where PLANCURSO_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}

	function fxBorrarDetPlanCurso($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "delete from UMO221A where PLANCURSO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}

	function fxGuardarDetPlanCurso($msPlanCurso, $msModulo, $mnHPreseciales, $mnHpracticas, $mnHtotal, $mnEncuentros,$mnPeriodo)
{
    $m_cnx_MySQL = fxAbrirConexion();
	
    $msConsulta = "select ifnull(MAX(CONSECUTIVOC_REL),0) as Ultimo from UMO221A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnConsecutivo = intval($mFila["Ultimo"]) + 1;
    $msConsulta = "insert into UMO221A (CONSECUTIVOC_REL, PLANCURSO_REL, MODULO_REL, HRSPRESENCIALES_221, HRSPRACTICA_221, HRSTOTAL_221, ENCUENTROS_221, PERIODO_221) 
                   values (?, ?, ?, ?, ?, ?, ?,?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$mnConsecutivo, $msPlanCurso, $msModulo, $mnHPreseciales, $mnHpracticas, $mnHtotal, $mnEncuentros, $mnPeriodo]);
}
function fxObtenerDetPlanCurso($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select D.CONSECUTIVOC_REL, D.PERIODO_221, C.REGIMEN_220, D.MODULO_REL,
                        D.ENCUENTROS_221, D.HRSPRESENCIALES_221, D.HRSPRACTICA_221, D.HRSTOTAL_221
                   from UMO221A D
                   join UMO220A C on D.PLANCURSO_REL = C.PLANCURSO_REL
                   where D.PLANCURSO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxNombreModulo($codigoModulo, $m_cnx_MySQL) {
    $mDatos = $m_cnx_MySQL->prepare("SELECT NOMBRE_280 FROM UMO280A WHERE MODULO_REL = ?");
    $mDatos->execute([$codigoModulo]);
    $fila = $mDatos->fetch();
    return $fila ? $fila["NOMBRE_280"] : "";
}
?>