<?php
function fxGuardarCobrosEstudiantes($mfAdeudado, $mfAbonado, $mnMoneda, $mfDescuento, $mbAnulado, $msMatricula)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "select ifnull(mid(max(COBRO_REL), 4), 0) as Ultimo from UMO131A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "CBRES" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO131A (COBRO_REL, MATRICULA_REL, ADEUDADO_131, ABONADO_131, MONEDA_131, DESCUENTO_131, ANULADO_131) values (?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msMatricula, $mfAdeudado, $mfAbonado, $mnMoneda, $mfDescuento, $mbAnulado]);
    return $msCodigo;
}


function fxDevuelveEncabezado($mbLlenaGrid, $msCodigo = "") //en el gridCbrEstudiante.php
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select UMO030A.CARRERA_REL, UMO030A.MATRICULA_REL, UMO010A.APELLIDO1_010, UMO010A.APELLIDO2_010, UMO010A.NOMBRE1_010, UMO010A.NOMBRE2_010, UMO040A.NOMBRE_040, 
            case 
            when UMO030A.ESTADO_030 = 1 then 'Inactivo'
            else 'Activo'
            end as ESTADO_030,
            SUM(UMO131A.ADEUDADO_131) as ADEUDADO_131,  
            SUM(UMO131A.ABONADO_131) as ABONADO_131,  
            SUM(UMO131A.DESCUENTO_131) as DESCUENTO_131, 
            MAX(UMO131A.MONEDA_131) as MONEDA_131  
            from UMO030A
            join 
            UMO040A ON UMO030A.CARRERA_REL = UMO040A.CARRERA_REL
            join 
            UMO010A ON UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL
            left join 
            UMO131A ON UMO030A.MATRICULA_REL = UMO131A.MATRICULA_REL  
            where  
            UMO030A.ESTADO_030 <> 1 
            and UMO131A.COBRO_REL IS NOT NULL  
            group by 
            UMO030A.CARRERA_REL, UMO030A.MATRICULA_REL,  
            UMO010A.APELLIDO1_010, UMO010A.APELLIDO2_010, 
            UMO010A.NOMBRE1_010, UMO010A.NOMBRE2_010,    
            UMO040A.NOMBRE_040, UMO030A.ESTADO_030
            order by   
            UMO030A.MATRICULA_REL;";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select MATRICULA_REL, ESTUDIANTE_REL, CARRERA_REL from UMO030A where MATRICULA_REL = ?;";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
    function fxObtenerMostrarC($msCodigo) 
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = " 
        select p.COBRO_REL, u.DESC_130,  p.MATRICULA_REL, p.ADEUDADO_131, p.ABONADO_131, p.DESCUENTO_131,
        case 
        when p.ANULADO_131 = 0 then 'No'
        when p.ANULADO_131 = 1 then 'Sí'
        else 'Desconocido'
        end as ANULADO_131,
        case 
        when p.MONEDA_131 = 0 then 'Córdobas'
        when p.MONEDA_131 = 1 then 'Dólares'
        end as MONEDA_131
        from 
        UMO131A p
        join 
        UMO130A u on p.COBRO_REL = u.COBRO_REL 
        where p.MATRICULA_REL = ?; ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]); 
        return $mDatos;
    }    
?>