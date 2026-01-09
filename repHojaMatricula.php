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

$Registro = fxVerificaUsuario();
	
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
		public $msMatricula;

		// Page header
		function Header()
		{
			$mid_x = 210; // width of the "PDF screen", fixed by now.

			// Logos
			$this->Image('imagenes/logoRep.jpg',15,8,0,16);
			$this->Image('imagenes/kanji.jpg',31,8,0,16);

			// Title
			$this->SetFont('helvetica','B',12);
			$msTitulo = 'UNIVERSIDAD DE MEDICINA';
			$this->Text(41, 8, $msTitulo);
			$msTitulo = 'ORIENTAL JAPON-NICARAGUA';
			$this->Text(41, 13, $msTitulo);
			$msTitulo = 'Excelencia académica con espíritu humanista';
			$this->SetFont('helvetica','I',9);
			$this->Text(41, 20, $msTitulo);
			
			$this->Line(120,10,120,22);
			$this->SetFont('helvetica','',7);
			$msTitulo = 'Puente del paso a desnivel de Rubenia 7c. al oeste';
			$this->Text(128, 10, $msTitulo);
			$msTitulo = 'Barrio Venezuela. Managua, Nicaragua';
			$this->Text(128, 13, $msTitulo);
			$msTitulo = 'registro.academico@umojn.edu.ni';
			$this->Text(128, 16, $msTitulo);
			$msTitulo = '2253-0340 / 2253-0344';
			$this->Text(128, 19, $msTitulo);

			$this->SetTextColor(0,0,250);
			$this->SetFont('helvetica','B',15);
			$msTitulo = 'HOJA DE MATRICULA ' . $this->msMatricula;
			$this->Text(($mid_x - $this->GetStringWidth($msTitulo)) / 2, 25, $msTitulo);
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

	function fxFechaCorta($Fecha)
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

	function fxFechaLarga($Fecha)
	{
		$FechaDividida = explode("-", $Fecha);
		
		$Anno = $FechaDividida[0];
		$Mes = $FechaDividida[1];
		$Dia = $FechaDividida[2];
		
		switch ($Mes)
			{
				case "01":
					$NombreMes = "Enero";
					break;
				case "02":
					$NombreMes = "Febrero";
					break;
				case "03":
					$NombreMes = "Marzo";
					break;
				case "04":
					$NombreMes = "Abril";
					break;
				case "05":
					$NombreMes = "Mayo";
					break;
				case "06":
					$NombreMes = "Junio";
					break;
				case "07":
					$NombreMes = "Julio";
					break;
				case "08":
					$NombreMes = "Agosto";
					break;
				case "09":
					$NombreMes = "Septiembre";
					break;
				case "10":
					$NombreMes = "Octubre";
					break;
				case "11":
					$NombreMes = "Noviembre";
					break;
				case "12":
					$NombreMes = "Diciembre";
					break;
			}
		return ($Dia . " de " . $NombreMes . " de " . $Anno);
	}

	function fxCalculaEdad($Fecha)
	{
		//fecha actual
		$annoHoy=date("Y");
		$mesHoy=date("n");
		$diaHoy=date("j");

		//fecha de nacimiento
		$FechaDividida = explode("-", $Fecha);
		$annoNac = $FechaDividida[0];
		$mesNac = $FechaDividida[1];
		$diaNac = $FechaDividida[2];
		
		$edad= $annoHoy-$annoNac;

		if ($mesHoy < ($mesNac - 1))
			$edad -= 1;
			
		if (($mesNac - 1) == $mesHoy and $diaHoy < $diaNac)
			$edad -= 1;	
		
		return $edad;
	}

	$codMatricula = trim($_POST["UMOJN"]);

	//Obtención de datos
	$msConsulta = "select UMO030A.MATRICULA_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, SEXO_010, ANNOACADEMICO_030, CARNET_010, ";
	$msConsulta .= "FECHA_030, RECIBO_030, NACIONALIDAD_010, ETNIA_010, LUGARNAC_010, FECHANAC_010, CEDULA_010, TELEFONO_010, CELULAR_010, ";
	$msConsulta .= "DIRECCION_010, CORREOE_010, CORREOI_010, EMERGENCIA_010, TEL_EMERGENCIA_010, CEL_EMERGENCIA_010, MEDIO_010, PERIODO_050, ";
	$msConsulta .= "NOMBRE_040, NOMBRE_020, TIPO_020, IFNULL((select RUTA_011 from UMO011A where UMO011A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and TIPO_011 = 4), '') as FOTO, ";
	$msConsulta .= "DIPLOMA_030, NOTAS_030, CEDULA_030, ACTANACIMIENTO_030, TURNO_050 ";
	$msConsulta .= "from UMO030A, UMO010A, UMO040A, UMO050A, UMO020A ";
	$msConsulta .= "where UMO030A.CARRERA_REL = UMO040A.CARRERA_REL and UMO030A.PLANESTUDIO_REL = UMO050A.PLANESTUDIO_REL and ";
	$msConsulta .= "UMO030A.ESTUDIANTE_REL = UMO010A.ESTUDIANTE_REL and UMO010A.COLEGIO_REL = UMO020A.COLEGIO_REL and ";
	$msConsulta .= "MATRICULA_REL = ?";

	$m_cnx_MySQL = fxAbrirConexion();
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$codMatricula]);

	$mFila = $mDatos->fetch();
	$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
	
	$msMatricula = $mFila["MATRICULA_REL"];
	$msFoto = $mFila["FOTO"];
	$msNombre1 = $mFila["NOMBRE1_010"];
	$msNombre2 = $mFila["NOMBRE2_010"];
	$msApellido1 = $mFila["APELLIDO1_010"];
	$msApellido2 = $mFila["APELLIDO2_010"];
	$msSexo = $mFila["SEXO_010"];
	$mnAnnoAcademico = $mFila["ANNOACADEMICO_030"];
	$msCarnet = $mFila["CARNET_010"];
	$msFechaMat = $mFila["FECHA_030"];
	$msRecibo = $mFila["RECIBO_030"];
	$msNacinalidad = $mFila["NACIONALIDAD_010"];
	$msEtnia = $mFila["ETNIA_010"];
	$msLugarNac = $mFila["LUGARNAC_010"];
	$msFechaNac = $mFila["FECHANAC_010"];
	$msCedula = $mFila["CEDULA_010"];
	$msTelefono = $mFila["TELEFONO_010"];
	$msCelular = $mFila["CELULAR_010"];
	$msDireccion = $mFila["DIRECCION_010"];
	$msCorreoE = $mFila["CORREOE_010"];
	$msCorreoI = $mFila["CORREOI_010"];
	$msEmergencia = $mFila["EMERGENCIA_010"];
	$msTelEmergencia = $mFila["TEL_EMERGENCIA_010"];
	$msCelEmergencia = $mFila["CEL_EMERGENCIA_010"];
	$msMedio = $mFila["MEDIO_010"];
	$msPlanEstudio = $mFila["PERIODO_050"];
	$mnTurno = $mFila["TURNO_050"];
	$msCarrera = $mFila["NOMBRE_040"];
	$msColegio = $mFila["NOMBRE_020"];
	$mnTipoColegio = $mFila["TIPO_020"];
	$mbDiploma = $mFila["DIPLOMA_030"];
	$mbNotas = $mFila["NOTAS_030"];
	$mbCedula = $mFila["CEDULA_030"];
	$mbActaNac = $mFila["ACTANACIMIENTO_030"];
	
	$pdf->msMatricula=$msMatricula;
	
	$pdf->AddPage();
	$pdf->SetTextColor(0,0,0);

	if ($msFoto != '')
		$pdf->Image($msFoto,15,30,0,25);
	else{
		$pdf->Rect(15,35,18,20);
		$pdf->SetFont('helvetica','',10);
		$pdf->Text(18, 43, 'FOTO');
	}
	
	//Generales del estudiante
	$msEstudiante = $msNombre1;

	if (trim($msNombre2) != "")
		$msEstudiante .= ' ' . $msNombre2;
	
	$msEstudiante .= ' ' . $msApellido1;

	if (trim($msApellido2) != "")
		$msEstudiante .= ' ' . $msApellido2;
	
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(35, 35, $msEstudiante);
	
	$pdf->SetFont('helvetica','',10);
	if ($msSexo == "M")
		$msRotulo = "Nacido en " . $msLugarNac;
	else
		$msRotulo = "Nacida en " . $msLugarNac;
	$pdf->Text(35, 41, $msRotulo);

	$msRotulo = "el " . fxFechaLarga($msFechaNac) . " (" . fxCalculaEdad($msFechaNac) . " años)";
	$pdf->Text(35, 45, $msRotulo);

	$msRotulo = "Cédula " . $msCedula;
	$pdf->Text(35, 51, $msRotulo);

	//Generales de la matrícula
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 35, 'Fecha de la matrícula');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 35, fxFechaCorta($msFechaMat));

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 40, 'Recibo');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 40, $msRecibo);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 45, 'Carnet');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 45, $msCarnet);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(125, 50, 'Año académico');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(165, 50, $mnAnnoAcademico);

	//Datos académicos
	switch($mnTipoColegio){
		case 0:
			$msTipoColegio = "Privado";
			break;
		case 1:
			$msTipoColegio = "Público";
			break;
		case 2:
			$msTipoColegio = "Subvencionado";
			break;
		default:
			$msTipoColegio = "Otro";
	}

	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 60, 'Datos académicos');

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 65, 'Carrera');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 65, $msCarrera);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 70, 'Turno');

	$pdf->SetFont('helvetica','',10);
	if ($mnTurno == 0)
		$pdf->Image('imagenes/check.jpg', 60, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg',60, 70, 5);
	$pdf->Text(65,70,"Regular");

	if ($mnTurno == 1)
		$pdf->Image('imagenes/check.jpg', 80, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg', 80, 70, 5);
	$pdf->Text(85,70,"Sabatino");

	if ($mnTurno == 2)
		$pdf->Image('imagenes/check.jpg', 105, 70, 5);
	else
		$pdf->Image('imagenes/uncheck.jpg',105, 70, 5);
	$pdf->Text(110,70,"Dominical");

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 75, 'Plan de estudio');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 75, $msPlanEstudio);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 80, 'Colegio de procedencia');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 80, $msColegio . " (" . $msTipoColegio . ")");

	//Datos de contacto
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 90, 'Datos de contacto');

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 95, 'Teléfono');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 95, $msTelefono);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 100, 'Celular');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 100, $msCelular);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(100, 95, 'Correo particular');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(140, 95, $msCorreoE);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(100, 100, 'Correo institucional');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(140, 100, $msCorreoI);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 105, 'Dirección domiciliar');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 105, $msDireccion);

	//Contacto de emergencia
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 115, 'Contacto de emergencia');

	if ($msTelEmergencia != ''){
		$msTelsEmergencia = $msTelEmergencia;
		if ($msCelEmergencia != '')
			$msTelsEmergencia .= ' / ' . $msCelEmergencia;
	}
	else
		$msTelsEmergencia = $msCelEmergencia;

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 120, 'Nombre');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 120, $msEmergencia);

	$pdf->SetFont('helvetica','B',10);
	$pdf->Text(15, 125, 'Teléfono(s)');

	$pdf->SetFont('helvetica','',10);
	$pdf->Text(60, 125, $msTelsEmergencia);

	//Documentos entregados
	$pdf->SetTextColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Text(15, 135, 'Documentos entregados');

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',10);

	if ($mbCedula == 0)
		$pdf->Image('imagenes/uncheck.jpg', 30, 140, 5);
	else
		$pdf->Image('imagenes/check.jpg',30, 140, 5);
	$pdf->Text(35,140,"Cédula de identidad");

	if ($mbDiploma == 0)
		$pdf->Image('imagenes/uncheck.jpg', 80, 140, 5);
	else
		$pdf->Image('imagenes/check.jpg',80, 140, 5);
	$pdf->Text(85,140,"Diploma de bachiller");

	if ($mbActaNac == 0)
		$pdf->Image('imagenes/uncheck.jpg', 30, 145, 5);
	else
		$pdf->Image('imagenes/check.jpg',30, 145, 5);
	$pdf->Text(35,145,"Acta de nacimiento");

	if ($mbNotas == 0)
		$pdf->Image('imagenes/uncheck.jpg', 80, 145, 5);
	else
		$pdf->Image('imagenes/check.jpg',80, 145, 5);
	$pdf->Text(85,145,"Calificaciones de secundaria");

	/*Asignaturas inscritas*/
	$msConsulta = "select UMO031A.ASIGNATURA_REL, NOMBRE_060, CODIGO_060, CREDITOS_051 from UMO031A, UMO060A, UMO051A, UMO030A ";
	$msConsulta .= "where UMO031A.ASIGNATURA_REL = UMO060A.ASIGNATURA_REL and UMO031A.ASIGNATURA_REL = UMO051A.ASIGNATURA_REL ";
	$msConsulta .= "and UMO031A.MATRICULA_REL = ? and UMO030A.MATRICULA_REL = UMO031A.MATRICULA_REL and ";
	$msConsulta .= "UMO030A.PLANESTUDIO_REL = UMO051A.PLANESTUDIO_REL";
	$m_cnx_MySQL = fxAbrirConexion();
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$codMatricula]);
	$mRegistros = $mDatos->rowCount();

	$mnLinea = 155;

	$pdf->SetFont('helvetica','',10);
	if ($mRegistros > 0)
	{
		//Asignaturas inscritas
		$pdf->SetTextColor(0,0,250);
		$pdf->SetFont('helvetica','B',12);
		$pdf->Text(15, $mnLinea, 'Asignaturas inscritas');

		$msHTML = '<table>';
		$msHTML .= '<tr>';
		$msHTML .= '<th style="text-align: left;background-color:rgb(0,0,255);color: white; width: 20%;"><strong>Código</strong></th>';
		$msHTML .= '<th style="text-align: left;background-color:rgb(0,0,255);color: white; width: 60%;"><strong>Asignatura</strong></th>';
		$msHTML .= '<th style="text-align: center;background-color:rgb(0,0,255);color: white; width: 20%;"><strong>Créditos</strong></th>';
		$msHTML .= '</tr>';

		while ($mFila = $mDatos->fetch())
		{
			$msHTML .= '<tr>';
			$msHTML .= '<td>' . $mFila["CODIGO_060"] . '</td>';
			$msHTML .= '<td>' . $mFila["NOMBRE_060"] . '</td>';
			$msHTML .= '<td style="text-align: center">' . $mFila["CREDITOS_051"] . '</td>';
			$msHTML .= '</tr>';
		}
		$msHTML .= '</table>';
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('helvetica','',10);
		$mnLinea += 5;
		$pdf->SetY($mnLinea);
		$pdf->writeHTML($msHTML);
		$mnLinea += 5 * $mRegistros;
		$mnLinea += 5;
	}

	//Mensaje final
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,250);
	$pdf->SetFont('helvetica','B',12);
	$pdf->SetXY(15, $mnLinea);
	$pdf->Cell(30, 0, 'IMPORTANTE', 0, 0, 0, true);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('helvetica','',12);
	$mnLinea += 5;
	$pdf->Text(15,$mnLinea,"1.-Los pagos de los aranceles comprenden desde Enero hasta Diciembre del año lectivo.");
	$mnLinea += 5;
	$pdf->Text(15,$mnLinea,"2.-No se realizan reembolsos o transferencias del monto pagado de Matrícula y/o Mensualidad.");

	$pdf->Output();
}
?>