<?php
require_once ("fxGeneral.php");

/**********Validar la cédula del estudiante**********/
if (isset($_POST["cedulaEstudiante"]) and isset($_POST["codEstudiante"]) and isset($_POST["carreraEstudiante"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCedula = $_POST["cedulaEstudiante"];
	$msCodigo = $_POST["codEstudiante"];
	$msCarrera = $_POST["carreraEstudiante"];
	$msConsulta = "Select concat(ESTUDIANTE_REL, ' (', NOMBRE1_010, ' ', APELLIDO1_010, ')') as ESTUDIANTE from UMO010A where CEDULA_010 = ? and CARRERA_REL = ? and ESTUDIANTE_REL <> ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCedula, $msCarrera, $msCodigo]);
	$mnRegistros = $mDatos->rowCount();
	
	if ($mnRegistros > 0)
	{
		$mFila = $mDatos->fetch();
		$msResultado = $mFila["ESTUDIANTE"];
	}
	else
	{
		$msResultado = "";
	}
	
	echo $msResultado;
}

/**********Obtener el número de carnet**********/
if (isset($_POST["fechaNac"]) and isset($_POST["codCarrera"]) and isset($_POST["generacion"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$mdFechaNac = $_POST["fechaNac"];
	$msCarrera = $_POST["codCarrera"];
	$mnGeneracion = $_POST["generacion"];

	$msConsulta = "Select SIGLAS_040 from UMO040A where CARRERA_REL = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera]);
	$mFila = $mDatos->fetch();
	$msSiglas = $mFila["SIGLAS_040"];

	$msConsulta = "Select count(ESTUDIANTE_REL) as CONTEO from UMO010A where CARRERA_REL = ? and GENERACION_010 = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCarrera, $mnGeneracion]);
	$mFila = $mDatos->fetch();
	$mnConteo = intval($mFila["CONTEO"]) + 1;

	$mFecha = explode("-", $mdFechaNac);

	$msResultado = $msSiglas . "-" . substr($mnGeneracion, -2) . "-" . $mFecha[1] . substr($mFecha[0], -2) . "-" . $mnConteo;
	echo $msResultado;
}

/**********Validar el correo institucional del estudiante**********/
if (isset($_POST["correoEstudiante"]))
{
	$m_cnx_MySQL = fxAbrirConexion();
	$msCorreo = $_POST["correoEstudiante"];
	$msConsulta = "Select concat(NOMBRE1_010, ' ', NOMBRE2_010 , ' ', APELLIDO1_010, ' ', APELLIDO2_010, '(', ESTUDIANTE_REL, ')') as ESTUDIANTE from UMO010A where CORREOI_010 = ?";
	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCorreo]);
	$mnRegistros = $mDatos->rowCount();
	
	if ($mnRegistros > 0)
	{
		$mFila = $mDatos->fetch();
		$msResultado = $mFila["ESTUDIANTE"];
	}
	else
	{
		$msResultado = "";
	}
	
	echo $msResultado;
}
?>