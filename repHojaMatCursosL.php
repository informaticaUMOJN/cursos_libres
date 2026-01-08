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
<?php 
}
else
{
    class PDF extends TCPDF
    {
        public $msMatricula;

        // Page header
        function Header()
        {
            $mid_x = 210;

            $this->Image('imagenes/logoRep.jpg',15,8,0,16);
            $this->Image('imagenes/kanji.jpg',31,8,0,16);

            $this->SetFont('helvetica','B',12);
            $this->Text(41, 8, 'UNIVERSIDAD DE MEDICINA');
            $this->Text(41, 13, 'ORIENTAL JAPON-NICARAGUA');
            $this->SetFont('helvetica','I',9);
            $this->Text(41, 20, 'Excelencia académica con espíritu humanista');

            $this->Line(120,10,120,22);
            $this->SetFont('helvetica','',7);
            $this->Text(128, 10, 'Puente del paso a desnivel de Rubenia 7c. al oeste');
            $this->Text(128, 13, 'Barrio Venezuela. Managua, Nicaragua');
            $this->Text(128, 16, 'registro.academico@umojn.edu.ni');
            $this->Text(128, 19, '2253-0340 / 2253-0344');

            $this->SetTextColor(0,0,250);
            $this->SetFont('helvetica','B',15);
            $msTitulo = 'HOJA DE MATRÍCULA ' . $this->msMatricula;
            $this->Text(($mid_x - $this->GetStringWidth($msTitulo)) / 2, 25, $msTitulo);
        }

        // Page footer
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('helvetica','I',8);
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

        $Meses = ["01"=>"Ene","02"=>"Feb","03"=>"Mar","04"=>"Abr","05"=>"May","06"=>"Jun",
                  "07"=>"Jul","08"=>"Ago","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dic"];
        return ($Dia . "-" . $Meses[$Mes] . "-" . $Anno);
    }

    function fxFechaLarga($Fecha)
    {
        $FechaDividida = explode("-", $Fecha);
        $Anno = $FechaDividida[0];
        $Mes = $FechaDividida[1];
        $Dia = $FechaDividida[2];

        $Meses = ["01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
                  "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre"];
        return ($Dia . " de " . $Meses[$Mes] . " de " . $Anno);
    }

    function fxCalculaEdad($Fecha)
    {
        $annoHoy=date("Y");
        $mesHoy=date("n");
        $diaHoy=date("j");

        $FechaDividida = explode("-", $Fecha);
        $annoNac = $FechaDividida[0];
        $mesNac = $FechaDividida[1];
        $diaNac = $FechaDividida[2];
        
        $edad= $annoHoy-$annoNac;
        if ($mesHoy < ($mesNac - 1)) $edad -= 1;
        if (($mesNac - 1) == $mesHoy and $diaHoy < $diaNac) $edad -= 1;  
        return $edad;
    }

    $codMatricula = trim($_POST["UMOJN"]);

    //Consulta de datos principales
    $msConsulta = "
    SELECT 
        u210.MATCURSO_REL,
        u200.NOMBRES_200, u200.APELLIDOS_200, u200.SEXO_200, u210.FECHA_210,
        u210.RECIBO_210, u200.NACIONALIDAD_200, u200.FECHANAC_200, u200.CEDULA_200,
        u200.TELEFONO_200, u200.DIRECCION_200, u200.EMAIL_200, u200.MEDIO_200, u200.ESTADOCIVIL_200, u200.HIJOS_200, u200.DISCAPACIDAD_200,
				u200.CONDICIONLAB_200, u200.OCUPACION_200, u200.ENTIDADLAB_200, u200.INGRESOMENSUAL_200, u200.IDIOMA_200, u200.DOMINIOIDIOMA_200, 
				u200.NOMBREREF_200,u200.CEDULAREFERENTE_200,u200.CELULARREFERENTE_200,u200.DIRECCIONREF_200,u200.SECTOR_200, u200.DEFICIENCIA_200,
        u190.NOMBRE_190, u220.TURNO_220, u220.PERIODO_220
    FROM UMO210A u210
    INNER JOIN UMO200A u200 ON u210.ALUMNO_REL = u200.ALUMNO_REL
    INNER JOIN UMO190A u190 ON u210.CURSOS_REL = u190.CURSOS_REL
    INNER JOIN UMO220A u220 ON u210.PLANCURSO_REL = u220.PLANCURSO_REL
    WHERE u210.MATCURSO_REL = ?";

    $m_cnx_MySQL = fxAbrirConexion();
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$codMatricula]);
    $mFila = $mDatos->fetch();

    $pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $msMatricula = $mFila["MATCURSO_REL"];
    $msNombres = $mFila["NOMBRES_200"];
    $msApellidos = $mFila["APELLIDOS_200"];
    $msSexo = $mFila["SEXO_200"];
    $msFechaMat = $mFila["FECHA_210"];
    $msRecibo = $mFila["RECIBO_210"];
    $msNacinalidad = $mFila["NACIONALIDAD_200"];
    $msFechaNac = $mFila["FECHANAC_200"];
    $msCedula = $mFila["CEDULA_200"];
    $msTelefono = $mFila["TELEFONO_200"];
    $msDireccion = $mFila["DIRECCION_200"];
    $msCorreoE = $mFila["EMAIL_200"];

    $medios = [
    1=>"Visita al colegio", 2=>"Facebook", 3=>"Instagram", 4=>"Clínica ODM",
    5=>"Radio", 6=>"Estudiante UMO-JN", 7=>"Publicidad en la Calle", 8=>"Por un amigo o familiar",
    9=>"Feria de Salud", 10=>"Tik Tok", 11=>"Clínica PAMIC", 12=>"Televisión",
    13=>"Búsqueda en la web", 14=>"Feria universitaria", 15=>"Sitio web UMO-JN",
    16=>"Funcionario UMO-JN", 17=>"WhatsApp", 18=>"Cursos libres", 19=>"Otros"
];
$msMedio = isset($medios[$mFila["MEDIO_200"]]) ? $medios[$mFila["MEDIO_200"]] : "No especificado";


    $msPlanEstudio = $mFila["PERIODO_220"];
    $mnTurno = $mFila["TURNO_220"];
    $msCarrera = $mFila["NOMBRE_190"];
    $mbCedula = $mFila["CEDULA_200"];

	$mnHijos = $mFila["HIJOS_200"];
	$sectores = [
    0=>"No aplica", 1=>"Agricultura, ganadería, caza y silvicultura", 2=>"Pesca",
    3=>"Minas y canteras", 4=>"Industria manufacturas", 5=>"Electricidad, gas y agua",
    6=>"Construcción", 7=>"Comercio", 8=>"Hoteles y restaurantes",
    9=>"Transporte, almacenamiento y comunicación",
    10=>"Actividades inmobiliarias, empresariales y de alquiler",
    11=>"Administración pública y defensa, planes de seguridad social",
    12=>"Enseñanza", 13=>"Servicios sociales y de salud",
    14=>"Otros servicios comunales, sociales y personales",
    15=>"Hogares privados con servicio doméstico",
    16=>"Organizaciones y órganos extraterritoriales"
];
$msSector = isset($sectores[$mFila["SECTOR_200"]]) ? $sectores[$mFila["SECTOR_200"]] : "No especificado";

	$msProfesion = $mFila["OCUPACION_200"];
	$msIngreso = $mFila["INGRESOMENSUAL_200"];
	$msEntidadLab = $mFila["ENTIDADLAB_200"];
	$msDiscapacidad = $mFila["DISCAPACIDAD_200"];
	$msDeficiencia = $mFila["DEFICIENCIA_200"];

	switch ($mFila["ESTADOCIVIL_200"]) {
    case 0: $msEstadoCivil = "Soltero(a)"; break;
    case 1: $msEstadoCivil = "Casado(a)"; break;
    case 2: $msEstadoCivil = "Unión de hecho"; break;
    case 3: $msEstadoCivil = "Viudo(a)"; break;
    default: $msEstadoCivil = "No especificado";
}
	if ($mFila["CONDICIONLAB_200"] == 1) {
    $msCondicionLab = "Empleado";
} elseif ($mFila["CONDICIONLAB_200"] == 0) {
    $msCondicionLab = "Desempleado";
} else {
    $msCondicionLab = "No especificado";
}


    
    $pdf->msMatricula=$msMatricula;
    $pdf->AddPage();
    $pdf->SetTextColor(0,0,0);

    // ===============================
    // 1. FECHA DE MATRÍCULA
    // ===============================
    $pdf->SetFont('helvetica','B',10);
    $pdf->Text(15, 35, 'Fecha de matrícula:');
    $pdf->SetFont('helvetica','',10);
    $pdf->Text(55, 35, fxFechaCorta($msFechaMat));
    // ===============================
    // 2. OFERTA ACADÉMICA DE CURSO
    // ===============================
    $pdf->SetFont('helvetica','B',12);
    $pdf->SetTextColor(0,0,250);
    $pdf->Text(15, 42, 'OFERTA ACADÉMICA DE CURSO');

    $pdf->SetY(50);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica','',10);

    $tablaOferta = '
    <table border="1" cellpadding="4">
        <tr style="background-color:rgb(0,0,255);color:white;">
            <th width="50%">Nombre del curso</th>
            <th width="25%">Turno</th>
            <th width="25%">Número de encuentro</th>
        </tr>
        <tr>
            <td>'. $msCarrera .'</td>
            <td>';
            if ($mnTurno == 0) { $tablaOferta .= 'Regular'; }
            else if ($mnTurno == 1) { $tablaOferta .= 'Sabatino'; }
            else if ($mnTurno == 2) { $tablaOferta .= 'Dominical'; }
    $tablaOferta .= '</td>
            <td>'. $msPlanEstudio .'</td>
        </tr>
    </table>';
    $pdf->writeHTML($tablaOferta);

    // ===============================
    // 3. DATOS GENERALES
    // ===============================
    $pdf->Ln(2);
    $pdf->SetFont('helvetica','B',12);
    $pdf->SetTextColor(0,0,250);
    $pdf->Cell(0, 6, 'DATOS GENERALES', 0, 1, 'L');

    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica','',10);

    $nombreCompleto = trim($msNombres . ' ' . $msApellidos);
    $edad = fxCalculaEdad($msFechaNac);

    $tablaDatos = '
    <table border="1" cellpadding="4">
        <tr>
            <td colspan="2" style="font-weight:bold;"><b>Nombre completo: </b>'. $nombreCompleto .'</td>
        </tr>
        <tr>
            <td width="50%"><b>Edad:</b> '. $edad .' años</td>
            <td width="50%"><b>Nacionalidad:</b> '. $msNacinalidad .'</td>
        </tr>
        <tr>
            <td><b>Cédula:</b> '. $msCedula .'</td>
			 <td><b>Deficiencia:</b> '. $msDeficiencia .'</td>
        </tr>

		 <tr>
            <td><b>Estado civil:</b> '. $msEstadoCivil .'</td>
			    <td><b>Sexo:</b> '. ($msSexo == 'F' ? 'Femenino' : 'Masculino') .'</td>
        </tr>

		
		 <tr>
            <td><b>Numero de hijos:</b> '. $mnHijos .'</td>
			 <td><b>Discapacidad:</b> '. $msDiscapacidad .'</td>
        </tr>
		
        <tr>
            <td><b>Condicion laboral:</b> '. $msCondicionLab .'</td>
            <td><b>Profesion:</b> '. $msProfesion .'</td>
        </tr>

		 <tr>
            <td><b>Sector:</b> '. $msSector .'</td>
            <td><b>Ingreso Mensual:</b> '. $msIngreso .'</td>
        </tr>
		 <tr>
            <td><b>Entidad laboral:</b> '. $msEntidadLab .'</td>
			 <td><b>Fecha de nacimiento:</b> '. fxFechaLarga($msFechaNac) .'</td>
        </tr>

		 <tr>
            <td><b>Medio de Contacto:</b> '. $msMedio .'</td>
			 <td><b>Telefono:</b> '. $msTelefono .'</td>
        </tr>

    </table>';
    $pdf->writeHTML($tablaDatos);


    // ===============================
    // MÓDULOS INSCRITOS (original)
    // ===============================
    $msConsulta = "SELECT UMO211A.MODULO_REL, UMO280A.NOMBRE_280
        FROM UMO211A, UMO280A, UMO210A, UMO220A
        WHERE 
            UMO211A.MODULO_REL = UMO280A.MODULO_REL
            AND UMO210A.MATCURSO_REL = ?
            AND UMO210A.MATCURSO_REL = UMO211A.MATCURSO_REL
            AND UMO210A.PLANCURSO_REL = UMO220A.PLANCURSO_REL";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$codMatricula]);
    $mRegistros = $mDatos->rowCount();

    if ($mRegistros > 0)
    {
        $pdf->Ln(2);
        $pdf->SetTextColor(0,0,250);
        $pdf->SetFont('helvetica','B',12);
        $pdf->Cell(0, 10, 'Módulos inscritos', 0, 1, 'L');

        $msHTML = '<table border="1" cellpadding="4">
        <tr style="background-color:rgb(0,0,255);color:white;">
            <th width="100%">Nombre del módulo</th>
        </tr>';
        while ($fila = $mDatos->fetch())
        {
            $msHTML .= '<tr><td>'.$fila["NOMBRE_280"].'</td></tr>';
        }
        $msHTML .= '</table>';
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('helvetica','',10);
        $pdf->writeHTML($msHTML);
    }

	 // ===============================
    // DATOS DE REFERENCIA
    // ===============================
    $pdf->Ln(2);
    $pdf->SetFont('helvetica','B',12);
    $pdf->SetTextColor(0,0,250);
    $pdf->Cell(0, 10, 'DATOS DE REFERENCIA', 0, 1, 'L');

    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica','',10);

    $tablaReferencia = '
    <table border="1" cellpadding="4">
        <tr style="background-color:rgb(0,0,255);color:white;">
            <th width="30%">Nombre del referente</th>
            <th width="20%">N° de cédula</th>
            <th width="20%">N° celular</th>
            <th width="30%">Dirección</th>
        </tr>
        <tr>
            <td>'. $mFila["NOMBREREF_200"] .'</td>
            <td>'. $mFila["CEDULAREFERENTE_200"] .'</td>
            <td>'. $mFila["CELULARREFERENTE_200"] .'</td>
            <td>'. $mFila["DIRECCIONREF_200"] .'</td>
        </tr>
    </table>';
    $pdf->writeHTML($tablaReferencia);

    // ===============================
    // INFORMACIÓN FINAL (Recibo y monto)
    // ===============================
  // ===============================
// INFORMACIÓN FINAL (Recibo y monto)
// ===============================
$pdf->Ln(2); // menos espacio
$pdf->SetFont('helvetica','',10);

// ¿Cómo se enteró?
$pdf->MultiCell(0, 5, '¿Cómo se enteró de los cursos libres?: ' . $msMedio, 0, 'L', false, 1);

// Espacio pequeño
$pdf->Ln(2);

// Número de recibo, Monto y Saldo en la misma línea
$y = $pdf->GetY();
$pdf->SetY($y);
$pdf->SetX(15);
$pdf->Cell(60, 6, 'N° de recibo: '.$msRecibo, 0, 0, 'L');
$pdf->SetX(80);
$pdf->Cell(60, 6, 'Monto cancelado: ___________________', 0, 0, 'L');
$pdf->SetX(150);
$pdf->Cell(40, 6, 'Saldo: _______________', 0, 1, 'L');

// Espacio antes de firmas
$pdf->Ln(13);

// Firmas
$pdf->SetX(15);
$pdf->Cell(90, 6, 'Nombre y firma del tutor o estudiante', 0, 0, 'L');
$pdf->SetX(120);
$pdf->Cell(80, 6, 'Firma de funcionario UMOJN', 0, 1, 'L');

    $pdf->Output();
	   
}
?>
