<?php
function fxGuardarMatriculaCursos($msAlumno, $msCursos, $msPlanEstudio, $mdFecha, $msRecibo, $mnBeca, $mbDiploma, $mbCedula, $mbActaNacimiento, $mnEstado)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "SELECT IFNULL(MID(MAX(MATCURSO_REL), 5), 0) AS Ultimo FROM UMO210A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $msCodigo = "MTCL" . str_pad($mnNumero, 8, "0", STR_PAD_LEFT);
    $msConsulta = "INSERT INTO UMO210A (MATCURSO_REL, ALUMNO_REL, CURSOS_REL,PLANCURSO_REL, FECHA_210, RECIBO_210, BECA_210, DIPLOMA_210, CEDULA_210, ACTANACIMIENTO_210,ESTADO_210) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msAlumno, $msCursos, $msPlanEstudio, $mdFecha, $msRecibo, $mnBeca, $mbDiploma, $mbCedula, $mbActaNacimiento, $mnEstado]);
    return $msCodigo;
}

function fxModificarMatricula($msCodigo, $msAlumno, $msCursos, $msPlanEstudio, $mdFecha, $msRecibo, $mnBeca, $mbDiploma,  $mbCedula, $mbActaNacimiento, $mnEstado)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "UPDATE UMO210A SET 
    ALUMNO_REL = ?, CURSOS_REL = ?, PLANCURSO_REL = ?, FECHA_210 = ?, RECIBO_210 = ?, BECA_210 = ?, DIPLOMA_210 = ?, CEDULA_210 = ?, ACTANACIMIENTO_210 = ?, ESTADO_210 = ? WHERE MATCURSO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msAlumno, $msCursos, $msPlanEstudio, $mdFecha, $msRecibo, $mnBeca, $mbDiploma, $mbCedula, $mbActaNacimiento, $mnEstado, $msCodigo]);
}

function fxDevuelveMatriculaCurso($mbLlenaGrid, $msCodigo = "")
{
    $m_cnx_MySQL = fxAbrirConexion();
    if ($mbLlenaGrid == 1) {
        $msConsulta = "SELECT MATCURSO_REL, APELLIDOS_200, NOMBRES_200, NOMBRE_190,FECHA_210, ESTADO_210 FROM UMO210A 
        JOIN UMO200A ON UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
        JOIN UMO190A ON UMO210A.CURSOS_REL = UMO190A.CURSOS_REL
        ORDER BY MATCURSO_REL DESC;";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute();
    } else {
        $msConsulta = "SELECT * FROM UMO210A WHERE MATCURSO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    return $mDatos;
}
?>