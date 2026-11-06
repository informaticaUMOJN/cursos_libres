<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}

require_once ("funciones/fxGeneral.php");
require_once ("funciones/fxUsuarios.php");
require_once ("tcpdf/tcpdf.php");
$m_cnx_MySQL = fxAbrirConexion();
$mnRegistro = fxVerificaUsuario();
$mbAdministrador = fxVerificaAdministrador();

if ($mnRegistro == 0)
{
?>
	<div class="container text-center">
    	<div id="DivContenido">
        	<img src="imagenes/errordeacceso.png"/>
        </div>
    </div>
<?php 
}
else
{
	class PDF extends TCPDF
	{
		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,12,0,18);
			// Title
			$mid_x = 210; // width of the "PDF screen", fixed by now.

			$this->SetFont('helvetica','B',15);
			$Titulo = 'ESTUDIANTES MATRICULADOS';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 15, $Titulo);
		}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica','I',8);
			// Page number
			$this->Cell(0,10,'Página '.$this->PageNo().' de '.$this->getAliasNbPages(),0,0,'L');
			$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
		}
	}

	function fxDevuelveEstado($mnEstado)
	{
		switch(intval($mnEstado))
		{
			case 0:
				$msEstado = "Pre-matriculado";
			case 1:
				$msEstado = "Activo";
				break;
			case 2:
				$msEstado = "Retirado";
				break;
			case 3:
				$msEstado = "Certificado";
				break;
		}

		return $msEstado;
	}

	$mbTipo = $_POST["mbTipo"];
	$mdFechaIni = $_POST["mdFechaIni"];
	$mdFechaFin = $_POST["mdFechaFin"];

	$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}

	if ($mbTipo == 0)
	{
		$msConsulta = "select CURSOS_REL, concat(NOMBRE_190, ' (', COHORTE_190, '/G', GRUPO_190, ')') as NOMBRE_190 from UMO190A where ESTADO_190 = 1";
		$mCursos = $m_cnx_MySQL->prepare($msConsulta);
		$mCursos->execute();
	}
	else
	{
		$msConsulta = "select CURSOS_REL, concat(NOMBRE_190, ' (', COHORTE_190, '/G', GRUPO_190, ')') as NOMBRE_190 from UMO190A where FECHAINICIO_190 between ? and ?";
		$mCursos = $m_cnx_MySQL->prepare($msConsulta);
		$mCursos->execute([$mdFechaIni, $mdFechaFin]);
	}

	$mnCuentaCursos = 1;
	$mnCursosTotales = $mCursos->rowCount();

	$pdf->addPage();
	$pdf->setFontSize(8);

	while ($mrCursos = $mCursos->fetch())
	{
		$msCodCurso = $mrCursos["CURSOS_REL"];
		$msCurso = $mrCursos["NOMBRE_190"];
		$msConsulta = "select MATCURSO_REL, concat(NOMBRES_200, ' ', APELLIDOS_200) as ESTUDIANTE, ESTADO_210 from UMO210A join UMO200A on UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL where CURSOS_REL = ?";
		$mEstudiantes = $m_cnx_MySQL->prepare($msConsulta);
		$mEstudiantes->execute([$msCodCurso]);

		$msHTML = '<style>';
		$msHTML .= "th{";
		$msHTML .= "background-color:rgb(0,0,255); color: white;";
		$msHTML .= "}";
		$msHTML .= ".fondo{";
		$msHTML .= "background-color: rgb(235,235,235);";
		$msHTML .= "}";
		$msHTML .= ".centro{";
		$msHTML .= "text-align: center;";
		$msHTML .= "}";
		$msHTML .= ".derecha{";
		$msHTML .= "text-align: right;";
		$msHTML .= "}";
		$msHTML .= ".bordeSuperior{";
		$msHTML .= "border-top: 2px solid black; border-right: none; border-bottom: none; border-left: none;";
		$msHTML .= "}";
		$msHTML .= '</style>';

		$msHTML .= '<h3>' . $msCurso . '</h3>';

		$msHTML .= '<table cellpadding="2">';
		$msHTML .= '<thead><tr>';
		$msHTML .= '<th width="15%"><b>MATRICULA</b></th>';
		$msHTML .= '<th width="70%"><b>ESTUDIANTE</b></th>';
		$msHTML .= '<th width="15%"><b>ESTADO</b></th>';
		$msHTML .= '</tr></thead>';
		$msHTML .= '<tbody>';
		
		$mbColorea = 0;
		while ($mrEstudiantes = $mEstudiantes->fetch())
		{
			$msMatricula = $mrEstudiantes["MATCURSO_REL"];
			$msEstudiante = trim($mrEstudiantes["ESTUDIANTE"]);
			$msEstado = fxDevuelveEstado($mrEstudiantes["ESTADO_210"]);

			$msHTML .= '<tr>';

			if ($mbColorea == 0)
			{
				$msHTML .= '<td width="15%">' . $msMatricula . '</td>';
				$msHTML .= '<td width="70%">' . $msEstudiante . '</td>';
				$msHTML .= '<td width="15%">' . $msEstado . '</td>';
			}
			else
			{
				$msHTML .= '<td width="15%" class="fondo">' . $msMatricula . '</td>';
				$msHTML .= '<td width="70%" class="fondo">' . $msEstudiante . '</td>';
				$msHTML .= '<td width="15%" class="fondo">' . $msEstado . '</td>';
			}

			$msHTML .= '</tr>';

			if ($mbColorea == 0)
				$mbColorea = 1;
			else
				$mbColorea = 0;
		}
		
		$msHTML .= '</tbody>';
		$msHTML .= '</table>';
		$pdf->writeHTML($msHTML);

		$mnCuentaCursos++;
		if ($mnCuentaCursos <= $mnCursosTotales)
			$pdf->addPage();
	}
	
	$pdf->Output();
}
?>