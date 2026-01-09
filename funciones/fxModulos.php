<?php
//***** MODULOS DE LOS CURSOS LIBRES ************************************************************//

function fxGuardarModulo($msCurso, $msNombre, $msDocente )
{
        $m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "SELECT IFNULL(MAX(CAST(SUBSTRING(MODULO_REL, 4) AS UNSIGNED)),0) AS Ultimo FROM UMO280A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]) + 1;
		$msCodigo = "MCL" . str_pad($mnNumero, 4, "0", STR_PAD_LEFT);
        $msConsulta = "insert into UMO280A (MODULO_REL, CURSOS_REL, NOMBRE_280, DOCENTE_REL ) VALUES (?,?, ?, ?)";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo, $msCurso, $msNombre, $msDocente]);
        return ($msCodigo);
}

function fxModificarModulo($msCodigo, $msCurso, $msNombre, $msDocente )
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "UPDATE UMO280A SET CURSOS_REL=?, NOMBRE_280=?, DOCENTE_REL=? WHERE MODULO_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCurso, $msNombre, $msDocente, $msCodigo]);
}


function fxBorrarModulo($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "DELETE FROM UMO280A WHERE MODULO_REL=?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveModulo($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1) {
        $msConsulta = " SELECT m.MODULO_REL, m.CURSOS_REL, c.NOMBRE_190 AS NOMBRE_190, m.NOMBRE_280 
            FROM UMO280A m
            JOIN UMO190A c ON m.CURSOS_REL = c.CURSOS_REL
            ORDER BY m.MODULO_REL DESC ";
        $mDatos = $m_cnx_MySQL->query($msConsulta);
    } else {
        $msConsulta = "SELECT m.MODULO_REL,m.DOCENTE_REL, m.CURSOS_REL, c.NOMBRE_190 AS NOMBRE_190, m.NOMBRE_280 
            FROM UMO280A m
            JOIN UMO190A c ON m.CURSOS_REL = c.CURSOS_REL
            WHERE m.MODULO_REL = ?
        ";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}

function fxDevuelveModuloCurso($msCurso)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select MODULO_REL, NOMBRE_280 from UMO280A WHERE CURSOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCurso]);
		return $mDatos;
	}

	function fxDevuelveModuloMatricula($msMatricula)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select UMO211A.MODULO_REL, NOMBRE_280, MATCURSO_REL FROM UMO211A JOIN UMO280A ON UMO211A.MODULO_REL = UMO280A.MODULO_REL WHERE MATCURSO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msMatricula]);
		return $mDatos;
	}
?>