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
	
	$Registro = fxVerificaUsuario();
	
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
		$mbPermisoUsuario = fxPermisoUsuario("catUsuarios");
		
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
			if (isset($_POST["Guardar"]))
			{
				$msCodigo = $_POST["txtCodUsuario"];
				$msNombre = $_POST["txtNomUsuario"];
				$msCorreo = $_POST["txtCorreo"];
				$msClave = $_POST["txtClave"];
				$msClave1 = $_POST["txtClave1"];
				$mbSupervisor = $_POST["optSupervisor"];
				$mbAdministrador = $_POST["optAdministrador"];
				$mbActivo = $_POST["optActivo"];
				
				if ($msClave != $msClave1)
				{
					?>
					<script>
						$.messager.alert('UMOJN','La Contraseña no se confirmó correctamente.','warning');
						$("a").click(function(){window.location="catUsuarios.php"});
					</script>
					<?php
				}
				else
				{
					if (isset($_POST["Guardar"]))
						{
							if (fxExisteUsuario($msCodigo) == 0)
							{
								fxGuardarUsuario ($msCodigo, $msNombre, $msCorreo, $msClave, $mbSupervisor, $mbAdministrador);
								$msBitacora = $msCodigo . "; " . $msNombre . "; " . $msCorreo . "; " . $msClave . "; " . $mbSupervisor . "; " . $mbAdministrador;
								fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO002A", $msCodigo, "", "Agregar", $msBitacora);
							}
							else
							{
								fxModificarUsuario ($msCodigo, $msNombre, $msCorreo, $msClave, $mbSupervisor, $mbAdministrador, $mbActivo);
								$msBitacora = $msCodigo . "; " . $msNombre . "; " . $msCorreo . "; " . $msClave . "; " . $mbSupervisor . "; " . $mbAdministrador;
								fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO002A", $msCodigo, "", "Modificar", $msBitacora);
							}
						}
						
					?><meta http-equiv="Refresh" content="0;url=gridUsuarios.php"/><?php
				}
		}
			else
			{
				if (isset($_POST["UMO"]))
					$msCodigo = $_POST["UMO"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$RecordSet = fxDevuelveUsuario(0, $msCodigo);
					$mFila = $RecordSet->fetch();
					$msNombre = $mFila["NOMBRE_002"];
					$msCorreo = $mFila["CORREO_002"];
					$msClave = $mFila["CLAVE_002"];
					$mbSupervisor = $mFila["SUPERVISOR_002"];
					$mbActivo = $mFila["ACTIVO_002"];
				}
				else
				{
					$msNombre = "";
					$msCorreo = "";
					$msClave = "";
					$mbSupervisor = 0;
					$mbActivo = 0;
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Usuarios de la aplicación</strong></div>
				</div>
			</div>
        	<div class = "row">
				<div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                    <form name="hrrUsuarios" action="hrrUsuarios.php" method="post" onsubmit="return verificarFormulario()">
                        <div class = "form-group row">
                            <label for="txtCodUsuario" class="col-sm-12 col-md-3 form-label">Código del Usuario</label>
                            <div class="col-sm-12 col-md-3">
                                <?php
                                    if (trim($msCodigo) != "")
                                        echo('<input type="text" class="form-control" id="txtCodUsuario" name="txtCodUsuario" value="' . $msCodigo . '"  readonly />'); 
                                    else
                                        echo('<input type="text" class="form-control" id="txtCodUsuario" name="txtCodUsuario" value="' . $msCodigo . '" />'); 
                                ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtNomUsuario" class="col-sm-12 col-md-3 form-label">Nombre del Usuario</label>
                            <div class="col-sm-12 col-md-5">
                                <?php echo('<input type="text" class="form-control" id="txtNomUsuario" name="txtNomUsuario" value="' . $msNombre . '" />'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtCorreo" class="col-sm-12 col-md-3 form-label">Correo electrónico</label>
                            <div class="col-sm-12 col-md-5">
                                <?php echo('<input type="text" class="form-control" id="txtCorreo" name="txtCorreo" value="' . $msCorreo . '" />'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtClave" class="col-sm-12 col-md-3 col-form-label">Clave del Usuario</label>
                            <div class="col-sm-12 col-md-3">
                                <?php echo('<input type="password" class="form-control" id="txtClave" name="txtClave" value="' . $msClave . '" />'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="txtClave1" class="col-sm-12 col-md-3 form-label">Confirme la Clave</label>
                            <div class="col-sm-12 col-md-3">
                                <?php echo('<input type="password" class="form-control" id="txtClave1" name="txtClave1" value="' . $msClave . '" />'); ?>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="optSupervisor" class="col-sm-12 col-md-3 form-label">Supervisor</label>
                            <div class="col-sm-12 col-md-3">
                                <div class = "radio">
                                <?php
                                    if ($mbSupervisor == 1)
                                    {
                                        echo('<input type="radio" id="optSupervisor1" name="optSupervisor" value="0" /> No <input type="radio" id="optSupervisor2" name="optSupervisor" value="1" checked="checked" /> Si');
                                    }
                                    else
                                    {
                                        echo('<input type="radio" id="optSupervisor1" name="optSupervisor" value="0" checked="checked" /> No <input type="radio" id="optSupervisor2" name="optSupervisor" value="1" /> Si');
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
						<div class = "form-group row">
                            <label for="optAdministrador" class="col-sm-12 col-md-3 form-label">Administrador</label>
                            <div class="col-sm-12 col-md-3">
                                <div class = "radio">
                                <?php
                                    if ($mbSupervisor == 1)
                                    {
                                        echo('<input type="radio" id="optAdministrador1" name="optAdministrador" value="0" /> No <input type="radio" id="optAdministrador2" name="optAdministrador" value="1" checked="checked" /> Si');
                                    }
                                    else
                                    {
                                        echo('<input type="radio" id="optAdministrador1" name="optAdministrador" value="0" checked="checked" /> No <input type="radio" id="optAdministrador2" name="optAdministrador" value="1" /> Si');
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class = "form-group row">
                            <label for="optActivo" class="col-sm-12 col-md-3 form-label">Activo</label>
                            <div class="col-sm-12 col-md-3">
                                <div class = "radio">
                                <?php
                                    if ($mbActivo == 1)
                                    {
                                        echo('<input type="radio" id="optActivo1" name="optActivo" value="0" /> No <input type="radio" id="optActivo2" name="optActivo" value="1" checked="checked" /> Si');
                                    }
                                    else
                                    {
                                        echo('<input type="radio" id="optActivo1" name="optActivo" value="0" checked="checked" /> No <input type="radio" id="optActivo2" name="optActivo" value="1" /> Si');
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class = "row">
                            <div class="col-auto offset-sm-0 col-md-10 offset-md-3">
                                <input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary"/>
                                <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridUsuarios.php';"/>
                            </div>
                        </div>
                    </form>			
	<?php	}
		}
	}
?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<script type='text/javascript'>
	function verificarFormulario()
	{
		if (document.getElementById('txtCodUsuario').value=="")
		{
			$.messager.alert('UMOJN','Falta el Código del Usuario.','warning');
			return false;
		}
		
		if(document.getElementById('txtNomUsuario').value=="")
		{
			$.messager.alert('UMOJN','Falta el Nombre del Usuario.','warning');
			return false;
		}
		
		return true;
	}
</script>