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
    require_once ("funciones/fxPagos.php");
    $m_cnx_MySQL = fxAbrirConexion();
	$Registro = fxVerificaUsuario();
	if ($Registro == 0)
	{
?>
<div class="container text-center">
    <div id="DivContenido">
        <img src="imagenes/errordeacceso.png" />
    </div>
</div>
<?php 
}
else
{
    $mbAdministrador = fxVerificaAdministrador();
    $mbPermisoUsuario = fxPermisoUsuario("procCbrEstudiante");
    if ($mbAdministrador == 0 and $mbPermisoUsuario == 0)
    {
        ?>
        <div class="container text-center">
			<div id="DivContenido">
				<img src="imagenes/errordeacceso.png" />
			</div>
		</div>
        <?php
    }
    else
    {
        if (isset($_POST["txtPagos"]))
        {
            $msCodigo = $_POST["txtPagos"];
            $msPago = $_POST["txtPagos"];
            $msMatricula = $_POST['matricula'] ;  
            $msEstudiante = $_POST["cboEstudiante"];
            $msRecibi = $_POST["RECIBI_140"];
            $mnRecibo = $_POST["RECIBO_140"];
            $msFecha = $_POST["dtpFecha"];
            $optMoneda = $_POST["MONEDA_140"];
            $mfCantidad = $_POST["CANTIDAD_140"];
            $msConcepto = $_POST["CONCEPTO_140"];
            $mfTipo = $_POST ["TIPO_140"];
            $mfTasa = $_POST ["TASA_140"];
        
            if ($msCodigo == "")
            {
                 $msBitacora = $msCodigo . "; " . $mfAdeudado . "; " . "; " . $mfAbonado.";".";".$mnMoneda.";" .";".$mfDescuento . ";" . ";". $mbAnulado .";".";".$msMatricula;
                 fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $msCodigo, "", "Agregar", $msBitacora);
            }
            else
            {
                $msBitacora = $msCodigo . "; " . $mfAdeudado . "; " . "; " . $mfAbonado.";".";".$mnMoneda.";" .";".$mfDescuento . ";" . ";". $mbAnulado .";".";".$msMatricula;
                fxAgregarBitacora ($_SESSION["gsUsuario"], "UMO130A", $Codigo, "", "Modificar", $msBitacora);
            }
            ?><meta http-equiv="Refresh" content="0;url=gridSyllabus.php" /><?php
        }
        else
        {
            if (isset($_POST["UMOJN"]))
            $msCodigo = trim($_POST["UMOJN"]);
        else
        $msCodigo = "";
    $objRecordSet = fxPagosRealizados(0, $msCodigo);
    $mFila = $objRecordSet->fetch();
    if ($mFila !== false) 
    {
         $msPago = $mFila["PAGO_REL"];
         $msMatricula = $mFila["MATRICULA_REL"];
         $msEstudiante = $mFila["ESTUDIANTE_REL"];
         $msRecibi = $mFila["RECIBI_140"];
         $mnRecibo = $mFila["RECIBO_140"];
         $msFecha = $mFila["FECHA_140"];
         $optMoneda = $mFila["MONEDA_140"];
         $mfCantidad = $mFila["CANTIDAD_140"];
         $msConcepto = $mFila["CONCEPTO_140"];
         $mfTipo = $mFila ["TIPO_140"];
         $mfTasa = $mFila ["TASACAMBIO_140"];
    } 
    else
    {
        $msPago = "";
        $msEstudiante = "";
        $msRecibi = "";
        $mnRecibo = "";
        $msFecha = "";
        $optMoneda = "";
        $mfCantidad = "";
        $msConcepto = "";
        $mfTipo = "";
        $mfTasa = "";
    }
	?>
<div class="container text-left">
    <div id="DivContenido">
        <div class="row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Detalle de pagos</strong></div>
            </div>
            <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                    <input type="button" id="Regresar" name="Regresar" value="Regresar" class="btn btn-primary"  onclick="location.href='gridPagos.php';" />
                </div>
        </div>
        <form id="procPagos" name="procPagos" action="procPagos.php" method="post">
            <div class="col-sm-12 offset-sm-0 col-md-10 offset-md-2">
                <div class="form-group row">
                    <label for="txtPago" class="col-sm-12 col-md-2 form-label">Pago</label>
                    <div class="col-sm-12 col-md-3">
                        <input type="text" class="form-control" id="txtPago" name="txtPago" value="<?php echo htmlspecialchars($msCodigo); ?>" readonly/>
                    </div>
                </div>

                <div class="col-sm-12 col-md-3">
                        <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($msMatricula); ?>" readonly/>
                    </div>
                
                <div class="form-group row">
                    <label for="txtRecibi" class="col-sm-12 col-md-2 form-label">Recibi de:</label>
                    <div class="col-sm-12 col-md-7">
                        <?php echo('<input type="text" class="form-control" id="txtRecibi" name="txtRecibi" value="' . $msRecibi . '" readonly />'); ?> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cboEstudiante" class="col-sm-12 col-md-2 col-form-label">Estudiante</label>
                    <div class="col-sm-12 col-md-7">
                        <?php
                        if ($msEstudiante != "")
                        {
                            echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" disabled>');
                            echo("<option value='" . htmlspecialchars($msEstudiante) . "'>" . htmlspecialchars($msEstudiante) . "</option>");
                            $msConsulta = "select ESTUDIANTE_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 from UMO010A order by APELLIDO1_010, NOMBRE1_010 desc"; 
                            $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                            $mDatos->execute();
                            echo('</select>');
                        }
                        ?>    
                    </div>
                </div>
                <div class = "form-group row">
                    <label for="txnRecibo" class="col-sm-12 col-md-2 form-label">Recibo</label>
                    <div class="col-sm-12 col-md-3">
                        <?php echo('<input type="number" class="form-control" id="txnRecibo" name="txnRecibo" value="' . $mnRecibo . '" readonly/>'); ?>
                    </div>
                </div>
                <div class = "form-group row">
                    <label for="dtpFecha" class="col-sm-12 col-md-2 form-label">Fecha</label>
                    <div class="col-sm-12 col-md-4">
                        <input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="<?php echo htmlspecialchars($msFecha); ?>"readonly />
					</div>
                </div>
                <div class="form-group row">
                    <label for="optMoneda" class="col-sm-auto col-md-2 form-label">Moneda</label>
                    <div class="col-sm-12 col-md-3">
                        <div class="radio">
                            <?php
                                if ($optMoneda == 1)
                                echo('<input type="radio" id="optMoneda" name="optMoneda" value="0" disabled />&nbsp Córdobas &nbsp
                                <input type="radio" id="optMoneda" name="optMoneda" value="1" checked disabled /> Dólares');
                                else
                                echo('<input type="radio" id="optMoneda" name="optMoneda" value="0" checked disabled /> Córdobas &nbsp
                                <input type="radio" id="optMoneda" name="optMoneda" value="1" disabled />&nbsp Dólares');
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="txnCantidad" class="col-sm-12 col-md-2 form-label">Cantidad</label>
                    <div class="col-sm-12 col-md-3">
                        <input type="number" class="form-control" id="txnCantidad" name="txnCantidad" value="<?php echo number_format((float)$mfCantidad, 3, '.', ''); ?>" readonly step="0.01" style="text-align: right;" />
                    </div>
                </div>
                <div class = "form-group row">
                    <label for="txtConcepto" class="col-sm-12 col-md-2 form-label">Concepto</label>
                    <div class="col-sm-12 col-md-7">
                        <textarea class="form-control" id="txtConcepto" name="txtConcepto" rows="2" maxlength="400" readonly><?php echo $msConcepto; ?></textarea>
                    </div>
                </div>
                <div class = "form-group row">
                    <label for="txtTasa" class="col-sm-12 col-md-2 form-label">Tasa de cambio</label>
                    <div class="col-sm-12 col-md-3">
                        <input type="text" class="form-control" id="txtTasa" name="txtTasa" value="<?php echo $mfTasa; ?>" readonly />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mfTipo" class="col-sm-auto col-md-2 form-label">Tipo</label>
                    <div class="col-sm-12 col-md-7">
                        <div class="radio">
                            <?php
                                if ($mfTipo == 0 or $msCodigo =="")
									echo('<input type="radio" id="txtTipo" name="txtTipo" value="0" checked disabled />Efectivo &nbsp');
								else
                                    echo('<input type="radio" id="txtTipo" name="txtTipo" value="0" disabled />Efectivo &nbsp');
								if ($mfTipo == 1)
                                    echo('<input type="radio" id="txtTipo" name="txtTipo" value="1"  checked disabled />Transferencia');
								else
                                    echo(' <input type="radio" id="txtTipo" name="txtTipo" value="1" disabled  />Transferencia &nbsp ');
								if ($mfTipo == 2)
									echo('<input type="radio" id="txtTipo" name="txtTipo" value="2"  checked disabled />Deposito bancario');
								else
                                    echo(' <input type="radio" id="txtTipo" name="txtTipo" value="2"  disabled />Deposito bancario &nbsp ');
								if ($mfTipo == 3)
									echo('<input type="radio" id="txtTipo" name="txtTipo" value="3"  checked disabled />Tarjeta');
								else
                                    echo(' <input type="radio" id="txtTipo" name="txtTipo" value="3"  disabled />Tarjeta &nbsp ');     
                            ?> 
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="dgPG" class="col-sm-12 col-md-2 form-label">Detalle de los cobros</label>
                    <div class="col-sm-auto col-md-7">
                        <div id="dvPG">
                            <table id="dgPG" data-options="iconCls:'icon-edit', toolbar:'#tbPG', footer:'#ftPG', singleSelect:true, method:'get', fitColumns:true">
                                <thead>
                                    <tr> 
                                        <th data-options="field:'cobro'">Cobro</th>
                                        <th data-options="field:'concepto', width:'66%',align:'left'">Descripcion del pago</th>
                                        <th data-options="field:'descuento', width:'19%',align:'left'">Descuento</th>
                                        <th data-options="field:'valor', editor:'text', width:'15%',align:'left'">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                               $mDatos = fxMostrarPorPago($msCodigo, $msMatricula); 

                                while ($mFila = $mDatos->fetch())
                                {
                                    echo ("<tr data-cobro='" . $mFila["COBRO_REL"] . "' data-matricula='" . $mFila["MATRICULA_REL"] . "'>");
                                   // echo ("<td>" . $mFila["MATRICULA_REL"] . "</td>");
                                    echo ("<td>" . $mFila["PAGO_REL"] . "</td>");
                                    echo ("<td>" . $mFila["DESC_130"] . "</td>");
                                    echo ("<td>" . $mFila["DESCUENTO_141"] . "</td>");
                                    echo ("<td>" . $mFila["VALOR_141"] . "</td>");
                                    echo ("</tr>");
                                }
                                ?>  
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="ftPG" style="height:auto"></div>
                </div>
            </div>
        </div>
    </form>
</div> 
<?php  }}} ?>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
let editIndex = undefined;
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('matricula').style.display = 'none';
    $('#dgPG').datagrid({
        striped: true,
        footer: '#ftPG',
        singleSelect: true,
        method: 'get',
    });
    $(document).ready(function () {
        $('#dgPG').datagrid('hideColumn', 'cobro');
    });
});
</script>