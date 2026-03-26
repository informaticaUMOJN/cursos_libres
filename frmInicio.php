<?php
	session_start();
	if (!isset($_SESSION["gnVerifica"]) or $_SESSION["gnVerifica"] != 1)
	{
		echo('<meta http-equiv="Refresh" content="0;url=index.php">');
		exit('');
    }
	
	include ("masterApp.php");
	require_once ("funciones/fxGeneral.php");
    $m_cnx_MySQL = fxAbrirConexion();
?>
    <div class="container">
        <div id="DivContenido">
        	<div class = "row">
            	<div class="col-xs-12 col-md-12">
            		<div class="degradado">
                		<strong><?php echo($_SESSION["gsNombre"]) ?></strong>
                    </div>
                </div>
            </div>
            
        	<div class = "row">
				  
               <div class="row justify-content-center text-center">

				<div class="col-sm-4 col-md-3">
					<div class="divBotonInicio">
						<a href="gridCursosLibres.php">
							<img src="imagenes/btnCurso.png" style="border-radius:10%" class="img-fluid" />
						</a>
					</div>
				</div>

				<div class="col-sm-4 col-md-3">
					<div class="divBotonInicio">
						<a href="gridAsistenciaCL.php">
							<img src="imagenes/btnAsistencia.png" style="border-radius:10%" class="img-fluid" />
						</a>
					</div>
				</div>

				<div class="col-sm-4 col-md-3">
					<div class="divBotonInicio">
						<a href="gridMatriculaCursosL.php">
							<img src="imagenes/btnMatricula.png" style="border-radius:10%" class="img-fluid" />
						</a>
					</div>
				</div>

			</div>
                
            </div>
    	</div>
    </div>
</body>
</html>