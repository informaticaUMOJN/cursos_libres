<?php
    function fxObtenerEstudiantesActivos($msCurso)
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = "select MATCURSO_REL, concat_ws(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE from UMO210A join UMO200A on UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL where CURSOS_REL = ? and ESTADO_210 = 1";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCurso]);
        return $mDatos;
    }

    function fxObtenerEstudiantesCertificados($msCurso)
    {
        $m_cnx_MySQL = fxAbrirConexion();
        $msConsulta = "select UMO371A.MATCURSO_REL, PARTIDA_REL, concat_ws(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE, TOMO_371, FOLIO_371 from UMO210A, UMO200A, UMO371A ";
        $msConsulta .= "where UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL and UMO210A.MATCURSO_REL = UMO371A.MATCURSO_REL ";
        $msConsulta .= "and CURSOS_REL = ? and ESTADO_210 = 3";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCurso]);
        return $mDatos;
    }

    function fxCertificar($msCurso, $msLibro)
    {
        $m_cnx_MySQL = fxAbrirConexion();

        /*Cabecera del Registro*/
        $msConsulta = "select REGISTRO_REL from UMO370A where CURSOS_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCurso]);
        $mnRegistro = $mDatos->rowCount();
        if ($mnRegistro == 0)
        {
            $msConsulta = "select ifnull(MID(MAX(REGISTRO_REL), 3), 0) as Ultimo from UMO370A";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute();
            $mFila = $mAux->fetch();
            $mnNumero = intval($mFila["Ultimo"]) + 1;
            $msCodigo = "RG" . str_pad($mnNumero, 8, "0", STR_PAD_LEFT);

            $mdFechaHoy = date('Y-m-d');
            $msConsulta = "insert into UMO370A values (?, ?, ?, ?)";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msCodigo, $msLibro, $msCurso, $mdFechaHoy]);
        }
        else
        {
            $mFila = $mDatos->fetch();
            $msCodigo = $mFila["REGISTRO_REL"];
        }

        /*Detalle del Registro*/

        //Obtiene el número de Folio
        $msConsulta = "select max(FOLIO_371) as FOLIO_371 from UMO371A, UMO370A ";
        $msConsulta .= "where UMO371A.REGISTRO_REL = UMO370A.REGISTRO_REL and LIBRO_REL = ?";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msLibro]);
        $mFila = $mDatos->fetch();
        $mnFolio = intval($mFila["FOLIO_371"]);
        if ($mnFolio == 0)
            $mnFolio = 1;

        $msConsulta = "select MATCURSO_REL from UMO210A where CURSOS_REL = ? and ESTADO_210 = 1";
        $mDatos = $m_cnx_MySQL->prepare($msConsulta);
        $mDatos->execute([$msCurso]);

        while($mFila = $mDatos->fetch())
        {
            $msMatricula = $mFila["MATCURSO_REL"];

            //Verifica si ya existen dos partidas en el folio
            $msConsulta = "select count(UMO371A.REGISTRO_REL) as CONTEO from UMO371A, UMO370A ";
            $msConsulta .= "where UMO371A.REGISTRO_REL = UMO370A.REGISTRO_REL and LIBRO_REL = ? and FOLIO_371 = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msLibro, $mnFolio]);
            $mFilaAux = $mAux->fetch();
            $mnConteo = intval($mFilaAux["CONTEO"]);
            if ($mnConteo == 2)
                $mnFolio++;

            //Incrementa la partida
            $msConsulta = "select TOMO_330, ULTIMAPARTIDA_330 from UMO330A where LIBRO_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msLibro]);
            $mFilaAux = $mAux->fetch();
            $msTomo = $mFilaAux["TOMO_330"]; //NUMERO DE TOMO//
            $mnPartida = intval($mFilaAux["ULTIMAPARTIDA_330"]);
            $mnPartida++;
            $msConsulta = "update UMO330A set ULTIMAPARTIDA_330 = ? where LIBRO_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$mnPartida, $msLibro]);

            //Ingreso del registro
            $msConsulta = "insert into UMO371A values (?, ?, ?, ?, ?)";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msCodigo, $mnPartida, $msMatricula, $msTomo, $mnFolio]);

            //Actualización del estado de la matrícula
            $msConsulta = "update UMO210A set ESTADO_210 = 3 where MATCURSO_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msMatricula]);

            //Generar el diploma
            $msConsulta = "select CONCAT_WS(' ', NOMBRES_200, APELLIDOS_200) as ESTUDIANTE, NOMBRE_190 ";
            $msConsulta .= "from UMO210A, UMO200A, UMO190A ";
            $msConsulta .= "where UMO210A.ALUMNO_REL = UMO200A.ALUMNO_REL and UMO210A.CURSOS_REL = UMO190A.CURSOS_REL ";
            $msConsulta .= "and MATCURSO_REL = ?";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute([$msMatricula]);
            $mFilaAux = $mAux->fetch();
            $msNombreEstudiante = $mFilaAux["ESTUDIANTE"];
            $msNombreCurso = $mFilaAux["NOMBRE_190"];

            $msConsulta = "Select ifnull(mid(max(DIPLOMA_REL), 3), 0) as Ultimo from UMO003B";
            $mAux = $m_cnx_MySQL->prepare($msConsulta);
            $mAux->execute();
            $mFilaAux = $mAux->fetch();
            $mnNumero = intval($mFilaAux["Ultimo"]);
            $mnNumero += 1;
            $mnLongitud = strlen($mnNumero);
            $msDiploma = "DP" . str_repeat("0", 8 - $mnLongitud) . trim($mnNumero);
            $mdFechaHoy = date('Y-m-d H:i:s');
            $msConsulta = "insert into UMO003B values (?,?,?,?,?,?)";
            $mResultado = $m_cnx_MySQL->prepare($msConsulta);
            $mResultado->execute([$msDiploma, $mdFechaHoy, $msNombreCurso, $msNombreEstudiante, "", ""]);
        }
    }
?>