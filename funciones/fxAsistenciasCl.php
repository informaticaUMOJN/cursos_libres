<?php
require_once("fxGeneral.php");

function fxGuardarAsistencia($msDocente, $msCursos, $mdFecha, $mnTurno, $mnAnno)
{
    $m_cnx_MySQL = fxAbrirConexion(); 
    $msConsulta = "SELECT IFNULL(MID(MAX(ASISTENCIACL_REL), 4), 0) AS Ultimo FROM UMO320A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $mnLongitud = strlen($mnNumero);
    $msCodigo = "ASC" . str_repeat("0", 7 - $mnLongitud) . trim($mnNumero);
    $msConsulta = "INSERT INTO UMO320A (ASISTENCIACL_REL, DOCENTE_REL, CURSOS_REL, FECHA_320, TURNO_320, ANNO_320)
                   VALUES (?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msDocente, $msCursos, $mdFecha, $mnTurno, $mnAnno]);

    return $msCodigo;
}

function fxModificarAsistencia($msCodigo, $msDocente, $msCursos, $mdFecha, $mnTurno, $mnAnno)
{
    $m_cnx_MySQL = fxAbrirConexion(); 
    $msConsulta = "UPDATE UMO320A
                   SET DOCENTE_REL = ?, CURSOS_REL = ?, FECHA_320 = ?, TURNO_320 = ?, ANNO_320 = ?
                   WHERE ASISTENCIACL_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msDocente, $msCursos, $mdFecha, $mnTurno, $mnAnno, $msCodigo]);
}

function fxBorrarAsistencia($msCodigo)
{
    $m_cnx_MySQL = fxAbrirConexion();

    // Borrar detalle
    $msConsulta = "DELETE FROM UMO321A WHERE ASISTENCIACL_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);

    // Borrar cabecera
    $msConsulta = "DELETE FROM UMO320A WHERE ASISTENCIACL_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
}

function fxGuardarDetAsistencia($msCodigo, $msMatCurso, $mnEstado) {
    $m_cnx_MySQL = fxAbrirConexion();
    $msConsulta = "INSERT INTO UMO321A (ASISTENCIACL_REL, MATCURSO_REL, ESTADO_321)
                   VALUES (?, ?, ?)
                   ON DUPLICATE KEY UPDATE ESTADO_321 = VALUES(ESTADO_321)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo, $msMatCurso, $mnEstado]);
}

function fxDevuelveAsistencia($mbLlenaGrid, $msDocente="", $msCodigo="")
{
    $m_cnx_MySQL = fxAbrirConexion();

    if ($mbLlenaGrid == 1) {
        if ($msDocente == "") {
            $msConsulta = "SELECT A.ASISTENCIACL_REL, D.NOMBRE_100, C.NOMBRE_190, A.FECHA_320, A.TURNO_320, A.ANNO_320
                           FROM UMO320A A
                           INNER JOIN UMO100A D ON A.DOCENTE_REL = D.DOCENTE_REL
                           INNER JOIN UMO190A C ON A.CURSOS_REL = C.CURSOS_REL
                           ORDER BY A.ASISTENCIACL_REL DESC";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute(); 
        } else {
            $msConsulta = "SELECT A.ASISTENCIACL_REL, D.NOMBRE_100, C.NOMBRE_190, A.FECHA_320, A.TURNO_320, A.ANNO_320
                           FROM UMO320A A
                           INNER JOIN UMO100A D ON A.DOCENTE_REL = D.DOCENTE_REL
                           INNER JOIN UMO190A C ON A.CURSOS_REL = C.CURSOS_REL
                           WHERE A.DOCENTE_REL = ?
                           ORDER BY A.ASISTENCIACL_REL DESC";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$msDocente]);
        }
    } else {
        $msConsulta = "SELECT A.ASISTENCIACL_REL, A.DOCENTE_REL, A.CURSOS_REL, A.FECHA_320, A.TURNO_320, A.ANNO_320,
                              D.NOMBRE_100, C.NOMBRE_190
                       FROM UMO320A A
                       INNER JOIN UMO100A D ON D.DOCENTE_REL = A.DOCENTE_REL
                       INNER JOIN UMO190A C ON C.CURSOS_REL = A.CURSOS_REL
                       WHERE A.ASISTENCIACL_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCodigo]);
    }

    return $mDatos;
}
?>