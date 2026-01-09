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
	require_once("funciones/fxPagos.php");

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
<?php }
	else
	{
		$mbAdministrador = fxVerificaAdministrador();
		$mbPermisoUsuario = fxPermisoUsuario("procIngresos");
		
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
			if (isset($_POST["txtPago"])) 
			{
				  $msCodigo = $_POST["txtPago"];
                  $msRecibi = $_POST["txtRecibi"];
                  $mnRecibo = $_POST["txnRecibo"];
                  $msFecha = $_POST["dtpFecha"];
                  $mnMoneda = $_POST["optMoneda"];
                  $mnCantidad = $_POST["txnCantidad"];
                  $msConcepto = $_POST["txtConcepto"];
                  $msTasa = $_POST["txtTasa"];
                  $msTipo = $_POST["optTipo"];
                if ($msCodigo == "")
                    {
                        $msCodigo = fxGuardarPagos($msRecibi, $mnRecibo, $msFecha, $mnMoneda, $mnCantidad, $msConcepto, $msTasa, $msTipo);
                        $msBitacora = "$msCodigo; $msRecibi, $mnRecibo; $msFecha; $mnMoneda; $mnCantidad; $msConcepto; $msTasa; $msTipo";
                        fxAgregarBitacora($_SESSION["gsUsuario"], "UMO140A", $msCodigo, "", "Agregar", $msBitacora);
                    }
                    else
                        {}
                    ?><meta http-equiv="Refresh" content="0;url=gridOtroIng.php"/><?php
            }
            else
                {
                    if (isset($_POST["UMOJN"]))
                        $msCodigo = $_POST["UMOJN"];
                    else
                        $msCodigo = "";
                    if ($msCodigo != "")
                        {
                            $objRecordSet =  fxDevuelveEncabezadoP(0, $msMatriculaCodigo);
                            $msEstudiante = $mFila["ESTUDIANTE_REL"];
                        }
                        else{
                            $msCodigo = "";
                            $msMatriculaCodigo = "";
                            $msEstudiante = "";
                            $msRecibi = "";
                            $mnRecibo = "";
                            $msFecha = date('Y-m-d');
                            $mnMoneda = "";
                            $mnCantidad = "";
                            $msConcepto = "";
                            $msTasa = "";
                            $msTipo = ""; 
                        }
	?>
 <div class="container text-left">
    	<div id="DivContenido">
			<div class = "row">
				<div class="col-xs-12 col-md-11">
                    <div class="degradado"><strong>Pagos de otros Ingresos</strong></div>
                </div>
            </div>
			<div class = "row">
                <div class="col-sm-13 offset-sm-0 col-md-9 offset-md-2">
					<form id="procIngresos" name="procIngresos" action="procIngresos.php" onsubmit="return verificarFormulario()" method="post">
                    <div class="form-group row">
                        <label for="txtPago"class="col-sm-12 col-md-2 form-label">Pago</label>
                        <div class="col-sm-12 col-md-3">
                            <input type="text" class="form-control" id="txtPago" name="txtPago" value="<?php echo htmlspecialchars($msCodigo); ?>" readonly/>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <label for="txtRecibi"class="col-sm-12 col-md-2 form-label">Recibi de:</label>
                        <div class="col-sm-12 col-md-6">
                            <?php echo('<input type="text" class="form-control" id="txtRecibi" name="txtRecibi" value="' . $msRecibi . '" />'); ?>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <label for="txnRecibo"class="col-sm-12 col-md-2 form-label">Recibo</label>
                        <div class="col-sm-12 col-md-3">
                            <?php 
                                $msConsultaRecibo = "select MAX(RECIBO_140) as RECIBO_140 from UMO140A";
                                $mDatosRecibo = $m_cnx_MySQL->prepare($msConsultaRecibo);
                                $mDatosRecibo->execute();
                                $mFilaRecibo = $mDatosRecibo->fetch();
                                $mnRecibo = $mFilaRecibo['RECIBO_140'] + 1;
                                echo('<input type="number" class="form-control" id="txnRecibo" name="txnRecibo" value="' . $mnRecibo . '" />');
                            ?>
                        </div>
                    </div>
                    
                    <div class = "form-group row">
                        <label for="dtpFecha"class="col-sm-12 col-md-2 form-label">Fecha</label>
                        <div class="col-sm-12 col-md-4">
                            <input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="<?php echo htmlspecialchars($msFecha); ?>" />
						</div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="optMoneda" class="col-sm-auto col-md-2 form-label">Moneda</label>
                        <div class="col-sm-11 col-md-3">
                            <div class="radio">
                                <?php
                                    if ($mnMoneda == 0)
                                    echo('<input type="radio" id="optMonedaC" name="optMoneda" value="0"/>Córdobas &nbsp
                                    <input type="radio" id="optMonedaD" name="optMoneda" value="1" checked/>Dólares');
                                    else
                                    echo('<input type="radio" id="optMonedaC" name="optMoneda" value="0" checked/>Córdobas &nbsp
                                    <input type="radio" id="optMonedaD" name="optMoneda" value="1"/>Dólares');    
                                ?>       
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="txnCantidad"class="col-sm-12 col-md-2 form-label">Cantidad</label>
                        <div class="col-sm-12 col-md-3">
                            <input type="text" class="form-control"id="txnCantidad" name="txnCantidad" >
                        </div>
                    </div>
                    
                    <div class = "form-group row">
                        <label for="txtConcepto"class="col-sm-12 col-md-2 form-label">Concepto</label>
                        <div class="col-sm-12 col-md-6">
                        <textarea class="form-control" id="txtConcepto" name="txtConcepto" rows="2" maxlength="400"><?php echo htmlspecialchars($msConcepto . " Pago en concepto de"); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtTasa"class="col-sm-12 col-md-2 form-label">Tasa de cambio</label>
                        <div class="col-sm-12 col-md-3">
                            <input type="text" class="form-control" id="txtTasa" name="txtTasa" value="" onchange="calcularCambio()" />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="msTipo" class="col-sm-auto col-md-2 form-label">Tipo</label>
                        <div class="col-sm-12 col-md-6">
                            <div class="radio">
                                <?php
                                    if ($msTipo == 0 or $msCodigo =="")
										echo('<input type="radio" id="optTipo" name="optTipo" value="0" checked/>Efectivo &nbsp');
									    else
										    echo('<input type="radio" id="optTipo" name="optTipo" value="0" />Efectivo &nbsp');
									    if ($msTipo == 1)
										    echo('<input type="radio" id="optTipo" name="optTipo" value="1"  checked/>Transferencia');
									    else
										    echo(' <input type="radio" id="optTipo" name="optTipo" value="1"  />Transferencia &nbsp ');
									    if ($msTipo == 2)
										    echo('<input type="radio" id="optTipo" name="optTipo" value="2"  checked/>Deposito bancario');
									    else
										    echo(' <input type="radio" id="optTipo" name="optTipo" value="2"  />Deposito bancario &nbsp ');
									    if ($msTipo == 3)
										    echo('<input type="radio" id="optTipo" name="optTipo" value="3"  checked/>Tarjeta');
									    else
										    echo(' <input type="radio" id="optTipo" name="optTipo" value="3"  />Tarjeta &nbsp ');     
                                ?>   
                            </div>
                        </div>
                    </div>
                    <div class="col-auto offset-sm-0 col-md-5 offset-md-2">  
                        <input type="submit" id="Pagar" name="Pagar" value="Pagar" class="btn btn-primary"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
}}}
?>
</body>
</html><script>
    document.addEventListener('DOMContentLoaded', function() {
        inicializarEventos();

        const form = document.getElementById('procIngresos');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Valida recibo antes de enviar
                if (!validarRecibo() || !verificarFormulario()) {
                    e.preventDefault(); // Bloquea envío si hay error
                    return false;
                }
            });
        }
    });

    function inicializarEventos() {
        const tasaInput = document.getElementById("txtTasa");
        if (tasaInput) {
            tasaInput.value = 36.62;
        }
    }

    function verificarFormulario() {
        const recibidor = document.getElementById('txtRecibi');
        const recibo = document.getElementById('txnRecibo');

        if (recibidor && !recibidor.value.trim()) {
            recibidor.focus();
            $.messager.alert('UMOJN', 'Falta la persona que recibe.', 'warning');
            return false;
        }
        if (recibo && !recibo.value.trim()) {
            recibo.focus();
            $.messager.alert('UMOJN', 'Falta el recibo', 'warning');
            return false;
        }
        if ($('#dgPG').length && $('#dgPG').datagrid('getRows').length === 0) {
            $.messager.alert('UMOJN', 'Falta algún dato en la tabla de los pagos', 'warning');
            return false;
        }
        return true;
    }

    const recibosExistentes = [
        <?php
        $consulta = $m_cnx_MySQL->query("SELECT RECIBO_140 FROM UMO140A");
        while ($row = $consulta->fetch()) {
            echo "'" . $row['RECIBO_140'] . "',";
        }
        ?>
    ];

    let alertaMostrada = false;

    function validarRecibo() {
        const reciboInput = document.getElementById('txnRecibo');
        if (reciboInput && recibosExistentes.includes(reciboInput.value.trim())) {
            if (!alertaMostrada) {
                alertaMostrada = true;
                $.messager.alert('UMOJN', 'El número de recibo ya existe. Por favor ingrese un número diferente.', 'warning', function(){
                    reciboInput.focus();
                    alertaMostrada = false;
                });
            }
            return false;
        }
        return true;
    }
</script>
