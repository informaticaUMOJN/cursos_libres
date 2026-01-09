<?php
function fxGuardarDepartamento($msNombre)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(DEPARTAMENTO_REL), 3), 0) as Ultimo from UMO110A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "DP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO110A (DEPARTAMENTO_REL, NOMBRE_110) values(?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msNombre]);
    return ($msCodigo);
}

function fxModificarDepartamento($msCodigo, $msNombre)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "update UMO110A set NOMBRE_110 = ? where DEPARTAMENTO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msNombre, $msCodigo]);
}

function fxDevuelveDepartamento($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        $msConsulta = "select DEPARTAMENTO_REL, NOMBRE_110 from UMO110A order by DEPARTAMENTO_REL desc";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "select DEPARTAMENTO_REL, NOMBRE_110 from UMO110A where DEPARTAMENTO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}
?>