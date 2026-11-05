<?php
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
    {
        echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
        exit('');
    }
    require_once ("funciones/fxGeneral.php");
    require_once ("funciones/fxNumerosLetras.php");
    require_once ("tcpdf/tcpdf.php");
    $m_cnx_MySQL = fxAbrirConexion();

    class PDF extends TCPDF
    {
		// Page header
		function Header(){}

        // Page footer
		function Footer()
        {}
    }

    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 12, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
    }
    $pdf->AddPage();

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

    if (isset($_POST['UMOJN']))
        $msLibro = $_POST['UMOJN'];

    $msConsulta = "select DESC_330, APERTURA_330 from UMO330A where LIBRO_REL = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msLibro]);
    $mFila = $mDatos->fetch();
    $msDescripcion = $mFila["DESC_330"];
    $msApertura = $mFila["APERTURA_330"];

    $pdf->SetFont('helvetica','',12);

    $msTexto = 'APERTURA DEL LIBRO DE ACTAS DE REGISTRO DE CERTIFICADOS DE EDUCACION PERMANENTE';
    $msHTML = '<table width="100%" style="padding: 10px; text-align: center;">';
    $msHTML .= '<tr>';
    $msHTML .= '<td><h2>' . $msTexto . '</h2></td>';
    $msHTML .= '</tr>';
    $msHTML .= '<table>';

    $msTexto = "En la ciudad de Managua, el día " . devuelveFecha($msApertura, 0) . ", se procede a la apertura del Libro de Actas de ";
    $msTexto .= $msDescripcion . ", el cual tendrá como finalidad dejar constancia ordenada, cronológica ";
    $msTexto .= "y verificable de los certificados emitidos por la institución, correspondientes a los programas de educación permanente.";
    $msHTML .= '<table width="100%" style="padding-right: 10px; padding-left: 10px; padding-top: 15px; text-align: justify;">';
    $msHTML .= '<tr>';
    $msHTML .= '<td>' . $msTexto . '</td>';
    $msHTML .= '</tr>';
    $msHTML .= '<table>';

    $msTexto = "En el presente libro se registrarán los datos esenciales de cada certificado, tales como: nombre completo del ";
    $msTexto .= "participante, nombre del programa, período de realización, número de horas, fecha de emisión del certificado y ";
    $msTexto .= "número correlativo de registro, así como cualquier otra información que la institución considere pertinente ";
    $msTexto .= "para efectos académicos y administrativos.";
    $msHTML .= '<table width="100%" style="padding-right: 10px; padding-left: 10px; padding-top: 15px; text-align: justify;">';
    $msHTML .= '<tr>';
    $msHTML .= '<td>' . $msTexto . '</td>';
    $msHTML .= '</tr>';
    $msHTML .= '<table>';

    $msTexto = "Este libro se habilita para garantizar la transparencia, control, validez y respaldo institucional de los ";
    $msTexto .= "certificados emitidos, conforme a las normativas internas vigentes y permanecerá bajo la custodia de la ";
    $msTexto .= "autoridad administrativa correspondiente.";
    $msHTML .= '<table width="100%" style="padding-right: 10px; padding-left: 10px; padding-top: 15px; text-align: justify;">';
    $msHTML .= '<tr>';
    $msHTML .= '<td>' . $msTexto . '</td>';
    $msHTML .= '</tr>';
    $msHTML .= '<table>';

    $msTexto = "Para los fines que estime convenientes, se firma la presente acta de apertura.";
    $msHTML .= '<table width="100%" style="padding-right: 10px; padding-left: 10px; padding-top: 15px; text-align: justify;">';
    $msHTML .= '<tr>';
    $msHTML .= '<td>' . $msTexto . '</td>';
    $msHTML .= '</tr>';
    $msHTML .= '<table>';

    $msHTML .= '<table width="100%" style="padding-right: 10px; padding-left: 10px; padding-top: 150px; text-align: center;">';
    $msHTML .= '<tr>';
    $msHTML .= '<td>Rector(a)</td>';
    $msHTML .= '<td>Secretario(a) General</td>';
    $msHTML .= '<td>Registro académico</td>';
    $msHTML .= '</tr>';
    $msHTML .= '<table>';

    $pdf->setY(15);
    $pdf->writeHTML($msHTML);
    $pdf->AddPage();

    $msConsulta = "SELECT PARTIDA_REL, FOLIO_371, TOMO_371, FECHA_370, CONCAT_WS(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE, NOMBRE_190, TIPOC_190, ACREDITABLE_190, FECHAINICIO_190, FECHAFIN_190, HRSTOTAL_190 ";
    $msConsulta .= "FROM UMO371A, UMO370A, UMO330A, UMO190A, UMO210A, UMO200A ";
    $msConsulta .= "where UMO371A.REGISTRO_REL = UMO370A.REGISTRO_REL ";
    $msConsulta .= "and UMO371A.MATCURSO_REL = UMO210A.MATCURSO_REL ";
    $msConsulta .= "and UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL ";
    $msConsulta .= "and UMO370A.CURSOS_REL = UMO190A.CURSOS_REL ";
    $msConsulta .= "and UMO370A.LIBRO_REL = UMO330A.LIBRO_REL ";
    $msConsulta .= "and UMO330A.LIBRO_REL = ?";

    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msLibro]);
    $mnExtra = 0;
    $msHTML = '';
    
    while ($mFila = $mDatos->fetch())
    {
        $msPartida = $mFila["PARTIDA_REL"];
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

        //Número de partida
        $pdf->SetFont('helvetica','',10);

        $msHTML .= '<table width="100%" style="border: 1px solid rgb(0,0,0); padding: 10px;"><tr><td>'; //Tabla principal
        
        $msHTML .= '<table style="padding-bottom: 3px;">'; //Tabla partida
        $msHTML .= '<tr>';
        $msHTML .= '<td><h3><b>Partida ' . $msPartida . '</b></h3></td>';
        $msHTML .= '<td style="text-align: center;"><img src="imagenes/logosinfondo.png" style="height: 40px" /></td>';
        $msHTML .= '<td>&nbsp;</td>';
        $msHTML .= '</tr>';
        $msHTML .= '</table>';

        $msTexto = "El suscrito (a) Secretario(a) General de la Universidad de Medicina Oriental Japón Nicaragua (UMO-JN). ";
        $msTexto .= "Procede a inscribir el certificado que literalmente dice: La Universidad de Medicina Oriental Japón-Nicaragua ";
        if ($mbAcreditable == 0)
            $msTexto .= "(UMO-JN). Otorga el Presente, CERTIFICADO DE PARTICIPACION a:";
        else
            $msTexto .= "(UMO-JN). Otorga el Presente, CERTIFICADO a:";
        $msHTML .= '<table style="padding-bottom: 10px;"><tr>'; //Tabla párrafo 1
        $msHTML .= '<td>' . $msTexto . '</td>';
        $msHTML .= '</tr></table>';

        $msHTML .= '<table style="padding-bottom: 10px; text-align: center;"><tr>'; //Tabla estudiante
        $msHTML .= '<td><b>' . $msEstudiante . '</b></td>';
        $msHTML .= '</tr></table>';

        if ($mbAcreditable == 0)
        {
            if (intval($mnTipoCurso) == 1)
                $msTexto = "Por su participación en la " . DevuelveTipoCurso($mnTipoCurso) . " de <b>" . $msCurso . "</b>, impartida durante el período comprendido ";
            else
                $msTexto = "Por su participación en el " . DevuelveTipoCurso($mnTipoCurso) . " de <b>" . $msCurso . "</b>, impartido durante el período comprendido ";
        }
        else
        {
            if (intval($mnTipoCurso) == 1)
                $msTexto = "Por su desempeño satisfactorio en la " . DevuelveTipoCurso($mnTipoCurso) . " de <b>" . $msCurso . "</b>, impartida durante el período comprendido ";
            else
                $msTexto = "Por su desempeño satisfactorio en el " . DevuelveTipoCurso($mnTipoCurso) . " de <b>" . $msCurso . "</b>, impartido durante el período comprendido ";
        }
        $msTexto .= "del <b>" . DevuelveFecha($mdFechaIni, 0) . "</b> al <b>" . DevuelveFecha($mdFechaFin, 0) . "</b>, con una carga ";
        $msTexto .= "teórico - práctica de <b>" . $mnHoras . " horas</b>. ";
        $msTexto .= "Con las facultades y prerrogativas que legalmente le corresponde. Dado en la ciudad de Managua, ";
        $msTexto .= "República de Nicaragua, a los " . strtolower(DevuelveFecha($mdFechaFin, 1)) . ".";
        $msHTML .= '<table style="padding-bottom: 10px;"><tr>'; //Tabla párrafo 2
        $msHTML .= '<td>' . $msTexto . '</td>';
        $msHTML .= '</tr></table>';

        $msHTML .= '<table style="padding-bottom: 10px;"><tr>'; //Tabla conforme
        $msHTML .= '<td>Es conforme con su original con el que fue debidamente cotejado.</td>';
        $msHTML .= '</tr></table>';

        $msTexto = "Registrado número: <b>" . $msPartida . "</b>, Folio: <b>" . $mnFolio . "</b>, Tomo del registro de Certificados de Educación Permanente: <b>" . $msTomo;
        $msTexto .= "</b>. En la ciudad de Managua, el día " . DevuelveFecha($mdFechaFin, 0) . ".";

        $msHTML .= '<table style="padding-bottom: 10px;"><tr>'; //Tabla registro
        $msHTML .= '<td>' . $msTexto . '</td>';
        $msHTML .= '</tr></table>';

        $msHTML .= '<table style="padding-bottom: 3px; text-align: center;">'; //Tabla firmas
        $msHTML .= '<tr>';
        $msHTML .= '<td><img src="imagenes/rectoria.png" style="height: 50px;" /></td>';
        $msHTML .= '<td><img src="imagenes/secretario.png" style="height: 50px;" /></td>';
        $msHTML .= '<td><img src="imagenes/registro.png" style="height: 50px;" /></td>';
        $msHTML .= '</tr>';
        $msHTML .= '<tr>';
        $msHTML .= '<td>Firma del Rector(a)</td>';
        $msHTML .= '<td>Firma del Secretario(a) general</td>';
        $msHTML .= '<td>Firma de Registro académico</td>';
        $msHTML .= '</tr>';
        $msHTML .= '</table>';

        $msHTML .= '</td></tr></table>'; //Fin de la tabla principal

        if ($mnExtra == 0)
        {
            $msHTML .= '<table style="padding-bottom: 15px;"><tr>'; //Tabla extra de separación
            $msHTML .= '<td>&nbsp;</td>';
            $msHTML .= '</tr></table>';
            $mnExtra = 1;
        }
        else
        {
            $msHTML .= '<table width="100%" style="padding-bottom: 30px; padding-top:10px; text-align: center;"><tr>'; //Tabla extra de separación
            $msHTML .= '<td><h3>Folio ' . $mnFolio .'</h3></td>';
            $msHTML .= '</tr></table>';
            $msHTML .= '<div style="break-after: page;"></div>';
            $mnExtra = 0;
        }
    }
    
    $pdf->setY(12);
    $pdf->writeHTML($msHTML);
    $pdf->Output();
?>