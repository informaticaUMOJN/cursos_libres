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
        $msConsultaMoneda = "select MONEDA_131 from UMO131A where COBRO_REL = ? and MATRICULA_REL = ?";
        $mDatosMoneda = $m_cnx_MySQL->prepare($msConsultaMoneda);
        $mDatosMoneda->execute([$cobro,$matricula]);
        $monedaData = $mDatosMoneda->fetch();
        $moneda = $monedaData['MONEDA_131'];

        $msConsultaTasa = "select TASACAMBIO_140 from UMO140A where PAGO_REL = ?"; // Obtener la tasa de cambio desde UMO140A para el pago
        $mDatosTasa = $m_cnx_MySQL->prepare($msConsultaTasa);
        $mDatosTasa->execute([$pagoRel]);
        $tasaData = $mDatosTasa->fetch();
        $tasaCambio = $tasaData['TASACAMBIO_140']; 

        $msConsultaMonedaPago = "select MONEDA_140 from UMO140A where PAGO_REL = ?";
        $mDatosMonedaPago = $m_cnx_MySQL->prepare($msConsultaMonedaPago);
        $mDatosMonedaPago->execute([$pagoRel]);
        $monedaPagoData = $mDatosMonedaPago->fetch();
        $monedaPago = $monedaPagoData['MONEDA_140'];
        
        $msConsulta = "select ADEUDADO_131, ABONADO_131, DESCUENTO_131 from UMO131A where COBRO_REL = ? and MATRICULA_REL = ? for update";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$cobro, $matricula]);
        $mFila = $mDatos->fetch();
        $currentAdeudado = $mFila['ADEUDADO_131'];
        $currentAbonado = $mFila['ABONADO_131'];
        $currentDescuento = $mFila['DESCUENTO_131'];
        if ($moneda != $monedaPago)
        {
            if ($moneda == 1 && $monedaPago == 0)
            {
                $abonado = $abonado / $tasaCambio;// Deuda en Dólares, pago en Córdobas → convertir a dólares
                $descuento = $descuento / $tasaCambio;
            } 
            elseif ($moneda == 0 && $monedaPago == 1)
            {
                // Deuda en Córdobas, pago en Dólares → convertir a córdobas
                $abonado = $abonado * $tasaCambio;
                $descuento = $descuento * $tasaCambio;
            }
        }
        $nuevoAdeudado = max(0, $currentAdeudado - $abonado - $descuento); 
        $nuevoDescuento = $currentDescuento + $descuento;
        $nuevoAbonado = $currentAbonado + $abonado; 
        
        $msConsulta = "update UMO131A set ADEUDADO_131 = ?, ABONADO_131 = ?, DESCUENTO_131 = ? where COBRO_REL = ? and MATRICULA_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$nuevoAdeudado, $nuevoAbonado, $nuevoDescuento, $cobro, $matricula]);

        $msConsulta = "insert into UMO141A (PAGO_REL, COBRO_REL, MATRICULA_REL, DESCUENTO_141, VALOR_141, ANULADO_141) values (?, ?, ?, ?, ?, 0)";
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
        //valores de UMO141A
        $msConsulta = "select COBRO_REL, MATRICULA_REL, VALOR_141, DESCUENTO_141 from UMO141A where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$pago_rel]);
        $mFila = $mDatos->fetch();
        
        if (!$mFila) {
            throw new Exception();
        }
        
        $cobro_rel = $mFila['COBRO_REL'];
        $matricula_rel = $mFila['MATRICULA_REL'];
        $valor_141 = $mFila['VALOR_141'];
        $descuento_141 = $mFila['DESCUENTO_141'];  
        
        // valores de UMO140A
        $msConsulta = "select MONEDA_140, CANTIDAD_140 from UMO140A where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$pago_rel]);
        $mFila = $mDatos->fetch();
        
        if (!$mFila) {
            throw new Exception();
        }
        
        $moneda_140 = $mFila['MONEDA_140'];
        $cantidad_140 = $mFila['CANTIDAD_140'];
        // valores de UMO131A
        $msConsulta = "select ADEUDADO_131, ABONADO_131, MONEDA_131, DESCUENTO_131 from UMO131A where COBRO_REL = ? and MATRICULA_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$cobro_rel, $matricula_rel]);
        $mFila = $mDatos->fetch();        
        
        if (!$mFila) {
            throw new Exception("No se encontraron registros en UMO131A para el cobro.");
        }
        
        $moneda_131 = $mFila['MONEDA_131'];
        $adeudado_131 = $mFila['ADEUDADO_131'];
        $abonado_131 = $mFila['ABONADO_131'];
        $descuento_131 = $mFila['DESCUENTO_131'];
    
        if ($moneda_131 == 0 && $moneda_140 == 1) {
            $cantidad_140 *= 36.62; // De dolares a cordobas
        } elseif ($moneda_131 == 1 && $moneda_140 == 0) {
            $cantidad_140 /= 36.62; // De cordobas a dlares
        }
        
        // Actualizar en UMO131A
        $nuevo_adeudado = $adeudado_131 + $valor_141 + $descuento_141;
        $nuevo_abonado = max($abonado_131 - $valor_141, 0);
        $nuevo_descuento_131 = max($descuento_131 - $descuento_141, 0); 
        $msConsulta = "update UMO131A set ADEUDADO_131 = ?, ABONADO_131 = ?, DESCUENTO_131 = ? where COBRO_REL = ? and MATRICULA_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$nuevo_adeudado, $nuevo_abonado, $nuevo_descuento_131, $cobro_rel, $matricula_rel]);        
        
        // Actualizar UMO140A 
        $msConsulta = "update UMO140A set CANTIDAD_140 = ? where PAGO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([0, $pago_rel]);  
        
        // Actualizar UMO141A
        $msConsulta = "update UMO141A set VALOR_141 = ?, DESCUENTO_141 = ?, ANULADO_141 = ? where PAGO_REL = ?";
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
    $msConsulta  = "select u30.CARRERA_REL, u30.MATRICULA_REL, ";
$msConsulta .= "u10.APELLIDO1_010, u10.APELLIDO2_010, ";
$msConsulta .= "u10.NOMBRE1_010, u10.NOMBRE2_010, u40.NOMBRE_040 ";
$msConsulta .= "FROM UMO030A u30 ";
$msConsulta .= "JOIN UMO040A u40 ON u30.CARRERA_REL = u40.CARRERA_REL ";
$msConsulta .= "JOIN UMO010A u10 ON u30.ESTUDIANTE_REL = u10.ESTUDIANTE_REL ";
$msConsulta .= "JOIN UMO131A u131 ON u30.MATRICULA_REL = u131.MATRICULA_REL ";
$msConsulta .= "WHERE u30.ESTADO_030 <> 1 ";
$msConsulta .= "and u131.COBRO_REL is not null ";
$msConsulta .= "and u131.ADEUDADO_131 > 0 ";
$msConsulta .= "GROUP BY u30.CARRERA_REL, u30.MATRICULA_REL, ";
$msConsulta .= "u10.APELLIDO1_010, u10.APELLIDO2_010, ";
$msConsulta .= "u10.NOMBRE1_010, u10.NOMBRE2_010, ";
$msConsulta .= "u40.NOMBRE_040, u30.ESTADO_030 ";
$msConsulta .= "ORDER BY u30.MATRICULA_REL;";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        $msConsulta = "SELECT u30.MATRICULA_REL, u30.ESTUDIANTE_REL, u30.CARRERA_REL
                       FROM UMO030A u30
                       JOIN UMO131A u131 ON u30.MATRICULA_REL = u131.MATRICULA_REL
                       WHERE u30.MATRICULA_REL = ?
                         AND u131.COBRO_REL IS NOT NULL
                         AND u131.ADEUDADO_131 > 0;";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}


    function fxObtenerRealizarPago($msCodigo) //se podria decir que es la tabla de los adeudados c$ o $ del agregar pago
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = " select p.COBRO_REL, u.DESC_130,  p.MATRICULA_REL, p.ADEUDADO_131,p.ABONADO_131, p.DESCUENTO_131, case 
        when p.ANULADO_131 = 0 then 'No' when p.ANULADO_131 = 1 then 'Sí' else 'Desconocido'end as ANULADO_131, case
        when p.MONEDA_131 = 0 then 'Córdobas' when p.MONEDA_131 = 1 then 'Dólares'end as MONEDA_131 from UMO131A p
        join UMO130A u ON p.COBRO_REL = u.COBRO_REL where p.MATRICULA_REL = ? and p.ADEUDADO_131 != 0 and p.ANULADO_131 != 1;  ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]); 
        return $mDatos;
    }    
    function fxMostrarPorPago($msCodigo, $msMatricula) // LLENA LA TABLA DE LA DESCRIPCION POR PAGO en el proDetallePago
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta ="select u141.MATRICULA_REL, u141.PAGO_REL, u141.COBRO_REL, u130.DESC_130, u141.DESCUENTO_141, u141.VALOR_141  ";
        $msConsulta .=" from UMO141A u141 ";
        $msConsulta .=" join UMO130A u130 on u141.COBRO_REL = u130.COBRO_REL";
        $msConsulta .="  where u141.PAGO_REL = ? 
                         and u141.MATRICULA_REL = ? 
                         and u141.VALOR_141 > 0";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msMatricula]); 
        return $mDatos;
    }
    
 /*   function fxDevuelvePagos($mbLlenaGrid, $msCodigo = "")
    {
        $m_cnx_MySQL = fxAbrirConexion();
        if ($mbLlenaGrid == 1) {
            $msConsulta = " select  umo141a.PAGO_REL, UMO030A.MATRICULA_REL, UMO010A.NOMBRE1_010, UMO010A.NOMBRE2_010, UMO010A.APELLIDO1_010,
                UMO010A.APELLIDO2_010, umo141a.COBRO_REL, 
                case 
                when UMO141A.ANULADO_141 = 0 then 'Activo'
                when UMO141A.ANULADO_141 = 1 then 'Inactivo'
                end as ANULADO_141 from umo141a
                join UMO030A ON umo141a.MATRICULA_REL = UMO030A.MATRICULA_REL
                join UMO010A ON UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL 
                where umo141a.PAGO_REL =?";

            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$msCodigo]);
        } else {
            $msConsulta = "select PAGO_REL, RECIBO_140, FECHA_140, MONEDA_140, CANTIDAD_140, CONCEPTO_140, TASACAMBIO_140, TIPO_140 from umo140a";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute();
        }
        return $mDatos;
    }
     */ 
function fxPagosRealizados($mbLlenaGrid, $msCodigo = "") { //DETALLE DEL PAGO GRID - 2
    $m_cnx_MySQL = fxAbrirConexion();
    if ($mbLlenaGrid == 1) {
        $msConsulta = "select UMO030A.MATRICULA_REL, UMO141A.PAGO_REL, ";
        $msConsulta .= "  concat(UMO010A.NOMBRE1_010, ' ', UMO010A.NOMBRE2_010, ' ', UMO010A.APELLIDO1_010, ' ', UMO010A.APELLIDO2_010) as ESTUDIANTE_REL, UMO140A.CONCEPTO_140,";
        $msConsulta .= "  UMO140A.FECHA_140, UMO140A.RECIBO_140,";
        $msConsulta .= " case  when UMO141A.ANULADO_141 = 0 then 'Activo' when UMO141A.ANULADO_141 = 1 then 'Inactivo' end as ANULADO_141 from UMO141A";
        $msConsulta .= " join UMO030A on UMO141A.MATRICULA_REL = UMO030A.MATRICULA_REL
                         join UMO010A on UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL
                         join UMO140A on UMO141A.PAGO_REL = UMO140A.PAGO_REL;";

        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    } else {
        // DEVUELVE UN DETALLE DE PAGO
        $msConsulta = "select UMO030A.MATRICULA_REL, UMO141A.PAGO_REL, UMO131A.COBRO_REL, UMO140A.RECIBO_140, ";
        $msConsulta .= " concat(UMO010A.NOMBRE1_010, ' ', UMO010A.NOMBRE2_010, ' ', UMO010A.APELLIDO1_010, ' ', UMO010A.APELLIDO2_010) as ESTUDIANTE_REL, ";
        $msConsulta .= " UMO140A.CONCEPTO_140,UMO140A.FECHA_140, UMO140A.CANTIDAD_140, UMO140A.RECIBO_140, UMO140A.RECIBI_140, ";
        $msConsulta .= " UMO140A.TASACAMBIO_140, UMO140A.TIPO_140, UMO140A.MONEDA_140 FROM UMO141A ";
        $msConsulta .= " join UMO030A on UMO141A.MATRICULA_REL = UMO030A.MATRICULA_REL
                         join UMO131A on UMO141A.COBRO_REL = UMO131A.COBRO_REL
                         join UMO010A on UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL
                         join UMO140A on UMO141A.PAGO_REL = UMO140A.PAGO_REL 
                         where UMO141A.PAGO_REL = ?";                 
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}
?>