<?php
require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion();

ob_start();

$htmlCursos = "";
$estudiantes = [];

$docente    = $_POST["docenteSelect"] ?? "";
$turno      = $_POST["turnoSelect"] ?? "";
$asistencia = $_POST["asistencia"] ?? "";  

if ($docente != "" && $turno != "") {
 
    $msConsulta = " SELECT CURSOS_REL, NOMBRE_190
        FROM UMO190A
        WHERE DOCENTE_REL = ?
          AND TURNO_190 = ?
          AND ESTADO_190 = 1
        ORDER BY NOMBRE_190
    ";
    $stmtCursos = $m_cnx_MySQL->prepare($msConsulta);
    $stmtCursos->execute([$docente, $turno]);

    $primerCurso = "";

    while ($fila = $stmtCursos->fetch(PDO::FETCH_ASSOC)) {
        if ($primerCurso == "") {
            $primerCurso = $fila["CURSOS_REL"];
        }
        $htmlCursos .= "<option value='{$fila["CURSOS_REL"]}'>{$fila["NOMBRE_190"]}</option>";
    }
 
    if ($primerCurso != "") { 
        if ($asistencia != "") {
            $msConsulta = " SELECT 
                    M.MATCURSO_REL,
                    A.APELLIDOS_200,
                    A.NOMBRES_200,
                    IFNULL(D.ESTADO_321, 0) AS ESTADO
                FROM UMO210A M
                INNER JOIN UMO200A A ON M.ALUMNO_REL = A.ALUMNO_REL
                LEFT JOIN UMO321A D
                    ON D.MATCURSO_REL = M.MATCURSO_REL
                   AND D.ASISTENCIACL_REL = ?
                WHERE M.CURSOS_REL = ?
                ORDER BY A.APELLIDOS_200, A.NOMBRES_200
            ";
            $stmtEst = $m_cnx_MySQL->prepare($msConsulta);
            $stmtEst->execute([$asistencia, $primerCurso]);

        }  
        else {

            $msConsulta = "
                SELECT 
                    M.MATCURSO_REL,
                    A.APELLIDOS_200,
                    A.NOMBRES_200,
                    0 AS ESTADO
                FROM UMO210A M
                INNER JOIN UMO200A A ON M.ALUMNO_REL = A.ALUMNO_REL
                WHERE M.CURSOS_REL = ?
                ORDER BY A.APELLIDOS_200, A.NOMBRES_200
            ";
            $stmtEst = $m_cnx_MySQL->prepare($msConsulta);
            $stmtEst->execute([$primerCurso]);
        }

        while ($fila = $stmtEst->fetch(PDO::FETCH_ASSOC)) {
            $estudiantes[] = [
                "matricula"  => $fila["MATCURSO_REL"],
                "estudiante" => $fila["APELLIDOS_200"].", ".$fila["NOMBRES_200"],
                "estado"     => intval($fila["ESTADO"])
            ];
        }
    }

    ob_clean();
    echo json_encode([
        "cursos" => $htmlCursos,
        "estudiantes" => $estudiantes
    ]);
}
 
elseif (isset($_POST["cursoAsg"])) {

    $curso = $_POST["cursoAsg"];

    $msConsulta = "
        SELECT 
            M.MATCURSO_REL,
            A.APELLIDOS_200,
            A.NOMBRES_200
        FROM UMO210A M
        INNER JOIN UMO200A A ON M.ALUMNO_REL = A.ALUMNO_REL
        WHERE M.CURSOS_REL = ?
        ORDER BY A.APELLIDOS_200, A.NOMBRES_200
    ";

    $stmtEst = $m_cnx_MySQL->prepare($msConsulta);
    $stmtEst->execute([$curso]);

    while ($fila = $stmtEst->fetch(PDO::FETCH_ASSOC)) {
        $estudiantes[] = [
            "matricula"  => $fila["MATCURSO_REL"],
            "estudiante" => $fila["APELLIDOS_200"].", ".$fila["NOMBRES_200"],
            "estado"     => 0
        ];
    }

    ob_clean();
    echo json_encode($estudiantes);
}
?>
