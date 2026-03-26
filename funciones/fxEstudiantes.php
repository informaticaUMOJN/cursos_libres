<?php
	function fxGuardarEstudiantes($msColegio, $msCarrera, $msMunicipio, $msUsuario, $msCodEstudiantil, $msFecha, $mnGeneracion, $msCarnet, $msNombre1, $msNombre2, 
	$msApellido1, $msApellido2, $mbNacional, $msFechaNac, $msLugarNac, $msPais, $msNacionalidad, $msEtnia, $mnPeso, $mnTalla, $mnTipoSangre, $msCedula,
	$msPasaporte, $msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $mbDireccion, $msZona, $mbMedio, $msEmergencia,
	$msTelEmergencia, $msCelEmergencia,	$mbOtroIdioma, $msIdioma, $msDominioIdioma, $mnNivelEstudio, $mbCondicionLaboral, $msOcupacion, $mnSalarioEstudiante,
	$mbDiscapacidad, $msNombreMadre, $msNombrePadre, $msTelefonoPadre, $msTelefonoMadre, $msCelularMadre, $msCelularPadre, $mbTrabajaMadre, $mbTrabajaPadre,
	$msTrabajoMadre, $msTrabajoPadre, $mnSalarioMadre, $mnSalarioPadre, $mnMenores, $mnMayores, $mnDependientes)
	{
		$m_cnx_MySQL = fxAbrirConexion();

		/*Verifica la existencia del usuario*/
		$msConsulta = "select NOMBRE_002 from UMO002A where USUARIO_REL = ?";
		$mResultado = $m_cnx_MySQL->prepare($msConsulta);
		$mResultado->execute([$msUsuario]);
		$mnRegistros = $mResultado->rowCount();

		if ($mnRegistros == 0)
		{
			/*Crea el usuario del estudiantes en UMO002A*/
			$msNombre = $msNombre1;
			if (trim($msNombre2) != "")
				$msNombre .= " " . $msApellido1;
			if (trim($msApellido2 != ""))
				$msNombre .= " " . $msApellido2;
			
			$msClave = "";
			for ($i=0; $i<strlen($msCarnet); $i++)
			{
				$mChar = substr($msCarnet, $i, 1);
				if ($mChar != "-")
					$msClave .= $mChar;
			}

			$msEncriptado = crypt($msClave, '_appUMOJN');
			$msConsulta = "insert into UMO002A (USUARIO_REL, NOMBRE_002, CORREO_002, CLAVE_002, SUPERVISOR_002, ARCHIVOS_002, ESTUDIANTE_002, ADMINISTRADOR_002, ACTIVO_002) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$mResultado = $m_cnx_MySQL->prepare($msConsulta);
			$mResultado->execute([$msUsuario, $msNombre, $msCorreoI, $msEncriptado, 0, 0, 1, 0, 1]);
		}

		$msConsulta = "Select ifnull(mid(max(ESTUDIANTE_REL), 3), 0) as Ultimo from UMO010A";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute();
		$mFila = $mDatos->fetch();
		$mnNumero = intval($mFila["Ultimo"]);
		$mnNumero += 1;
		$mnLongitud = strlen($mnNumero);
		$msCodigo = "ES" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
		$msConsulta = "insert into UMO010A (ESTUDIANTE_REL, COLEGIO_REL, CARRERA_REL, MUNICIPIO_REL, USUARIO_REL, CODESTUDIANTIL_010, FECHA_010, GENERACION_010, CARNET_010, NOMBRE1_010, NOMBRE2_010, ";
		$msConsulta .= "APELLIDO1_010, APELLIDO2_010, NACIONAL_010, FECHANAC_010, LUGARNAC_010, PAIS_010, NACIONALIDAD_010, ETNIA_010, PESO_010, TALLA_010, TIPOSANGRE_010, CEDULA_010, ";
		$msConsulta .= "PASAPORTE_010, SEXO_010, ESTADOCIVIL_010, HIJOS_010, TELEFONO_010, CELULAR_010, CORREOE_010, CORREOI_010, DIRECCION_010, ZONA_010, MEDIO_010, EMERGENCIA_010, ";
		$msConsulta .= "TEL_EMERGENCIA_010, CEL_EMERGENCIA_010, OTROIDIOMA_010, IDIOMA_010, DOMINIOIDIOMA_010, NIVELESTUDIO_010, CONDICIONLABORAL_010, OCUPACION_010, SALARIOESTUDIANTE_010, ";
		$msConsulta .= "DISCAPACIDAD_010, NOMBREMADRE_010, NOMBREPADRE_010, TELEFONOMADRE_010, TELEFONOPADRE_010, CELULARMADRE_010, CELULARPADRE_010, TRABAJAMADRE_010, TRABAJAPADRE_010, ";
		$msConsulta .= "TRABAJOMADRE_010, TRABAJOPADRE_010, SALARIOMADRE_010, SALARIOPADRE_010, MENORES_010, MAYORES_010, DEPENDIENTES_010) ";
		$msConsulta .= "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msColegio, $msCarrera, $msMunicipio, $msUsuario, $msCodEstudiantil, $msFecha, $mnGeneracion, $msCarnet, $msNombre1, $msNombre2, 
		$msApellido1, $msApellido2, $mbNacional, $msFechaNac, $msLugarNac, $msPais, $msNacionalidad, $msEtnia, $mnPeso, $mnTalla, $mnTipoSangre, $msCedula,
		$msPasaporte, $msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $mbDireccion, $msZona, $mbMedio, $msEmergencia,
		$msTelEmergencia, $msCelEmergencia,	$mbOtroIdioma, $msIdioma, $msDominioIdioma, $mnNivelEstudio, $mbCondicionLaboral, $msOcupacion, $mnSalarioEstudiante,
		$mbDiscapacidad, $msNombreMadre, $msNombrePadre, $msTelefonoPadre, $msTelefonoMadre, $msCelularMadre, $msCelularPadre, $mbTrabajaMadre, $mbTrabajaPadre,
		$msTrabajoMadre, $msTrabajoPadre, $mnSalarioMadre, $mnSalarioPadre, $mnMenores, $mnMayores, $mnDependientes]);
		return ($msCodigo);
	}
	
	function fxModificarEstudiantes($msCodigo, $msColegio, $msCarrera, $msMunicipio, $msUsuario, $msCodEstudiantil, $msFecha, $mnGeneracion, $msCarnet, $msNombre1, $msNombre2, 
	$msApellido1, $msApellido2, $mbNacional, $msFechaNac, $msLugarNac, $msPais, $msNacionalidad, $msEtnia, $mnPeso, $mnTalla, $mnTipoSangre, $msCedula,
	$msPasaporte, $msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $mbDireccion, $msZona, $mbMedio, $msEmergencia,
	$msTelEmergencia, $msCelEmergencia,	$mbOtroIdioma, $msIdioma, $msDominioIdioma, $mnNivelEstudio, $mbCondicionLaboral, $msOcupacion, $mnSalarioEstudiante,
	$mbDiscapacidad, $msNombreMadre, $msNombrePadre, $msTelefonoPadre, $msTelefonoMadre, $msCelularMadre, $msCelularPadre, $mbTrabajaMadre, $mbTrabajaPadre,
	$msTrabajoMadre, $msTrabajoPadre, $mnSalarioMadre, $mnSalarioPadre, $mnMenores, $mnMayores, $mnDependientes)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "update UMO010A set COLEGIO_REL = ?, CARRERA_REL = ?, MUNICIPIO_REL = ?, USUARIO_REL = ?, CODESTUDIANTIL_010 = ?, FECHA_010 = ?, GENERACION_010 = ?, ";
		$msConsulta .= "CARNET_010 = ?, NOMBRE1_010 = ?, NOMBRE2_010 = ?, APELLIDO1_010 = ?, APELLIDO2_010 = ?, NACIONAL_010 = ?, FECHANAC_010 = ?, ";
		$msConsulta .= "LUGARNAC_010 = ?, PAIS_010 = ?, NACIONALIDAD_010 = ?, ETNIA_010 = ?, PESO_010 = ?, TALLA_010 = ?, TIPOSANGRE_010 = ?, CEDULA_010 = ?, PASAPORTE_010 = ?, SEXO_010 = ?, ";
		$msConsulta .= "ESTADOCIVIL_010 = ?, HIJOS_010 = ?, TELEFONO_010 = ?, CELULAR_010 = ?, CORREOE_010 = ?, CORREOI_010 = ?, DIRECCION_010 = ?, ZONA_010 = ?, ";
		$msConsulta .= "MEDIO_010 = ?, EMERGENCIA_010 = ?, TEL_EMERGENCIA_010 = ?, CEL_EMERGENCIA_010 = ?, OTROIDIOMA_010 = ?, IDIOMA_010 = ?, DOMINIOIDIOMA_010 = ?, ";
		$msConsulta .= "NIVELESTUDIO_010 = ?, CONDICIONLABORAL_010 = ?, OCUPACION_010 = ?, SALARIOESTUDIANTE_010 = ?, DISCAPACIDAD_010 = ?, ";
		$msConsulta .= "NOMBREMADRE_010 = ?, NOMBREPADRE_010 = ?, TELEFONOMADRE_010 = ?, TELEFONOPADRE_010 = ?, CELULARMADRE_010 = ?, CELULARPADRE_010 = ?, ";
		$msConsulta .= "TRABAJAMADRE_010 = ?, TRABAJAPADRE_010 = ?, TRABAJOMADRE_010 = ?, TRABAJOPADRE_010 = ?, SALARIOMADRE_010 = ?, SALARIOPADRE_010 = ?, ";
		$msConsulta .= "MENORES_010 = ?, MAYORES_010 = ?, DEPENDIENTES_010 = ? where ESTUDIANTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msColegio, $msCarrera, $msMunicipio, $msUsuario, $msCodEstudiantil, $msFecha, $mnGeneracion, $msCarnet, $msNombre1, $msNombre2, 
		$msApellido1, $msApellido2, $mbNacional, $msFechaNac, $msLugarNac, $msPais, $msNacionalidad, $msEtnia, $mnPeso, $mnTalla, $mnTipoSangre, $msCedula,
		$msPasaporte, $msSexo, $mnEstadoCivil, $mnHijos, $msTelefono, $msCelular, $msCorreoE, $msCorreoI, $mbDireccion, $msZona, $mbMedio, $msEmergencia,
		$msTelEmergencia, $msCelEmergencia,	$mbOtroIdioma, $msIdioma, $msDominioIdioma, $mnNivelEstudio, $mbCondicionLaboral, $msOcupacion, $mnSalarioEstudiante,
		$mbDiscapacidad, $msNombreMadre, $msNombrePadre, $msTelefonoPadre, $msTelefonoMadre, $msCelularMadre, $msCelularPadre, $mbTrabajaMadre, $mbTrabajaPadre,
		$msTrabajoMadre, $msTrabajoPadre, $mnSalarioMadre, $mnSalarioPadre, $mnMenores, $mnMayores, $mnDependientes, $msCodigo]);
	}
	
	function fxDevuelveEstudiantes($mbLlenaGrid, $msCodigo = "")
	{
		$m_cnx_MySQL = fxAbrirConexion();
		
		if ($mbLlenaGrid == 1)
		{
			$msConsulta = "select ESTUDIANTE_REL, FECHA_010, NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, CELULAR_010, CORREOI_010 ";
			$msConsulta .= "from UMO010A order by ESTUDIANTE_REL desc";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute();
		}
		else
		{
			$msConsulta = "select ESTUDIANTE_REL, COLEGIO_REL, CARRERA_REL, MUNICIPIO_REL, USUARIO_REL, CODESTUDIANTIL_010, FECHA_010, GENERACION_010, CARNET_010, ";
			$msConsulta .= "NOMBRE1_010, NOMBRE2_010, APELLIDO1_010, APELLIDO2_010, NACIONAL_010, FECHANAC_010, LUGARNAC_010, PAIS_010, NACIONALIDAD_010, ETNIA_010, ";
			$msConsulta .= "PESO_010, TALLA_010, TIPOSANGRE_010, CEDULA_010, PASAPORTE_010, SEXO_010, ESTADOCIVIL_010, HIJOS_010, TELEFONO_010, CELULAR_010, CORREOE_010, CORREOI_010, DIRECCION_010, ZONA_010, ";
			$msConsulta .= "MEDIO_010, EMERGENCIA_010, TEL_EMERGENCIA_010, CEL_EMERGENCIA_010, OTROIDIOMA_010, IDIOMA_010, DOMINIOIDIOMA_010, NIVELESTUDIO_010, ";
			$msConsulta .= "CONDICIONLABORAL_010, OCUPACION_010, SALARIOESTUDIANTE_010, DISCAPACIDAD_010, NOMBREMADRE_010, NOMBREPADRE_010, TELEFONOMADRE_010, TELEFONOPADRE_010, ";
			$msConsulta .= "CELULARMADRE_010, CELULARPADRE_010, TRABAJAMADRE_010, TRABAJAPADRE_010, TRABAJOMADRE_010, TRABAJOPADRE_010, SALARIOMADRE_010, SALARIOPADRE_010, MENORES_010, MAYORES_010, DEPENDIENTES_010 ";
			$msConsulta .= "from UMO010A where ESTUDIANTE_REL = ?";
			$mDatos = $m_cnx_MySQL->prepare($msConsulta);
			$mDatos->execute([$msCodigo]);
		}
		
		return $mDatos;
	}

	/*****Detalle Documento (UMO011A)***********/

	function fxGuardarDetDocumento($msCodigo, $msArchivo, $mnTipoDoc, $msDescripcion, $msRuta)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "insert into UMO011A (ESTUDIANTE_REL, ARCHIVO_REL, TIPO_011, DESC_011, RUTA_011) values (?, ?, ?, ?, ?)";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msArchivo, $mnTipoDoc, $msDescripcion, $msRuta]);
	}
	
	function fxBorrarDetDocumento($msCodigo, $msImagen)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "delete from UMO011A where ESTUDIANTE_REL = ? and ARCHIVO_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo, $msImagen]);
	}
	
	function fxDevuelveDetDocumento($msCodigo)
	{
		$m_cnx_MySQL = fxAbrirConexion();
		$msConsulta = "select ESTUDIANTE_REL, ARCHIVO_REL, TIPO_011, DESC_011, RUTA_011 from UMO011A where ESTUDIANTE_REL = ?";
		$mDatos = $m_cnx_MySQL->prepare($msConsulta);
		$mDatos->execute([$msCodigo]);
		return $mDatos;
	}
?>