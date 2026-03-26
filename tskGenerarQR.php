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

    $msExpDigital = $_POST['UMOJN'];

    $msConsulta = "select CARNET_REL, CARRERA_001, NOMBRE_002, REGISTRO_002, VERIFICACION_002, FOLDER_002 from UMO002B join UMO001B ";
    $msConsulta .= "on UMO002B.EXPDIGITAL_REL = UMO001B.EXPDIGITAL_REL where UMO002B.EXPDIGITAL_REL = ? ";
    //$msConsulta .= "and CARNET_REL = ? ";
    $msConsulta .= "order by REGISTRO_002";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msExpDigital]);
    
    while ($mFila = $mDatos->fetch())
    {
        $pdf->AddPage();
        $msCarnet = $mFila["CARNET_REL"];
        $msCarrera = $mFila["CARRERA_001"];
        $msNombre = $mFila["NOMBRE_002"];
        $msRegistro = $mFila["REGISTRO_002"];
        $msVerificacion = $mFila["VERIFICACION_002"];
        $msFolder = $mFila["FOLDER_002"];

        if ($msVerificacion == "" and $msFolder == "")
        {
            $mCarnet = explode('-', $msCarnet);
            $mnElementos = count($mCarnet);
            
            //Inicia el codigo de verificación
            for ($i=0; $i<$mnElementos; $i++)
            {
                $msVerificacion .= $mCarnet[$i];
                $msFolder .= $mCarnet[$i];
            }

            //Completa el codigo de verificación
            $msVerificacion .= $msRegistro . rand(10000, 99999);

            $msConsulta = "update UMO002B set FOLDER_002 = ?, VERIFICACION_002 = ? where CARNET_REL = ? and EXPDIGITAL_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msFolder, $msVerificacion, $msCarnet, $msExpDigital]);
        }

        //Nombre del estudiante
        $pdf->SetFont('helvetica','B',22);
        $pdf->setXY(20,20);
        $pdf->Cell(200,10,$msCarrera,0,0,'L');
        $pdf->setXY(20,30);
        $pdf->Cell(200,10,$msCarnet,0,0,'L');
        $pdf->setXY(20,40);
        $pdf->Cell(200,10,$msNombre,0,0,'L');
        
        //Código QR
		$style = array(
			'border' => false,
			'vpadding' => 0,
			'hpadding' => 0,
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
		);
        $msCodigoQR = "https://umojn.edu.ni/expediente/expdigital.php?UMOJN=" . $msVerificacion;
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 60, 30, 30, $style, 'N');
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 100, 35, 35, $style, 'N');
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 145, 40, 40, $style, 'N');
    }
    $pdf->Output();
?>