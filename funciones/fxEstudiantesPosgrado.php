<?php
	//*****ESTUDIANTES DE POSGRADO************************************************************//
	function fxGuardarEstudiantePos($msCarrera, $msUniversidad, $msMunicipio, $msUsuario, $mdFecha, $mnAnnoAcademico, $msCarnet,
	$msNombre1, $msNombre2, $msApellido1, $msApellido2, $msGradoAcademico, $mdFechaNac, $msNacionalidad, $mnPeso, $mnTalla, $msTipoSangre, $msCedula,
	$msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $mnMedio, $msEmergencia,
	$msTelEmergencia, $msCelEmergencia, $mbLaboral, $msOcupacion, $mnIngresoMensual, $msCentroTrabajo, $msDireccionTrabajo,
	$mbOtroIdioma, $msIdioma)
	{
		if ($msUsuario == "") {$msUsuario = null;}
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "Select ifnull(mid(max(ESTUDIANTEPOS_REL), 3), 0) as Ultimo from UMO250A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "EP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO250A (ESTUDIANTEPOS_REL, CARRERA_REL, UNIVERSIDAD_REL, MUNICIPIO_REL, USUARIO_REL, FECHA_250, ";
		$msConsulta .= "ANNOACADEMICO_250, CARNET_250, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, GRADOACADEMICO_250, ";
		$msConsulta .= "FECHANAC_250, NACIONALIDAD_250, PESO_250, TALLA_250, TIPOSANGRE_250, CEDULA_250, SEXO_250, ESTADOCIVIL_250, HIJOS_250, ";
		$msConsulta .= "TELEFONO_250, CELULAR_250, CORREOE_250, CORREOI_250, DIRECCION_250, MEDIO_250, EMERGENCIA_250, ";
		$msConsulta .= "TEL_EMERGENCIA_250, CEL_EMERGENCIA_250, CONDICIONLABORAL_250, OCUPACION_250, INGRESOMENSUAL_250, ";
		$msConsulta .= "CENTROTRABAJO_250, DIRECCIONTRABAJO_250, OTROIDIOMA_250, IDIOMA_250) ";
		$msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msCarrera, $msUniversidad, $msMunicipio, $msUsuario, $mdFecha, $mnAnnoAcademico, $msCarnet,
		$msNombre1, $msNombre2, $msApellido1, $msApellido2, $msGradoAcademico, $mdFechaNac, $msNacionalidad, $mnPeso, $mnTalla, $msTipoSangre, $msCedula,
		$msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $mnMedio, $msEmergencia,
		$msTelEmergencia, $msCelEmergencia, $mbLaboral, $msOcupacion, $mnIngresoMensual, $msCentroTrabajo, $msDireccionTrabajo,
		$mbOtroIdioma, $msIdioma]);
		return $msCodigo;
	}
	
	function fxModificarEstudiantePos($msCodigo, $msCarrera, $msUniversidad, $msMunicipio, $msUsuario, $mdFecha, $mnAnnoAcademico, $msCarnet,
	$msNombre1, $msNombre2, $msApellido1, $msApellido2, $msGradoAcademico, $mdFechaNac, $msNacionalidad, $mnPeso, $mnTalla, $msTipoSangre, $msCedula,
	$msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $mnMedio, $msEmergencia,
	$msTelEmergencia, $msCelEmergencia, $mbLaboral, $msOcupacion, $mnIngresoMensual, $msCentroTrabajo, $msDireccionTrabajo,
	$mbOtroIdioma, $msIdioma)
	{
		if ($msUsuario == "") {$msUsuario = null;}
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO250A set CARRERA_REL = ?, UNIVERSIDAD_REL = ?, MUNICIPIO_REL = ?, USUARIO_REL = ?, FECHA_250 = ?, ";
		$msConsulta .= "ANNOACADEMICO_250 = ?, CARNET_250 = ?, NOMBRE1_250 = ?, NOMBRE2_250 = ?, APELLIDO1_250 = ?, APELLIDO2_250 = ?, ";
		$msConsulta .= "GRADOACADEMICO_250 = ?, FECHANAC_250 = ?, NACIONALIDAD_250 = ?, PESO_250 = ?, TALLA_250 = ?, TIPOSANGRE_250 = ?, CEDULA_250 = ?, ";
		$msConsulta .= "SEXO_250 = ?, ESTADOCIVIL_250 = ?, HIJOS_250 = ?, TELEFONO_250 = ?, CELULAR_250 = ?, CORREOE_250 = ?, ";
		$msConsulta .= "CORREOI_250 = ?, DIRECCION_250 = ?, MEDIO_250 = ?, EMERGENCIA_250 = ?, TEL_EMERGENCIA_250 = ?, ";
		$msConsulta .= "CEL_EMERGENCIA_250 = ?, CONDICIONLABORAL_250 = ?, OCUPACION_250 = ?, INGRESOMENSUAL_250 = ?, CENTROTRABAJO_250 = ?, ";
		$msConsulta .= "DIRECCIONTRABAJO_250 = ?, OTROIDIOMA_250 = ?, IDIOMA_250 = ? where ESTUDIANTEPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCarrera, $msUniversidad, $msMunicipio, $msUsuario, $mdFecha, $mnAnnoAcademico, $msCarnet, $msNombre1, $msNombre2,
		$msApellido1, $msApellido2, $msGradoAcademico, $mdFechaNac, $msNacionalidad, $mnPeso, $mnTalla, $msTipoSangre, $msCedula, $msSexo,
		$mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $msDireccion, $mnMedio, $msEmergencia,
		$msTelEmergencia, $msCelEmergencia, $mbLaboral, $msOcupacion, $mnIngresoMensual, $msCentroTrabajo,
		$msDireccionTrabajo, $mbOtroIdioma, $msIdioma, $msCodigo]);
	}
	
	function fxBorrarEstudiantePos($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO250A where ESTUDIANTEPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
	}
	
	function fxDevuelveEstudiantePos($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();

		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select ESTUDIANTEPOS_REL, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, CELULAR_250, CORREOI_250 ";
			$msConsulta .= "from UMO250A order by ESTUDIANTEPOS_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select ESTUDIANTEPOS_REL, CARRERA_REL, UNIVERSIDAD_REL, MUNICIPIO_REL, USUARIO_REL, FECHA_250, ";
			$msConsulta .= "ANNOACADEMICO_250, CARNET_250, NOMBRE1_250, NOMBRE2_250, APELLIDO1_250, APELLIDO2_250, GRADOACADEMICO_250, ";
			$msConsulta .= "FECHANAC_250, NACIONALIDAD_250, PESO_250, TALLA_250, TIPOSANGRE_250, CEDULA_250, SEXO_250, ESTADOCIVIL_250, HIJOS_250, ";
			$msConsulta .= "TELEFONO_250, CELULAR_250, CORREOE_250, CORREOI_250, DIRECCION_250, MEDIO_250, EMERGENCIA_250, ";
			$msConsulta .= "TEL_EMERGENCIA_250, CEL_EMERGENCIA_250, CONDICIONLABORAL_250, OCUPACION_250, INGRESOMENSUAL_250, ";
			$msConsulta .= "CENTROTRABAJO_250, DIRECCIONTRABAJO_250, OTROIDIOMA_250, IDIOMA_250 from UMO250A where ESTUDIANTEPOS_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		return $mDatos;
	}

	/*****Detalle Documento (UMO251A)***********/

	function fxGuardarDetDocumentoPos($msCodigo, $mnTipoDoc, $msArchivo, $msDescripcion, $msRuta)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO251A (ESTUDIANTEPOS_REL, TIPO_REL, ARCHIVO_251, DESC_251, RUTA_251) values (?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $mnTipoDoc, $msArchivo, $msDescripcion, $msRuta]);
	}
	
	function fxBorrarDetDocumentoPos($msCodigo, $msArchivo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO251A where ESTUDIANTEPOS_REL = ? and ARCHIVO_251 = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msArchivo]);
	}
	
	function fxDevuelveDetDocumentoPos($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select ESTUDIANTEPOS_REL, TIPO_REL, ARCHIVO_251, DESC_251, RUTA_251 from UMO251A where ESTUDIANTEPOS_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
?>