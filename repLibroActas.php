<?php
    require_once ("funciones/fxGeneral.php");
    require_once ("tcpdf/tcpdf.php");
    $m_cnx_MySQL = fxAbrirConexion();

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(0);
	$pdf->SetFooterMargin(0);

	// remove default footer
	$pdf->setPrintFooter(false);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
    }

    if (isset($_POST['UMOJN']))
        $msUMOJN = $_POST['UMOJN'];

    $arrDiplomas = explode(',', $msUMOJN);
    $mnConteo = count($arrDiplomas);

    $msConsulta = "select DIPLOMA_REL, FECHA_003, ESTUDIO_003, NOMBRE_003, VERIFICACION_003, RUTA_003 ";
    $msConsulta .= "from UMO003B where DIPLOMA_REL in (";
    for ($i=0; $i<$mnConteo; $i++)
    {
        if ($i == $mnConteo - 1)
            $msConsulta .= "'" . $arrDiplomas[$i] . "'";
        else
            $msConsulta .= "'" . $arrDiplomas[$i] . "',";
    }
    $msConsulta .= ")";

    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    
    while ($mFila = $mDatos->fetch())
    {
        $pdf->AddPage();
        $msDiploma = $mFila["DIPLOMA_REL"];
        $msFecha = $mFila["FECHA_003"];
        $msEstudio = $mFila["ESTUDIO_003"];
        $msNombre = $mFila["NOMBRE_003"];
        $msVerificacion = $mFila["VERIFICACION_003"];
        $msRuta = $mFila["RUTA_003"];

        if ($msVerificacion == "")
        {
            $mdAhora = date('YmdHis');
            $msVerificacion .= rand(10000, 99999) . $mdAhora;

            $msConsulta = "update UMO003B set VERIFICACION_003 = ? where DIPLOMA_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msVerificacion, $msDiploma]);
        }

        //Nombre del estudiante
        $pdf->SetFont('helvetica','B',22);
        $pdf->setXY(20,20);
        $pdf->Cell(200,10,$msEstudio,0,0,'L');
        $pdf->setXY(20,30);
        $pdf->Cell(200,10,$msNombre,0,0,'L');
        
        //Código QR
		$style = array(
			'border' => false,
			'vpadding' => 0,
			'hpadding' => 0,
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
		);
        $msCodigoQR = "https://diplomas.umojn.edu.ni/diploma.php?UMOJN=" . $msVerificacion;
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 60, 30, 30, $style, 'N');
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 100, 35, 35, $style, 'N');
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 145, 40, 40, $style, 'N');
    }
    $pdf->Output();
?>