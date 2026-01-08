<?php
/* FUNCIONES DEL GRID DIAS FERIADOS  TENGA EN CUENTA QUE DNH SIGNIFICA DIAS NO HABILES*/
function fxGuardarNHabiles( $msFecha, $msMotivo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(DIASNOHABILES_REL), 8), 0) as Ultimo from UMO007A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "DNH" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO007A (DIASNOHABILES_REL,  FECHA_007, MOTIVO_007) ";
    $msConsulta .= "values(?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msFecha, $msMotivo]);
    return ($msCodigo);
}

function fxModificarNHabiles($msCodigo, $msFecha, $msMotivo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "update UMO007A set  FECHA_007=?, MOTIVO_007=? where DIASNOHABILES_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([ $msFecha, $msMotivo, $msCodigo]);
}

function fxBorrarNHabiles($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "delete from UMO007A where DIASNOHABILES_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveNHabiles($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select DIASNOHABILES_REL, MOTIVO_007, DATE_FORMAT(FECHA_007, '%d-%m-%Y') as FECHA_007 from UMO007A order by DIASNOHABILES_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select DIASNOHABILES_REL,  FECHA_007, MOTIVO_007 from UMO007A where DIASNOHABILES_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}
?>