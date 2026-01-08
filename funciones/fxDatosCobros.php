<?php
require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion();

header('Content-Type: application/json'); 

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    /**********Obtener los detalles de un cobro**********/
    if ($action == 'getCobroDetails') {
        $cobroId = $_POST['cobroId'];
        $matriculaRel = $_POST['matriculaRel'];  

        try {
            $msConsulta = "select  u.DESC_130,  u.VALOR_130 as ADEUDADO_131, 0 as ABONADO_131,   0 as DESCUENTO_131, u.MONEDA_130 as MONEDA_131,
                0 as ANULADO_131   from   UMO130A u where  u.COBRO_REL = :cobroId";
            
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->bindParam(':cobroId', $cobroId);
            $mDatos->execute();
            $cobro = $mDatos->fetch();

            if ($cobro) {
                $checkSql = "select COUNT(*) from UMO131A where COBRO_REL = ? and MATRICULA_REL = ?";
                $checkmDatos = $m_cnx_MySQL->prepare($checkSql);
                $checkmDatos->execute([$cobroId, $matriculaRel]);
                $exists = $checkmDatos->fetchColumn();

                if ($exists == 0) {
                    $insertSql = " insert into UMO131A (COBRO_REL, MATRICULA_REL, ADEUDADO_131, ABONADO_131, MONEDA_131, DESCUENTO_131, ANULADO_131)
                    values (:cobroRel, :matriculaRel, :adeudado, :abonado, :moneda, :descuento, :anulado)";
                    
                    $msConsulta = $m_cnx_MySQL->prepare($insertSql);
                    $msConsulta->execute([
                        ':cobroRel' => $cobroId,
                        ':matriculaRel' => $matriculaRel,
                        ':adeudado' => $cobro['ADEUDADO_131'],
                        ':abonado' => $cobro['ABONADO_131'],
                        ':moneda' => $cobro['MONEDA_131'],
                        ':descuento' => $cobro['DESCUENTO_131'],
                        ':anulado' => 0  
                    ]);
                }

                $anuladoText = ($cobro['ANULADO_131'] == 1) ? 'Sí' : 'No';
                echo json_encode([
                    'success' => true,
                    'descripcion' => $cobro['DESC_130'],
                    'adeudado' => $cobro['ADEUDADO_131'],
                    'abonado' => $cobro['ABONADO_131'],
                    'descuento' => $cobro['DESCUENTO_131'],
                    'moneda' => $cobro['MONEDA_131'],
                    'anulado' => $anuladoText 
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Cobro no encontrado']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    /**********Eliminar un cobro**********/
    elseif ($action == 'eliminarCobro') {
        if (isset($_POST['COBRO_REL']) && isset($_POST['MATRICULA_REL'])) {
            $cobro = $_POST['COBRO_REL'];
            $matricula_rel = $_POST['MATRICULA_REL'];

            try {
                $msConsulta = "delete from UMO131A where cobro_rel = :COBRO_REL AND matricula_rel = :MATRICULA_REL";
                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                $mDatos->bindParam(':COBRO_REL', $cobro);
                $mDatos->bindParam(':MATRICULA_REL', $matricula_rel);

                if ($mDatos->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el cobro.']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Datos de cobro_rel y matricula_rel no recibidos.']);
        }
    }
    /**********Anular un cobro**********/
    elseif ($action == 'anularCobro') {
        $cobro = $_POST['COBRO_REL'];
        $matricula = $_POST['MATRICULA_REL'];

        try {
            $msConsulta = "update UMO131A set ANULADO_131 = 1 where COBRO_REL = ? and MATRICULA_REL = ?";
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->execute([$cobro, $matricula]);

            if ($mDatos->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo realizar la anulación']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Acción no recibida']);
}
?>