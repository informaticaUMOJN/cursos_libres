<?php

require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion();

if (isset($_POST['curso'])) {
    $curso = $_POST['curso'];
    $msConsulta = "SELECT MODULO_REL, NOMBRE_280 FROM UMO280A  WHERE CURSOS_REL = ? ORDER BY NOMBRE_280";
    $msDatos = $m_cnx_MySQL->prepare($msConsulta);
    $msDatos->execute([$curso]);

    while ($row = $msDatos->fetch()) {
        $valor = htmlspecialchars($row['MODULO_REL']);
        $texto = htmlspecialchars($row['NOMBRE_280']);
        echo "<option value='$valor'>$texto</option>";
    }
}
?>