<?php
function fxGuardarDiasClase($msAsignatura, $msAnnoLectivo, $mnDiaSemana, $msFechaIni, $msFechaFin)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(DIACLASE_REL), 3), 0) as Ultimo from UMO080A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "DC" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO080A (DIACLASE_REL, ASIGNATURA_REL, ANNOLECTIVO_080, DIASEMANA_080, FECHAINI_080, FECHAFIN_080) ";
    $msConsulta .= "values(?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msAsignatura, $msAnnoLectivo, $mnDiaSemana, $msFechaIni, $msFechaFin]);
    return ($msCodigo);
}

function fxModificarDiasClase($msCodigo, $msAsignatura, $msAnnoLectivo, $mnDiaSemana, $msFechaIni, $msFechaFin)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "update UMO080A set ASIGNATURA_REL=?, ANNOLECTIVO_080=?, DIASEMANA_080=?, FECHAINI_080=?, FECHAFIN_080=? ";
    $msConsulta .= "where DIACLASE_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msAsignatura, $msAnnoLectivo, $mnDiaSemana, $msFechaIni, $msFechaFin, $msCodigo]);
}

function fxDevuelveDiasClase($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select DIACLASE_REL, NOMBRE_060, NOMBRE_040, ANNOLECTIVO_080 from UMO080A, UMO040A, UMO060A where UMO080A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and UMO060A.CARRERA_REL = UMO040A.CARRERA_REL order by DIACLASE_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select DIACLASE_REL, ASIGNATURA_REL, ANNOLECTIVO_080, DIASEMANA_080, FECHAINI_080, FECHAFIN_080 from UMO080A where DIACLASE_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}

function fxBorrarDetDiasClase($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO081A where DIACLASE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetDiasClase($msCodigo, $mnSemana, $msFecha, $mbHabil)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO051A (DIACLASE_REL, SEMANA_REL, FECHA_081, HABIL_081 values (?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnSemana, $msFecha, $mbHabil]);
}

function fxObtenerDetDiasClase($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select DIACLASE_REL, SEMANA_REL, FECHA_081, HABIL_081 from UMO081A where DIACLASE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}
?>