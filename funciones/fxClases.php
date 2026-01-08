<?php
	//*****CLASES DE LOS CURSOS LIBRES************************************************************//
	function fxGuardarClases( $msModulo, $msCurso, $msNombre, $msContenido, $msC, $msCP, $mnTotal)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(CLASES_REL), 3), 0) as Ultimo from UMO290A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "CLS" . str_repeat("0", 4 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO290A (CLASES_REL, CURSOS_REL, MODULO_REL, NOMBRE_290, CONTENIDO_290, C_290, CP_290, HRSTOTAL_290) values(?, ?, ?, ?, ?, ?,?,?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCurso, $msModulo, $msNombre, $msContenido, $msC, $msCP, $mnTotal]);
		return $msCodigo;
	}
	
	function fxModificarClases($msCodigo, $msModulo, $msCurso, $msNombre, $msContenido, $msC, $msCP, $mnTotal)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO290A set CURSOS_REL = ?, MODULO_REL = ?, NOMBRE_290 = ?, CONTENIDO_290 = ?, C_290 = ?, CP_290 = ?, HRSTOTAL_290 = ? where CLASES_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCurso, $msModulo, $msNombre, $msContenido, $msC, $msCP, $mnTotal, $msCodigo]);
	}
	
	function fxBorrarClases($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO290A where CLASES_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveClases($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1)
    {
        // Consulta para llenar el grid con curso, módulo y clase
        $msConsulta = "
            SELECT 
                UMO190A.NOMBRE_190 AS NOMBRE_CURSO,
                UMO280A.NOMBRE_280 AS NOMBRE_MODULO,
                UMO290A.NOMBRE_290 AS NOMBRE_CLASE,
                UMO290A.CLASES_REL
            FROM 
                UMO290A
            INNER JOIN UMO280A 
                ON UMO290A.MODULO_REL = UMO280A.MODULO_REL 
               AND UMO290A.CURSOS_REL = UMO280A.CURSOS_REL
            INNER JOIN UMO190A 
                ON UMO280A.CURSOS_REL = UMO190A.CURSOS_REL
            ORDER BY 
                UMO290A.CLASES_REL DESC
        ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    }
    else
    {
        // Consulta para obtener información de una clase específica
      $msConsulta = "
    SELECT 
        UMO290A.CLASES_REL,
        UMO290A.MODULO_REL,
        UMO290A.CURSOS_REL,
        UMO290A.NOMBRE_290,
        UMO290A.CONTENIDO_290,
        UMO290A.C_290,
        UMO290A.CP_290,
        UMO290A.HRSTOTAL_290,
        UMO280A.NOMBRE_280 AS NOMBRE_MODULO,
        UMO190A.NOMBRE_190 AS NOMBRE_CURSO
    FROM 
        UMO290A
    INNER JOIN UMO280A 
        ON UMO290A.MODULO_REL = UMO280A.MODULO_REL 
       AND UMO290A.CURSOS_REL = UMO280A.CURSOS_REL
    INNER JOIN UMO190A 
        ON UMO280A.CURSOS_REL = UMO190A.CURSOS_REL
    WHERE 
        UMO290A.CLASES_REL = ?

        ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }

    return $mDatos;
}


	function fxDevuelveClasesCursos($msClases)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select CLASES_REL, NOMBRE_290 from UMO290A where CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msClases]);

		return $mDatos;
	}

	function fxDevuelveAsignaturaMatricula($msMatricula)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		$msConsulta = "select UMO031A.CLASES_REL, NOMBRE_290, MATRICULA_REL from UMO031A join UMO290A on UMO031A.CLASES_REL = UMO290A.CLASES_REL where MATRICULA_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMatricula]);

		return $mDatos;
	}
?>