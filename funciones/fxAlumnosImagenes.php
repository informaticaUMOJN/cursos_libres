<?php
require_once ("fxGeneral.php");
require_once ("fxAlumnos.php");

/* =======================
   SUBIR IMAGEN
======================= */
if (!empty($_FILES['archivo'])) {

    $mnTipoDoc      = $_POST["cboTipoDoc"];
    $msEstudiante   = $_POST["txtAlumno"];
    $msDescripcion  = $_POST["txtDescripcion"];
    $msArchivoOriginal = $_FILES['archivo']['name'];

    // Carpeta de destino
    $msCarpeta = '../alumnos/'.$msEstudiante;

    // Crear carpeta si no existe y asignar permisos correctos
    if (!file_exists($msCarpeta)) {
        if (!mkdir($msCarpeta, 0775, true)) {
            error_log("No se pudo crear la carpeta $msCarpeta");
            echo 0;
            exit;
        }
        // Asegurarse que la carpeta sea escribible
        chmod($msCarpeta, 0775);
    } elseif (!is_writable($msCarpeta)) {
        // Si la carpeta existe pero no es escribible
        chmod($msCarpeta, 0775);
    }

    // Separar nombre y extensión
    $extension = pathinfo($msArchivoOriginal, PATHINFO_EXTENSION);
    $nombreSinExt = pathinfo($msArchivoOriginal, PATHINFO_FILENAME);

    // Generar nombre único si ya existe
    $msArchivo = $msArchivoOriginal;
    $contador = 1;
    while (file_exists($msCarpeta.'/'.$msArchivo)) {
        $msArchivo = $nombreSinExt . '-' . $contador . '.' . $extension;
        $contador++;
    }

    $msRutaBD = 'alumnos/'.$msEstudiante.'/'.$msArchivo;

    // Mover archivo a carpeta destino
    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $msCarpeta.'/'.$msArchivo)) {
        fxGuardarDetDocumento($msEstudiante, $msArchivo, $mnTipoDoc, $msDescripcion, $msRutaBD);
        echo construirTablaDocumentos($msEstudiante);
        exit;
    } else {
        // Error al mover archivo
        error_log("Error al mover archivo: " . $_FILES["archivo"]["tmp_name"] . " a " . $msCarpeta.'/'.$msArchivo);
        echo 0;
        exit;
    }
}

/* =======================
   BORRAR IMAGEN
======================= */
if (isset($_POST["CodEstudiante"], $_POST["CodImagen"])) {

    $msEstudiante = $_POST["CodEstudiante"];
    $msImagen     = $_POST["CodImagen"];
    $msRuta       = '../alumnos/'.$msEstudiante.'/'.$msImagen;

    if (file_exists($msRuta)) {
        unlink($msRuta);
        fxBorrarDetDocumento($msEstudiante, $msImagen);
        echo construirTablaDocumentos($msEstudiante);
        exit;
    }

    echo 0;
    exit;
}

echo 0;
exit;

/* =======================
   FUNCIÓN PARA CONSTRUIR HTML
======================= */
function construirTablaDocumentos($codigo) {

    $mnCuenta = 0;
    $html = '<table width="100%">';

    $mDatos = fxDevuelveDetDocumento($codigo);

    while ($mFila = $mDatos->fetch()) {

        $extension = strtoupper(pathinfo($mFila["EVIDENCIAS_REL"], PATHINFO_EXTENSION));

        if ($mnCuenta == 0) $html .= '<tr>';

        $html .= '<td width="23%" valign="top">';
        $html .= '<img src="imagenes/imageDel.png" 
                    id="'.$mFila["EVIDENCIAS_REL"].'" 
                    style="cursor:pointer"
                    onclick="borrarImagen(this)">
                  <label style="font-size:small"> Borrar '.$mFila["EVIDENCIAS_REL"].'</label>';

        if ($extension != 'PDF') {
            $html .= '<br>
                      <a href="'.$mFila["RUTA_201"].'" target="_blank">
                        <img src="'.$mFila["RUTA_201"].'" style="width:100%">
                      </a>';
        } else {
            $html .= '<br>
                      <a href="'.$mFila["RUTA_201"].'" target="_blank">
                        <img src="imagenes/pdf.png" style="width:80%">
                      </a>';
        }

        $html .= '<div>'.$mFila["DESC_201"].'</div>';
        $html .= '</td>';

        $mnCuenta++;
        if ($mnCuenta == 4) {
            $html .= '</tr>';
            $mnCuenta = 0;
        }
    }

    if ($mnCuenta > 0) {
        while ($mnCuenta < 4) {
            $html .= '<td></td>';
            $mnCuenta++;
        }
        $html .= '</tr>';
    }

    $html .= '</table>';

    return $html;
}
?>
