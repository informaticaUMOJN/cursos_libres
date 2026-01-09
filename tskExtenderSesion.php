<?php
    // Tiempo en segundos (ejemplo: 30 minutos)
    $tiempo_sesion = 30 * 60;
    
    // Configurar el tiempo de vida de la cookie de sesión
    session_set_cookie_params([
    'lifetime' => $tiempo_sesion,
    'path' => '/',
    'domain' => '', // Vacío para el dominio actual
    'secure' => isset($_SERVER['HTTPS']), // Solo HTTPS si aplica
    'httponly' => true, // Evita acceso desde JavaScript
    'samesite' => 'Lax' // O 'Strict' según necesidad
    ]);

    // Ajustar parámetros internos de PHP
    ini_set('session.gc_maxlifetime', $tiempo_sesion);
?>