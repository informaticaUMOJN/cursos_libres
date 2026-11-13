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
        $msCurso = $_POST['UMOJN'];

    $msConsulta = "SELECT PARTIDA_REL, UMO210A.MATCURSO_REL, concat_ws(' ', NOMBRES_200, APELLIDOS_200) as ALUMNO, NOMBRE_190, VERIFICACION_371
    FROM UMO371A, UMO370A, UMO210A, UMO200A, UMO190A
    where UMO371A.REGISTRO_REL = UMO370A.REGISTRO_REL
    and UMO371A.MATCURSO_REL = UMO210A.MATCURSO_REL
    and UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
    and UMO210A.CURSOS_REL = UMO190A.CURSOS_REL
    and UMO370A.CURSOS_REL = ? order by PARTIDA_REL";

    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$msCurso]);
    
    while ($mFila = $mDatos->fetch())
    {
        $pdf->AddPage();
        $msPartida = $mFila["PARTIDA_REL"];
        $msMatricula = $mFila["MATCURSO_REL"];
        $msAlumno = $mFila["ALUMNO"];
        $msCurso = $mFila["NOMBRE_190"];
        $msVerificacion = $mFila["VERIFICACION_371"];

        if ($msVerificacion == "")
        {
            $mdAhora = date('YmdHis');
            $msVerificacion .= rand(10000, 99999) . $msPartida . $mdAhora;

            $msConsulta = "update UMO371A set VERIFICACION_371 = ? where MATCURSO_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msVerificacion, $msMatricula]);
        }

        //Nombre del estudiante
        $pdf->SetFont('helvetica','B',18);
        $pdf->setXY(20,20);
        $pdf->Cell(200,10,$msCurso,0,0,'L');
        $pdf->setXY(20,30);
        $pdf->Cell(200,10,'Partida ' . $msPartida,0,0,'L');
        $pdf->setXY(20,40);
        $pdf->Cell(200,10,$msAlumno,0,0,'L');
        
        //Código QR
		$style = array(
			'border' => false,
			'vpadding' => 0,
			'hpadding' => 0,
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
		);
        $msCodigoQR = "https://cursoslibres.umojn.edu.ni/diploma.php?UMOJN=" . $msVerificacion;
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 60, 30, 30, $style, 'N');
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 100, 35, 35, $style, 'N');
        $pdf->write2DBarcode($msCodigoQR, 'QRCODE,H', 20, 145, 40, 40, $style, 'N');
    }
    $pdf->Output();
?>