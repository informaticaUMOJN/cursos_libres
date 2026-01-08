<?php
require_once ("fxDiplomas.php");

if (is_array($_FILES) and count($_FILES) > 0 and isset($_POST["Codigo1"])) {
	$msCodigo = $_POST["Codigo1"];
	$msArchivo = $_FILES['archivo']['name'];
	$msRuta = 'certificados/' . $_FILES['archivo']['name'];
	$miCarpeta = '../certificados';
	if (!file_exists($miCarpeta)) {
		mkdir($miCarpeta, 0777, true);
	}

	if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $miCarpeta."/".$_FILES['archivo']['name'])) {
		fxModificarRuta ($msCodigo, $msRuta);
		echo $msRuta;
	} else {
		echo "No hay subida";
	}
} else {
    echo "No hay archivo";
}

if (isset($_POST["Codigo2"])) {
	$msCodigo = $_POST["Codigo2"];
	$msRuta = fxDevuelveRuta($msCodigo);
	$msRuta = '../' . $msRuta;

	array_map('unlink', glob($msRuta));
	fxModificarRuta ($msCodigo, "");
} else {
	echo "";
}
?>