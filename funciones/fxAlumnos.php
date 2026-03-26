<?php
function fxGuardarAlumnos($msFechaIns, $msUniversidad, $msNombres, $msApellidos, $msFechaNac, $msNacionalidad, $msMunicipio, $msCedula,
$msDeficiencia, $msSexo,$mnPeso,$msSangre ,$mnAltura, $mnEstadoCivil, $mnHijos, $mbDiscapacidad, $mnNivelEstudio, $msColegio, $msCurso, $msTelefono, $msCelular,
$msEmail, $mbOtroIdioma, $msIdioma, $msDominioIdioma, $msDireccion, $mnMedio, $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual,
$msEntidad, $msNombrePadre,$mbTrabajaPadre,$msTrabajoPadre,$msNombreMadre ,$mbTrabajaMadre,$msTrabajoMadre ,$msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef)
{
    $m_cnx_MySQL = fxAbrirConexion();

    $msConsulta = "select ifnull(MID(MAX(ALUMNO_REL), 4), 0) as Ultimo from UMO200A";
    $mDatos = $m_cnx_MySQL->prepare($msConsulta);
    $mDatos->execute();
    $mFila = $mDatos->fetch();
    $mnNumero = intval($mFila["Ultimo"]) + 1;
    $msCodigo = "ALU" . str_pad($mnNumero, 7, "0", STR_PAD_LEFT);
	
	$msConsulta = "insert into UMO200A (ALUMNO_REL, FECHAINS_200, UNIVERSIDAD_REL, NOMBRES_200, APELLIDOS_200, FECHANAC_200, ";
	$msConsulta .= "NACIONALIDAD_200, MUNICIPIO_REL, CEDULA_200, DEFICIENCIA_200, SEXO_200, PESO_200, SANGRE_200, ALTURA_200, ESTADOCIVIL_200, HIJOS_200, ";
	$msConsulta .= "DISCAPACIDAD_200, NIVELESTUDIOS_200, COLEGIO_REL, CURSOS_REL, TELEFONO_200, CELULAR_200, EMAIL_200, ";
	$msConsulta .= "OTROIDIOMA_200, IDIOMA_200,	DOMINIOIDIOMA_200, DIRECCION_200, MEDIO_200, CONDICIONLAB_200, OCUPACION_200, ";
	$msConsulta .= "SECTOR_200, INGRESOMENSUAL_200,	ENTIDADLAB_200,NPADRE_200,PTRABAJA_200,PTRABAJO_200,NMADRE_200,MTRABAJA_200,MTRABAJO_200, NOMBREREF_200, CEDULAREFERENTE_200, CELULARREFERENTE_200, ";
	$msConsulta .= "DIRECCIONREF_200) ";
	$msConsulta .= "values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,? ,?,?,?,?,?,?,?,?)";

	$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([$msCodigo,$msFechaIns, $msUniversidad,$msNombres, $msApellidos,$msFechaNac,$msNacionalidad,$msMunicipio, $msCedula,
		$msDeficiencia,$msSexo,$mnPeso,$msSangre ,$mnAltura,$mnEstadoCivil,$mnHijos,$mbDiscapacidad, $mnNivelEstudio, $msColegio, $msCurso, $msTelefono, $msCelular,$msEmail,
		$mbOtroIdioma, $msIdioma, $msDominioIdioma,$msDireccion,$mnMedio,$mbLaboral,$msOcupacion,$mnSector,$mnIngresoMensual, $msEntidad,$msNombrePadre,$mbTrabajaPadre,$msTrabajoPadre,$msNombreMadre ,$mbTrabajaMadre,$msTrabajoMadre ,
		$msNombreRef, $msCedulaRef,$msCelularRef,$msDireccionRef]);
	return $msCodigo;
}

function fxModificarAlumnos($msCodigo, $msFechaIns, $msUniversidad, $msNombres, $msApellidos, $msFechaNac,  $msNacionalidad, $msMunicipio, $msCedula,$msDeficiencia,$msSexo,$mnPeso,$msSangre ,$mnAltura,
	$mnEstadoCivil, $mnHijos,$mbDiscapacidad, $mnNivelEstudio, $msColegio, $msCurso, $msTelefono, $msCelular, $msEmail, $mbOtroIdioma ,$msIdioma, $msDominioIdioma, $msDireccion, $mnMedio,
	$mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad,$msNombrePadre,$mbTrabajaPadre,$msTrabajoPadre,$msNombreMadre ,$mbTrabajaMadre,$msTrabajoMadre ,	$msNombreRef, $msCedulaRef, $msCelularRef,$msDireccionRef)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO200A set FECHAINS_200 = ?,  UNIVERSIDAD_REL = ?,    NOMBRES_200 = ?, APELLIDOS_200 = ?, ";
		$msConsulta .= "FECHANAC_200 = ?,  NACIONALIDAD_200 = ?,  MUNICIPIO_REL = ?,  CEDULA_200 = ?, ";
		
		$msConsulta .= "DEFICIENCIA_200 = ?, SEXO_200 = ?, PESO_200 = ?, SANGRE_200 = ?, ALTURA_200 = ?, ";
        $msConsulta .= "ESTADOCIVIL_200 = ?, HIJOS_200 = ?, DISCAPACIDAD_200 = ?, NIVELESTUDIOS_200 = ?, ";

		$msConsulta .= "COLEGIO_REL = ?,  CURSOS_REL = ?,  TELEFONO_200 = ?,  CELULAR_200 = ?,  EMAIL_200 = ?, OTROIDIOMA_200 = ?, ";
		$msConsulta .= "IDIOMA_200 = ?,  DOMINIOIDIOMA_200 = ?,  DIRECCION_200 = ?, MEDIO_200 = ?, CONDICIONLAB_200 = ?, ";
		$msConsulta .= "OCUPACION_200 = ?, SECTOR_200 = ?,  INGRESOMENSUAL_200 = ?,  ENTIDADLAB_200 = ?, ";
		$msConsulta .="NPADRE_200=?,PTRABAJA_200 =? ,PTRABAJO_200 =?,NMADRE_200 =? ,MTRABAJA_200 =? ,MTRABAJO_200=?,";
		
		$msConsulta .=" NOMBREREF_200 = ?,CEDULAREFERENTE_200 = ?, CELULARREFERENTE_200 = ?, DIRECCIONREF_200 = ? where ALUMNO_REL = ?";
	
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
	$mDatos->execute([
    $msFechaIns, $msUniversidad, $msNombres, $msApellidos, $msFechaNac,
    $msNacionalidad, $msMunicipio, $msCedula,

    $msDeficiencia, $msSexo, $mnPeso, $msSangre, $mnAltura,
    $mnEstadoCivil, $mnHijos, $mbDiscapacidad, $mnNivelEstudio,

    $msColegio, $msCurso, $msTelefono, $msCelular, $msEmail, $mbOtroIdioma,
    $msIdioma, $msDominioIdioma, $msDireccion, $mnMedio,
    $mbLaboral, $msOcupacion, $mnSector, $mnIngresoMensual, $msEntidad,

    $msNombrePadre, $mbTrabajaPadre, $msTrabajoPadre,
    $msNombreMadre, $mbTrabajaMadre, $msTrabajoMadre,

    $msNombreRef, $msCedulaRef, $msCelularRef, $msDireccionRef,

    $msCodigo
]);
	
		}
	
function fxDevuelveAlumnos($mbLlenaGrid, $msCodigo = "")
{
	$m_cnx_MySQL = fxAbrirConexion();
		
	if ($mbLlenaGrid == 1)
	{
		$msConsulta = "select ALUMNO_REL, NOMBRES_200, APELLIDOS_200, CELULAR_200, CURSOS_REL ";
		$msConsulta .= "from UMO200A order by ALUMNO_REL desc";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
	}
	else
	{
		$msConsulta = "select ALUMNO_REL,FECHAINS_200,  UNIVERSIDAD_REL,    NOMBRES_200, APELLIDOS_200,  FECHANAC_200,  ";
		$msConsulta .= "NACIONALIDAD_200,  MUNICIPIO_REL,  CEDULA_200,  DEFICIENCIA_200, SEXO_200, PESO_200, SANGRE_200, ALTURA_200, NIVELESTUDIOS_200, ";
		$msConsulta .= "ESTADOCIVIL_200, HIJOS_200,  DISCAPACIDAD_200, NIVELESTUDIOS_200, COLEGIO_REL,  CURSOS_REL,  ";
		$msConsulta .= "TELEFONO_200,  CELULAR_200,  EMAIL_200, OTROIDIOMA_200, IDIOMA_200,  DOMINIOIDIOMA_200, DIRECCION_200, ";
		$msConsulta .= "MEDIO_200, CONDICIONLAB_200, OCUPACION_200, SECTOR_200,  INGRESOMENSUAL_200,  ENTIDADLAB_200, ";
		$msConsulta .= "NPADRE_200,PTRABAJA_200,PTRABAJO_200,NMADRE_200,MTRABAJA_200,MTRABAJO_200,";
		$msConsulta .= "NOMBREREF_200, CEDULAREFERENTE_200, CELULARREFERENTE_200, DIRECCIONREF_200 ";
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