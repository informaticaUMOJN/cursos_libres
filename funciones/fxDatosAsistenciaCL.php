<?php
require_once ("fxGeneral.php");


if (isset($_POST['docenteCursos']))
{
    include_once("fxGeneral.php");
   $m_cnx_MySQL = fxAbrirConexion();
    $docente = $_POST['docenteCursos'];

    $sql = "SELECT DISTINCT UMO190A.CURSOS_REL, UMO190A.NOMBRE_190
            FROM UMO280A
            INNER JOIN UMO190A ON UMO280A.CURSOS_REL = UMO190A.CURSOS_REL
            WHERE UMO280A.DOCENTE_REL = ?
            ORDER BY UMO190A.NOMBRE_190";
    
    $stmt =$m_cnx_MySQL->prepare($sql);
    $stmt->execute([$docente]);

    $html = "";
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $valor = trim($fila['CURSOS_REL']);
        $texto = trim($fila['NOMBRE_190']);
        $html .= "<option value='$valor'>$texto</option>";
    }

    echo $html;
    exit;
}
// Cuando se selecciona un curso, traer los módulos de ese curso y docente
if (isset($_POST['carreraAsg']) && isset($_POST['docenteAsg'])) {
    include_once("fxGeneral.php");
   $m_cnx_MySQL = fxAbrirConexion(); // <-- Faltaba
    $curso = trim($_POST['carreraAsg']);
    $docente = trim($_POST['docenteAsg']);

    $sql = "SELECT MODULO_REL, NOMBRE_280 
            FROM UMO280A 
            WHERE CURSOS_REL = ? AND DOCENTE_REL = ? 
            ORDER BY NOMBRE_280";
    $stmt =$m_cnx_MySQL->prepare($sql);
    $stmt->execute([$curso, $docente]);

    $opciones = "<option value=''>Seleccione un módulo</option>";
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $opciones .= "<option value='" . $fila['MODULO_REL'] . "'>" . $fila['NOMBRE_280'] . "</option>";
    }

    echo $opciones;
    exit;
}


if (isset($_POST["asignatura"]) && isset($_POST["turno"])) {
    $m_cnx_MySQL = fxAbrirConexion();
    
    $msCodigo = $_POST["asignatura"];
    $mnTurno = $_POST["turno"];
    
    $msConsulta = "
        SELECT
            '' AS ASISTENCIACL_REL,
            UMO210A.MATCURSO_REL,
            UMO200A.NOMBRES_200,
            UMO200A.APELLIDOS_200,
            1 AS ESTADO_321
        FROM
            UMO210A
        INNER JOIN UMO200A ON UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
        INNER JOIN UMO220A ON UMO210A.PLANCURSO_REL = UMO220A.PLANCURSO_REL
        INNER JOIN UMO221A ON UMO210A.PLANCURSO_REL = UMO221A.PLANCURSO_REL
        WHERE
            UMO221A.MODULO_221 LIKE ?
            AND UMO220A.TURNO_220 = ?
            AND UMO210A.ESTADO_210 = 0
        ORDER BY
            UMO200A.NOMBRES_200, UMO200A.APELLIDOS_200;
    ";
    
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute(["%$msCodigo%", $mnTurno]);
    
    $result = [];
    while ($mFila = $mDatos->fetch(PDO::FETCH_ASSOC)) {
        $msEstudiante = trim($mFila["APELLIDOS_200"]) . ", " . trim($mFila["NOMBRES_200"]);
        $msEstado = "Presente"; // según tu código
        $result[] = [
            "matricula" => $mFila["MATCURSO_REL"],
            "estudiante" => $msEstudiante,
            "estado" => $msEstado
        ];
    }

    // **Limpiar salida y retornar solo JSON**
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
