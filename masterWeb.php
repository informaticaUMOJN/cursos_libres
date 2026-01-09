<?php
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="ES-NI">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Control Administrativo y Académico de UMOJN."/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="icon" href="imagenes/favicon.png" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap441.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="bootstrap/css/jquery.bootgrid.css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css" />
<link rel="stylesheet" type="text/css" href="css/icon.css" />
<link rel="stylesheet" type="text/css" href="css/StyleUMO.css"/>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery.easyui.min.js"></script>
<script src="js/datagrid-detailview.js"></script>
<script src="js/jquery.redirect.js"></script>
<script src="bootstrap/js/bootstrap.bundle.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/js/moderniz.2.8.1.js"></script>

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

<title>Aplicación web KDSA</title>
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
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Catálogos</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item dropdown-toggle" href="#">Académico</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item" href="gridEstudiantes.php">Estudiantes</a></li>
                                            <li><a class="dropdown-item" href="gridCursos.php">Cursos</a></li>
                                            <li><a class="dropdown-item" href="gridDocentes.php">Docentes</a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="gridCobros.php">Cobros</a></li>
                                    <li><a class="dropdown-item" href="gridProspectos.php">Prospectos</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Procesos</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item dropdown-toggle" href="#">Financiero</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item" href="procCobrosIndividuales.php">Cobros individuales</a></li>
                                            <li><a class="dropdown-item" href="gridPagos.php">Pagos de los estudiantes</a></li>
                                            <li><a class="dropdown-item" href="gridOtrosIngresos.php">Otros ingresos</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="dropdown-item dropdown-toggle" href="#">Docencia</a> 
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item" href="procPlanificacion.php">Planificación programática</a></li>
                                            <li><a class="dropdown-item" href="gridPlanClase.php">Planificación de clases</a></li>
                                            <li><a class="dropdown-item" href="gridAsistencia.php">Asistencias</a></li>
                                            <li><a class="dropdown-item" href="gridCalificaciones.php">Calificaciones</a></li>
                                            <li><a class="dropdown-item" href="gridIncidencias.php">Incidencias</a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="gridMatricula.php">Matrícula</a></li>
                                    <li><a class="dropdown-item" href="gridEstadoMatricula.php">Estado de las matrículas</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Reportes y Consultas</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item dropdown-toggle" href="#">Académico</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item" href="frmHojaMatricula.php">Hoja de matrícula</a></li>
                                            <li><a class="dropdown-item" href="frmMatriculados.php">Estudiantes matriculados</a></li>
                                            <li><a class="dropdown-item" href="frmMatPeriodo.php">Matriculados por período</a></li>
                                            <li><a class="dropdown-item" href="frmAsistencia.php">Asistencia estudiantil</a></li>
                                            <li><a class="dropdown-item" href="frmCalificaciones.php">Calificaciones</a></li>
                                            <li><a class="dropdown-item" href="frmPlanesClases.php">Planes de Clases</a></li>
                                            <li><a class="dropdown-item" href="consCursos.php">Cursos activos</a></li>
                                            <li><a class="dropdown-item" href="frmAsistenciaSemanal.php">Asistencia por período</a></li>
                                            <li><a class="dropdown-item" href="frmMatGeneral.php">Matrícula general</a></li>
                                            <li><a class="dropdown-item" href="frmAlumnoActivo.php">Constancia de alumno activo</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="dropdown-item dropdown-toggle" href="#">Financiero</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item" href="frmEstadoCuentas.php">Estado de cuentas del estudiante</a></li>
                                            <li><a class="dropdown-item" href="frmIngresos.php">Ingresos</a></li>
                                            <li><a class="dropdown-item" href="frmProyecciones.php">Proyecciones</a></li>
                                            <li><a class="dropdown-item" href="frmCtasPorCobrar.php">Cuentas por cobrar</a></li>
                                            <li><a class="dropdown-item" href="frmSolventes.php">Estudiantes solventes</a></li>
                                            <li><a class="dropdown-item" href="frmProximosPagos.php">Próximos Pagos de Estudiantes</a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="frmBitacora.php">Bitácora</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Herramientas</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="gridUsuarios.php">Usuarios</a></li>
                                    <li><a class="dropdown-item" href="gridGrupos.php">Grupos</a></li>
                                    <li><a class="dropdown-item" href="gridCfgModulo.php">Módulos de los Cursos</a></li>
                                    <li><a class="dropdown-item" href="gridFeriados.php">Días no hábiles</a></li>
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