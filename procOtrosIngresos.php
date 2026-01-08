<?php
session_start();
if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1) {
    echo('<meta http-equiv="Refresh" content="0;url=index.php"/>');
}
include("masterApp.php");
require_once("funciones/fxGeneral.php");
require_once("funciones/fxUsuarios.php");
require_once("funciones/fxPagos.php");

$m_cnx_MySQL = fxAbrirConexion();
$Registro = fxVerificaUsuario();

if ($Registro == 0) {
    ?>
    <div class="container text-center">
        <div id="DivContenido">
            <img src="imagenes/errordeacceso.png"/>
        </div>
    </div>
    <?php
    exit;
}

$mbAdministrador = fxVerificaAdministrador();
$mbPermisoUsuario = fxPermisoUsuario("procOtrosIngresos");

if ($mbAdministrador == 0 && $mbPermisoUsuario == 0) {
    ?>
    <div class="container text-center">
        <div id="DivContenido">
            <img src="imagenes/errordeacceso.png"/>
        </div>
    </div>
    <?php
    exit;
}

    ?>

    <!-- FORMULARIO -->
    <div class="container text-left">
        <div id="DivContenido">
            <div class="row">
                <div class="col-xs-12 col-md-11">
                    <div class="degradado"><strong>Otros pagos</strong></div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 offset-sm-none col-md-12 offset-md-1">
                    <form id="procOtrosIngresos" name="procOtrosIngresos" action="procOtrosIngresos.php" method="POST">
                        
                        <!-- Campo Pago -->
                        <div class="form-group row">
                            <label for="txtPago" class="col-sm-12 col-md-2 form-label">Pago</label>
                            <div class="col-sm-12 col-md-3">
                                <input type="text" class="form-control" id="txtPago" name="txtPago" value="<?php echo htmlspecialchars($msCodigo); ?>" readonly/>
                            </div>
                        </div>

                        <!-- Recibi de -->
                        <div class="form-group row">
                            <label for="txtRecibi" class="col-sm-12 col-md-2 form-label">Recibí de:</label>
                            <div class="col-sm-12 col-md-6">
                                <input type="text" class="form-control" id="txtRecibi" name="txtRecibi" value="<?php echo $msRecibi; ?>" />
                            </div>
                        </div>

                        <!-- Recibo -->
                        <div class="form-group row">
                            <label for="txnRecibo" class="col-sm-12 col-md-2 form-label">Recibo</label>
                            <div class="col-sm-12 col-md-3">
                                <?php 
                                $msConsultaRecibo = "SELECT MAX(RECIBO_140) as RECIBO_140 FROM UMO140A";
                                $mDatosRecibo = $m_cnx_MySQL->prepare($msConsultaRecibo);
                                $mDatosRecibo->execute();
                                $mFilaRecibo = $mDatosRecibo->fetch();
                                $mnRecibo = $mFilaRecibo['RECIBO_140'] + 1;
                                ?>
                                <input type="number" class="form-control" id="txnRecibo" name="txnRecibo" value="<?php echo $mnRecibo; ?>" />
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="form-group row">
                            <label for="dtpFecha" class="col-sm-12 col-md-2 form-label">Fecha</label>
                            <div class="col-sm-12 col-md-4">
                                <input type="date" class="form-control" id="dtpFecha" name="dtpFecha" value="<?php echo htmlspecialchars($msFecha); ?>" />
                            </div>
                        </div>

                        <!-- Moneda -->
                        <div class="form-group row">
                            <label for="optMoneda" class="col-sm-auto col-md-2 form-label">Moneda</label>
                            <div class="col-sm-11 col-md-3">
                                <div class="radio">
                                    <?php
                                    echo $mnMoneda == 0
                                        ? '<input type="radio" id="optMonedaC" name="optMoneda" value="0" checked/>Córdobas &nbsp
                                           <input type="radio" id="optMonedaD" name="optMoneda" value="1"/>Dólares'
                                        : '<input type="radio" id="optMonedaC" name="optMoneda" value="0"/>Córdobas &nbsp
                                           <input type="radio" id="optMonedaD" name="optMoneda" value="1" checked/>Dólares';
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="form-group row">
                            <label for="txnCantidad" class="col-sm-12 col-md-2 form-label">Cantidad</label>
                            <div class="col-sm-12 col-md-3">
                                <input type="text" class="form-control" id="txnCantidad" name="txnCantidad">
                            </div>
                        </div>

                        <!-- Concepto -->
                        <div class="form-group row">
                            <label for="txtConcepto" class="col-sm-12 col-md-2 form-label">Concepto</label>
                            <div class="col-sm-12 col-md-6">
                                <textarea class="form-control" id="txtConcepto" name="txtConcepto" rows="2" maxlength="400"><?php echo htmlspecialchars($msConcepto . " Pago en concepto de"); ?></textarea>
                            </div>
                        </div>

                        <!-- Tasa de cambio -->
                        <div class="form-group row">
                            <label for="txtTasa" class="col-sm-12 col-md-2 form-label">Tasa de cambio</label>
                            <div class="col-sm-12 col-md-3">
                                <input type="text" class="form-control" id="txtTasa" name="txtTasa" value="" onchange="calcularCambio()" />
                            </div>
                        </div>

                        <!-- Tipo de pago -->
                        <div class="form-group row">
                            <label for="optTipo" class="col-sm-auto col-md-2 form-label">Tipo</label>
                            <div class="col-sm-12 col-md-6">
                                <div class="radio">
                                    <?php
                                    $tipos = ["Efectivo", "Transferencia", "Deposito bancario", "Tarjeta"];
                                    foreach ($tipos as $i => $label) {
                                        $checked = ($msTipo == $i || ($i == 0 && $msCodigo == "")) ? "checked" : "";
                                        echo "<input type='radio' id='optTipo' name='optTipo' value='$i' $checked/>$label &nbsp";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de pago -->
                        <div class="col-auto offset-sm-0 col-md-5 offset-md-2">  
                            <input type="submit" id="Pagar" name="Pagar" value="Pagar" class="btn btn-primary"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php  ?>
</body>
</html>

<script>
    function inicializarEventos() {
        const tasaInput = document.getElementById("txtTasa");
        tasaInput.value = 36.62;
        const tasa = parseFloat(tasaInput.value) || 0;
        if (tasa > 0) {
            recalcularValoresConTasa(tasa);
            calcularCambio();
        }
        tasaInput.addEventListener('input', () => {
            const nuevaTasa = parseFloat(tasaInput.value) || 0;
            recalcularValoresConTasa(nuevaTasa);
            calcularCambio();
        });
    }

    function asignarEventosCalculo() {
        actualizarAdeudado();
    }

    function actualizarAdeudado() {
        const tasa = parseFloat(document.getElementById('txtTasa').value) || 1;
        let totalCordobas = 0;
        document.querySelectorAll("#dgPG tbody tr").forEach(row => {
            const moneda = row.dataset.moneda;
            let montoAbonado = 0;

            if (moneda === "USD") {
                const original = parseFloat(row.querySelector('.adeudadoDolares')?.dataset.original) || 0;
                montoAbonado = original * tasa;
            } else if (moneda === "NIO") {
                montoAbonado = parseFloat(row.querySelector('.adeudadocordobas')?.textContent) || 0;
            }
            totalCordobas += montoAbonado;
        });
        document.getElementById("txnCantidad").value = totalCordobas.toFixed(2);
    }

    function verificarFormulario() {
        const recibidor = document.getElementById('txtRecibi');
        const recibo = document.getElementById('txnRecibo');
        if (!recibidor.value) {
            recibidor.focus();
            $.messager.alert('UMOJN', 'Falta la persona que recibe.', 'warning');
            return false;
        }
        if (!recibo.value) {
            recibo.focus();
            $.messager.alert('UMOJN', 'Falta el recibo', 'warning');
            return false;
        }
        if ($('#dgPG').datagrid('getRows').length === 0) {
            $.messager.alert('UMOJN', 'Falta algún dato en la tabla de los pagos', 'warning');
            return false;
        }
        return true;
    }

    function validarTodosLosAbonos() {
        const rows = $('#dgPG').datagrid('getRows');
        const monedaSeleccionada = $('input[name="optMoneda"]:checked').val();
        let esValido = true;

        rows.forEach((row, index) => {
            const edAbonado = $('#dgPG').datagrid('getEditor', { index, field: 'abonado' });
            const abonado = edAbonado ? parseFloat($(edAbonado.target).numberbox('getValue')) || 0 : parseFloat(row.abonado) || 0;

            const adeudado = parseFloat((monedaSeleccionada === '1' ? row.adeudado : row.cAdeudado).toString().replace(/,/g, '')) || 0;

            if (abonado > adeudado) {
                $.messager.alert('UMOJN', `Revisa el monto abonado en la fila ${index + 1}. No puede ser mayor que lo adeudado.`, 'error');
                esValido = false;
                return false;
            }
        });
        return esValido;
    }
    function calcularCambio() {
        const tasa = parseFloat(document.getElementById("txtTasa").value) || 0;
        console.log("Tasa actual:", tasa);
        console.log("Cantidad de filas encontradas:", document.querySelectorAll("#dgPG tbody tr").length);
    }
</script>