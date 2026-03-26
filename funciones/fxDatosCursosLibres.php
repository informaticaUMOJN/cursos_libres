<?php
ob_start();
session_start();
header('Content-Type: application/json');

function respuestaError($mensaje) {
    echo json_encode(["success" => false, "mensaje" => $mensaje]);
    exit();
}

if (!isset($_SESSION["gnVerifica"]) || $_SESSION["gnVerifica"] != 1) {
    respuestaError("Sesión no válida");
}

require_once("fxGeneral.php"); 
require_once("fxUsuarios.php");

function fxCambiarEstadoHorario($msCodigo, $dia, $turno, $hora_inicio, $hora_fin, $nuevoEstado) {
    try {
        $m_cnx_MySQL = fxAbrirConexion();

        $sql = "UPDATE UMO191A 
                SET ESTADO_191 = ? 
                WHERE CURSOS_REL=? AND DIA_191=? AND TURNO_191=? AND HRSINICIO_191=? AND HRSFIN_191=?";
        $stmt = $m_cnx_MySQL->prepare($sql);
        $stmt->execute([$nuevoEstado, $msCodigo, $dia, $turno, $hora_inicio, $hora_fin]);

        if ($stmt->rowCount() == 0) {
            return ["success" => false, "mensaje" => "No se encontró el horario o ya tiene ese estado."];
        }

        $mensaje = ($nuevoEstado == 1) ? "Horario activado correctamente." : "Horario inactivado correctamente.";
        return ["success" => true, "mensaje" => $mensaje, "nuevoEstado" => $nuevoEstado];

    } catch(PDOException $e) {
        error_log("Error al cambiar estado del horario: " . $e->getMessage());
        return ["success" => false, "mensaje" => "Error de base de datos."];
    }
}

try {
    $accion = $_POST['ajax'] ?? '';
    $curso = $_POST['curso'] ?? '';
    $dia = $_POST['dia'] ?? '';
    $turno = $_POST['turno'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';

    if ($curso === '' || $dia === '' || $turno === '' || $hora_inicio === '' || $hora_fin === '') {
        respuestaError("Faltan datos para procesar el horario");
    }

    if ($accion === 'anularHorario') {
        $resultado = fxCambiarEstadoHorario($curso, $dia, $turno, $hora_inicio, $hora_fin, 0);

    } elseif ($accion === 'toggleHorario') {
        $estado = $_POST['estado'] ?? 1; // por defecto activo
        $estado = intval($estado);
        $resultado = fxCambiarEstadoHorario($curso, $dia, $turno, $hora_inicio, $hora_fin, $estado);

    } else {
        respuestaError("Acción Ajax no válida");
    }

    ob_end_clean();
    echo json_encode($resultado);
    exit();

} catch (Exception $e) {
    ob_end_clean();
    respuestaError("Error interno: " . $e->getMessage());
}
