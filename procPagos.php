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
$mbPermisoUsuario = fxPermisoUsuario("procPagos");

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

if (isset($_POST["txtPago"])) {
    $msCodigo = $_POST["txtPago"];
    $msRecibi = $_POST["txtRecibi"];
    $mnRecibo = $_POST["txnRecibo"];
    $msFecha = $_POST["dtpFecha"];
    $mnMoneda = isset($_POST["optMoneda"]) ? intval($_POST["optMoneda"]) : 0;

    echo "Moneda recibida: " . $mnMoneda; 
    $mnCantidad = $_POST["txnCantidad"];
    $msConcepto = $_POST["txtConcepto"];
    $msTasa = $_POST["txtTasa"];
    $msTipo = $_POST["optTipo"];
    $gridDetalle = $_POST["gridDetalle"];

 
    if ($msCodigo== "")
    { 
        $msCodigo = fxGuardarPagos($msRecibi, $mnRecibo, $msFecha, $mnMoneda, $mnCantidad, $msConcepto, $msTasa, $msTipo);
        $msBitacora = "$msCodigo; $msRecibi, $mnRecibo; $msFecha; $mnMoneda; $mnCantidad; $msConcepto; $msTasa; $msTipo";
        fxAgregarBitacora($_SESSION["gsUsuario"], "UMO140A", $msCodigo, "", "Agregar", $msBitacora);
    }
    else {
        }
        foreach ($gridDetalle as $mRegistro) 
        {
            $cobro = $mRegistro['cobro'];
            $matricula = $mRegistro['matricula'];
            $abonado = floatval($mRegistro['abonado']);
            $descuento = floatval($mRegistro['descuento']);
            if ($abonado > 0 || $descuento > 0) {
                fxGuardarDetPago($cobro, $matricula, $abonado, $descuento, $msCodigo);
            }
        }
        
    echo '<meta http-equiv="Refresh" content="0;url=gridPagos.php"/>';
    exit;
} elseif (isset($_POST["UMOJN"])) {
    $msMatriculaCodigo = $_POST["UMOJN"];

    if (!empty($msMatriculaCodigo)) {
        $objRecordSet = fxDevuelveEncabezadoP(0, $msMatriculaCodigo);
        if ($objRecordSet && $mFila = $objRecordSet->fetch()) {
            $msEstudiante = $mFila["ESTUDIANTE_REL"];
        }
    }
?>

<div class="container text-left">
    <div id="DivContenido">
        <div class="row">
            <div class="col-xs-12 col-md-11">
                <div class="degradado"><strong>Catálogo de pagos</strong></div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 offset-sm-none col-md-12 offset-md-1">
               <form id="procPagos" name="procPagos" action="procPagos.php" method="POST" onsubmit="return validarRecibo();">

                    <div class="form-group row">
                        <input type="text" name="matricula" id="matricula" value="<?php echo htmlspecialchars($msMatriculaCodigo); ?>">
                    </div>
                    
                    <div class="form-group row">
                        <label for="txtPago"class="col-sm-12 col-md-2 form-label">Pago</label>
                        <div class="col-sm-12 col-md-3">
                            <input type="text" class="form-control" id="txtPago" name="txtPago" value="<?php echo htmlspecialchars($msCodigo); ?>" readonly/>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="cboEstudiante" class="col-sm-12 col-md-2 form-label">Estudiante</label>
                        <div class="col-sm-12 col-md-6">
                            <?php
                                if ($msEstudiante == "")
                                    echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante"disabled>');
                                else
                                    echo('<select class="form-control" id="cboEstudiante" name="cboEstudiante" disabled>');
                                    $msConsulta = "select ESTUDIANTE_REL, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010 from UMO010A order by APELLIDO1_010, NOMBRE1_010 desc"; 
                                    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
                                    $mDatos->execute();
                                while ($mFila = $mDatos->fetch())
                                {
                                    $msValor = trim($mFila["ESTUDIANTE_REL"]); 
                                    $msTexto = trim($mFila["NOMBRE1_010"]);
                                    if (trim($mFila["NOMBRE2_010"]) != "")
								    $msTexto .= " " . $mFila["NOMBRE2_010"];
								    $msTexto .= ", " . $mFila["APELLIDO1_010"];
								    if (trim($mFila["APELLIDO2_010"]) != "")
								    $msTexto .= " " . $mFila["APELLIDO2_010"];
                                    if ($msEstudiante == "")
                                    {
                                        echo("<option value='" . $msValor . "'>" . $msTexto . "</option>");
                                        $msEstudiante = $msValor;
                                    }
                                    else
                                    {
                                        if ($msEstudiante == $msValor)
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
                            <input type="text" class="form-control"id="txnCantidad" name="txnCantidad" readonly>
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
                    
                    <div class="form-group row">
                        <div class="col-sm-auto col-md-10">
                            <label for="dgPG" class="col-sm-12 col-md-5 form-label">Detalle de los cobros</label>
                        </div>
                        <div class="col-sm-auto col-md-10">
                            <div id="dvPG">
                                <table id="dgPG" class="easyui-datagrid table" data-options="iconCls:'icon-edit', fitColumns:true, height:'200px', width:'100%'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'cobro'">Cobro</th>
                                            <th data-options="field:'matricula',width:'20%',align:'left'">Matricula</th>
                                            <th data-options="field:'descripcion',width:'50%',align:'left'">Descripción</th>
                                            <th data-options="field:'adeudado', width:'14%',align:'left'">$ Adeudado</th>
                                            <th data-options="field:'c_adeudado', width:'14%',align:'left'">C$ Adeudado</th>
                                            <th data-options="field:'abonado', editor:'text', align:'left'">Abonado</th>
                                            <th data-options="field:'descuento', editor:'text', width:'10%',align:'left'">Descuento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $mDatos = fxObtenerRealizarPago($msMatriculaCodigo);
                                            while ($mFila = $mDatos->fetch()) 
                                            {
                                                $moneda = $mFila["MONEDA_131"];
                                                $adeudado = $mFila["ADEUDADO_131"] ?? 0;
                                                $descripcion = $mFila["DESC_130"];
                                                $abonado = $mFila["ABONADO_131"] ?? 0;
                                                $descuento = $mFila["DESCUENTO_131"] ?? 0;
                                                if ($abonado > 0) {
                                                    $descuento = 0;
                                                    $abonado = 0;
                                                } 
                                                echo "<tr data-cobro='" . $mFila["COBRO_REL"] . "' data-moneda='" . ($moneda == "Dólares" ? "USD" : "NIO") . "'>";
                                                echo "<td>" . $mFila["COBRO_REL"] . "</td>";
                                                echo "<td>" . $mFila["MATRICULA_REL"] . "</td>";
                                                echo "<td>" . $descripcion . "</td>";
                                                if ($moneda == 'Córdobas') {
                                                    echo "<td class='adeudadoDolares'></td>";
                                                    echo "<td class='adeudadocordobas'>" . number_format($adeudado, 2, '.', '') . "</td>";
                                                } elseif ($moneda == 'Dólares') {
                                                    echo "<td class='adeudadoDolares' data-original='" . number_format($adeudado, 2, '.', '') . "'>" . number_format($adeudado, 2, '.', '') . "</td>";
                                                    echo "<td class='adeudadocordobas'></td>";
                                                }
                                                echo "<td>" . $abonado . "</td>"; 
                                                echo "<td>" . $descuento . "</td>"; 
                                                echo "</tr>";
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="ftPG" style="height:auto"></div>
                    <div class="col-auto offset-sm-0 col-md-5 offset-md-2">  
                        <input type="submit" id="Pagar" name="Pagar" value="Pagar" class="btn btn-primary"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
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

    document.addEventListener("DOMContentLoaded", () => {
        $('#dgPG').datagrid({
            striped: true,
            footer: '#ftPG',
            singleSelect: true,
            method: 'get',
            columns: [[
                { field: 'cobro', title: 'Cobro', hidden: true },
                { field: 'matricula', title: 'Matrícula', hidden: true },
                { field: 'descripcion', title: 'Descripción' },
                { field: 'adeudado', title: '$ Adeudado' },
                { field: 'cAdeudado', title: 'C$ Adeudado' },
                { field: 'abonado', title: 'Abonado', editor: 'numberbox' },
                { field: 'descuento', title: 'Descuento', editor: 'numberbox' }
            ]],
            onClickRow(index) {
                $(this).datagrid('beginEdit', index);
            },
            onBeginEdit(index) {
                const editor = $('#dgPG').datagrid('getEditor', { index, field: 'abonado' });
                if (editor) {
                    $(editor.target).numberbox({
                        precision: 2,
                        groupSeparator: ",",
                        decimalSeparator: ".",
                        onChange: calcularTotalDesdeFilas,
                    });
                    setTimeout(() => {
                        $(editor.target).next().find('input.textbox-text').on('input', calcularTotalDesdeFilas);
                    }, 0);
                }
                validarTodosLosAbonos(index);
            },
            onAfterEdit: calcularTotalDesdeFilas,
            onLoadSuccess() {
                asignarEventosCalculo();
                calcularTotalDesdeFilas();
            }
        });
        document.getElementById('matricula').style.display = 'none';
        inicializarEventos();
        calcularCambio();
    });

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

    function calcularTotalDesdeFilas() {
        const rows = $('#dgPG').datagrid('getRows');
        let total = 0;

        rows.forEach((row, index) => {
            let valor = 0;
            try {
                const editor = $('#dgPG').datagrid('getEditor', { index, field: 'abonado' });
                valor = editor ? parseFloat($(editor.target).numberbox('getValue')) || 0 : parseFloat(row.abonado) || 0;
            } catch {
                valor = 0;
            }
            total += isFinite(valor) ? valor : 0;
        });
        document.getElementById("txnCantidad").value = total.toFixed(2);
    }

    function recalcularValoresConTasa(tasa) {
        const nuevosDatos = [];
        document.querySelectorAll("#dgPG tbody tr").forEach(row => {
            const moneda = row.dataset.moneda;
            const cells = row.children;
            const adeudadoDolaresCell = row.querySelector('.adeudadoDolares');
            const adeudadoCordobasCell = row.querySelector('.adeudadocordobas');
            let adeudado = 0, c_adeudado = 0;

            if (moneda === "USD") {
                adeudado = parseFloat(adeudadoDolaresCell?.textContent.replace(/[^0-9.-]+/g, "")) || 0;
                c_adeudado = adeudado * tasa;
                adeudadoCordobasCell.textContent = c_adeudado.toFixed(2);
            } else if (moneda === "NIO") {
                c_adeudado = parseFloat(adeudadoCordobasCell?.textContent.replace(/[^0-9.-]+/g, "")) || 0;
                adeudado = c_adeudado / tasa;
                adeudadoDolaresCell.textContent = adeudado.toFixed(2);
            }

            nuevosDatos.push({
                cobro: cells[0].textContent,
                matricula: cells[1].textContent,
                descripcion: cells[2].textContent,
                adeudado: adeudado.toFixed(2),
                cAdeudado: c_adeudado.toFixed(2),
                abonado: row.querySelector('input.abonado')?.value || '0',
                descuento: row.querySelector('input.descuento')?.value || '0'
            });
        });
        $('#dgPG').datagrid('loadData', nuevosDatos);
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
      // Array con los números de recibo existentes
    const recibosExistentes = [
        <?php
        $consulta = $m_cnx_MySQL->query("SELECT RECIBO_140 FROM UMO140A");
        while ($row = $consulta->fetch()) {
            echo "'" . $row['RECIBO_140'] . "',";
        }
        ?>
    ];
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('procPagos');

        form.addEventListener('submit', function(e) {
            //valida recibo
            if (!validarRecibo()) {
                e.preventDefault(); // Bloquea envío si el recibo ya existe
                return false;
            }
        });
    });

    let alertaMostrada = false;

function validarRecibo() {
    const reciboInput = document.getElementById('txnRecibo').value.trim();

    if (recibosExistentes.includes(reciboInput)) {
        if (!alertaMostrada) {
            alertaMostrada = true;
            $.messager.alert('UMOJN', 'El número de recibo ya existe. Por favor ingrese un número diferente.', 'warning', function(){
                document.getElementById('txnRecibo').focus();
                alertaMostrada = false; // permitir futuras alertas
            });
        }
        return false; // bloquea submit
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

    $('form').submit(function (e) 
    {
        e.preventDefault();
         if (!validarRecibo()) {
            return false; 
        }
        if (verificarFormulario() && validarTodosLosAbonos())
        {
            var texto = '';
            var datos;
            var i;
            var gridDetalle = $('#dgPG').datagrid('getData');
            var registros = gridDetalle.rows.length;
            texto += '{"txtPago":"' + $('#txtPago').val() + '", ';
            texto += '"txtRecibi":"' + $('#txtRecibi').val() + '", ';
            texto += '"txnRecibo":"' + $('#txnRecibo').val() + '", ';
            texto += '"dtpFecha":"' + $('#dtpFecha').val() + '", ';
            const monedaSeleccionada = $('input[name="optMoneda"]:checked').val() || 0;
            texto += '"optMoneda":"' + monedaSeleccionada + '", ';
            texto += '"txnCantidad":"' + $('#txnCantidad').val() + '", ';
            texto += '"txtConcepto":"' + $('#txtConcepto').val() + '", ';
            texto += '"txtTasa":"' + $('#txtTasa').val() + '", ';
            texto += '"optTipo":"' + $('#optTipo').val() + '", ';
        
            if (registros > 0) 
            {
                texto += '"gridDetalle": [';
                var panel = $('#dgPG').datagrid('getPanel');
                var bodyRows = panel.find('div.datagrid-body tr');
            
                for (i = 0; i < registros; i++) 
                {
                    var row = gridDetalle.rows[i];
                    var $fila = $(bodyRows[i]);
                    var edAbonado = $('#dgPG').datagrid('getEditor', {index: i, field: 'abonado'});
                    var edDescuento = $('#dgPG').datagrid('getEditor', {index: i, field: 'descuento'});
                    var abonado = edAbonado ? parseFloat($(edAbonado.target).val()) || 0 : parseFloat(row.abonado) || 0;
                    var descuento = edDescuento ? parseFloat($(edDescuento.target).val()) || 0 : parseFloat(row.descuento) || 0;
                    console.log('Valor real abonado:', abonado);
                    texto += '{"cobro":"' + row.cobro + '", ';
                    texto += '"matricula":"' + row.matricula + '", ';
                    texto += '"abonado":"' + abonado + '", ';
                    texto += '"descuento":"' + descuento + '"}';
                    if (i < registros - 1) 
                    {
                        texto += ',';
                    }
                }
                texto += ']}';
            }
            console.log(gridDetalle); 
            datos = JSON.parse(texto); 
            console.log(datos);
        
            $.ajax({
                url: 'procPagos.php',
                type: 'post',
                data: datos,
                datos : JSON.parse(texto),
            })
            .done(function () {
                location.href = "gridPagos.php"; 
                datos = JSON.parse(texto); 
            })
            .fail(function () {
            console.log('Error');
        });
    }
});
</script>