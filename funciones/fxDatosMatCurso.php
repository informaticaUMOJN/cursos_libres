<?php
require_once("fxGeneral.php");

/********** Llenar el combo del Plan de Estudio **********/
if (isset($_POST["cursoPe"])) {
    $m_cnx_MySQL = fxAbrirConexion();
    $msCodigo = $_POST["cursoPe"];

    $msConsulta = " SELECT PLANCURSO_REL, PERIODO_220 FROM UMO220A  WHERE CURSOS_REL = ? ORDER BY PERIODO_220";

    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "";

    if ($mnRegistros > 0) {
        while ($mFila = $mDatos->fetch()) {
            $msTexto = "Período " . trim($mFila["PERIODO_220"]);
            $msResultado .= "<option value='" . $mFila["PLANCURSO_REL"] . "'>" . $msTexto . "</option>";
        }
    }

    echo $msResultado;
}

/********** Llenar el combo de las Asignaturas (Módulos) **********/
if (isset($_POST["cursoAsg"])) {
    $m_cnx_MySQL = fxAbrirConexion();
    $msCurso = $_POST["cursoAsg"];
    $msConsulta = " SELECT MODULO_REL, NOMBRE_280 FROM UMO280A WHERE CURSOS_REL = ?  ORDER BY NOMBRE_280";

    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCurso]);
    $mnRegistros = $mDatos->rowCount();
    $msResultado = "";

    if ($mnRegistros > 0) {
        while ($mFila = $mDatos->fetch()) {
            $msResultado .= "<option value='" . $mFila["MODULO_REL"] . "'>" . $mFila["NOMBRE_280"] . "</option>";
        }
    }

    echo $msResultado;
}

/********** Llenar la carrera del estudiante **********/
if (isset($_POST["alumnoCrl"])) {
    $m_cnx_MySQL = fxAbrirConexion();
    $msCodigo = $_POST["alumnoCrl"];

    $msConsulta = "  SELECT CURSOS_REL FROM UMO200A WHERE ESTUDIANTE_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodigo]);
    $mnRegistros = $mDatos->rowCount();

    if ($mnRegistros == 0) {
        $mnResultado = 0;
    } else {
        $mFila = $mDatos->fetch();
        $mnResultado = $mFila["CURSOS_REL"];
    }
    echo $mnResultado;
}
?>