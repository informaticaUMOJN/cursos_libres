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
$mnAdministrador = fxVerificaAdministrador();
$mnSupervisor = fxVerificaSupervisor();

if ($mnRegistro == 0)
{
?>
	<div class="container text-center">
    	<div id="DivContenido">
        	<img src="imagenes/errordeacceso.png"/>
        </div>
    </div>
<?php }
else
{
	class PDF extends TCPDF
	{
		public $msCurso;
		public $msDocente;
		public $msFecha;

		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,12,0,16);
			// Title
			$mid_x = 278; // width of the "PDF screen", fixed by now.
			// Helvetica bold 18
			$this->SetFont('helvetica','B',14);
			$Titulo = 'ASISTENCIA ESTUDIANTIL';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 13, $Titulo);
			// Helvetica normal 18
			$this->SetFont('helvetica','',12);
			$Titulo = $this->msCurso;
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 19, $Titulo);
			$this->Text(($mid_x - $this->GetStringWidth($this->msFecha)) / 2, 24, $this->msFecha);
			$this->SetFont('helvetica','',10);
			$Titulo = "Docente: " . $this->msDocente;
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 30, $Titulo);
		}
		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Helvetica italic 8
			$this->SetFont('helvetica','I',8);
			// Page number
			$this->Cell(0,10,'Página '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'L');
			$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
		}
	}

	function DevuelveTurno($turno)
	{
		switch(intval($turno))
		{
			case 1:
				$resultado = "Diurno";
				break;
			case 2:
				$resultado = "Matutino";
				break;
			case 3:
				$resultado = "Vespertino";
				break;
			case 4:
				$resultado = "Nocturno";
				break;
			case 5:
				$resultado = "Sabatino";
				break;
			case 6:
				$resultado = "Dominical";
				break;
		}
		return $resultado;
	}
	
	function DevuelveFecha($Fecha)
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
		return ($Dia . " de " . $NombreMes . " de " . $Anno);
	}

	$msCodCurso = $_POST["msCurso"];
	$msFecha = $_POST["msFecha"];

	$msConsulta = "select NOMBRE_190, NOMBRE_340 from UMO190A join UMO340A on UMO190A.DOCENTECL_REL = UMO340A.DOCENTECL_REL where CURSOS_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodCurso]);
	$mRegistros = $mDatos->rowCount();

	if ($mRegistros > 0)
	{
		$mFila = $mDatos->fetch();
		$msDocente = $mFila["NOMBRE_340"];
		$msCurso = $mFila["NOMBRE_190"];
	}
	else
	{
		$msDocente = "";
		$msCurso = "";		
	}

	$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
		require_once(dirname(__FILE__).'/lang/spa.php');
		$pdf->setLanguageArray($l);
	}
	
	$pdf->msCurso=$msCurso;
	$pdf->msDocente=$msDocente;
	$pdf->msFecha=DevuelveFecha($msFecha);
	$pdf->AddPage();

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',10);

	$mbRelleno = true;
	$mbEscribeRotulo = true;
	$mnLinea = 35;
	$msHTML = '<style>';
	$msHTML .= ".ancho{";
	$msHTML .= "width: 70";
	$msHTML .= "}";
	$msHTML .= ".alto{";
	$msHTML .= "height: 30;";
	$msHTML .= "}";
	$msHTML .= ".celdaTh{";
	$msHTML .= "background-color: rgb(0,0,230); color: rgb(255,255,255)";
	$msHTML .= "}";
	$msHTML .= ".anchoE{";
	$msHTML .= "width: 200;";
	$msHTML .= "}";
	$msHTML .= ".anchoJ{";
	$msHTML .= "width: 200;";
	$msHTML .= "}";
	$msHTML .= ".centro{";
	$msHTML .= "text-align: center;";
	$msHTML .= "}";
	$msHTML .= ".bordeSuperior{";
	$msHTML .= "border-top: 2px solid black; border-right: none; border-bottom: none; border-left: none;";
	$msHTML .= "}";
	$msHTML .= ".bordeIzquierdo{";
	$msHTML .= "border-top: none; border-right: none; border-bottom: none; border-left: 1px solid black;";
	$msHTML .= "}";
	$msHTML .= ".relleno{";
	$msHTML .= "background-color: rgb(230,230,230);";
	$msHTML .= '</style>';

	$msHTML .= '<table style="width: 100%">';
	$msHTML .= '<thead><tr>';
	$msHTML .= '<th class="anchoE alto celdaTh">Estudiante</th>';
	$msHTML .= '<th class="ancho alto centro celdaTh bordeIzquierdo">Presente</th>';
	$msHTML .= '<th class="ancho alto centro celdaTh bordeIzquierdo">Ausente</th>';
	$msHTML .= '<th class="ancho alto centro celdaTh bordeIzquierdo">Justificado</th>';
	$msHTML .= '<th class="anchoJ alto celdaTh bordeIzquierdo">Justificación</th>';
	$msHTML .= '</tr></thead>';

	$msConsulta = "select UMO210A.MATCURSO_REL, NOMBRES_200, APELLIDOS_200 from UMO210A, UMO200A ";
	$msConsulta .= "where UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL and CURSOS_REL = ?";
	$mEstudiantes = $m_cnx_MySQL->prepare($msConsulta);
	$mEstudiantes->execute([$msCodCurso]);
		
	$msHTML .= '<tbody>';
	
	while ($mFilaEstudiantes = $mEstudiantes->fetch())
	{
		$msMatricula = $mFilaEstudiantes["MATCURSO_REL"];
		$msEstudiante = $mFilaEstudiantes["APELLIDOS_200"] . ', ' . $mFilaEstudiantes["NOMBRES_200"];
		
		$msHTML .= '<tr>';
		if ($mbRelleno)
		{
			$msHTML .= '<td class="anchoE alto relleno">' . $msEstudiante . '</td>';
			$msHTML .= '<td class="ancho alto bordeIzquierdo relleno">&nbsp;</td>';
			$msHTML .= '<td class="ancho alto bordeIzquierdo relleno">&nbsp;</td>';
			$msHTML .= '<td class="ancho alto bordeIzquierdo relleno">&nbsp;</td>';
			$msHTML .= '<td class="anchoJ alto bordeIzquierdo relleno">&nbsp;</td>';
		}
		else
		{
			$msHTML .= '<td class="anchoE alto">' . $msEstudiante . '</td>';
			$msHTML .= '<td class="ancho alto bordeIzquierdo">&nbsp;</td>';
			$msHTML .= '<td class="ancho alto bordeIzquierdo">&nbsp;</td>';
			$msHTML .= '<td class="ancho alto bordeIzquierdo">&nbsp;</td>';
			$msHTML .= '<td class="anchoJ alto bordeIzquierdo">&nbsp;</td>';
		}
		$msHTML .= '</tr>';
		$mbRelleno = !$mbRelleno;
	}

	$msHTML .= '</tbody></table>';
	$msHTML .= '<br><br><br><br><br><br>';
	$msHTML .= '<table>';
	$msHTML .= '<tr>';
	$msHTML .= '<td style="width: 35%;">&nbsp;</td>';
	$msHTML .= '<td style="width: 30%;" class="bordeSuperior centro">' . $msDocente . '<br>Docente</td>';
	$msHTML .= '<td style="width: 35%;">&nbsp;</td>';
	$msHTML .= '</tr>';
	$msHTML .= '</table>';
	$pdf->SetXY(15,$mnLinea);
	$pdf->writeHTML($msHTML);
	$pdf->Output();
}
?>