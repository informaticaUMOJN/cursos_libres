<?php
function fxGuardarPagos($msRecibi, $mnRecibo, $msFecha, $mnMoneda, $mnCantidad, $msConcepto, $msTasa, $msTipo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "select ifnull(MID(MAX(PAGO_REL), 6), 0) as Ultimo from UMO140A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "PGS" . str_repeat("0", 6 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO140A (PAGO_REL, RECIBI_140, RECIBO_140, FECHA_140, MONEDA_140, CANTIDAD_140, CONCEPTO_140, TASACAMBIO_140, TIPO_140) 
                   values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msRecibi, $mnRecibo, $msFecha, $mnMoneda, $mnCantidad, $msConcepto, $msTasa, $msTipo]);
    return $msCodigo;
}

function fxGuardarDetPago($cobro, $matricula, $abonado, $descuento, $pagoRel)
{
    $m_cnx_MySQL = fxAbrirConexion(); 
    try 
    {
        $m_cnx_MySQL->beginTransaction();

        $msConsultaMoneda = "select MONEDA_132 from UMO132A where COBRO_REL = ? and MATCURSO_REL = ?";
        $mDatosMoneda = $m_cnx_MySQL->prepare($msConsultaMoneda);
        $mDatosMoneda->execute([$cobro,$matricula]);
        $monedaData = $mDatosMoneda->fetch();
        $moneda = $monedaData['MONEDA_132'];

        $msConsultaTasa = "select TASACAMBIO_140 from UMO140A where PAGO_REL = ?";
        $mDatosTasa = $m_cnx_MySQL->prepare($msConsultaTasa);
        $mDatosTasa->execute([$pagoRel]);
        $tasaData = $mDatosTasa->fetch();
        $tasaCambio = $tasaData['TASACAMBIO_140']; 

        $msConsultaMonedaPago = "select MONEDA_140 from UMO140A where PAGO_REL = ?";
        $mDatosMonedaPago = $m_cnx_MySQL->prepare($msConsultaMonedaPago);
        $mDatosMonedaPago->execute([$pagoRel]);
        $monedaPagoData = $mDatosMonedaPago->fetch();
        $monedaPago = $monedaPagoData['MONEDA_140'];
        
        $msConsulta = "select ADEUDADO_132, ABONADO_132, DESCUENTO_132 from UMO132A where COBRO_REL = ? and MATCURSO_REL = ? for update";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$cobro, $matricula]);
        $mFila = $mDatos->fetch();
        $currentAdeudado = $mFila['ADEUDADO_132'];
        $currentAbonado = $mFila['ABONADO_132'];
        $currentDescuento = $mFila['DESCUENTO_132'];

        if ($moneda != $monedaPago)
        {
            if ($moneda == 1 && $monedaPago == 0)
            {
                $abonado = $abonado / $tasaCambio;
                $descuento = $descuento / $tasaCambio;
            } 
            elseif ($moneda == 0 && $monedaPago == 1)
            {
                $abonado = $abonado * $tasaCambio;
                $descuento = $descuento * $tasaCambio;
            }
        }

        $nuevoAdeudado = max(0, $currentAdeudado - $abonado - $descuento); 
        $nuevoDescuento = $currentDescuento + $descuento;
        $nuevoAbonado = $currentAbonado + $abonado; 
        
        $msConsulta = "update UMO132A set ADEUDADO_132 = ?, ABONADO_132 = ?, DESCUENTO_132 = ? where COBRO_REL = ? and MATCURSO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$nuevoAdeudado, $nuevoAbonado, $nuevoDescuento, $cobro, $matricula]);

      $msConsulta = "insert into UMO142A (PAGO_REL, COBRO_REL, MATCURSO_REL, DESCUENTO_142, VALOR_142, ANULADO_142) values (?, ?, ?, ?, ?, 0)";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$pagoRel, $cobro, $matricula, $descuento, $abonado]);

         $m_cnx_MySQL->commit();
        return true;
    } catch (Exception $e){
        $m_cnx_MySQL->rollBack();
        return false;
    } 
}

function fxAnularPagos($pago_rel)
{
    $m_cnx_MySQL = fxAbrirConexion();
    try {
        $m_cnx_MySQL->beginTransaction(); 

        $msConsulta = "select COBRO_REL, MATCURSO_REL, VALOR_142, DESCUENTO_142 from UMO142A where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$pago_rel]);
        $mFila = $mDatos->fetch();
        if (!$mFila) throw new Exception(); 

        $cobro_rel = $mFila['COBRO_REL'];
        $MATCURSO_REL = $mFila['MATCURSO_REL'];
        $valor_142 = $mFila['VALOR_142'];
        $descuento_142 = $mFila['DESCUENTO_142'];   

        $msConsulta = "select MONEDA_140, CANTIDAD_140 from UMO140A where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$pago_rel]);
        $mFila = $mDatos->fetch(); 
        if (!$mFila) throw new Exception();

        $moneda_140 = $mFila['MONEDA_140'];
        $cantidad_140 = $mFila['CANTIDAD_140']; 

        $msConsulta = "select ADEUDADO_132, ABONADO_132, MONEDA_132, DESCUENTO_132 from UMO132A where COBRO_REL = ? and MATCURSO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$cobro_rel, $MATCURSO_REL]);
        $mFila = $mDatos->fetch();     
        if (!$mFila) throw new Exception("No se encontraron registros en UMO132A para el cobro.");

        $moneda_132 = $mFila['MONEDA_132'];
        $adeudado_132 = $mFila['ADEUDADO_132'];
        $abonado_132 = $mFila['ABONADO_132'];
        $descuento_132 = $mFila['DESCUENTO_132']; 

        if ($moneda_132 == 0 && $moneda_140 == 1) $cantidad_140 *= 36.62;
        elseif ($moneda_132 == 1 && $moneda_140 == 0) $cantidad_140 /= 36.62;

        $nuevo_adeudado = $adeudado_132 + $valor_142 + $descuento_142;
        $nuevo_abonado = max($abonado_132 - $valor_142, 0);
        $nuevo_descuento_132 = max($descuento_132 - $descuento_142, 0); 

        $msConsulta = "update UMO132A set ADEUDADO_132 = ?, ABONADO_132 = ?, DESCUENTO_132 = ? where COBRO_REL = ? and MATCURSO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$nuevo_adeudado, $nuevo_abonado, $nuevo_descuento_132, $cobro_rel, $MATCURSO_REL]);        

        $msConsulta = "update UMO140A set CANTIDAD_140 = ? where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([0, $pago_rel]);   

        $msConsulta = "update UMO142A set VALOR_142 = ?, DESCUENTO_142 = ?, ANULADO_142 = ? where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([0, 0, 1, $pago_rel]); 

        $m_cnx_MySQL->commit(); 
    } catch (Exception $e) {
        $m_cnx_MySQL->rollBack();
    }
}

function fxDevuelveEncabezadoP($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();    

    if ($mbLlenaGrid == 1)
    {
        $msConsulta  = "select u30.CURSOS_REL, u30.MATCURSO_REL, ";
        $msConsulta .= "u10.APELLIDOS_200, u10.NOMBRES_200, u40.NOMBRE_190 ";
        $msConsulta .= "FROM UMO210A u30 ";
        $msConsulta .= "JOIN UMO190A u40 ON u30.CURSOS_REL = u40.CURSOS_REL ";
        $msConsulta .= "JOIN UMO200A u10 ON u30.ALUMNO_REL = u10.ALUMNO_REL ";
        $msConsulta .= "JOIN UMO132A u132 ON u30.MATCURSO_REL = u132.MATCURSO_REL ";
        $msConsulta .= "WHERE u30.ESTADO_210 <> 1 ";
        $msConsulta .= "and u132.COBRO_REL is not null ";
        $msConsulta .= "and u132.ADEUDADO_132 > 0 ";
        $msConsulta .= "GROUP BY u30.CURSOS_REL, u30.MATCURSO_REL, ";
        $msConsulta .= "u10.APELLIDOS_200, u10.NOMBRES_200, ";
        $msConsulta .= "u40.NOMBRE_190, u30.ESTADO_210 ";
        $msConsulta .= "ORDER BY u30.MATCURSO_REL;";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "SELECT u30.MATCURSO_REL, u30.ALUMNO_REL, u30.CURSOS_REL FROM UMO210A u30 JOIN UMO132A u132 ON u30.MATCURSO_REL = u132.MATCURSO_REL
                       WHERE u30.MATCURSO_REL = ?  AND u132.COBRO_REL IS NOT NULL  AND u132.ADEUDADO_132 > 0;";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}

function fxObtenerRealizarPago($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "select p.COBRO_REL, u.DESC_130, p.MATCURSO_REL, p.ADEUDADO_132, p.ABONADO_132, p.DESCUENTO_132,
                   case when p.ANULADO_132 = 0 then 'No' when p.ANULADO_132 = 1 then 'Sí' else 'Desconocido' end as ANULADO_132,
                   case when p.MONEDA_132 = 0 then 'Córdobas' when p.MONEDA_132 = 1 then 'Dólares' end as MONEDA_132
                   from UMO132A p
                   join UMO130A u ON p.COBRO_REL = u.COBRO_REL
                   where p.MATCURSO_REL = ? and p.ADEUDADO_132 != 0 and p.ANULADO_132 != 1;";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]); 
    return $mDatos;
}

function fxMostrarPorPago($msCodigo, $msMatricula)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta ="select u142.MATCURSO_REL, u142.PAGO_REL, u142.COBRO_REL, u130.DESC_130, u142.DESCUENTO_142, u142.VALOR_142
                  from UMO142A u142
                  join UMO130A u130 on u142.COBRO_REL = u130.COBRO_REL
                  where u142.PAGO_REL = ? and u142.MATCURSO_REL = ? and u142.VALOR_142 > 0";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msMatricula]); 
    return $mDatos;
}

function fxPagosRealizados($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    if ($mbLlenaGrid == 1) {
        $msConsulta = "select UMO210A.MATCURSO_REL, UMO142A.PAGO_REL,
                       concat(UMO200A.NOMBRES_200, ' ', UMO200A.APELLIDOS_200) as ALUMNO_REL,
                       UMO140A.CONCEPTO_140, UMO140A.FECHA_140, UMO140A.RECIBO_140,
                       case when UMO142A.ANULADO_142 = 0 then 'Activo' when UMO142A.ANULADO_142 = 1 then 'Inactivo' end as ANULADO_142
                       from UMO142A
                       join UMO210A on UMO142A.MATCURSO_REL = UMO210A.MATCURSO_REL
                       join UMO200A on UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
                       join UMO140A on UMO142A.PAGO_REL = UMO140A.PAGO_REL;";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    } else {
        $msConsulta = "select UMO210A.MATCURSO_REL, UMO142A.PAGO_REL, UMO132A.COBRO_REL, UMO140A.RECIBO_140,
                       concat(UMO200A.NOMBREs_200, ' ', UMO200A.APELLIDOS_200) as ALUMNO_REL,
                       UMO140A.CONCEPTO_140, UMO140A.FECHA_140, UMO140A.CANTIDAD_140, UMO140A.RECIBO_140, UMO140A.RECIBI_140,
                       UMO140A.TASACAMBIO_140, UMO140A.TIPO_140, UMO140A.MONEDA_140
                       FROM UMO142A
                       join UMO210A on UMO142A.MATCURSO_REL = UMO210A.MATCURSO_REL
                       join UMO132A on UMO142A.COBRO_REL = UMO132A.COBRO_REL
                       join UMO200A on UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
                       join UMO140A on UMO142A.PAGO_REL = UMO140A.PAGO_REL
                       where UMO142A.PAGO_REL = ?";                 
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}
?>