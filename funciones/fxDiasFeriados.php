<?php
function fxGuardarDiasFeriados($msAsignatura, $msFecha, $msMotivo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(DIAFERIADO_REL), 3), 0) as Ultimo from UMO090A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "DF" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO090A (DIAFERIADO_REL, ASIGNATURA_REL, FECHA_090, MOTIVO_090) ";
    $msConsulta .= "values(?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msAsignatura, $msFecha, $msMotivo]);
    return ($msCodigo);
}

function fxModificarDiasFeriados($msCodigo, $msAsignatura, $msFecha, $msMotivo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "update UMO090A set ASIGNATURA_REL=?, FECHA_090=?, MOTIVO_090=? where DIAFERIADO_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msAsignatura, $msFecha, $msMotivo, $msCodigo]);
}

function fxBorrarDiasFeriados($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO090A where DIAFERIADO_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveDiasClase($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select DIAFERIADO_REL, NOMBRE_060, NOMBRE_040, FECHA_090 from UMO090A, UMO040A, UMO060A where UMO090A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and UMO060A.CARRERA_REL = UMO040A.CARRERA_REL order by DIAFERIADO_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select DIAFERIADO_REL, ASIGNATURA_REL, FECHA_090, MOTIVO_090 from UMO090A where DIAFERIADO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}
?>