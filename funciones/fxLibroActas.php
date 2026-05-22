<?php
    function fxGuardarLibro($msDescripcion, $mdApertura, $msTomo, $mnUltimaPartida)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(LIBRO_REL), 3), 0) as Ultimo from UMO330A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "LB" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO330A (LIBRO_REL, DESC_330, APERTURA_330, TOMO_330, ULTIMAPARTIDA_330) values(?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msDescripcion, $mdApertura, $msTomo, $mnUltimaPartida]);
    return ($msCodigo);
}

function fxModificarLibro($msCodigo, $msDescripcion, $mdApertura, $msTomo, $mnUltimaPartida)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "update UMO330A set DESC_330 = ?, APERTURA_330 = ?, TOMO_330 = ?, ULTIMAPARTIDA_330 = ? where LIBRO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDescripcion, $mdApertura, $msTomo, $mnUltimaPartida, $msCodigo]);
}

function fxDevuelveLibro($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select LIBRO_REL, DESC_330, APERTURA_330 from UMO330A order by LIBRO_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select LIBRO_REL, DESC_330, APERTURA_330, TOMO_330, ULTIMAPARTIDA_330 from UMO330A where LIBRO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}
?>