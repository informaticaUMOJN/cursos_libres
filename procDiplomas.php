<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxDiplomas.php");

	$m_cnx_MySQL = fxAbrirConexion();

	if (isset($_POST["txtDiploma"]))
	{
		$msCodigo = $_POST["txtDiploma"];
		$mdFecha = $_POST["dtpFecha"];
		$msEstudio = $_POST["txtEstudio"];
		$msNombre = $_POST["txtNombre"];
		$msRuta = $_POST["txtRuta"];
					
		if ($msCodigo == "")
		{
			$msCodigo = fxAgregarDiploma ($msEstudio, $msNombre);
			$msBitacora = $msCodigo . "; " . $msEstudio . "; " . "; " . $msNombre;
			fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO003B", $msCodigo, "", "Agregar", $msBitacora);
		}
		else
		{
			fxModificarDiploma($msCodigo, $msEstudio, $msNombre);
			$msBitacora = $msCodigo . "; " . $msEstudio . "; " . "; " . $msNombre;
			fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO003B", $msCodigo, "", "Modificar", $msBitacora);
		}
		
		?><meta http-equiv="Refresh" content="0;url=gridDiplomas.php"/><?php
	}
	else
	{
		if (isset($_POST["UMOJN"]))
			$msCodigo = $_POST["UMOJN"];
		else
			$msCodigo = "";

		if ($msCodigo != "")
		{
			$objRecordSet = fxDevuelveDiploma(0, $msCodigo);
			$mFila = $objRecordSet->fetch();
			$mdFecha = $mFila["FECHA_003"];
			$msEstudio = $mFila["ESTUDIO_003"];
			$msNombre = $mFila["NOMBRE_003"];
			$msRuta = $mFila["RUTA_003"];
		}
		else
		{
			$mdFecha = date('Y-m-d');
			$msEstudio = "";
			$msNombre = "";
			$msRuta = "";
		}
	}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Ingreso de diplomas</strong></div>
				</div>
			</div>
			<div class = "row">
                <div class="col-xs-12 col-md-12 offset-md-2">
					<form name="procDiplomas" id="procDiplomas" action="procDiplomas.php" method="post">
						<div class = "row">
							<div class="col-auto col-md-11">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridDiplomas.php';"/>
							</div>
						</div>

						<div class="col-sm-auto col-md-12">
							<div class = "form-group row">
								<label for="txtDiploma" class="col-sm-12 col-md-2 form-label">Diploma</label>
								<div class="col-sm-12 col-md-2">
								<?php
									echo('<input type="text" class="form-control" id="txtDiploma" name="txtDiploma" value="' . $msCodigo . '" readonly />');
									echo('<input type="hidden" class="form-control" id="txtRuta" name="txtRuta" value="' . $msRuta . '" />');
								?>
								</div>
							</div>
									
							<div class = "form-group row">
								<label for="dtpFecha" class="col-sm-12 col-md-2 form-label">Fecha</label>
								<div class="col-sm-12 col-md-2">
								<?php echo('<input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="' . $mdFecha . '" readonly />'); ?>
								</div>
							</div>

							<div class = "form-group row">
								<label for="txtEstudio" class="col-sm-12 col-md-2 form-label">Estudio</label>
								<div class="col-sm-12 col-md-6">
								<?php echo('<input type="text" class="form-control" id="txtEstudio" name="txtEstudio" value="' . $msEstudio . '" />'); ?>
								</div>
							</div>

							<div class = "form-group row">
								<label for="txtNombre" class="col-sm-12 col-md-2 form-label">Nombre</label>
								<div class="col-sm-12 col-md-6">
								<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
								</div>
							</div>

							<div class = "form-group row">
								<label class="col-sm-12 col-md-2 form-label">Imagen</label>
								<div class="col-xs-auto col-md-7">
									<div style="height:auto; padding-top:1%; padding-bottom:2%">
										<table width="100%">
											<tr>
												<td><input type="file" id="fbArchivo" name="fbArchivo"></td>
											</tr>
											<tr>
												<td>
													<input type="button" id="btnSubir" name="btnSubir" value="Subir" onclick="subirArchivo()">
													<input type="button" id="btnBorrar" name="btnBorrar" value="Borrar" onclick="borrarArchivo()">
												</td>
											</tr>
											<tr>
												<td>
													<?php echo('<img class"img-fluid" src="' . $msRuta . '" alt="SIN DIPLOMA" id="imgDiploma" name="imgDiploma" />'); ?>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</form>
                </div>
			</div>
		</div>
	</div>
</body>
</html>
<script>
	function verificarFormulario()
	{
		if(document.getElementById('txtEstudio').value=="")
		{
			$.messager.alert('UMOJN','Falta el estudio realizado.','warning');
			return false;
		}

		if(document.getElementById('txtNombre').value=="")
		{
			document.getElementById('txtNombre').focus();
			$.messager.alert('UMOJN','Falta el Nombre.','warning');
			return false;
		}
		
		return true;
	}

	function subirArchivo()
	{
		var msArchivo = $('#fbArchivo').val();
		var datos = new FormData();
        var files = $('#fbArchivo')[0].files[0];
		var diploma = $('#txtDiploma').val();
		datos.append('archivo', files);
		datos.append('Codigo1', diploma);

		if (document.getElementById('txtDiploma').value=="")
		{
			$.messager.alert('UMOJN','Guarde el registro del diplomas antes de subir la imagen.','warning');
			return false;
		}

		if (msArchivo == ""){
        	$.messager.alert('UMOJN', 'No ha seleccionado la imagen.', 'warning');
        	return false;
    	}

		$.ajax({
            url: 'funciones/fxDiplomasImagenes.php',
            type: 'post',
            data: datos,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response != "") {
                    document.getElementById('imgDiploma').src = response;
                    document.getElementById('fbArchivo').value = "";
                } else {
                    $.messager.alert('UMOJN', 'Error en la subida del archivo.', 'warning');
                }
            }
        });
	}

	function borrarArchivo()
	{
		var datos = new FormData();
		var diploma = $('#txtDiploma').val();
		datos.append('Codigo2', diploma);

		$.ajax({
        url: 'funciones/fxDiplomasImagenes.php',
        type: 'post',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response != "") {
                document.getElementById('imgDiploma').src = "";
            } else {
                $.messager.alert('UMOJN', 'Error en la eliminación del archivo.', 'warning');
            }
        }
    });
	}
</script>