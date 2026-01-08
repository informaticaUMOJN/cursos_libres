<?php
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="es-NI">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Control Administrativo y Académico de UMOJN."/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="icon" href="imagenes/favicon.png" />
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" href="bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="bootstrap/css/jquery.bootgrid.css" />
<link rel="stylesheet" href="css/easyui.css" />
<link rel="stylesheet" href="css/icon.css" />
<link rel="stylesheet" href="css/StyleUMO.css"/>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery.easyui.min.js"></script>
<script src="js/datagrid-detailview.js"></script>
<script src="js/jquery.redirect.js"></script>
<script src="bootstrap/js/bootstrap.bundle.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="js/moderniz.2.8.1.js"></script>

<script>
    // jquery ready start
    $(document).ready(function() {
        //////////////////////// Prevent closing from click inside dropdown
        $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
        });

        // make it as accordion for smaller screens
        if ($(window).width() < 992) {
            $('.dropdown-menu a').click(function(e){
                e.preventDefault();
                if($(this).next('.submenu').length){
                    $(this).next('.submenu').toggle();
                }
                $('.dropdown').on('hide.bs.dropdown', function () {
                $(this).find('.submenu').hide();
                })
            });
        }
    }); // jquery end
</script>

<style type="text/css">
	@media (min-width: 992px){
		.dropdown-menu .dropdown-toggle:after{
			border-top: .3em solid transparent;
		    border-right: 0;
		    border-bottom: .3em solid transparent;
		    border-left: .3em solid;
		}

		.dropdown-menu .dropdown-menu{
			margin-left:0; margin-right: 0;
		}

		.dropdown-menu li{
			position: relative;
		}
		.nav-item .submenu{ 
			display: none;
			position: absolute;
			left:100%; top:-7px;
		}
		.nav-item .submenu-left{ 
			right:100%; left:auto;
		}

		.dropdown-menu > li:hover{ background-color: #f1f1f1 }
		.dropdown-menu > li:hover > .submenu{
			display: block;
		}
	}
</style>

<title>Aplicación web UMOJN</title>
</head>

<body>
<div id="cabecera">
    <div class="container-fluid">
        <div class="row">
            <img src="imagenes/header.png" width="100%" />
        </div>
    </div>
</div>
<div id="cabecera2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="main_nav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="frmInicio.php">Inicio</a>
                            </li> 

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Catalogos</a>
                                <ul class="dropdown-menu"> 
                                    <li> 
                                        <a class="dropdown-item" href="gridCursosLibres.php">Cursos</a>
                                    </li>
                                     <li> 
                                        <a class="dropdown-item"  href="gridModulos.php">Modulos</a>
                                    </li>

                                    <li>
                                        <a  class="dropdown-item" href="gridAlumnos.php">Estudiantes</a></a>
                                    </li>
                                      
                                </ul>
                            </li>

                           <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Procesos</a>
                                <ul class="dropdown-menu"> 
                                    <li> 
                                        <a class="dropdown-item" href="gridAsistenciaCL.php">Asistencias</a>
                                    </li>
                                     <li> 
                                        <a class="dropdown-item"  href="gridMatriculaCursosL.php">Matrícula</a>
                                    </li>

                                    <li>
                                        <a  class="dropdown-item" href="gridPlanCurso.php">Plan de estudios</a>
                                    </li>
                                      
                                </ul>
                            </li> 

                             <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Certificados</a>
                                <ul class="dropdown-menu"> 
                                    <li> 
                                        <a class="dropdown-item" href="gridDiplomas.php">Diplomas</a>
                                    </li>

                                     <li> 
                                        <a class="dropdown-item" href="gridImprimirQR.php">Imprimir QR</a>
                                    </li>
                                </ul>
                            </li>
                           
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Reportes</a>
                                <ul class="dropdown-menu"> 
                                    <li> 
                                        <a class="dropdown-item" href="gridAsistenciaCL.php">Asistencias</a>
                                    </li>
                                </ul>
                            </li>
                           
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Cerrar sesión</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="col-md-3 text-right">
                <div style="display:inline-block; vertical-align:middle; margin-left:1%; margin-top:1%">
                    <img src="imagenes/user.png" width="90%" />
                </div>
                <div style="display:inline-block; vertical-align:middle; margin-top:0.8%; color: rgb(255, 255, 255)">
                    <?php echo($_SESSION["gsNombre"]) ?>
                </div>
            </div>
        </div>
	</div>
</div>