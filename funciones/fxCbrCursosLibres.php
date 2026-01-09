<?php
function fxGuardarCobrosEstudiantes($mfAdeudado, $mfAbonado, $mnMoneda, $mfDescuento, $mbAnulado, $msMatricula)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "select ifnull(mid(max(COBRO_REL), 4), 0) as Ultimo from UMO132A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "CBRES" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "insert into UMO132A (COBRO_REL, MATCURSO_REL, ADEUDADO_132, ABONADO_132, MONEDA_132, DESCUENTO_132, ANULADO_132) values (?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msMatricula, $mfAdeudado, $mfAbonado, $mnMoneda, $mfDescuento, $mbAnulado]);
    return $msCodigo;
}


function fxDevuelveCursos($mbLlenaGrid, $msCodigo = "") //en el gridCbrEstudiante.php
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select UMO210A.CURSOS_REL, UMO210A.MATCURSO_REL, UMO200A.APELLIDOS_200, UMO200A.NOMBRES_200, UMO190A.NOMBRE_190, 
            case 
            when UMO210A.ESTADO_210 = 1 then 'Inactivo'
            else 'Activo'
            end as ESTADO_210,
            SUM(UMO132A.ADEUDADO_132) as ADEUDADO_132,  
            SUM(UMO132A.ABONADO_132) as ABONADO_132,  
            SUM(UMO132A.DESCUENTO_132) as DESCUENTO_132, 
            MAX(UMO132A.MONEDA_132) as MONEDA_132  
            from UMO210A
            join 
            UMO190A ON UMO210A.CURSOS_REL = UMO190A.CURSOS_REL
            join 
            UMO200A ON UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
            left join 
            UMO132A ON UMO210A.MATCURSO_REL = UMO132A.MATCURSO_REL  
            where  
            UMO210A.ESTADO_210 <> 1 
            and UMO132A.COBRO_REL IS NOT NULL  
            group by 
            UMO210A.CURSOS_REL, UMO210A.MATCURSO_REL,  
            UMO200A.APELLIDOS_200, 
            UMO200A.NOMBRES_200,    
            UMO190A.NOMBRE_190, UMO210A.ESTADO_210
            order by   
            UMO210A.MATCURSO_REL;";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select MATCURSO_REL, ALUMNO_REL, CURSOS_REL from UMO210A where MATCURSO_REL = ?;";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}
    function fxObtenerMostrarC($msCodigo) 
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = " 
        select p.COBRO_REL, u.DESC_130,  p.MATCURSO_REL, p.ADEUDADO_132, p.ABONADO_132, p.DESCUENTO_132,
        case 
        when p.ANULADO_132 = 0 then 'No'
        when p.ANULADO_132 = 1 then 'Sí'
        else 'Desconocido'
        end as ANULADO_132,
        case 
        when p.MONEDA_132 = 0 then 'Córdobas'
        when p.MONEDA_132 = 1 then 'Dólares'
        end as MONEDA_132
        from 
        UMO132A p
        join 
        UMO130A u on p.COBRO_REL = u.COBRO_REL 
        where p.MATCURSO_REL = ?; ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]); 
        return $mDatos;
    }    
?>