<?php
    set_time_limit(300);
    require_once ("funciones/fxGeneral.php");
	$m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select USUARIO_REL from UMO002A order by USUARIO_REL";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();

    while ($mFila = $mDatos->fetch())
    {
        $msUsuario = $mFila["USUARIO_REL"];
        $msClave = crypt($msUsuario, '_appUMOJN');
        $msConsulta = "update UMO002A set CLAVE_002 = ? where USUARIO_REL = ?";
        $m_Auxiliar = $m_cnx_MySQL->prepare($msConsulta);
        $m_Auxiliar->execute([$msClave, $msUsuario]);
    }
    echo('<script>Completado</script>');
?>