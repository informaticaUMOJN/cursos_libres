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
$Registro = fxVerificaUsuario();
$mbAdministrador = fxVerificaAdministrador();

if ($Registro == 0)
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
		// Page header
		function Header()
		{
			// Logos
			$this->Image('imagenes/logoRep.jpg',15,6,0,16);
			// Title
			$mid_x = 278; // width of the "PDF screen", fixed by now.

			$this->SetFont('helvetica','',9);
			$Titulo = 'UNIVERSIDAD DE MEDICINA ORIENTAL JAPON-NICARAGUA';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 10, $Titulo);
			$Titulo = 'Dirección Académica';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 15, $Titulo);
			
			$this->SetFont('helvetica','B',11);
			$Titulo = 'SYLLABUS';
			$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 19, $Titulo);
		}

		// Page footer
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica','I',8);
			// Page number
			$this->Cell(0,10,'Página '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'L');
			$this->Cell(0,10,'Emitido: ' . date("d/m/Y h:i:s a") . '',0,0,'R');
		}
	}

	function devuelveFecha($Fecha)
	{
		$FechaDividida = explode("-", $Fecha);
		
		$Anno = $FechaDividida[0];
		$Mes = $FechaDividida[1];
		$Dia = $FechaDividida[2];
		
		switch ($Mes)
			{
				case "01":
					$NombreMes = "Ene";
					break;
				case "02":
					$NombreMes = "Feb";
					break;
				case "03":
					$NombreMes = "Mar";
					break;
				case "04":
					$NombreMes = "Abr";
					break;
				case "05":
					$NombreMes = "May";
					break;
				case "06":
					$NombreMes = "Jun";
					break;
				case "07":
					$NombreMes = "Jul";
					break;
				case "08":
					$NombreMes = "Ago";
					break;
				case "09":
					$NombreMes = "Sep";
					break;
				case "10":
					$NombreMes = "Oct";
					break;
				case "11":
					$NombreMes = "Nov";
					break;
				case "12":
					$NombreMes = "Dic";
					break;
			}
		return ($Dia . "-" . $NombreMes . "-" . $Anno);
	}

	function devuelveSemestre($mnSemestre)
	{
		switch(intval($mnSemestre))
		{
			case 1:
				$msResultado = "Primero";
				break;
			case 2:
				$msResultado = "Segundo";
				break;
			case 3:
				$msResultado = "Tercero";
				break;
			case 4:
				$msResultado = "Cuarto";
				break;
			case 5:
				$msResultado = "Quinto";
				break;
			case 6:
				$msResultado = "Sexto";
				break;
			case 7:
				$msResultado = "Séptimo";
				break;
			case 8:
				$msResultado = "Octavo";
				break;
			case 9:
				$msResultado = "Noveno";
				break;
			case 10:
				$msResultado = "Décimo";
				break;
		}
		return $msResultado;
	}

	function devuelveTurno($mnTurno)
	{
		switch($mnTurno)
		{
			case 1:
				$msResultado = "Diurno";
				break;
			case 2:
				$msResultado = "Matutino";
				break;
			case 3:
				$msResultado = "Vespertino";
				break;
			case 4:
				$msResultado = "Nocturno";
				break;
			case 5:
				$msResultado = "Sabatino";
				break;
			case 6:
				$msResultado = "Dominical";
				break;
		}
		return $msResultado;
	}

	function devuelveArea($msCodAsignatura)
	{
		$msSigla = substr($msCodAsignatura, -1);

		switch($msSigla)
		{
			case "B":
				$msResultado = "Básica";
				break;
			case "G":
				$msResultado = "General";
				break;
			case "P":
				$msResultado = "Profesionalizante";
				break;
		}
		return $msResultado;
	}

	$mSyllabus = $_POST["UMOJN"];

	$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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

	$msConsulta = "select UMO070A.SYLLABUS_REL, GRUPO_070, NOMBRE_040, NOMBRE_100, NOMBRE_060, CODIGO_060, SEMESTRE_051, ";
	$msConsulta .= "HTOTALES_051, HTRABAJO_051, HAUTOESTUDIO_051, HPRESENCIALES_051, CREDITOS_051, TURNO_050, EJESVALORES_070, ";
	$msConsulta .= "RECOMENDACIONES_070 from UMO070A, UMO050A, UMO040A, UMO100A, UMO060A, UMO051A ";
	$msConsulta .= "where UMO070A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and ";
	$msConsulta .= "UMO050A.PLANESTUDIO_REL = UMO051A.PLANESTUDIO_REL and ";
	$msConsulta .= "UMO050A.CARRERA_REL = UMO040A.CARRERA_REL and ";
	$msConsulta .= "UMO070A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and ";
	$msConsulta .= "UMO070A.ASIGNATURA_REL = UMO051A.ASIGNATURA_REL and ";
	$msConsulta .= "UMO070A.DOCENTE_REL = UMO100A.DOCENTE_REL and ";
	$msConsulta .= "UMO070A.SYLLABUS_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$mSyllabus]);
	$mFila = $mDatos->fetch();

	/***PRIMERA PAGINA***/
	$msGrupo = $mFila["GRUPO_070"];
	$msCarrera = $mFila["NOMBRE_040"];
	$msDocente = $mFila["NOMBRE_100"];
	$msAsignatura = $mFila["NOMBRE_060"];
	$msArea = devuelveArea($mFila["CODIGO_060"]);
	$msSemestre = devuelveSemestre($mFila["SEMESTRE_051"]);
	$mnHorasTotales = $mFila["HTOTALES_051"];
	$mnHorasTrabajo = $mFila["HTRABAJO_051"];
	$mnHorasAutoestudio = $mFila["HAUTOESTUDIO_051"];
	$mnHorasPresenciales = $mFila["HPRESENCIALES_051"];
	$mnCreditos = $mFila["CREDITOS_051"];
	$msTurno = devuelveTurno($mFila["TURNO_050"]);
	$msEjesValores = $mFila["EJESVALORES_070"];
	$msMediacion = $mFila["RECOMENDACIONES_070"];

	//Primera columna
	$mnLinea = 30;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Carrera");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msCarrera);
	
	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Turno");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msTurno);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Asignatura");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msAsignatura);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Docente");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msDocente);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Grupo");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msGrupo);

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Semestre");
	$pdf->SetXY(40, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msSemestre);

	//Segunda columna
	$mnLinea = 30;
	$pdf->SetXY(170, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Horas totales");
	$pdf->SetXY(220, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$mnHorasTotales);

	$mnLinea += 5;
	$pdf->SetXY(170, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Horas presenciales");
	$pdf->SetXY(220, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$mnHorasPresenciales);

	$mnLinea += 5;
	$pdf->SetXY(170, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Horas trabajo");
	$pdf->SetXY(220, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$mnHorasTrabajo);

	$mnLinea += 5;
	$pdf->SetXY(170, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Horas trabajo independiente");
	$pdf->SetXY(220, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$mnHorasAutoestudio);

	$mnLinea += 5;
	$pdf->SetXY(170, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Créditos");
	$pdf->SetXY(220, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$mnCreditos);

	$mnLinea += 5;
	$pdf->SetXY(170, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(25,0,"Area de formación");
	$pdf->SetXY(220, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(20,0,$msArea);
	
	/***OBJETIVOS GENERALES***/
	$mnLinea += 10;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(50,0,"OBJETIVOS GENERALES");
	
	$msConsulta = "select TEXTO_071 from UMO071A where SYLLABUS_REL = ?";
	$mObjGral = $m_cnx_MySQL->prepare($msConsulta);
	$mObjGral->execute([$mSyllabus]);
	$mnRegistros = $mObjGral->rowCount();
	
	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$msHTML = "<table>";
	$mnRow = 1;
	while ($mFilaObj = $mObjGral->fetch())
	{
		$msHTML .= '<tr>';
		if ($mnRow % 2 == 0)
			$msHTML .= '<td width="100%" style="background-color:rgb(230,230,230); text-align: justify;">* ' . $mFilaObj["TEXTO_071"] . '</td>';
		else
			$msHTML .= '<td width="100%" style="text-align: justify;">* ' . $mFilaObj["TEXTO_071"] . '</td>';

		$msHTML .= '</tr>';
		$mnRow++;
	}
	$msHTML .= "</table>";
	$pdf->writeHTML($msHTML);

	$mnLinea += 11 * $mnRegistros;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(50,0,"PROPOSITOS POR UNIDAD");

	$msConsulta = "select UNIDAD_072, TEXTO_072 from UMO072A where SYLLABUS_REL = ?";
	$mObjGral = $m_cnx_MySQL->prepare($msConsulta);
	$mObjGral->execute([$mSyllabus]);
	$mnRegistros = $mObjGral->rowCount();

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$msHTML = "<table>";
	$mnRow = 1;
	while ($mFilaObj = $mObjGral->fetch())
	{
		$msHTML .= '<tr>';
		if ($mnRow % 2 == 0)
		{
			$msHTML .= '<td width="30%" style="background-color:rgb(230,230,230); text-align: justify;">' . $mFilaObj["UNIDAD_072"] . '</td>';
			$msHTML .= '<td width="70%" style="background-color:rgb(230,230,230); text-align: justify;">' . $mFilaObj["TEXTO_072"] . '</td>';
		}
		else
		{
			$msHTML .= '<td width="30%" style="text-align: justify;">' . $mFilaObj["UNIDAD_072"] . '</td>';
			$msHTML .= '<td width="70%" style="text-align: justify;">' . $mFilaObj["TEXTO_072"] . '</td>';
		}
		$msHTML .= '</tr>';
		$mnRow++;
	}
	$msHTML .= "</table>";
	$pdf->writeHTML($msHTML);

	$mnLinea += 11 * $mnRegistros;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(50,0,"EJES TRANSVERSALES Y VALORES");

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(200,0,$msEjesValores);

	$mnLinea += 10;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Cell(50,0,"MEDIACION PEDAGOGICA");

	$mnLinea += 5;
	$pdf->SetXY(15, $mnLinea);
	$pdf->SetFont('helvetica','',10);
	$pdf->Cell(200,0,$msMediacion);

	/***SEGUNDA PAGINA***/
	$pdf->AddPage();
	$msConsulta = "select * from UMO073A where SYLLABUS_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$mSyllabus]);

	$mbColorea = 0;
	$pdf->SetXY(15,30);

	$msHTML = '<table cellpadding="5px">';
	$msHTML .= '<thead>';
	$msHTML .= '<tr style="color:rgb(255,255,255); background-color:rgb(0,0,230); text-align: justify;">';
	$msHTML .= '<th width="8%">Semana</th>';
	$msHTML .= '<th width="10%">Fecha</th>';
	$msHTML .= '<th width="10%">Unidad</th>';
	$msHTML .= '<th width="12%">Contenido</th>';
	$msHTML .= '<th width="22%">Objetivo específico</th>';
	$msHTML .= '<th width="18%">Mediación pedagógica y ejes transversales</th>';
	$msHTML .= '<th width="10%">Recursos didácticos</th>';
	$msHTML .= '<th width="10%">Evaluación</th>';
	$msHTML .= '</tr></thead>';
	while ($mFila = $mDatos->fetch())
	{
		$msHTML .= "<tr>";
		if ($mbColorea == 1)
		{
			$msHTML .= '<td width="8%" style="background-color:rgb(230,230,230); text-align: justify;">' . $mFila["DETSYLLABUS_REL"] . '</td>';
			$msHTML .= '<td width="10%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . devuelveFecha($mFila["FECHA_073"]) . '</td>';
			$msHTML .= '<td width="10%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . $mFila["UNIDAD_073"] . '</td>';
			$msHTML .= '<td width="12%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . $mFila["CONTENIDO_073"] . '</td>';
			$msHTML .= '<td width="22%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . $mFila["OBJETIVOESP_073"] . '</td>';
			$msHTML .= '<td width="18%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . $mFila["FORMA_073"] . '</td>';
			$msHTML .= '<td width="10%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . $mFila["MEDIOS_073"] . '</td>';
			$msHTML .= '<td width="10%" style="background-color:rgb(230,230,230); text-align: justify; border-left: 1px solid black;">' . $mFila["EVALUACION_073"] . '</td>';
		}
		else
		{
			$msHTML .= '<td width="8%" style="text-align: justify;">' . $mFila["DETSYLLABUS_REL"] . '</td>';
			$msHTML .= '<td width="10%" style="text-align: justify; border-left: 1px solid black; padding: 2px;">' . devuelveFecha($mFila["FECHA_073"]) . '</td>';
			$msHTML .= '<td width="10%" style="text-align: justify; border-left: 1px solid black;">' . $mFila["UNIDAD_073"] . '</td>';
			$msHTML .= '<td width="12%" style="text-align: justify; border-left: 1px solid black;">' . $mFila["CONTENIDO_073"] . '</td>';
			$msHTML .= '<td width="22%" style="text-align: justify; border-left: 1px solid black;">' . $mFila["OBJETIVOESP_073"] . '</td>';
			$msHTML .= '<td width="18%" style="text-align: justify; border-left: 1px solid black;">' . $mFila["FORMA_073"] . '</td>';
			$msHTML .= '<td width="10%" style="text-align: justify; border-left: 1px solid black;">' . $mFila["MEDIOS_073"] . '</td>';
			$msHTML .= '<td width="10%" style="text-align: justify; border-left: 1px solid black;">' . $mFila["EVALUACION_073"] . '</td>';
		}
		$msHTML .= "</tr>";

		if ($mbColorea == 0)
			$mbColorea = 1;
		else
			$mbColorea = 0;
	}
	$msHTML .= "</table>";
	$pdf->writeHTML($msHTML);
	$pdf->Output();
}
?>