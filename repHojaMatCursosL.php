<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION["gnVerifica"]) || $_SESSION["gnVerifica"] != 1) {
    echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
    exit('');
}

require_once("funciones/fxGeneral.php");
require_once("funciones/fxUsuarios.php");
require_once("tcpdf/tcpdf.php");

$m_cnx_MySQL = fxAbrirConexion();
$Registro   = fxVerificaUsuario();

if ($Registro == 0) {
    echo '<img src="imagenes/errordeacceso.png"/>';
    exit;
}

/* ===================== CLASE PDF ===================== */
class PDF extends TCPDF {
    function Header() {
        $this->Image('imagenes/logoRep.jpg',15,8,0,16);
        $this->Image('imagenes/kanji.jpg',31,8,0,16);
        $mid_x = 210; // width of the "PDF screen", fixed by now.

        $this->SetFont('helvetica','B',12);
        $Titulo = 'UNIVERSIDAD DE MEDICINA';
		$this->Text(33, 8, $Titulo);
        $Titulo = 'ORIENTAL JAPON-NICARAGUA';
		$this->Text(33, 13, $Titulo);
        
        $this->SetFont('helvetica','I',9);
        $Titulo = 'Excelencia académica con espíritu humanista';
		$this->Text(33, 20, $Titulo);

        $this->Line(120,10,120,22);
        $this->SetFont('helvetica','',7);
        $this->Text(128, 10, 'Puente del paso a desnivel de Rubenia 7c. al oeste');
        $this->Text(128, 13, 'Barrio Venezuela. Managua, Nicaragua');
        $this->Text(128, 16, 'registro.academico@umojn.edu.ni');
        $this->Text(128, 19, '2253-0340 / 2253-0344');

        $this->SetTextColor(0,70,140);
        $this->SetFont('helvetica','B',15);
        $this->Ln(20);
        $Titulo = 'HOJA DE MATRÍCULA CURSOS DE EDUCACIÓN CONTINUA';
		$this->Text(($mid_x - $this->GetStringWidth($Titulo)) / 2, 25, $Titulo);

        $this->SetTextColor(0,0,0);
        $this->Ln(50);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().' / '.$this->getAliasNbPages(),0,0,'L');
        $this->Cell(0,10,'Emitido: '.date("d/m/Y h:i a"),0,0,'R');
    }
}

/* ===================== FUNCIONES ===================== */
function fxFechaCorta($f) {
    if (!$f) return '';
    $d = explode("-", $f);
    $m = ["01"=>"Ene","02"=>"Feb","03"=>"Mar","04"=>"Abr","05"=>"May","06"=>"Jun",
          "07"=>"Jul","08"=>"Ago","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dic"];
    return $d[2]."-".$m[$d[1]]."-".$d[0];
}

function fxCalculaEdad($f) {
    if (!$f) return '';
    return (new DateTime())->diff(new DateTime($f))->y;
}

function fxHoraBonita($hora) {
    if (!$hora) return '';
    return date("g:i a", strtotime($hora));
}

/* ===================== CONSULTA ===================== */
$codMatricula = trim($_POST["UMOJN"]);

$sql = "SELECT u210.MATCURSO_REL, FECHA_210, u200.*,  NOMBRE_190, TURNO_190, HRSINICIO_190, HRSFIN_190, HRSTOTAL_190, NOMBRE_350 AS NOMBRE_COLEGIO, NOMBRE_360 AS NOMBRE_UNIVERSIDAD
FROM UMO210A u210
INNER JOIN UMO200A u200 ON u210.ALUMNO_REL = u200.ALUMNO_REL
INNER JOIN UMO190A u190 ON u210.CURSOS_REL = u190.CURSOS_REL
LEFT JOIN UMO350A c ON u200.COLEGIOCL_REL = c.COLEGIOCL_REL
LEFT JOIN UMO360A u ON u200.UNIVERSIDADCL_REL = u.UNIVERSIDADCL_REL
WHERE u210.MATCURSO_REL = ?";

$stmt = $m_cnx_MySQL->prepare($sql);
$stmt->execute([$codMatricula]);
$mFila = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mFila) exit('No se encontraron datos de matrícula');

/* ===================== CONSULTA DOCUMENTOS ===================== */
$sqlDocs = "SELECT DESC_201 FROM UMO201A WHERE ALUMNO_REL = ?";
$stmtDocs = $m_cnx_MySQL->prepare($sqlDocs);
$stmtDocs->execute([$mFila['ALUMNO_REL']]);
$docs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

/* ======= TURNO ======= */
$turnos = [
    1 => 'Diurno', 2 => 'Matutino', 3 => 'Vespertino',
    4 => 'Nocturno', 5 => 'Sabatino', 6 => 'Dominical'
];
$nombreTurno = $turnos[$mFila["TURNO_190"]] ?? 'No definido';

/* ======= MEDIO ======= */
$medios = [
    1 => 'Visita al colegio', 2 => 'Facebook', 3 => 'Instagram',
    4 => 'Clínica ODM', 5 => 'Radio', 6 => 'Estudiante UMOJN',
    7 => 'Publicidad en la calle', 8 => 'Amigo o familiar',
    9 => 'Feria de salud', 10 => 'TikTok', 11 => 'Clínica PAMIC',
    12 => 'Sitio web', 13 => 'YouTube', 14 => 'Otros'
];
$medioTexto = $medios[$mFila["MEDIO_200"]] ?? 'No especificado';

/* ======= NIVEL DE ESTUDIO ======= */
$niveles = [
    0 => 'Primaria', 1 => 'Bachiller', 2 => 'Técnico',
    3 => 'Licenciado', 4 => 'Ingeniero', 5 => 'Doctor'
];
$nivelEstudioTexto = $niveles[$mFila["NIVELESTUDIOS_200"]] ?? 'No especificado';

/* ===================== PDF ===================== */
$pdf = new PDF('P','mm','A4');
$pdf->SetMargins(15,10,15);
$pdf->AddPage();
$pdf->SetFont('helvetica','',10);

/* ===================== ENCABEZADO ===================== */
$pdf->Ln(25);
$pdf->writeHTML("
<table cellpadding='4' border='1'>
<tr>
<td width='60%'><b>Curso:</b> {$mFila['NOMBRE_190']}</td>
<td width='40%'><b>Turno:</b> {$nombreTurno}</td>
</tr>
<tr>
<td><b>Fecha de Matrícula:</b> ".fxFechaCorta($mFila["FECHA_210"])."</td>
<td><b>Modalidad:</b> Presencial</td>
</tr>
<tr>
<td><b>Horario:</b> ".fxHoraBonita($mFila['HRSINICIO_190'])." - ".fxHoraBonita($mFila['HRSFIN_190'])."</td>
<td width='60%'><b>Hora Total:</b> {$mFila['HRSTOTAL_190']}</td>
</tr>
</table>
");

/* ===================== DATOS GENERALES ===================== */ 
//$pdf->Ln(2);
$pdf->SetFont('helvetica','B',10);
$pdf->Cell(0,6,'DATOS GENERALES',0,1);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());

$pdf->SetFont('helvetica','',10); // Fuente base para el contenido normal
$pdf->writeHTML("
<table cellpadding='4' border='1'> 
<tr> 
    <td width='50%'><b>Nombre:</b> {$mFila['NOMBRES_200']} {$mFila['APELLIDOS_200']}</td> 
    <td width='50%'><b>Edad:</b> ".fxCalculaEdad($mFila["FECHANAC_200"])." años</td>
</tr>
<tr> 
    <td><b>Cédula:</b> {$mFila['CEDULA_200']}</td> 
    <td><b>Sexo:</b> ".($mFila["SEXO_200"]=='F'?'Femenino':'Masculino')."</td>
</tr>
<tr>
    <td><b>Celular:</b> {$mFila['CELULAR_200']}</td>
    <td><b>Correo:</b> {$mFila['EMAIL_200']}</td>
</tr>
<tr>
    <td><b>N.º Hijos:</b> {$mFila['HIJOS_200']}</td>
    <td><b>Discapacidad:</b> {$mFila['DISCAPACIDAD_200']}</td>
</tr>
<tr>
    <td><b>Peso:</b> {$mFila['PESO_200']}</td>
    <td><b>Altura:</b> {$mFila['ALTURA_200']}</td>
</tr>
<tr>
    <td colspan='2'><b>Dirección:</b> {$mFila['DIRECCION_200']}</td>
</tr> 
</table>
");

/* ===================== DATOS ACADÉMICOS ===================== */
//$pdf->Ln(4);
$pdf->SetFont('helvetica','B',10);
$pdf->Cell(0,6,'DATOS ACADÉMICOS',0,1);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());

$pdf->SetFont('helvetica','',9);
$pdf->writeHTML("
<table cellpadding='4' border='1'>
<tr>
<td width='25%'><b>Nivel de Estudio:</b></td>
<td colspan='3'>{$nivelEstudioTexto}</td>
</tr>
<tr>
<td width='25%'><b>Centro de Secundaria:</b></td>
<td width='25%'>".($mFila['NOMBRE_COLEGIO'] ?? 'No especificado')."</td>
</tr>
<tr>
<td width='25%'><b>Universidad:</b></td>
<td width='25%'>".($mFila['NOMBRE_UNIVERSIDAD'] ?? 'No especificada')."</td>
</tr>
</table>
");

/* ===================== DOCUMENTOS ===================== */
//$pdf->Ln(4);
$pdf->SetFont('helvetica','B',10);
$pdf->Cell(0,6,'DOCUMENTOS RECIBIDOS',0,1);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());
$pdf->Ln(3);

$pdf->SetFont('helvetica','',8);

if (count($docs) > 0) {
    $htmlDocs = "<table cellpadding='4' border='1'>";
    foreach ($docs as $doc) {
        $htmlDocs .= "
        <tr>
            <td width='100%'>{$doc['DESC_201']}</td>
        </tr>";
    }
    $htmlDocs .= "</table>";
    $pdf->writeHTML($htmlDocs);
} else {
    $pdf->Cell(0,6,'No se registran documentos entregados.',0,1);
}


/* ===================== FAMILIA ===================== */
//$pdf->Ln(4);
$pdf->SetFont('helvetica','B',11);
$pdf->Cell(0,6,'DATOS DE LA ESTRUCTURA FAMILIAR',0,1);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());

$pdf->SetFont('helvetica','',10);
$pdf->writeHTML("
<table cellpadding='4' border='1'>
<tr>
<td width='25%'><b>Nombre de la Madre:</b></td>
<td width='25%'>{$mFila['NMADRE_200']}</td>
<td width='25%'><b>Trabaja:</b></td>
<td width='25%'>".($mFila['MTRABAJA_200']?'Sí':'No')."</td>
</tr>
<tr>
<td width='25%'><b>Nombre del Padre:</b></td>
<td width='25%'>{$mFila['NPADRE_200']}</td>
<td width='25%'><b>Trabaja:</b></td>
<td width='25%'>".($mFila['PTRABAJA_200']?'Sí':'No')."</td>
</tr>
</table>
");

/* ===================== EMERGENCIA ===================== */
//$pdf->Ln(4);
$pdf->SetFont('helvetica','B',11);
$pdf->Cell(0,6,'DATOS PARA EMERGENCIAS',0,1);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());

$pdf->SetFont('helvetica','',10);
$pdf->writeHTML("
<table cellpadding='4' border='1'>
<tr>
<td width='25%'><b>Nombre Completo:</b></td>
<td width='25%'>{$mFila['NOMBREREF_200']}</td>
</tr>
<tr>
<td width='25%'><b>Celular:</b></td>
<td width='25%'>{$mFila['CELULARREF_200']}</td>
</tr>
<tr>
<td width='25%'><b>Dirección:</b></td>
<td colspan='3'>{$mFila['DIRECCIONREF_200']}</td>
</tr>
</table>
");

/* ===================== COMO SE ENTERÓ ===================== */
//$pdf->Ln(2);
$pdf->writeHTML("<table cellpadding='4' border='1'>
<tr>
<td><b>¿Cómo se enteró del curso?:</b> {$medioTexto}</td>
</tr>
</table>");

/* ===================== NOTA Y FIRMAS ===================== */
$pdf->SetFont('','B',8); // B = negrita
$pdf->Ln(2);
$pdf->Cell(0, 6, 'NOTA: Es requisito indispensable efectuar el pago de los módulos antes de iniciar clases.', 0, 1, 'C');
$pdf->Cell(0, 6, 'El incumplimiento dará lugar al cobro de mora correspondiente.', 0, 1, 'C');

$pdf->Ln(5);
$xCentro = ($pdf->GetPageWidth() - 46) / 2;
$yActual = $pdf->GetY();
$pdf->Image('imagenes/FIRM.jpg', $xCentro, $yActual, 40);
$pdf->SetFont('','B',9); // B = Bold (negrita)
$pdf->Ln(34);
//$pdf->Cell(30,6,'',0,0);
$pdf->Cell(0, 6, 'Registro Académico UMO-JN', 0, 1, 'C');
$pdf->Ln(25);
$pdf->Output();
