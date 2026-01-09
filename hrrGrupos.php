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
	require_once ("funciones/fxGrupos.php");
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
		$mbPermisoUsuario = fxPermisoUsuario("hrrGrupos");
		
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
			if (isset($_POST["txtCodGrupo"]))
			{
				$msCodigo = $_POST["txtCodGrupo"];
				$msNombre = $_POST["txtNomGrupo"];
				if (isset($_POST["gridDetalle"]))
					$gridDetalle = $_POST["gridDetalle"];

				if (isset($_POST["gridUsuario"]))
					$gridUsuario = $_POST["gridUsuario"];

				{
					if (fxExisteGrupo($msCodigo) == 0)
					{
						fxGuardarGrupo ($msCodigo, $msNombre);
						$msBitacora = $msCodigo . "; " . $msNombre;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO003A", $msCodigo, "", "Agregar", $msBitacora);
					}
					else
					{
						fxModificarGrupo ($msCodigo, $msNombre);
						fxBorrarPermiso ($msCodigo);
						fxBorrarUsuarioGrupo ($msCodigo);
						$msBitacora = $msCodigo . "; " . $msNombre;
						fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO003A", $msCodigo, "", "Modificar", $msBitacora);
					}
				}
				
				if (isset($_POST["gridDetalle"]))
				{
					foreach($gridDetalle as $mRegistro)
					{
						$msPagina = $mRegistro['pagina'];
						if ($mRegistro['agregar'] == "x")
							$mbAgregar = 1;
						else
							$mbAgregar = 0;
						
						if ($mRegistro['editar'] == "x")
							$mbModificar = 1;
						else
							$mbModificar = 0;
							
						if ($mRegistro['borrar'] == "x")
							$mbBorrar = 1;
						else
							$mbBorrar = 0;
						
						if ($mRegistro['anular'] == "x")
							$mbAnular = 1;
						else
							$mbAnular = 0;

						fxGuardarPermiso ($msCodigo, $msPagina, $mbAgregar, $mbModificar, $mbBorrar, $mbAnular);
					}
				}
				
				if (isset($_POST["gridUsuario"]))
				{
					foreach($gridUsuario as $mRegistro)
					{
						$msUsuario = $mRegistro['usuario'];
						fxGuardarUsuarioGrupo ($msCodigo, $msUsuario);
					}
				}
					
				?><meta http-equiv="Refresh" content="0;url=gridGrupos.php"/><?php
			}
			else
			{
				if (isset($_POST["UMOJN"]))
					$msCodigo = $_POST["UMOJN"];
				else
					$msCodigo = "";
				
				if ($msCodigo != "")
				{
					$RecordSet = fxDevuelveGrupo(0, $msCodigo);
					$mFila = $RecordSet->fetch();
					$msNombre = $mFila["NOMBRE_003"];
				}
				else
				{
					$msNombre = "";
				}
	?>
    <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
					<div class="degradado"><strong>Grupos de usuarios</strong></div>
				</div>
			</div>

			<div class = "row">
                <div class="col-sm-12 offset-sm-none col-md-10 offset-md-1">
				<form id="hrrGrupos" name="hrrGrupos" action="hrrGrupos.php">
                	<div class = "form-group row">
                        <label for="txtCodGrupo" class="col-sm-12 col-md-3 col-form-label">Código del Grupo</label>
                        <div class="col-sm-12 col-md-3">
                        <?php
                            if (trim($msCodigo) != "")
                                echo('<input type="text" class="form-control" id="txtCodGrupo" name="txtCodGrupo" value="' . $msCodigo . '" readonly />'); 
                            else
                                echo('<input type="text" class="form-control" id="txtCodGrupo" name="txtCodGrupo" value="' . $msCodigo . '" />'); 
                        ?>
                        </div>
                    </div>
                    
                    <div class = "form-group row">
						<label for="txtNomGrupo" class="col-sm-12 col-md-3 col-form-label">Nombre del Grupo</label>
                        <div class="col-sm-12 col-md-6">
						<?php echo('<input type="text" class="form-control" id="txtNomGrupo" name="txtNomGrupo" value="' . $msNombre . '" />'); ?>
                        </div>
                    </div>
                    
                    <div class = "form-group row">
						<label for="dgGRP" class="col-sm-12 col-md-3 form-label">Permisos del Grupo</label>
                        <div class="col-sm-auto col-md-7">
                            <select class="form-control" id="CboPagina" name="CboPagina">
                                <?php
                                    $mDatos = fxDevuelvePaginas(1);
                                    while ($mFila = $mDatos->fetch())
                                    {
                                        $Valor = rtrim($mFila["PAGINA_REL"]);
                                        $Texto = $mFila["DESC_004"];
                                       	echo("<option value='" . $Valor . "'>" . $Texto . "</option>");
                                    }
                                ?>
                            </select>
                            <div id="dvGRP">
								<table id="dgGRP" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbGRP', singleSelect:true, method:'get', onClickCell: onClickCellGRP">
									<thead>
										<tr>
											<th data-options="field:'pagina',width:'20%',align:'left'">Página</th>
											<th data-options="field:'descripcion',width:'40%',align:'left'">Nombre de la página</th>
											<th data-options="field:'agregar',width:'10%',align:'center',editor:{type:'checkbox',options:{on:'x',off:''}}">Agregar</th>
											<th data-options="field:'editar',width:'10%',align:'center',editor:{type:'checkbox',options:{on:'x',off:''}}">Editar</th>
											<th data-options="field:'borrar',width:'10%',align:'center',editor:{type:'checkbox',options:{on:'x',off:''}}">Borrar</th>
											<th data-options="field:'anular',width:'10%',align:'center',editor:{type:'checkbox',options:{on:'x',off:''}}">Anular</th>
										</tr>
									</thead>
									<?php
										$mDatos = fxDevuelvePermiso($msCodigo);
										while ($mFila = $mDatos->fetch())
										{
											echo ("<tr>");
											echo ("<td>" . $mFila["PAGINA_REL"] . "</td>");
											echo ("<td>" . $mFila["DESC_004"] . "</td>");

											if ($mFila['INCLUIR_005'] == 1)
												echo ("<td>x</td>");
											else
												echo ("<td>&nbsp;</td>");

											if ($mFila['MODIFICAR_005'] == 1)
												echo ("<td>x</td>");
											else
												echo ("<td>&nbsp;</td>");

											if ($mFila['BORRAR_005'] == 1)
												echo ("<td>x</td>");
											else
												echo ("<td>&nbsp;</td>");

											if ($mFila['ANULAR_005'] == 1)
												echo ("<td>x</td>");
											else
												echo ("<td>&nbsp;</td>");
											
											echo ("</tr>");
										}
									?>
								</table>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tbGRP" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="appendGRP()">Agregar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitGRP()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitGRP()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectGRP()">Deshacer</a>
                    </div>
                    
                    <div class = "form-group row">
						<label for="dgUSR" class="col-sm-12 col-md-3 form-label">Usuarios del Grupo</label>
                        <div class="col-sm-auto col-md-7">
                            <select class="form-control" id="CboUsuario" name="CboUsuario">
                                <?php
                                    $mDatos = fxDevuelveUsuario(1);
                                    while ($mFila = $mDatos->fetch())
                                    {
                                        $Valor = rtrim($mFila["USUARIO_REL"]);
                                        $Texto = rtrim($mFila["NOMBRE_002"]);
                                       	echo("<option value='" . $Valor . "'>" . $Texto . "</option>");
                                    }
                                ?>
                            </select>
                            <div id="dvUSR">
								<table id="dgUSR" class="easyui-datagrid table" data-options="iconCls:'icon-edit', toolbar:'#tbUSR', singleSelect:true, method:'get', onClickCell: onClickCellUSR">
									<thead>
										<tr>
											<th data-options="field:'nombre',width:'100%',align:'left'">Nombre del Usuario</th>
											<th data-options="field:'usuario',hidden:'true'"></th>
										</tr>
									</thead>
									<?php
										$mDatos = fxDevuelveUsuarioGrupo($msCodigo);
										while ($mFila = $mDatos->fetch())
										{
											echo ("<tr>");
											echo ("<td>" . $mFila["NOMBRE_002"] . "</td>");
											echo ("<td>" . $mFila["USUARIO_REL"] . "</td>");
											echo ("</tr>");
										}
									?>
								</table>
                            </div>
                        </div>
                    </div>
                    
                    <div id="tbUSR" style="height:auto">
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="appendUSR()">Agregar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="removeitUSR()">Borrar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="acceptitUSR()">Aceptar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true" onclick="rejectUSR()">Deshacer</a>
                    </div>
                    
					<div class = "row">
                    	<div class="col-auto offset-sm-none col-md-12 offset-md-3">
							<input type="submit" id="Guardar" name="Guardar" value="Guardar" class="btn btn-primary" />
                            <input type="button" id="Cancelar" name="Cancelar" value="Cancelar" class="btn btn-primary" onclick="location.href='gridGrupos.php';"/>
                        </div>
                    </div>
				</form>
                </div>
	<?php	}
		}
	}
?>
			</div>
		</div>
	</div>
</body>
</html>
<script type='text/javascript'>
	window.onload = function() 
	{
        var dgGrupo = $('#dgGRP');
		var dgUsuario = $('#dgUSR');
        dgGrupo.datagrid({striped: true});
		dgUsuario.datagrid({striped: true});

		$('.datagrid-wrap').width('100%');
		$('.datagrid-view').height('200px');
	}

	function verificarFormulario()
	{
		if (document.getElementById('txtCodGrupo').value=="")
		{
			$.messager.alert('UMOJN','Falta el Código del Grupo.','warning');
			return false;
		}
		
		if(document.getElementById('txtNomGrupo').value=="")
		{
			$.messager.alert('UMOJN','Falta el Nombre del Grupo.','warning');
			return false;
		}
		
		return true;
	}
	
	var editIndexGRP = undefined;
	var editIndexUSR = undefined;
	var lastIndexGRP;
	var lastIndexUSR;
	
	$('#dgGRP').datagrid({
		onClickRow:function(rowIndex){
			if (lastIndexGRP != rowIndex){
				$(this).datagrid('endEdit', lastIndexGRP);
				$(this).datagrid('beginEdit', rowIndex);
			}
			lastIndexGRP = rowIndex;
		}
	});
	
	$('#dgUSR').datagrid({
		onClickRow:function(rowIndex){
			if (lastIndexUSR != rowIndex){
				$(this).datagrid('endEdit', lastIndexUSR);
				$(this).datagrid('beginEdit', rowIndex);
			}
			lastIndexUSR = rowIndex;
		}
	});

	function endEditingGRP(){
		if (editIndexGRP == undefined){return true}
		if ($('#dgGRP').datagrid('validateRow', editIndexGRP)){
			$('#dgGRP').datagrid('endEdit', editIndexGRP);
			editIndexGRP = undefined;
			return true;
		} else {
			return false;
		}
	}
	
	function endEditingUSR(){
		if (editIndexUSR == undefined){return true}
		if ($('#dgUSR').datagrid('validateRow', editIndexUSR)){
			$('#dgUSR').datagrid('endEdit', editIndexUSR);
			editIndexUSR = undefined;
			return true;
		} else {
			return false;
		}
	}
	
	function onClickCellGRP(index, field){
		if (editIndexGRP != index){
			if (endEditingGRP()){
				$('#dgGRP').datagrid('selectRow', index)
						.datagrid('beginEdit', index);
				var ed = $('#dgGRP').datagrid('getEditor', {index:index,field:field});
				if (ed){
					($(ed.target).data('checkbox') ? $(ed.target).textbox('checkbox') : $(ed.target)).focus();
				}
				editIndexGRP = index;
			} else {
				setTimeout(function(){
					$('#dgGRP').datagrid('selectRow', editIndexGRP);
				},0);
			}
		}
	}
	
	function onClickCellUSR(index, field){
		if (editIndexUSR != index){
			if (endEditingUSR()){
				$('#dgUSR').datagrid('selectRow', index)
						.datagrid('beginEdit', index);
				editIndexUSR = index;
			} else {
				setTimeout(function(){
					$('#dgUSR').datagrid('selectRow', editIndexUSR);
				},0);
			}
		}
	}
	
	function appendGRP(){
		if (endEditingGRP()){
			var i;
			var codigo;
			var existePagina = false;
			var datos = $('#dgGRP').datagrid('getData');
			var registros = $('#dgGRP').datagrid('getRows').length;
			
			if (registros > 0)
            {
    			for (i=0; i<registros; i++)
    			{
    				if (datos.rows[i].pagina == $('#CboPagina option:selected').val())
						existePagina = true;
    			}
			}
			
			if (existePagina == true)
			{
				$.messager.alert('UMOJN',$('#CboPagina option:selected').text() + ' ya fue incluido.','warning');
				$('#CboPagina').focus()
			}
			else
			{
				$('#dgGRP').datagrid('appendRow',{pagina:$('#CboPagina option:selected').val(), descripcion:$('#CboPagina option:selected').text(), agregar:'', editar:'', borrar:'', anular:''});
				editIndex = $('#dgGRP').datagrid('getRows').length;
				$('#dgGRP').datagrid('selectRow', editIndex).datagrid('beginEdit', editIndex);
			}
		}
	}

	function appendUSR(){
		if (endEditingUSR()){
			var i;
			var codigo;
			var existeUsuario = false;
			var datos = $('#dgUSR').datagrid('getData');
			var registros = $('#dgUSR').datagrid('getRows').length;
			
			if (registros > 0)
            {
    			for (i=0; i<registros; i++)
    			{
    				if (datos.rows[i].usuario == $('#CboUsuario option:selected').val())
						existeUsuario = true;
    			}
			}
			
			if (existeUsuario == true)
			{
				$.messager.alert('UMOJN',$('#CboUsuario option:selected').text() + ' ya fue incluido.','warning');
				$('#CboUsuario').focus()
			}
			else
			{
				$('#dgUSR').datagrid('appendRow',{usuario:$('#CboUsuario option:selected').val(), nombre:$('#CboUsuario option:selected').text()});
				editIndexUSR = $('#dgUSR').datagrid('getRows').length;
				$('#dgUSR').datagrid('selectRow', editIndexUSR).datagrid('beginEdit', editIndexUSR);
			}
		}
	}
		
	function removeitGRP(){
		if (editIndexGRP == undefined){return}
		$('#dgGRP').datagrid('cancelEdit', editIndexGRP)
				.datagrid('deleteRow', editIndexGRP);
		editIndexGRP = undefined;
	}
	
	function removeitUSR(){
		if (editIndexUSR == undefined){return}
		$('#dgUSR').datagrid('cancelEdit', editIndexUSR)
				.datagrid('deleteRow', editIndexUSR);
		editIndexUSR = undefined;
	}
	
	function acceptitGRP(){
		if (endEditingGRP()){
			$('#dgGRP').datagrid('acceptChanges');
		}
	}

	function acceptitUSR(){
		if (endEditingUSR()){
			$('#dgUSR').datagrid('acceptChanges');
		}
	}
	
	function rejectGRP(){
		$('#dgGRP').datagrid('rejectChanges');
		editIndexGRP = undefined;
	}
	
	function rejectUSR(){
		$('#dgUSR').datagrid('rejectChanges');
		editIndexUSR = undefined;
	}
	
	$('form').submit(function(e){
	e.preventDefault();

	if (verificarFormulario() == true)
	{
		var texto;
		var datos;
		var registros;
		var sinGrupos = true;
		var i;
		var gridDetalle = $('#dgGRP').datagrid('getData');
		var gridUsuario = $('#dgUSR').datagrid('getData');
		
		texto = '{"txtCodGrupo":"' + document.getElementById("txtCodGrupo").value + '", ';
		texto += '"txtNomGrupo":"' + document.getElementById("txtNomGrupo").value + '", ';

		registros = $('#dgGRP').datagrid('getRows').length - 1;
		
		if (registros >= 0)
		{
			sinGrupos = false;
			texto += '"gridDetalle": [';
			for (i=0; i<=registros; i++)
			{
				texto += '{"pagina":"' + gridDetalle.rows[i].pagina + '", "descripcion":"' + gridDetalle.rows[i].descripcion + '", "agregar":"' + gridDetalle.rows[i].agregar + '", "editar":"' + gridDetalle.rows[i].editar + '", "borrar":"' + gridDetalle.rows[i].borrar + '", "anular":"' + gridDetalle.rows[i].anular;
				if (i==registros)
					texto += '"}],';
				else
					texto += '"},';
			}
		}
		
		registros = $('#dgUSR').datagrid('getRows').length - 1;
		
		if (registros >= 0)
		{
			texto += '"gridUsuario": [';
			for (i=0; i<=registros; i++)
			{
				texto += '{"usuario":"' + gridUsuario.rows[i].usuario;
				if (i==registros)
					texto += '"}]}';
				else
					texto += '"},';
			}
		}
		else
		{
			if (sinGrupos == true)
				texto = texto.substr(0, texto.length - 2) + '}'
			else
				texto = texto.substr(0, texto.length - 1) + '}'
		}

		datos = JSON.parse(texto);

		$.ajax({
			url:'hrrGrupos.php',
			type:'post',
			data:datos,
			beforeSend: function(){console.log(datos)}	
		})
		.done(function(){location.href="gridGrupos.php";})
		.fail(function(){console.log('Error')});
		}
	});
</script>