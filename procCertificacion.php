<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
		exit('');
	}
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
	require_once ("funciones/fxUsuarios.php");
	require_once ("funciones/fxCertificacion.php");

	$m_cnx_MySQL = fxAbrirConexion();
	$Registro = fxVerificaUsuario();
	
	if ($Registro == 0)
	{
?>
	<div class="container text-center">
		<div id="DivContenido">
			<img src="imagenes/errordeacceso.png"/>
		</div>
	</div>
<?php 
	}
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("procCertificacion");
		
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
			if (isset($_POST["Certificar"]))
			{
				$msCurso = $_POST["cboCursoNoCertificado"];
				$msLibro = $_POST["cboLibro"];
				fxCertificar($msCurso, $msLibro);
			}
			$msCursos = "";
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Certificación</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-xs-12 offset-sm-none col-md-10 offset-md-1">
					<form id="procCertificacion" name="procCertificacion" action="procCertificacion.php" method="post">
						<div class = "row">
							<div class="col-auto offset-sm-none col-md-12">
								<input type="submit" id="Certificar" name="Certificar" value="Certificar" class="btn btn-primary" />
								<input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='frmInicio.php';"/>
							</div>
						</div>

						<div class="easyui-tabs tabs-narrow" style="width:100%;height:auto"
							data-options="onSelect:function(title,index)
							{
								if(title == 'Certificado'){estadoBoton(true);}
								else if(title == 'Para certificar'){estadoBoton(false);}
							}">
							<div title="Para certificar" style="padding-left: 20px; padding-top: 10px">
								<div class="form-group row">
									<label for="cboCursoNoCertificado" class="col-sm-12 col-md-2 col-form-label">Curso Libre</label>
									<div class="col-sm-12 col-md-7">
										<?php
											echo('<select class="form-control" id="cboCursoNoCertificado" name="cboCursoNoCertificado">');

											$msConsulta = "SELECT CURSOS_REL, NOMBRE_190 FROM UMO190A WHERE ESTADO_190 = 1 ORDER BY NOMBRE_190";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute();
											while ($mFila = $mDatos->fetch())
											{
												$msValor = rtrim($mFila["CURSOS_REL"]);
												$msTexto = rtrim($mFila["NOMBRE_190"]);
												if ($msCursos == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msCursos = $msValor;
												}
												else
												{
													if ($msCursos == $msValor)
														echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
													else
														echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
												}
											}
											echo('</select>');
										?>
									</div>
								</div>

								<div class="form-group row">
									<label for="cboLibro" class="col-sm-12 col-md-2 col-form-label">Libro de certificación</label>
									<div class="col-sm-12 col-md-7">
										<?php
											echo('<select class="form-control" id="cboLibro" name="cboLibro">');

											$msConsulta = "SELECT LIBRO_REL, concat(DESC_330, ' (Tomo ', TOMO_330, ')') as DESC_330  FROM UMO330A";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute();
											while ($mFila = $mDatos->fetch())
											{
												$msValor = rtrim($mFila["LIBRO_REL"]);
												$msTexto = rtrim($mFila["DESC_330"]);
												echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
											}
											echo('</select>');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<div class="col-sm-auto col-md-7 offset-md-2">
										<div id="dvSinCertificar">
											<table id="dgSinCertificar" class="easyui-datagrid table" data-options="iconCls:'icon-edit', singleSelect:true, method:'get'">
												<thead>
													<tr>
														<th data-options="field:'matricula',width:'25%',align:'left'">Matrícula</th>
														<th data-options="field:'estudiante',width:'75%',align:'left'">Nombre del estudiante</th>
													</tr>
												</thead>
												<?php
													$mDatos = fxObtenerEstudiantesActivos($msCursos);
													while ($mFila = $mDatos->fetch())
													{
														echo ("<tr>");
														echo ("<td>" . $mFila["MATCURSO_REL"] . "</td>");
														echo ("<td>" . $mFila["ESTUDIANTE"] . "</td>");
														echo ("</tr>");
													}
												?>
											</table>
										</div>
									</div>
								</div>
							</div>

							<div title="Certificado" style="padding-left: 20px; padding-top: 10px">
								<div class="form-group row">
									<label for="cboCursoCertificado" class="col-sm-12 col-md-2 col-form-label">Curso Libre</label>
									<div class="col-sm-12 col-md-7">
										<?php
											echo('<select class="form-control" id="cboCursoCertificado" name="cboCursoCertificado">');

											$msConsulta = "SELECT CURSOS_REL, NOMBRE_190 FROM UMO190A WHERE ESTADO_190 = 1 ORDER BY NOMBRE_190";
											$mDatos = $m_cnx_MySQL->prepare($msConsulta);
											$mDatos->execute();
											while ($mFila = $mDatos->fetch())
											{
											$msValor = rtrim($mFila["CURSOS_REL"]);
											$msTexto = rtrim($mFila["NOMBRE_190"]);
												if ($msCursos == "")
												{
													echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
													$msCursos = $msValor;
												}
												else
												{
													if ($msCursos == $msValor)
														echo("<option value='" . $msValor . "' selected>" . $msTexto . "</option>");
													else
														echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
												}
											}
											echo('</select>');
										?>
									</div>
								</div>

								<div class = "form-group row">
									<div class="col-sm-auto col-md-9 offset-md-2">
										<div id="dvCertificado">
											<table id="dgCertificado" class="easyui-datagrid table" data-options="iconCls:'icon-edit', singleSelect:true, method:'get'">
												<thead>
													<tr>
														<th data-options="field:'matricula',width:'20%',align:'left'">Matrícula</th>
														<th data-options="field:'estudiante',width:'50%',align:'left'">Nombre del estudiante</th>
														<th data-options="field:'tomo',width:'10%',align:'left'">Tomo</th>
														<th data-options="field:'folio',width:'10%',align:'left'">Folio</th>
														<th data-options="field:'partida',width:'10%',align:'left'">Partida</th>
													</tr>
												</thead>
												<?php
													$mDatos = fxObtenerEstudiantesCertificados($msCursos);
													while ($mFila = $mDatos->fetch())
													{
														echo ("<tr>");
														echo ("<td>" . $mFila["MATCURSO_REL"] . "</td>");
														echo ("<td>" . $mFila["ESTUDIANTE"] . "</td>");
														echo ("<td>" . $mFila["TOMO_371"] . "</td>");
														echo ("<td>" . $mFila["FOLIO_371"] . "</td>");
														echo ("<td>" . $mFila["PARTIDA_REL"] . "</td>");
														echo ("</tr>");
													}
												?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php }} ?>
</body>
</html>
<script>
	function llenaGrid (curso, certificados)
	{
		var datos = new FormData();
		datos.append('curso', curso);
		datos.append('certificados', certificados);

		$.ajax({
			url: 'funciones/fxDatosMatCurso.php',
			type: 'post',
			data: datos,
			contentType: false,
			processData: false,
			success: function(response){
				document.getElementById('cboCurso').value = response;
				llenaCombos(response);
			}
		})
	}

	function estadoBoton (estado)
	{
		document.getElementById('Certificar').disabled = estado;
	}
</script>