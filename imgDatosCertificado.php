<?php
require_once ("funciones/fxGeneral.php");
require_once ("funciones/fxAlumnos.php");
require_once ("funciones/fxNumerosLetras.php");

function DevuelveFecha($Fecha, $annoTexto)
{
    $FechaDividida = explode("-", $Fecha);
    
    $Anno = $FechaDividida[0];
    $Mes = $FechaDividida[1];
    $Dia = $FechaDividida[2];
    
    switch ($Mes)
    {
        case "01":
            $NombreMes = "enero";
            break;
        case "02":
            $NombreMes = "febrero";
            break;
        case "03":
            $NombreMes = "marzo";
            break;
        case "04":
            $NombreMes = "abril";
            break;
        case "05":
            $NombreMes = "mayo";
            break;
        case "06":
            $NombreMes = "junio";
            break;
        case "07":
            $NombreMes = "julio";
            break;
        case "08":
            $NombreMes = "agosto";
            break;
        case "09":
            $NombreMes = "septiembre";
            break;
        case "10":
            $NombreMes = "octubre";
            break;
        case "11":
            $NombreMes = "noviembre";
            break;
        case "12":
            $NombreMes = "diciembre";
            break;
    }
    if ($annoTexto == 0)
        return ($Dia . " de " . $NombreMes . " de " . $Anno);
    else
        return ($Dia . " días de " . $NombreMes . " de " . fxNumerosLetras($Anno));
}

function DevuelveTipoCurso($Tipo)
{
    switch (intval($Tipo))
    {
        case 0:
            $tipoCurso = "Curso de extensión";
            break;
        case 1:
            $tipoCurso = "Conferencia";
            break;
        case 2:
            $tipoCurso = "Taller";
            break;
        case 3:
            $tipoCurso = "Diplomado de extensión";
            break;
        case 4:
            $tipoCurso = "Seminario";
            break;
        case 5:
            $tipoCurso = "Curso de posgrado";
    }
    return ($tipoCurso);
}

if (isset($_POST["UMOJN"]))
{
    $msCodCurso = $_POST["UMOJN"];
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "SELECT PARTIDA_REL, UMO210A.MATCURSO_REL, UMO210A.ALUMNO_REL, FOLIO_371, TOMO_371, FECHA_370, CONCAT_WS(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE, NOMBRE_190, TIPOC_190, ACREDITABLE_190, FECHAINICIO_190, FECHAFIN_190, HRSTOTAL_190
    FROM UMO371A, UMO370A, UMO330A, UMO190A, UMO210A, UMO200A
    where UMO371A.REGISTRO_REL = UMO370A.REGISTRO_REL
    and UMO371A.MATCURSO_REL = UMO210A.MATCURSO_REL
    and UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
    and UMO370A.CURSOS_REL = UMO190A.CURSOS_REL
    and UMO190A.CURSOS_REL = ? order by PARTIDA_REL";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCodCurso]);
    
    while($mFila = $mDatos->fetch())
    {
        $msMatricula = $mFila["MATCURSO_REL"];
        $msPartida = $mFila["PARTIDA_REL"];
        $msAlumno = $mFila["ALUMNO_REL"];
        $mnFolio = $mFila["FOLIO_371"];
        $msTomo = $mFila["TOMO_371"];
        $mdFecha = $mFila["FECHA_370"];
        $msEstudiante = $mFila["ESTUDIANTE"];
        $msCurso = $mFila["NOMBRE_190"];
        $mdFechaIni = $mFila["FECHAINICIO_190"];
        $mdFechaFin = $mFila["FECHAFIN_190"];
        $mnHoras = $mFila["HRSTOTAL_190"];
        $mnTipoCurso = $mFila["TIPOC_190"];
        $mbAcreditable = $mFila["ACREDITABLE_190"];

        // Define las dimensiones de la imagen en pixeles
        $ancho = 1000;
        $alto = 700;
        $y = 40;

        // Crea una nueva imagen en blanco
        $imagen = imagecreatetruecolor($ancho, $alto);

        // Asigna colores
        $blanco = imagecolorallocate($imagen, 255, 255, 255);
        $negro = imagecolorallocate($imagen, 0, 0, 0);

        //Rectángulo
        imagefilledrectangle($imagen, 0, 0, 700, 700, $negro);
        imagefilledrectangle($imagen, 2, 2, 996, 696, $blanco);

        //Logotipo
        $msLogotipo = imagecreatefrompng("imagenes/logosinfondo.png");
        $mnAlto = imagesy($msLogotipo);
        $mnAncho = imagesx($msLogotipo);
        imagecopyresampled($imagen, $msLogotipo, 450, 20, 0, 0, 90, 90, $mnAncho, $mnAlto);

        // Define la fuente y el texto
        $arialNormal = __DIR__ . '/fonts/arial.ttf'; // Reemplaza con la ruta a una fuente TTF válida
        $arialBold = __DIR__ . '/fonts/arialbd.ttf';

        $msTexto = "PARTIDA " . $msPartida;
        imagettftext($imagen, 18, 0, 20, $y, $negro, $arialBold, $msTexto);

        $y += 100;
        $msTexto = "El suscrito (a) Secretario(a) General de la Universidad de Medicina Oriental Japón Nicaragua (UMO-JN). ";
        $msTexto .= "Procede a inscribir el certificado que literalmente dice: La Universidad de Medicina Oriental Japón-Nicaragua ";
        if ($mbAcreditable == 0)
            $msTexto .= "(UMO-JN). Otorga el Presente, CERTIFICADO DE PARTICIPACION a:";
        else
            $msTexto .= "(UMO-JN). Otorga el Presente, CERTIFICADO a:";

        imagettftext_multiline($imagen, 15, 0, 20, $y, $negro, $arialNormal, $msTexto, 970);

        $y += 100;
        $msTexto = $msEstudiante;
        $bbox = imagettfbbox(15, 0, $arialBold, $msTexto);
        $textWidth  = $bbox[2] - $bbox[0];
        $x = ($ancho - $textWidth) / 2;
        imagettftext($imagen, 15, 0, $x, $y, $negro, $arialBold, $msTexto);

        $y += 50;
        if ($mbAcreditable == 0)
        {
            if (intval($mnTipoCurso) == 1)
                $msTexto = "Por su participación en la " . DevuelveTipoCurso($mnTipoCurso) . " de " . $msCurso . ", impartida durante el período comprendido ";
            else
                $msTexto = "Por su participación en el " . DevuelveTipoCurso($mnTipoCurso) . " de " . $msCurso . ", impartido durante el período comprendido ";
        }
        else
        {
            if (intval($mnTipoCurso) == 1)
                $msTexto = "Por su desempeño satisfactorio en la " . DevuelveTipoCurso($mnTipoCurso) . " de " . $msCurso . ", impartida durante el período comprendido ";
            else
                $msTexto = "Por su desempeño satisfactorio en el " . DevuelveTipoCurso($mnTipoCurso) . " de " . $msCurso . ", impartido durante el período comprendido ";
        }
        $msTexto .= "del " . DevuelveFecha($mdFechaIni, 0) . " al " . DevuelveFecha($mdFechaFin, 0) . ", con una carga ";
        $msTexto .= "teórico - práctica de " . $mnHoras . " horas. ";
        $msTexto .= "Con las facultades y prerrogativas que legalmente le corresponde. Dado en la ciudad de Managua, ";
        $msTexto .= "República de Nicaragua, a los " . strtolower(DevuelveFecha($mdFechaFin, 1)) . ".";
        imagettftext_multiline($imagen, 15, 0, 20, $y, $negro, $arialNormal, $msTexto, 970);

        $y += 130;
        $msTexto = "Es conforme con su original con el que fue debidamente cotejado.";
        imagettftext($imagen, 15, 0, 20, $y, $negro, $arialNormal, $msTexto);

        $y += 50;
        $msTexto = "Registrado número: " . $msPartida . ", Folio: " . $mnFolio . ", Tomo del registro de Certificados de Educación Permanente: " . $msTomo;
        $msTexto .= ". En la ciudad de Managua, el día " . DevuelveFecha($mdFechaFin, 0) . ".";
        imagettftext_multiline($imagen, 15, 0, 20, $y, $negro, $arialNormal, $msTexto, 970);

        $y += 50;
        $msLogotipo = imagecreatefrompng("imagenes/rectoria.png");
        $mnAlto = imagesy($msLogotipo);
        $mnAncho = imagesx($msLogotipo);
        imagecopyresampled($imagen, $msLogotipo, 70, $y, 0, 0, 90, 90, $mnAncho, $mnAlto);

        $msLogotipo = imagecreatefrompng("imagenes/secretario.png");
        $mnAlto = imagesy($msLogotipo);
        $mnAncho = imagesx($msLogotipo);
        imagecopyresampled($imagen, $msLogotipo, 450, $y, 0, 0, 90, 90, $mnAncho, $mnAlto);

        $msLogotipo = imagecreatefrompng("imagenes/registro.png");
        $mnAlto = imagesy($msLogotipo);
        $mnAncho = imagesx($msLogotipo);
        imagecopyresampled($imagen, $msLogotipo, 800, $y, 0, 0, 90, 90, $mnAncho, $mnAlto);

        $y += 100;
        $texto = "Firma del Rector(a)";
        imagettftext($imagen, 15, 0, 40, $y, $negro, $arialNormal, $texto);

        $texto = "Firma del Secretario(a) general";
        imagettftext($imagen, 15, 0, 350, $y, $negro, $arialNormal, $texto);

        $texto = "Firma de Registro Académico";
        imagettftext($imagen, 15, 0, 700, $y, $negro, $arialNormal, $texto);

        // Define el nombre del archivo donde se guardará la imagen
        $archivo = $msEstudiante . '.png';
        $ruta = 'estudiantes/';
        $ruta_archivo = 'estudiantes/' . $msAlumno . '.png';

        if (!file_exists($ruta)) {
            mkdir($ruta, 0777, true);
        }

        // Guarda la imagen como un archivo PNG en el servidor
        // La función imagepng() ahora acepta un segundo argumento: el nombre del archivo
        imagepng($imagen, $ruta_archivo);
        echo('<img src="' . $ruta_archivo . '" style="margin: 10px" width="500px" />');

        // Libera la memoria
        imagedestroy($imagen);
    }
}

/**
 * Dibuja un texto multilínea con una fuente TrueType
 *
 * @param resource $image El recurso de la imagen
 * @param int $font_size El tamaño de la fuente en puntos
 * @param int $angle El ángulo de rotación (generalmente 0)
 * @param int $x La coordenada X inicial
 * @param int $y La coordenada Y inicial
 * @param int $color El color del texto
 * @param string $font_file La ruta al archivo de la fuente .ttf
 * @param string $text El texto a dibujar
 * @param int $max_width El ancho máximo permitido para el texto
 */
function imagettftext_multiline($image, $font_size, $angle, $x, $y, $color, $font_file, $text, $max_width) {
    // Dividir el texto en palabras
    $palabras = explode(' ', $text);
    $linea_actual = '';
    $lineas = [];

    foreach ($palabras as $palabra) {
        // Concatenar la palabra a la línea actual
        $test_linea = ($linea_actual ? $linea_actual . ' ' : '') . $palabra;

        // Obtener el ancho del cuadro delimitador de la línea de prueba
        $caja = imagettfbbox($font_size, $angle, $font_file, $test_linea);
        $ancho = abs($caja[2] - $caja[0]); // Ancho total del texto

        if ($ancho <= $max_width) {
            $linea_actual = $test_linea;
        } else {
            // Si la línea excede el ancho, la guardamos y empezamos una nueva
            $lineas[] = $linea_actual;
            $linea_actual = $palabra;
        }
    }
    // Agregar la última línea
    if ($linea_actual !== '') {
        $lineas[] = $linea_actual;
    }
    
    // Altura de una línea de texto
    $caja_altura = imagettfbbox($font_size, $angle, $font_file, 'A');
    $altura_linea = abs($caja_altura[1] - $caja_altura[7]);

    // Dibujar cada línea en la imagen
    foreach ($lineas as $i => $linea) {
        imagettftext($image, $font_size, $angle, $x, $y + ($i * $altura_linea * 1.5), $color, $font_file, $linea);
    }
}
?>