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
	require_once ("funciones/fxDepartamentos.php");
	$Registro = fxVerificaUsuario();
	$m_cnx_MySQL = fxAbrirConexion();

	if ($Registro == 0)
	{
?>

<div class="container text-center">
	<div id="DivContenido">
	    <img src="imagenes/errordeacceso.png"/>
    </div>
 </div>
<?php }
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("catDepartamentos");
		
		if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
		{?>
        <div class="container text-center">
        	<div id="DivContenido">
				<img src="imagenes/errordeacceso.png"/>
            </div>
        </div>
		<?php }
		else
		{
			if (isset($_POST["txtDepartamento"]))
			{
				$msCodigo = $_POST["txtDepartamento"];
				$msNombre = $_POST["txtNombre"];

				{
					if ($msCodigo == "")
					{
						$msCodigo = fxGuardarDepartamento($msNombre);
						$msBitacora = $msCodigo . "; " . $msNombre;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO110A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarDepartamento($msCodigo, $msNombre);
						$msBitacora = $msCodigo . "; " . $msNombre;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO110A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridDepartamentos.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveDepartamento(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msDepartamento = $mFila["DEPARTAMENTO_REL"];
					$msNombre = $mFila["NOMBRE_110"];
				}
				else
				{
					$msDepartamento = "";
					$msNombre = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de departamentos</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catDepartamentos" name="catDepartamentos" action="catDepartamentos.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtDepartamento" class="col-sm-12 col-md-3 col-form-label">Departamento</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtDepartamento" name="txtDepartamento" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-3 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>

						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridDepartamentos.php';"/>
							</div>
						</div>
					</form>
                </div>
			</div>
		</div>
	</div>
<?php	}
	}
}
?>
</body>
</html>
<script type='text/javascript'>
	function verificarFormulario()
	{		
		if(document.getElementById('txtNombre').value=="")
		{
			$.messager.alert('UMOJN','Falta el Nombre.','warning');
			return false;
		}


		return true;
	}
</script>