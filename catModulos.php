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
	require_once ("funciones/fxModulos.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("catModulos");
		
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
			if (isset($_POST["txtModulo"]))
			{
				$msCodigo = $_POST["txtModulo"];
				$msCurso = $_POST["cboCurso"];
				$msNombre = $_POST["txtNombre"];
				$msDocente = $_POST["cboDocente"];

				if ($msCodigo == "")
				{
					$msCodigo = fxGuardarModulo($msCurso, $msNombre, $msDocente );
					 $msBitacora = "$msCodigo; $msCurso; $msNombre;$msDocente";
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO280A", $msCodigo, "", "Agregar", $msBitacora);
				}
				else
				{
					fxModificarModulo ($msCodigo, $msCurso, $msNombre,$msDocente);
					 $msBitacora = "$msCodigo; $msCurso; $msNombre;$msDocente";
					fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO280A", $msCodigo, "", "Modificar", $msBitacora);
				}
									
				?><meta http-equiv="Refresh" content="0;url=gridModulos.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$mDatos = fxDevuelveModulo(0, $msCodigo);
					$mFila = $mDatos->fetch();
					$msCurso = $mFila["CURSOS_REL"];
					$msNombre = $mFila["NOMBRE_280"];
					$msDocente = $mFila["DOCENTE_REL"];
				}
				else
				{
					$msCurso = "";
					$msNombre = "";
					$msDocente = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Catálogo de Modulo</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
					<form id="catModulos" name="catModulos" action="catModulos.php" onsubmit="return verificarFormulario()" method="post">
						<div class = "form-group row">
							<label for="txtModulo" class="col-sm-12 col-md-3 col-form-label">Modulo</label>
							<div class="col-sm-12 col-md-3">
							<?php
								echo('<input type="text" class="form-control" id="txtModulo" name="txtModulo" value="' . $msCodigo . '" readonly />'); 
							?>
							</div>
						</div>
						
                        <div class="form-group row">
                            <label for="cboCurso" class="col-sm-12 col-md-3 col-form-label">Curso</label>
                            <div class="col-sm-12 col-md-7">
                                <select class="form-control" id="cboCurso" name="cboCurso" required>
                                    <option value="">Seleccione un curso</option>
                                    <?php
                                    $msConsulta = "SELECT CURSOS_REL, NOMBRE_190 FROM UMO190A ORDER BY NOMBRE_190";
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute();
                                    while ($mFila = $mDatos->fetch()) {
                                        $msValor = rtrim($mFila["CURSOS_REL"]);
                                        $msTexto = rtrim($mFila["NOMBRE_190"]);
                                        $selected = ($msCurso == $msValor) ? "selected" : "";
                                        echo "<option value='$msValor' $selected>$msTexto</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

						<div class = "form-group row">
							<label for="txtNombre" class="col-sm-12 col-md-3 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-7">
							<?php echo('<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="' . $msNombre . '" />'); ?>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="cboDocente" class="col-sm-12 col-md-3 col-form-label">Docente</label>
							<div class="col-sm-12 col-md-7">
								<?php
								$disabled = ($msCodigo != "") ? "" : "";
								echo "<select class='form-control' id='cboDocente' name='cboDocente' $>";

								try {
									if (!isset($m_cnx_MySQL)) {
										throw new Exception("Error: conexión no inicializada.");
									}

									if ($msCodigo == "") {
										if (trim($_SESSION["gsDocente"]) != "" && $mbAdministrador == 0 && $mbSupervisor == 0) {
											$mDocente = $_SESSION["gsDocente"];
											$msConsulta = "SELECT DOCENTE_REL, NOMBRE_100 FROM UMO100A WHERE ACTIVO_100 = 1 AND DOCENTE_REL = ? ORDER BY NOMBRE_100";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute([$mDocente]);
										} else {
											$msConsulta = "SELECT DOCENTE_REL, NOMBRE_100 FROM UMO100A WHERE ACTIVO_100 = 1 ORDER BY NOMBRE_100";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute();
										}
									} else {
										$msConsulta = "SELECT DOCENTE_REL, NOMBRE_100 FROM UMO100A ORDER BY NOMBRE_100 DESC";
										$mDatos = $m_cnx_MySQL->prepare($msConsulta);
										$mDatos->execute();
									}

									if ($mDatos->rowCount() == 0) {
										echo "<option value=''>-- No hay docentes --</option>";
									} else {
										while ($mFila = $mDatos->fetch(PDO::FETCH_ASSOC)) {
											$Docente = trim($mFila["DOCENTE_REL"]);
											$Texto = trim($mFila["NOMBRE_100"]);
											$selected = ($msDocente == "" || $msDocente == $Docente) ? "selected" : "";
											echo "<option value='$Docente' $selected>$Texto</option>";
										}
									}

								} catch (Exception $e) {
									echo "<option value=''>Error: " . $e->getMessage() . "</option>";
								}

								echo "</select>";
								?>
							</div>
						</div>



						<div class = "row">
							<div class="col-auto offset-sm-0 col-md-12 offset-md-3">
								<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridModulos.php';"/>
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
    const curso = document.getElementById('cboCurso').value.trim();
    const nombre = document.getElementById('txtNombre').value.trim();

    if (curso === "") {
        $.messager.alert('UMOJN', 'Debe seleccionar un curso.', 'warning');
        return false;
    }

    if (nombre === "") {
        $.messager.alert('UMOJN', 'Falta el Nombre.', 'warning');
        return false;
    }
    return true;
}
</script>