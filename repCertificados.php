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
			$Titulo = 'ESTUDIANTES CERTIFICADOS';
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

	$msCurso = $_POST["UMOJN"];

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

	$msConsulta = "select UMO190A.CURSOS_REL, TIPOC_190, NOMBRE_190, FECHA_370, FECHAINICIO_190, FECHAFIN_190, HRSTOTAL_190
	from UMO190A, UMO370A
	where UMO190A.CURSOS_REL = UMO370A.CURSOS_REL
	and UMO190A.CURSOS_REL = ?";
	$mCurso = $m_cnx_MySQL->prepare($msConsulta);
	$mCurso->execute([$msCurso]);
	$mrCurso = $mCurso->fetch();

	$msCodCurso = $mrCurso["CURSOS_REL"];
	$mnTipo = $mrCurso["TIPOC_190"];
	$msNombreCurso = $mrCurso["NOMBRE_190"];
	$mdFechaReg = $mrCurso["FECHA_370"];
	$mdFechaIni = $mrCurso["FECHAINICIO_190"];
	$mdFechaFin = $mrCurso["FECHAFIN_190"];
	$mnHoras = $mrCurso["HRSTOTAL_190"];

	$pdf->addPage();
	$pdf->setFontSize(8);

	$msHTML = '<style>';
	$msHTML .= "th{";
	$msHTML .= "background-color:rgb(0,0,255); color: white;";
	$msHTML .= "}";
	$msHTML .= ".fondo{";
	$msHTML .= "background-color: rgb(220, 220, 220);";
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

	$msHTML .= '<h3>' . DevuelveTipoCurso($mnTipo) . '</h3>';
	$msHTML .= '<h2>' . $msNombreCurso . '</h2>';
	$msHTML .= '<h4>' . "Registrado: " . DevuelveFecha($mdFechaReg) . '</h4>';
	$msHTML .= '<h4>' . "Dado: " . DevuelveFecha($mdFechaFin) . '</h4>';
	$msHTML .= '<h4>' . "Horas Teórico-prácticas: " . $mnHoras . '</h4>';
	$msHTML .= '<h4>' . "Período: Del " . DevuelveFecha($mdFechaIni) . " al " . DevuelveFecha($mdFechaFin) . '</h4>';

	$msHTML .= '<table cellpadding="2">';
	$msHTML .= '<thead><tr>';
	$msHTML .= '<th width="5%" class="centro"><b>NO.</b></th>';
	$msHTML .= '<th width="65%"><b>NOMBRE COMPLETO</b></th>';
	$msHTML .= '<th width="10%" class="centro"><b>REGISTRO</b></th>';
	$msHTML .= '<th width="10%" class="centro"><b>FOLIO</b></th>';
	$msHTML .= '<th width="10%" class="centro"><b>TOMO</b></th>';
	$msHTML .= '</tr></thead>';
	$msHTML .= '<tbody>';

	$msConsulta = "select concat_ws(' ', NOMBRES_200, APELLIDOS_200) as ALUMNO, PARTIDA_REL, FOLIO_371, TOMO_371
	from UMO210A, UMO200A, UMO370A, UMO371A
	where UMO370A.REGISTRO_REL = UMO371A.REGISTRO_REL
	and UMO371A.MATCURSO_REL = UMO210A.MATCURSO_REL
	and UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
	and UMO370A.CURSOS_REL = ? order by PARTIDA_REL";

	$mnConteo = 1;
	$mbColorea = 0;

	$mCertificados = $m_cnx_MySQL->prepare($msConsulta);
	$mCertificados->execute([$msCurso]);
	while ($mrCertificados = $mCertificados->fetch())
	{
		$msAlumno = $mrCertificados["ALUMNO"];
		$msPartida = $mrCertificados["PARTIDA_REL"];
		$msFolio = $mrCertificados["FOLIO_371"];
		$msTomo = $mrCertificados["TOMO_371"];
		
		$msHTML .= '<tr>';

		if ($mbColorea == 0)
		{
			$msHTML .= '<td width="5%" class="centro">' . $mnConteo . '</td>';
			$msHTML .= '<td width="65%">' . $msAlumno . '</td>';
			$msHTML .= '<td width="10%" class="centro">' . $msPartida . '</td>';
			$msHTML .= '<td width="10%" class="centro">' . $msFolio . '</td>';
			$msHTML .= '<td width="10%" class="centro">' . $msTomo . '</td>';
		}
		else
		{
			$msHTML .= '<td width="5%" class="fondo centro">' . $mnConteo . '</td>';
			$msHTML .= '<td width="65%" class="fondo">' . $msAlumno . '</td>';
			$msHTML .= '<td width="10%" class="fondo centro">' . $msPartida . '</td>';
			$msHTML .= '<td width="10%" class="fondo centro">' . $msFolio . '</td>';
			$msHTML .= '<td width="10%" class="fondo centro">' . $msTomo . '</td>';
		}

		$msHTML .= '</tr>';

		$mnConteo++;

		if ($mbColorea == 0)
			$mbColorea = 1;
		else
			$mbColorea = 0;
	}

	$msHTML .= '</tbody>';
	$msHTML .= '</table>';
	$pdf->writeHTML($msHTML);
	$pdf->Output();
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
?>