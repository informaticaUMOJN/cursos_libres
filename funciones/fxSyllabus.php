<?php
function fxGuardarSyllabus($msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno, $msGrupo, $msMediacion, $msEjesValores)
{
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "Select ifnull(mid(max(SYLLABUS_REL), 3), 0) as Ultimo from UMO070A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]);
    $mnNumero += 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "SY" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
    //La variable $msMediacion se relaciona con el campo RECOMENDACIONES_070 con el fin de no modificar la tabla
    $msConsulta = "insert into UMO070A (SYLLABUS_REL, PLANESTUDIO_REL, DOCENTE_REL, ASIGNATURA_REL, FECHA_070, ANNO_070, SEMESTRE_170, ";
    $msConsulta .= "TURNO_070, GRUPO_070, RECOMENDACIONES_070, EJESVALORES_070, APROBADO_070, ACTIVO_070) ";
    $msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno, $msGrupo, $msMediacion, $msEjesValores, 0, 1]);
    return ($msCodigo);
}

function fxModificarSyllabus($msCodigo, $msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno, $msGrupo, $msMediacion, $msEjesValores)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "update UMO070A set PLANESTUDIO_REL = ?, DOCENTE_REL = ?, ASIGNATURA_REL = ?, FECHA_070 = ?, ANNO_070 = ?, SEMESTRE_170 = ?, TURNO_070 = ?, GRUPO_070 = ?, RECOMENDACIONES_070 = ?, ";
    $msConsulta .= "EJESVALORES_070 = ? where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msPlanEstudio, $msDocente, $msAsignatura, $mdFecha, $mnAnno, $mnSemestre, $mnTurno, $msGrupo, $msMediacion, $msEjesValores, $msCodigo]);
}

function fxBorrarSyllabus($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO075A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO074A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO073A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO072A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO071A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    $msConsulta = "delete from UMO070A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxDevuelveSyllabus($mbLlenaGrid, $msDocente, $msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();
    
    if ($mbLlenaGrid == 1)
    {
        if ($msDocente == "")
        {
            $msConsulta = "select SYLLABUS_REL, NOMBRE_100, NOMBRE_060, FECHA_070, ANNO_070, SEMESTRE_070, TURNO_070, GRUPO_070, APROBADO_070, ACTIVO_070 ";
            $msConsulta .= "from UMO070A, UMO100A, UMO060A where UMO070A.DOCENTE_REL = UMO100A.DOCENTE_REL and ";
            $msConsulta .= "UMO070A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL order by SYLLABUS_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute();
        }
        else
        {
            $msConsulta = "select SYLLABUS_REL, NOMBRE_100, NOMBRE_060, FECHA_070, ANNO_070, SEMESTRE_070, TURNO_070, GRUPO_070, APROBADO_070, ACTIVO_070 ";
            $msConsulta .= "from UMO070A, UMO100A, UMO060A where UMO070A.DOCENTE_REL = UMO100A.DOCENTE_REL and ";
            $msConsulta .= "UMO070A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and UMO070A.DOCENTE_REL = ? order by SYLLABUS_REL desc";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$msDocente]);
        }
    }
    else
    {
        $msConsulta = "select SYLLABUS_REL, PLANESTUDIO_REL, DOCENTE_REL, ASIGNATURA_REL, FECHA_070, ANNO_070, SEMESTRE_070, TURNO_070, GRUPO_070, RECOMENDACIONES_070, EJESVALORES_070, ACTIVO_070 from UMO070A where SYLLABUS_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }
    
    return $mDatos;
}

function fxBorrarDetObjGral($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO071A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObjGral($msCodigo, $mnConsecutivo, $msObjetivo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO071A (SYLLABUS_REL, OBJETIVOG_REL, TEXTO_071) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msObjetivo]);
}

function fxObtenerDetObjGral($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUS_REL, OBJETIVOG_REL, TEXTO_071 from UMO071A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetObjUnd($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO072A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObjUnd($msCodigo, $mnConsecutivo, $msUnidad, $msObjetivo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO072A (SYLLABUS_REL, OBJETIVOU_REL, UNIDAD_072, TEXTO_072) values (?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msUnidad, $msObjetivo]);
}

function fxObtenerDetObjUnd($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUS_REL, OBJETIVOU_REL, UNIDAD_072, TEXTO_072 from UMO072A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetObsDocente($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO074A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObsDocente($msCodigo, $mnConsecutivo, $msObservacion)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO074A (SYLLABUS_REL, OBSDOCENTE_REL, TEXTO_074) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msObservacion]);
}

function fxObtenerDetObsDocente($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUS_REL, OBSDOCENTE_REL, TEXTO_074 from UMO074A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetObsAcademica($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO075A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetObsAcademica($msCodigo, $mnConsecutivo, $msObservacion)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO075A (SYLLABUS_REL, OBSACADEMICA_REL, TEXTO_075) values (?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $msObservacion]);
}

function fxObtenerDetObsAcademica($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUS_REL, OBSACADEMICA_REL, TEXTO_075 from UMO075A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}

function fxBorrarDetSyllabus($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "delete from UMO073A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetSyllabus($msCodigo, $mnConsecutivo, $mdFecha, $msUnidad, $msContenido, $msObjetivoEsp, $msForma, $msMedios, $msEvaluacion)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "insert into UMO073A (SYLLABUS_REL, DETSYLLABUS_REL, FECHA_073, UNIDAD_073, CONTENIDO_073, OBJETIVOESP_073, FORMA_073, ";
    $msConsulta .= "MEDIOS_073, EVALUACION_073) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $mnConsecutivo, $mdFecha, $msUnidad, $msContenido, $msObjetivoEsp, $msForma, $msMedios, $msEvaluacion]);
}

function fxObtenerDetSyllabus($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select SYLLABUS_REL, DETSYLLABUS_REL, FECHA_073, UNIDAD_073, CONTENIDO_073, OBJETIVOESP_073, FORMA_073, ";
    $msConsulta .= "MEDIOS_073, EVALUACION_073 from UMO073A where SYLLABUS_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    return $mDatos;
}
?>