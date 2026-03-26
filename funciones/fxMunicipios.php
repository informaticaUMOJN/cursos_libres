<?php
function fxGuardarMunicipio($msDepartamento, $msNombre)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(MUNICIPIO_REL), 3), 0) as Ultimo from UMO120A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "DP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO120A (MUNICIPIO_REL, DEPARTAMENTO_REL, NOMBRE_120) values(?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msDepartamento, $msNombre]);
    return ($msCodigo);
}

function fxModificarMunicipio($msCodigo, $msDepartamento, $msNombre)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "update UMO120A set DEPARTAMENTO_REL = ?, NOMBRE_120 = ? where MUNICIPIO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDepartamento, $msNombre, $msCodigo]);
}

function fxDevuelveMunicipio($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select MUNICIPIO_REL, NOMBRE_110, NOMBRE_120 from UMO120A join UMO110A on UMO120A.DEPARTAMENTO_REL = UMO110A.DEPARTAMENTO_REL order by MUNICIPIO_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select MUNICIPIO_REL, DEPARTAMENTO_REL, NOMBRE_120 from UMO120A where MUNICIPIO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}
?>