<?php 
require_once("fxGeneral.php");

// Inicia el buffer de salida para manejar respuestas.
ob_start(); 
$m_cnx_MySQL = fxAbrirConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recibi_140 = $_POST['recibi'];
    $recibo_140 = $_POST['recibo'];
    $fecha_140 = $_POST['fecha'];
    $moneda_140 = $_POST['moneda'];
    $cantidad_140 = $_POST['cantidad'];
    $concepto_140 = $_POST['concepto'];
    $tasa_140 = $_POST['tasa'];  
    $tipo_140 = $_POST['tipo'];
    $cobro_rel = $_POST['cobroRel'];
    $adeudado = $_POST['adeudado'];
    $abonado = $_POST['abonado'];
    $descuento = $_POST['descuento'];
    $matricula_rel = $_POST['matricula'];

    if ($abonado > $adeudado) {
        $moneda_nombre = $moneda_140 == 0 ? "Córdoba" : "Dólares";
        echo json_encode([
            "error" => "El abonado es mayor que el adeudado en $moneda_nombre."
        ]);
        exit; 
    }

    $msConsulta = "select COUNT(*) from UMO140A where RECIBO_140 = ?";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$recibo_140]);
    $mFila = $mDatos->fetch();
    if ($mFila[0] > 0) {
        echo json_encode(["error" => "El código de recibo ya existe."]);
        exit;
    }

    $msConsulta = "select IFNULL(MID(MAX(PAGO_REL), 4), 0) as Ultimo from UMO140A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1; 
    $pago_rel = "PGS" . str_pad($mnNumero, 4, "0", STR_PAD_LEFT); 

    // Insertar en la UMO140A
    $msConsulta = "insert into UMO140A (PAGO_REL, RECIBI_140, RECIBO_140, FECHA_140, MONEDA_140, CANTIDAD_140, CONCEPTO_140, TASACAMBIO_140, TIPO_140) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute([$pago_rel, $recibi_140, $recibo_140, $fecha_140, $moneda_140, $cantidad_140, $concepto_140, $tasa_140, $tipo_140]);

    if (!empty($cobro_rel)) {
        // Obtener la información del cobro relacionado
        $msConsulta = "select MONEDA_131, ADEUDADO_131, ABONADO_131, DESCUENTO_131 from UMO131A where COBRO_REL = ? and MATRICULA_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$cobro_rel, $matricula_rel]);
        $mFila = $mDatos->fetch();

        $descuento_actual = $mFila ? $mFila['DESCUENTO_131'] : 0;

        // Convertir la cantidad y el descuento según la moneda seleccionada
        if ($mFila) {
            if ($mFila['MONEDA_131'] == 1 && $moneda_140 == 0) { // Convertir de Córdoba a Dólares
                $cantidad_140 = $cantidad_140 / $tasa_140;  
                $descuento = $descuento / $tasa_140;  // Solo se convierte el nuevo descuento
            } elseif ($mFila['MONEDA_131'] == 0 && $moneda_140 == 1) { // Convertir de Dólares a Córdoba
                $cantidad_140 = $cantidad_140 * $tasa_140;  
                $descuento = $descuento * $tasa_140;  // Solo se convierte el nuevo descuento
            }

            // Sumar el descuento convertido al existente
            $descuento_total = $descuento_actual + $descuento;

            // Calcular el nuevo adeudado y abonado, utilizando solo el nuevo descuento
            $nuevo_adeudado = $mFila['ADEUDADO_131'] - $cantidad_140 - $descuento;
            $nuevo_abonado = $mFila['ABONADO_131'] + $cantidad_140;
        } else {
            // Si no existe registro previo, se usa el descuento recibido
            $nuevo_adeudado = $cantidad_140;
            $nuevo_abonado = $cantidad_140;
            $descuento_total = $descuento;
        }

        // Actualizar la tabla UMO131A con los valores correctos según la moneda
        $msConsulta = "update UMO131A set ABONADO_131 = ?, ADEUDADO_131 = ?, DESCUENTO_131 = ? where COBRO_REL = ? AND MATRICULA_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$nuevo_abonado, $nuevo_adeudado, $descuento_total, $cobro_rel, $matricula_rel]);

        // Insertar el pago en la tabla de pagos
        $valor_141 = $cantidad_140;
        $anulado_141 = 0; 
        $msConsulta = "insert into UMO141A (PAGO_REL, COBRO_REL, MATRICULA_REL, DESCUENTO_141, VALOR_141, ANULADO_141) values (?, ?, ?, ?, ?, ?)";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$pago_rel, $cobro_rel, $matricula_rel, $descuento, $valor_141, $anulado_141]);
    }

    echo json_encode([
        "message" => "Pago registrado con éxito",
        "id" => $pago_rel,
        "abonado" => $nuevo_abonado,
        "nuevo_adeudado" => $nuevo_adeudado,
        "descuento" => $descuento_total,
        "filas_afectadas" => $mDatos->rowCount()
    ]);
} else {
    echo json_encode(["error" => "Método no permitido."]);
}

ob_end_flush();
?>
