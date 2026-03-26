<?php
// Incluir la conexión a la base de datos
//include 'conexion.php'; // Asegúrate de tener este archivo con la conexión adecuada
require_once("fxGeneral.php");
$m_cnx_MySQL = fxAbrirConexion(); // Usar la conexión establecida por fxAbrirConexion()

// Verificar si los parámetros necesarios fueron enviados
if (isset($_POST['codigo']) && isset($_POST['anulado'])) {
    $codigo = $_POST['codigo'];  // El código del estudiante
    $anulado = $_POST['anulado']; // El nuevo valor del campo "anulado"

    // Verificar que el valor de "anulado" sea válido (puedes ajustarlo según tus necesidades)
    if ($anulado !== 'Sí' && $anulado !== 'No') {
        echo 'Error: valor de "anulado" inválido.';
        exit;
    }

    // Actualizar la base de datos
    $sql = "UPDATE UMO131A SET anulado = ? WHERE codigo = ?";  // Asegúrate de que 'codigo' es el campo correcto
    $stmt = $m_cnx_MySQL->prepare($sql);  // Usar la conexión correcta
    $stmt->bind_param('ss', $anulado, $codigo);  // 'ss' indica que ambos parámetros son cadenas (strings)

    if ($stmt->execute()) {
        // Si la actualización fue exitosa, devolver "success"
        echo 'success';
    } else {
        // Si hubo un error en la actualización
        echo 'Error: no se pudo actualizar el cobro.';
    }

    // Cerrar la conexión
    $stmt->close();
    $m_cnx_MySQL->close(); // Usar la conexión correcta
} else {
    // Si no se enviaron los parámetros necesarios
    echo 'Error: datos insuficientes.';
}
?>
