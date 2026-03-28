<?php
function fxGuardarAlumnos($msUniversidad, $msMunicipio, $msColegio, $msFechaIns, $msNombres, $msApellidos, $msNacionalidad,
$msNumeroUnico, $msCedula, $msFechaNac, $msTelefono, $msCelular, $msEmail, $msDireccion, $msSexo, $mnPeso, $mnAltura, $msSangre,
$mbDiscapacidad, $msDeficiencia, $mnEstadoCivil, $mnHijos, $mnNivelEstudio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual,
$msEntidad, $msIdioma, $msDominioIdioma, $mnMedio, $msNombrePadre, $msNombreMadre, $mbTrabajaPadre, $mbTrabajaMadre,
$msTrabajoPadre, $msTrabajoMadre, $msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select ifnull(MID(MAX(ALUMNO_REL), 4), 0) as Ultimo from UMO200A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $msCodigo = "ALU" . str_pad($mnNumero, 7, "0", STR_PAD_LEFT);

	if ($msColegio == "")
		$msColegio = null;

	if ($msUniversidad == "")
		$msUniversidad = null;
	
	$msConsulta = "insert into UMO200A (ALUMNO_REL, UNIVERSIDADCL_REL, MUNICIPIO_REL, COLEGIOCL_REL, FECHAINS_200, NOMBRES_200, ";
   	$msConsulta .= "APELLIDOS_200, NACIONALIDAD_200, NUMEROUNICO_200, CEDULA_200, FECHANAC_200, TELEFONO_200, CELULAR_200, ";
   	$msConsulta .= "EMAIL_200, DIRECCION_200, SEXO_200, PESO_200, ALTURA_200, SANGRE_200, DISCAPACIDAD_200, DEFICIENCIA_200, ";
   	$msConsulta .= "ESTADOCIVIL_200, HIJOS_200, NIVELESTUDIOS_200, CONDICIONLAB_200, OCUPACION_200, SECTOR_200, INGRESOMENSUAL_200, ";
   	$msConsulta .= "ENTIDADLAB_200, IDIOMA_200, DOMINIOIDIOMA_200, MEDIO_200, NPADRE_200, NMADRE_200, PTRABAJA_200, ";
   	$msConsulta .= "MTRABAJA_200, PTRABAJO_200, MTRABAJO_200, NOMBREREF_200, CEDULAREF_200, CELULARREF_200, DIRECCIONREF_200) ";
	$msConsulta .= "values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo, $msUniversidad, $msMunicipio, $msColegio, $msFechaIns, $msNombres, $msApellidos, $msNacionalidad, $msNumeroUnico, $msCedula,
	$msFechaNac, $msTelefono, $msCelular, $msEmail, $msDireccion, $msSexo, $mnPeso, $mnAltura, $msSangre, $mbDiscapacidad, $msDeficiencia, $mnEstadoCivil, $mnHijos,
	$mnNivelEstudio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad, $msIdioma, $msDominioIdioma, $mnMedio, 
	$msNombrePadre, $msNombreMadre, $mbTrabajaPadre, $mbTrabajaMadre, $msTrabajoPadre, $msTrabajoMadre ,$msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef]);
	return $msCodigo;
}

function fxModificarAlumnos($msCodigo, $msUniversidad, $msMunicipio, $msColegio, $msFechaIns, $msNombres, $msApellidos, $msNacionalidad, $msNumeroUnico, $msCedula,
$msFechaNac, $msTelefono, $msCelular, $msEmail, $msDireccion, $msSexo, $mnPeso, $mnAltura, $msSangre, $mbDiscapacidad, $msDeficiencia, $mnEstadoCivil, $mnHijos,
$mnNivelEstudio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad, $msIdioma, $msDominioIdioma, $mnMedio, 
$msNombrePadre, $msNombreMadre, $mbTrabajaPadre, $mbTrabajaMadre, $msTrabajoPadre, $msTrabajoMadre ,$msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO200A set UNIVERSIDADCL_REL = ?, MUNICIPIO_REL = ?, COLEGIOCL_REL = ?, FECHAINS_200 = ?, NOMBRES_200 = ?, ";
   		$msConsulta .= "APELLIDOS_200 = ?, NACIONALIDAD_200 = ?, NUMEROUNICO_200 = ?, CEDULA_200 = ?, FECHANAC_200 = ?, TELEFONO_200 = ?, CELULAR_200 = ?, ";
   		$msConsulta .= "EMAIL_200 = ?, DIRECCION_200 = ?, SEXO_200 = ?, PESO_200 = ?, ALTURA_200 = ?, SANGRE_200 = ?, DISCAPACIDAD_200 = ?, DEFICIENCIA_200 = ?, ";
   		$msConsulta .= "ESTADOCIVIL_200 = ?, HIJOS_200 = ?, NIVELESTUDIOS_200 = ?, CONDICIONLAB_200 = ?, OCUPACION_200 = ?, SECTOR_200 = ?, INGRESOMENSUAL_200 = ?, ";
   		$msConsulta .= "ENTIDADLAB_200 = ?, IDIOMA_200 = ?, DOMINIOIDIOMA_200 = ?, MEDIO_200 = ?, NPADRE_200 = ?, NMADRE_200 = ?, PTRABAJA_200 = ?, ";
   		$msConsulta .= "MTRABAJA_200 = ?, PTRABAJO_200 = ?, MTRABAJO_200 = ?, NOMBREREF_200 = ?, CEDULAREF_200 = ?, CELULARREF_200 = ?, DIRECCIONREF_200 = ? ";
		$msConsulta .= "where ALUMNO_REL = ?";
	
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msUniversidad, $msMunicipio, $msColegio, $msFechaIns, $msNombres, $msApellidos, $msNacionalidad, $msNumeroUnico, $msCedula,
		$msFechaNac, $msTelefono, $msCelular, $msEmail, $msDireccion, $msSexo, $mnPeso, $mnAltura, $msSangre, $mbDiscapacidad, $msDeficiencia, $mnEstadoCivil, $mnHijos,
		$mnNivelEstudio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad, $msIdioma, $msDominioIdioma, $mnMedio, 
		$msNombrePadre, $msNombreMadre, $mbTrabajaPadre, $mbTrabajaMadre, $msTrabajoPadre, $msTrabajoMadre ,$msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef, $msCodigo]);
	}
	
function fxDevuelveAlumnos($mbLlenaGrid, $msCodigo = "")
{
	$m_cnx_MySQL = fxAbrirConexion();
		
	if ($mbLlenaGrid == 1)
	{
		$msConsulta = "select ALUMNO_REL, NOMBRES_200, APELLIDOS_200, CELULAR_200 ";
		$msConsulta .= "from UMO200A order by ALUMNO_REL desc";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
	}
	else
	{
		$msConsulta = "select ALUMNO_REL,ALUMNO_REL, UNIVERSIDADCL_REL, MUNICIPIO_REL, COLEGIOCL_REL, FECHAINS_200, NOMBRES_200, ";
   		$msConsulta .= "APELLIDOS_200, NACIONALIDAD_200, NUMEROUNICO_200, CEDULA_200, FECHANAC_200, TELEFONO_200, CELULAR_200, ";
   		$msConsulta .= "EMAIL_200, DIRECCION_200, SEXO_200, PESO_200, ALTURA_200, SANGRE_200, DISCAPACIDAD_200, DEFICIENCIA_200, ";
   		$msConsulta .= "ESTADOCIVIL_200, HIJOS_200, NIVELESTUDIOS_200, CONDICIONLAB_200, OCUPACION_200, SECTOR_200, INGRESOMENSUAL_200, ";
   		$msConsulta .= "ENTIDADLAB_200, IDIOMA_200, DOMINIOIDIOMA_200, MEDIO_200, NPADRE_200, NMADRE_200, PTRABAJA_200, ";
   		$msConsulta .= "MTRABAJA_200, PTRABAJO_200, MTRABAJO_200, NOMBREREF_200, CEDULAREF_200, CELULARREF_200, DIRECCIONREF_200 ";
		$msConsulta .= "from UMO200A where ALUMNO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	return $mDatos;
}

/*****Detalle Documento (UMO201)***********/

	function fxGuardarDetDocumento($msCodigo, $msArchivo, $mnTipoDoc, $msDescripcion, $msRuta)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO201A (ALUMNO_REL, EVIDENCIAS_REL, TIPO_201, DESC_201, RUTA_201) values (?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msArchivo, $mnTipoDoc, $msDescripcion, $msRuta]);
	}
	
	function fxBorrarDetDocumento($msCodigo, $msImagen)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO201A where ALUMNO_REL = ? and EVIDENCIAS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msImagen]);
	}
	
	function fxDevuelveDetDocumento($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select ALUMNO_REL, EVIDENCIAS_REL, TIPO_201, DESC_201, RUTA_201 from UMO201A where ALUMNO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
?>