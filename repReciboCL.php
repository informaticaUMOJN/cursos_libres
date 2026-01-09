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
    function numero_letra($num)
    {
        $unidades = [
            0 => 'cero', 1 => 'uno', 2 => 'dos', 3 => 'tres', 4 => 'cuatro', 5 => 'cinco', 
            6 => 'seis', 7 => 'siete', 8 => 'ocho', 9 => 'nueve', 10 => 'diez',
            11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince', 
            16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve', 
            20 => 'veinte'
        ];
        
        $decenas = [
            30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta', 60 => 'sesenta', 
            70 => 'setenta', 80 => 'ochenta', 90 => 'noventa'
        ];
        
        $centenas = [
            100 => 'cien', 200 => 'doscientos', 300 => 'trescientos', 400 => 'cuatrocientos', 
            500 => 'quinientos', 600 => 'seiscientos', 700 => 'setecientos', 
            800 => 'ochocientos', 900 => 'novecientos'
        ];
    
        if ($num == 0) return 'cero';
    
        $resultado = '';
    
        // Manejo de miles
        if ($num >= 1000)
        {
            $miles = floor($num / 1000);
            $num = $num % 1000;
    
            if ($miles == 1)
            {
                $resultado .= 'mil ';
            }
            else
            {
                $resultado .= numero_letra($miles) . ' mil ';
            }
        }
    
        // Manejo de centenas
        if ($num >= 100)
        {
            foreach (array_reverse($centenas, true) as $valor => $nombre)
            {
                if ($num >= $valor)
                {
                    if ($num == 100)
                    {  
                        return $resultado . 'cien';  
                    }
                    if ($valor == 100) 
                    {
                        $resultado .= 'ciento ';
                    }
                    else 
                    {
                        $resultado .= $nombre . ' ';
                    }
                    $num -= $valor;
                    break;
                }
            }
        }
    
        // Manejo de decenas y unidades
        if ($num > 0)
        {
            if ($num > 20 && $num < 30)
            {
                $resultado .= 'veinte y ' . $unidades[$num % 10];
            }
            else if ($num < 20)
            {
                $resultado .= $unidades[$num];
            }
            else
            {
                $decena = floor($num / 10) * 10;
                $resultado .= $decenas[$decena];
                $unidad = $num % 10;
                if ($unidad > 0)
                {
                    $resultado .= ' y ' . $unidades[$unidad];
                }
            }
        }
        return trim($resultado);
    }

    function numero_letra_con_centavos($cantidad, $moneda)
    {
        $entero = floor($cantidad);
        $centavos = round(($cantidad - $entero) * 100);
        $entero_texto = numero_letra($entero);
        $centavos_texto = "con " . str_pad($centavos, 2, "0", STR_PAD_LEFT) . "/100";
        $moneda_texto = ($moneda == "0" || $moneda == "C$") ? "córdobas" : "dólares";
        return ucfirst($entero_texto) . " " . $moneda_texto . " " . $centavos_texto;
    }

    function mes_texto($mes)
    {
        $meses =
        [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 
            6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[(int)$mes];
    }

    function formatear_fecha($fecha) 
    {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fecha_obj) return '';
        $dia = $fecha_obj->format('d');
        $mes = mes_texto($fecha_obj->format('m'));
        $anio = $fecha_obj->format('y'); // Solo últimos dos dígitos

        return
        [
            'dia' => $dia,
            'mes' => $mes,
            'anio' => $anio
        ];
    }
    class MYPDF extends TCPDF 
    {
        public function Header() 
        {
            $this->SetFont('helvetica', 'B', 12);
            $this->Ln(15);
          //  $this->Cell(0, 15, 'UNIVERSIDAD DE MEDICINA ORIENTAL JAPON - NICARAGUA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->Ln(5);
        }

        public function FooterPage() 
        {
            $this->SetLineWidth(0.9);  // Grosor del borde
            $this->Rect(10, 10, $this->getPageWidth() - 20, $this->getPageHeight() - 20);
        }
    }
    ob_start();
    
    $pdf = new MYPDF('L', 'mm', [215.9, 139.7], true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    //$pdf->SetAuthor('UMOJN');
   // $pdf->SetTitle('RECIBO OFICIAL DE CAJA');
    $pdf->SetMargins(5, 5, 5);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12);
    
    if (isset($_GET['selectedRows']) && !empty($_GET['selectedRows'])) 
    {
        $selectedRows = explode(',', $_GET['selectedRows']);
        $placeholders = rtrim(str_repeat('?,', count($selectedRows)), ',');
        $sql = "select UMO210A.ANNOACADEMICO_210, UMO140A.RECIBI_140, UMO210A.MATCURSO_REL, UMO142A.PAGO_REL, CONCAT(UMO200A.NOMBRES_200, UMO200A.APELLIDOS_200) AS ALUMNO_REL, 
                    UMO140A.CONCEPTO_140, UMO140A.FECHA_140, UMO140A.CANTIDAD_140, UMO140A.RECIBO_140, UMO140A.TASACAMBIO_140, UMO140A.TIPO_140, UMO140A.MONEDA_140
                from UMO142A
                join UMO210A on UMO142A.MATCURSO_REL = UMO210A.MATCURSO_REL
                join UMO200A on UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL
                join UMO140A on UMO142A.PAGO_REL = UMO140A.PAGO_REL
                WHERE UMO142A.PAGO_REL in ($placeholders)";
                $msDatos = $m_cnx_MySQL->prepare($sql);
                $msDatos->execute($selectedRows);
                
                while ($row = $msDatos->fetch())
                {
                    $cantidad = $row['CANTIDAD_140'];
                    $moneda = strtoupper(trim($row['MONEDA_140']));
                    $cantidad_texto = numero_letra_con_centavos($cantidad, $moneda);
                    
                    $pdf->SetFont('dejavusans', '', 10);
                    $pdf->Ln(7);
                   // $pdf->Cell(0, 5, '( U M O - JN)', 0, 1, 'C');
                    $pdf->Ln(5);
                    
                    $pdf->SetFont('dejavusans', '', 10);
                   // $pdf->Cell(0, 5, 'Telefono: 2253-0344 / Fax: 2253-0340', 0, 1, 'C');
                    $pdf->SetFont('dejavusans', 'B', 12);
                    $pdf->Cell(0, 5, 'Recibo oficial de caja', 0, 1, 'C');
                    $pdf->Ln(5);
            
                    $pdf->SetFont('dejavusans', '', 12);
                    $pdf->Text(46, 52, $row['ANNOACADEMICO_210']);
                    $pdf->Text(46, 59, $row['RECIBI_140']);
                    $pdf->Text(46, 68, $row['ALUMNO_REL']);
                    $pdf->Text(46,76, $cantidad_texto);
            
                    $pdf->SetXY(45, 84);
                    $pdf->MultiCell(0, 6, $row['CONCEPTO_140'], 0, 'L');
            
                    $pdf->Ln(10);
                    $fecha = formatear_fecha($row['FECHA_140']);
                    $pdf->SetFont('dejavusans', '', 10);
            
                    $pdf->SetXY(10, 99);
                    $pdf->SetXY(20, 99);
                    $pdf->Cell(13, 5, $fecha['dia'], 0, 0, 'C');
                    $pdf->SetXY(30, 99); 
                    $pdf->Cell(36, 5, $fecha['mes'], 0, 0, 'C'); 
                    $pdf->SetXY(85, 99); 
                    $pdf->Cell(28, 5, $fecha['anio'], 0, 0, 'C'); 
            
                    $pdf->Ln(10);
                    $pdf->Text(34,109, number_format($row['TASACAMBIO_140'], 2 ));
                    $simbolo = ($moneda == "0") ? "C$" : "$";
                    $pdf->Text(174, 52, $simbolo . ' ' . number_format($cantidad, 2));  
                    $pdf->Text(160, 53, "");
            
                    $pdf->SetFont('dejavusans', '', 22);
                    if ($moneda == "0") {
                        $pdf->Text(165, 39, "✓"); 
                    }else {
                        $pdf->Text(196, 37, "✓"); 
                    }
                    $pdf->SetFont('helvetica', '', 22);
                    if ($cantidad == 0) {
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Text(150, 90, 'Anulado');
                        $pdf->SetTextColor(0, 0, 0);
                    } 
                }
            }
        }
        ob_end_clean();
    $pdf->Output();
?>