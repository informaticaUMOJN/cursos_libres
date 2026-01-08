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
            $msConsulta = "select  u.DESC_130,  u.VALOR_130 as ADEUDADO_132, 0 as ABONADO_132,   0 as DESCUENTO_132, u.MONEDA_130 as MONEDA_132,
                0 as ANULADO_132   from   UMO130A u where  u.COBRO_REL = :cobroId";
            
            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
            $mDatos->bindParam(':cobroId', $cobroId);
            $mDatos->execute();
            $cobro = $mDatos->fetch();

            if ($cobro) {
                $checkSql = "select COUNT(*) from UMO132A where COBRO_REL = ? and MATCURSO_REL = ?";
                $checkmDatos = $m_cnx_MySQL->prepare($checkSql);
                $checkmDatos->execute([$cobroId, $matriculaRel]);
                $exists = $checkmDatos->fetchColumn();

                if ($exists == 0) {
                    $insertSql = " insert into UMO132A (COBRO_REL, MATCURSO_REL, ADEUDADO_132, ABONADO_132, MONEDA_132, DESCUENTO_132, ANULADO_132)
                    values (:cobroRel, :matriculaRel, :adeudado, :abonado, :moneda, :descuento, :anulado)";
                    
                    $msConsulta = $m_cnx_MySQL->prepare($insertSql);
                    $msConsulta->execute([
                        ':cobroRel' => $cobroId,
                        ':matriculaRel' => $matriculaRel,
                        ':adeudado' => $cobro['ADEUDADO_132'],
                        ':abonado' => $cobro['ABONADO_132'],
                        ':moneda' => $cobro['MONEDA_132'],
                        ':descuento' => $cobro['DESCUENTO_132'],
                        ':anulado' => 0  
                    ]);
                }

                $anuladoText = ($cobro['ANULADO_132'] == 1) ? 'Sí' : 'No';
                echo json_encode([
                    'success' => true,
                    'descripcion' => $cobro['DESC_130'],
                    'adeudado' => $cobro['ADEUDADO_132'],
                    'abonado' => $cobro['ABONADO_132'],
                    'descuento' => $cobro['DESCUENTO_132'],
                    'moneda' => $cobro['MONEDA_132'],
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
        if (isset($_POST['COBRO_REL']) && isset($_POST['MATCURSO_REL'])) {
            $cobro = $_POST['COBRO_REL'];
            $MATCURSO_REL = $_POST['MATCURSO_REL'];

            try {
                $msConsulta = "delete from UMO132A where cobro_rel = :COBRO_REL AND MATCURSO_REL = :MATCURSO_REL";
                $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                $mDatos->bindParam(':COBRO_REL', $cobro);
                $mDatos->bindParam(':MATCURSO_REL', $MATCURSO_REL);

                if ($mDatos->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el cobro.']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Datos de cobro_rel y MATCURSO_REL no recibidos.']);
        }
    }
    /**********Anular un cobro**********/
    elseif ($action == 'anularCobro') {
        $cobro = $_POST['COBRO_REL'];
        $matricula = $_POST['MATCURSO_REL'];

        try {
            $msConsulta = "update UMO132A set ANULADO_132 = 1 where COBRO_REL = ? and MATCURSO_REL = ?";
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