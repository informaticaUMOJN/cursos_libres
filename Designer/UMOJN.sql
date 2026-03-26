/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     14/01/2026 8:57:51 a. m.                     */
/*==============================================================*/


/*==============================================================*/
/* Table: UMO000A                                               */
/*==============================================================*/
create table UMO000A
(
   USUARIO_000          varchar(20)  comment '',
   FECHA_000            datetime  comment '',
   TABLA_000            varchar(30)  comment '',
   LLAVE1_000           varchar(20)  comment '',
   LLAVE2_000           varchar(20)  comment '',
   OPERACION_000        varchar(20)  comment '',
   REGISTRO_000         varchar(500)  comment 'Guarda una cadena con los datos actuales del registro afectado'
);

/*==============================================================*/
/* Table: UMO000C                                               */
/*==============================================================*/
create table UMO000C
(
   USUARIO_000          varchar(20)  comment '',
   FECHA_000            datetime  comment '',
   ENTIDAD_000          varchar(30)  comment '',
   CLAVE_000            varchar(20)  comment '',
   ACCION_000           varchar(20)  comment ''
);

/*==============================================================*/
/* Table: UMO001B                                               */
/*==============================================================*/
create table UMO001B
(
   EXPDIGITAL_REL       varchar(10) not null  comment '',
   FECHA_001            date  comment '',
   CARRERA_001          varchar(100)  comment '',
   primary key (EXPDIGITAL_REL)
);

/*==============================================================*/
/* Table: UMO002A                                               */
/*==============================================================*/
create table UMO002A
(
   USUARIO_REL          varchar(20) not null  comment '',
   NOMBRE_002           varchar(100)  comment '',
   CLAVE_002            varchar(100)  comment '',
   CORREO_002           varchar(100)  comment '',
   SUPERVISOR_002       bool  comment '',
   ARCHIVOS_002         bool  comment '',
   ADMINISTRADOR_002    bool  comment '',
   ESTUDIANTE_002       bool  comment '',
   ACTIVO_002           bool  comment '',
   primary key (USUARIO_REL)
);

/*==============================================================*/
/* Table: UMO002B                                               */
/*==============================================================*/
create table UMO002B
(
   EXPDIGITAL_REL       varchar(10) not null  comment '',
   CARNET_REL           varchar(20) not null  comment '',
   NOMBRE_002           varchar(100)  comment '',
   REGISTRO_002         varchar(10)  comment '',
   TOMO_002             varchar(10)  comment '',
   FOLIO_002            varchar(10)  comment '',
   VERIFICACION_002     varchar(50)  comment '',
   primary key (EXPDIGITAL_REL, CARNET_REL)
);

alter table UMO002B comment 'Esta es una tabla para guardar la información de los diploma';

/*==============================================================*/
/* Table: UMO003A                                               */
/*==============================================================*/
create table UMO003A
(
   GRUPO_REL            varchar(15) not null  comment '',
   NOMBRE_003           varchar(100)  comment '',
   primary key (GRUPO_REL)
);

/*==============================================================*/
/* Table: UMO003B                                               */
/*==============================================================*/
create table UMO003B
(
   DIPLOMA_REL          varchar(10) not null  comment '',
   FECHA_003            date  comment '',
   ESTUDIO_003          varchar(200)  comment '',
   NOMBRE_003           varchar(100)  comment '',
   VERIFICACION_003     varchar(50)  comment '',
   RUTA_003             varchar(255)  comment '',
   primary key (DIPLOMA_REL)
);

/*==============================================================*/
/* Table: UMO004A                                               */
/*==============================================================*/
create table UMO004A
(
   PAGINA_REL           varchar(20) not null  comment '',
   DESC_004             char(50)  comment '',
   primary key (PAGINA_REL)
);

/*==============================================================*/
/* Table: UMO005A                                               */
/*==============================================================*/
create table UMO005A
(
   PAGINA_REL           varchar(20) not null  comment '',
   GRUPO_REL            varchar(15) not null  comment '',
   INCLUIR_005          bool  comment '',
   MODIFICAR_005        bool  comment '',
   BORRAR_005           bool  comment '',
   ANULAR_005           bool  comment '',
   primary key (PAGINA_REL, GRUPO_REL)
);

/*==============================================================*/
/* Table: UMO006A                                               */
/*==============================================================*/
create table UMO006A
(
   GRUPO_REL            varchar(15) not null  comment '',
   USUARIO_REL          varchar(20) not null  comment '',
   primary key (GRUPO_REL, USUARIO_REL)
);

/*==============================================================*/
/* Table: UMO010A                                               */
/*==============================================================*/
create table UMO010A
(
   ESTUDIANTE_REL       varchar(10) not null  comment '',
   COLEGIO_REL          varchar(10)  comment '',
   USUARIO_REL          varchar(20)  comment '',
   CARRERA_REL          varchar(10)  comment '',
   MUNICIPIO_REL        varchar(10)  comment '',
   CODESTUDIANTIL_010   varchar(20)  comment '',
   FECHA_010            date  comment '',
   GENERACION_010       numeric(2,0)  comment '',
   CARNET_010           varchar(20)  comment 'Establece el orden en que fueron registrados los estudiantes de la Generación',
   NOMBRE1_010          varchar(50)  comment '',
   NOMBRE2_010          varchar(50)  comment '',
   APELLIDO1_010        varchar(50)  comment '',
   APELLIDO2_010        varchar(50)  comment '',
   NACIONAL_010         bool  comment '0.- Falso
             1.- Verdadero',
   FECHANAC_010         date  comment '',
   LUGARNAC_010         varchar(50)  comment '',
   PAIS_010             varchar(50)  comment '',
   NACIONALIDAD_010     varchar(100)  comment '',
   ETNIA_010            varchar(100)  comment '',
   PESO_010             numeric(3,0)  comment '',
   TALLA_010            numeric(3,0)  comment '',
   TIPOSANGRE_010       varchar(3)  comment '',
   CEDULA_010           varchar(20)  comment '',
   PASAPORTE_010        varchar(50)  comment 'Sólo cuando NACIONAL_010 es falso',
   SEXO_010             char(1)  comment '',
   ESTADOCIVIL_010      numeric(1,0)  comment '0.-Soltero
             1.-Casado
             2.-Divorciado
             3.-Viudo',
   HIJOS_010            numeric(2,0)  comment '',
   TELEFONO_010         varchar(20)  comment '',
   CELULAR_010          varchar(20)  comment '',
   CORREOE_010          varchar(100)  comment '',
   CORREOI_010          varchar(100)  comment '',
   DIRECCION_010        varchar(300)  comment '',
   ZONA_010             varchar(20)  comment '',
   MEDIO_010            numeric(2,0)  comment '1.-Visita al colegio
             2.-Facebook
             3.-Instagram
             4.-Clinica ODM
             5.-Radio
             6.-Estudiante UMOJN
             7.-Publicidad en la calle
             8.-Amigo o familiar
             9.-Feria de salud
             10.-Tik Tok
             11.-Clinica PAMIC
             12.-Sitio web
             13.-YouTube
             14.-Otros',
   EMERGENCIA_010       varchar(100)  comment '',
   TEL_EMERGENCIA_010   varchar(20)  comment '',
   CEL_EMERGENCIA_010   varchar(20)  comment '',
   OTROIDIOMA_010       bool  comment '',
   IDIOMA_010           varchar(200)  comment '',
   DOMINIOIDIOMA_010    varchar(200)  comment '',
   NIVELESTUDIO_010     numeric(1,0)  comment '0.-Bachiller
             1.-Técnico
             2.-Licenciado
             3.-Ingeniero
             4.-Doctor',
   CONDICIONLABORAL_010 bool  comment '0.-Desempleado
             1.-Empleado',
   OCUPACION_010        varchar(200)  comment '',
   SALARIOESTUDIANTE_010 decimal(10,2)  comment '',
   DISCAPACIDAD_010     bool  comment '',
   NOMBREMADRE_010      varchar(100)  comment '',
   NOMBREPADRE_010      varchar(100)  comment '',
   TELEFONOMADRE_010    varchar(20)  comment '',
   TELEFONOPADRE_010    varchar(20)  comment '',
   CELULARMADRE_010     varchar(20)  comment '',
   CELULARPADRE_010     varchar(20)  comment '',
   TRABAJAMADRE_010     bool  comment '',
   TRABAJAPADRE_010     bool  comment '',
   TRABAJOMADRE_010     varchar(100)  comment '',
   TRABAJOPADRE_010     varchar(100)  comment '',
   SALARIOMADRE_010     decimal(10,2)  comment '',
   SALARIOPADRE_010     decimal(10,2)  comment '',
   MENORES_010          numeric(2,0)  comment '',
   MAYORES_010          numeric(2,0)  comment '',
   DEPENDIENTES_010     numeric(2,0)  comment '',
   primary key (ESTUDIANTE_REL)
);

/*==============================================================*/
/* Table: UMO010C                                               */
/*==============================================================*/
create table UMO010C
(
   AREA_REL             varchar(10) not null  comment '',
   UMO_AREA_REL         varchar(10)  comment '',
   DESC_010             varchar(50)  comment '',
   HEREDAR_010          bool  comment '',
   primary key (AREA_REL)
);

alter table UMO010C comment 'Estas áreas son usadas en la aplicación de manejo de archivo';

/*==============================================================*/
/* Table: UMO011A                                               */
/*==============================================================*/
create table UMO011A
(
   ESTUDIANTE_REL       varchar(10) not null  comment '',
   ARCHIVO_REL          varchar(50) not null  comment '',
   TIPO_201             numeric(2,0)  comment '0.- Diploma de bachiller
             1.- Calificaciones de secundaria
             2.- Cédula de identidad
             3.- Acta de nacimiento
             4.- Foto
             5.- Cédula de residencia
             6.- Pasaporte
             7.- Plan de estudio
             8.- Acta de aprobación monográfica
             9.- Calificaciones universitarias
             10.- Certificación del título universitario
             11.- Datos generales del título
             12.- Publicación en la gaceta
             13.- Título universitario',
   DESC_201             varchar(100)  comment '',
   RUTA_201             varchar(255)  comment '',
   primary key (ESTUDIANTE_REL, ARCHIVO_REL)
);

/*==============================================================*/
/* Table: UMO011C                                               */
/*==============================================================*/
create table UMO011C
(
   USUARIO_REL          varchar(20) not null  comment '',
   AREA_REL             varchar(10) not null  comment '',
   primary key (USUARIO_REL, AREA_REL)
);

/*==============================================================*/
/* Table: UMO012C                                               */
/*==============================================================*/
create table UMO012C
(
   DOCUMENTO_REL        varchar(10) not null  comment '',
   AREA_REL             varchar(10) not null  comment '',
   FECHA_012            date  comment '',
   ARCHIVO_012          varchar(100)  comment '',
   NOMBRE_012           varchar(200)  comment '',
   PROPIETARIO_012      varchar(20)  comment '',
   RUTA_012             varchar(255)  comment '',
   primary key (AREA_REL, DOCUMENTO_REL)
);

/*==============================================================*/
/* Table: UMO020A                                               */
/*==============================================================*/
create table UMO020A
(
   COLEGIO_REL          varchar(10) not null  comment '',
   MUNICIPIO_REL        varchar(10)  comment '',
   NOMBRE_020           varchar(100)  comment '',
   TIPO_020             numeric(1,0)  comment '0.-Privado
             1.-Público
             2.-Subvencionado
             3.-Otro',
   primary key (COLEGIO_REL)
);

/*==============================================================*/
/* Table: UMO030A                                               */
/*==============================================================*/
create table UMO030A
(
   MATRICULA_REL        varchar(10) not null  comment '',
   CARRERA_REL          varchar(10)  comment '',
   PLANESTUDIO_REL      varchar(10)  comment '',
   ESTUDIANTE_REL       varchar(10)  comment '',
   FECHA_030            date  comment '',
   TIPOINGRESO_030      numeric(1,0)  comment '0.-Primer ingreso
             1.-Reingreso
             2.-Segunda carrera
             3.-Traslado interno
             4.-Traslado externo',
   ANNOINGRESO_030      numeric(4,0)  comment '',
   ANNOACADEMICO_030    numeric(4,0)  comment '',
   ANNOLECTIVO_030      numeric(4,0)  comment '',
   SEMESTREACADEMICO_030 numeric(1,0)  comment '',
   RECIBO_030           varchar(10)  comment '',
   BECA_030             numeric(1,0)  comment '0.-sin beca
             1.-beca 50%
             2.-beca 25%
             3.-beca 16%',
   DIPLOMA_030          bool  comment 'Establece si ya fue entregado el Diploma de bachiller',
   NOTAS_030            bool  comment 'Establece si ya fue entregado el Certificado de notas de secundaria',
   CEDULA_030           bool  comment 'Establece si ya fue entregada la Cédula',
   ACTANACIMIENTO_030   bool  comment 'Establece si ya fue entregada el Acta de nacimiento',
   ESTADO_030           numeric(1,0)  comment '0.- Activa
             1.- Inactiva
             2.- Pre-matriculado',
   primary key (MATRICULA_REL)
);

/*==============================================================*/
/* Table: UMO031A                                               */
/*==============================================================*/
create table UMO031A
(
   MATRICULA_REL        varchar(10) not null  comment '',
   ASIGNATURA_REL       varchar(10) not null  comment '',
   primary key (MATRICULA_REL, ASIGNATURA_REL)
);

/*==============================================================*/
/* Table: UMO040A                                               */
/*==============================================================*/
create table UMO040A
(
   CARRERA_REL          varchar(10) not null  comment '',
   NOMBRE_040           varchar(50)  comment '',
   SIGLAS_040           varchar(5)  comment '',
   POSGRADO_040         bool  comment '',
   primary key (CARRERA_REL)
);

/*==============================================================*/
/* Table: UMO050A                                               */
/*==============================================================*/
create table UMO050A
(
   PLANESTUDIO_REL      varchar(10) not null  comment '',
   CARRERA_REL          varchar(10)  comment '',
   PERIODO_050          varchar(30)  comment 'Tiempo de vigencia del plan',
   GRADO_050            varchar(50)  comment '',
   HORAS_050            numeric(6,0)  comment '',
   CREDITOS_050         numeric(4,0)  comment '',
   TURNO_050            numeric(1,0)  comment '1.-Diurno
             2.-Matutino
             3.-Vespertino
             4.-Nocturno
             5.-Sabatino
             6.-Dominical',
   REGIMEN_050          numeric(1,0)  comment '1.-Mensual
             2.-Bimestral
             3.-Trimestral
             4.-Cuatrimestral
             5.-Semestral
             6.-Intensivo',
   MODALIDAD_050        numeric(1,0)  comment '1.-Presencial
             2.-Por encuentro
             3.-Virtual
             4.-Mixta',
   ACTIVO_050           bool  comment '',
   primary key (PLANESTUDIO_REL)
);

/*==============================================================*/
/* Table: UMO051A                                               */
/*==============================================================*/
create table UMO051A
(
   PLANESTUDIO_REL      varchar(10) not null  comment '',
   CONSECUTIVO_REL      varchar(3) not null  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   UMO_ASIGNATURA_REL   varchar(10)  comment '',
   SEMESTRE_051         numeric(2,0)  comment '',
   HPRESENCIALES_051    numeric(5,0)  comment '',
   HAUTOESTUDIO_051     numeric(5,0)  comment '',
   HTRABAJO_051         numeric(5,0)  comment '',
   HTOTALES_051         numeric(5,0)  comment '',
   CREDITOS_051         numeric(5,0)  comment '',
   primary key (PLANESTUDIO_REL, CONSECUTIVO_REL)
);

/*==============================================================*/
/* Table: UMO060A                                               */
/*==============================================================*/
create table UMO060A
(
   ASIGNATURA_REL       varchar(10) not null  comment '',
   CARRERA_REL          varchar(10)  comment '',
   CODIGO_060           varchar(15)  comment '',
   NOMBRE_060           varchar(100)  comment '',
   DESCGRAL_060         varchar(300)  comment '',
   PARCIALES_060        numeric(1,0)  comment 'Cantidad de parciales en la asignatura',
   primary key (ASIGNATURA_REL)
);

/*==============================================================*/
/* Table: UMO070A                                               */
/*==============================================================*/
create table UMO070A
(
   SYLLABUS_REL         varchar(10) not null  comment '',
   PLANESTUDIO_REL      varchar(10)  comment '',
   DOCENTE_REL          varchar(10)  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   FECHA_070            date  comment '',
   APROBACION_070       date  comment '',
   ANNO_070             numeric(4,0)  comment '',
   SEMESTRE_070         numeric(1,0)  comment '',
   TURNO_070            numeric(1,0)  comment '1.-Diurno
             2.-Matutino
             3.-Verpertino
             4.-Nocturno
             5.-Sabatino
             6.-Dominical',
   GRUPO_070            varchar(30)  comment '',
   RECOMENDACIONES_070  varchar(300)  comment 'El campo se llamaba originalmente "Recomendaciones metodológicas"',
   EJESVALORES_070      varchar(300)  comment '',
   APROBADO_070         bool  comment '',
   ACTIVO_070           bool  comment '',
   primary key (SYLLABUS_REL)
);

/*==============================================================*/
/* Table: UMO071A                                               */
/*==============================================================*/
create table UMO071A
(
   SYLLABUS_REL         varchar(10) not null  comment '',
   OBJETIVOG_REL        numeric(2,0) not null  comment '',
   TEXTO_071            varchar(400)  comment '',
   primary key (SYLLABUS_REL, OBJETIVOG_REL)
);

/*==============================================================*/
/* Table: UMO072A                                               */
/*==============================================================*/
create table UMO072A
(
   SYLLABUS_REL         varchar(10) not null  comment '',
   OBJETIVOU_REL        numeric(2,0) not null  comment '',
   UNIDAD_072           varchar(100)  comment '',
   TEXTO_072            varchar(400)  comment '',
   primary key (SYLLABUS_REL, OBJETIVOU_REL)
);

/*==============================================================*/
/* Table: UMO073A                                               */
/*==============================================================*/
create table UMO073A
(
   SYLLABUS_REL         varchar(10) not null  comment '',
   DETSYLLABUS_REL      numeric(2,0) not null  comment '',
   FECHA_073            date  comment '',
   UNIDAD_073           varchar(200)  comment '',
   CONTENIDO_073        varchar(200)  comment '',
   OBJETIVOESP_073      varchar(200)  comment '',
   FORMA_073            varchar(900)  comment 'El campo se llamaba originalmente "Forma de enseñanza"',
   MEDIOS_073           varchar(100)  comment 'El campo se llamaba originalmente "Medios o recursos"',
   EVALUACION_073       varchar(200)  comment '',
   primary key (SYLLABUS_REL, DETSYLLABUS_REL)
);

alter table UMO073A comment 'Este campo también funge como Semana de clase';

/*==============================================================*/
/* Table: UMO074A                                               */
/*==============================================================*/
create table UMO074A
(
   SYLLABUS_REL         varchar(10) not null  comment '',
   OBSDOCENTE_REL       numeric(2,0) not null  comment '',
   TEXTO_074            varchar(300)  comment '',
   primary key (SYLLABUS_REL, OBSDOCENTE_REL)
);

/*==============================================================*/
/* Table: UMO075A                                               */
/*==============================================================*/
create table UMO075A
(
   SYLLABUS_REL         varchar(10) not null  comment '',
   OBSACADEMICA_REL     numeric(2,0) not null  comment '',
   TEXTO_075            varchar(300)  comment '',
   primary key (SYLLABUS_REL, OBSACADEMICA_REL)
);

/*==============================================================*/
/* Table: UMO080A                                               */
/*==============================================================*/
create table UMO080A
(
   DIACLASE_REL         varchar(10) not null  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   ANNOLECTIVO_080      numeric(4,0)  comment '',
   DIASEMANA_080        numeric(1,0)  comment '',
   FECHAINI_080         date  comment '',
   FECHAFIN_080         date  comment '',
   primary key (DIACLASE_REL)
);

/*==============================================================*/
/* Table: UMO081A                                               */
/*==============================================================*/
create table UMO081A
(
   DIACLASE_REL         varchar(10) not null  comment '',
   SEMANA_REL           numeric(2,0) not null  comment '',
   FECHA_081            date  comment '',
   HABIL_081            bool  comment '',
   primary key (DIACLASE_REL, SEMANA_REL)
);

/*==============================================================*/
/* Table: UMO090A                                               */
/*==============================================================*/
create table UMO090A
(
   DIAFERIADO_REL       varchar(10) not null  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   FECHA_090            date  comment '',
   MOTIVO_090           varchar(100)  comment '',
   primary key (DIAFERIADO_REL)
);

/*==============================================================*/
/* Table: UMO100A                                               */
/*==============================================================*/
create table UMO100A
(
   DOCENTE_REL          varchar(10) not null  comment '',
   USUARIO_REL          varchar(20)  comment '',
   NOMBRE_100           varchar(100)  comment '',
   TIPO_100             varchar(20)  comment '0.- De planta
             1.- Medio tiempo
             2.- Horario',
   ACTIVO_100           bool  comment '',
   primary key (DOCENTE_REL)
);

/*==============================================================*/
/* Table: UMO110A                                               */
/*==============================================================*/
create table UMO110A
(
   DEPARTAMENTO_REL     varchar(10) not null  comment '',
   NOMBRE_110           varchar(100)  comment '',
   primary key (DEPARTAMENTO_REL)
);

/*==============================================================*/
/* Table: UMO120A                                               */
/*==============================================================*/
create table UMO120A
(
   MUNICIPIO_REL        varchar(10) not null  comment '',
   DEPARTAMENTO_REL     varchar(10)  comment '',
   NOMBRE_120           varchar(100)  comment '',
   primary key (MUNICIPIO_REL)
);

/*==============================================================*/
/* Table: UMO130A                                               */
/*==============================================================*/
create table UMO130A
(
   COBRO_REL            varchar(10) not null  comment '',
   UMO_COBRO_REL        varchar(10)  comment '',
   CURSOS_REL           varchar(20)  comment '',
   MATRICULAPOS_REL     varchar(10)  comment '',
   CARRERA_REL          varchar(10)  comment '',
   DESC_130             varchar(150)  comment '',
   TIPO_130             numeric(1,0)  comment '0.-Matrícula
             1.-Mensualidad
             2.-Mora
             3.-Servicios académicos (Aplican a todas las carreras)',
   TURNO_130            numeric(1,0)  comment '',
   REGIMEN_130          numeric(1,0)  comment '0.-Semestral
             1.-Trimestral
             2.-Cuatrimestral',
   VALOR_130            float(8,2)  comment '',
   MONEDA_130           numeric(1,0)  comment '0.-Córdobas
             1.-Dólares',
   VENCIMIENTO_130      date  comment '',
   ACTIVO_130           bool  comment '',
   primary key (COBRO_REL)
);

/*==============================================================*/
/* Table: UMO131A                                               */
/*==============================================================*/
create table UMO131A
(
   COBRO_REL            varchar(10) not null  comment '',
   MATRICULA_REL        varchar(10) not null  comment '',
   ADEUDADO_131         float(8,2)  comment '',
   ABONADO_131          float(8,2)  comment '',
   MONEDA_131           numeric(1,0)  comment '0.-Córdobas
             1.-Dólares',
   DESCUENTO_131        float(8,2)  comment '',
   ANULADO_131          bool  comment '',
   primary key (COBRO_REL, MATRICULA_REL)
);

/*==============================================================*/
/* Table: UMO132A                                               */
/*==============================================================*/
create table UMO132A
(
   COBRO_REL            varchar(10) not null  comment '',
   ADEUDADO_132         float(8,2)  comment '',
   ABONADO_132          float(8,2)  comment '',
   MONEDA_132           numeric(1,0)  comment '',
   DESCUENTO_132        float(8,2)  comment '',
   ANULADO_132          bool  comment '',
   primary key (COBRO_REL)
);

/*==============================================================*/
/* Table: UMO140A                                               */
/*==============================================================*/
create table UMO140A
(
   PAGO_REL             varchar(10) not null  comment '',
   RECIBI_140           varchar(255)  comment '',
   RECIBO_140           numeric(10,0)  comment '',
   FECHA_140            date  comment '',
   MONEDA_140           numeric(1,0)  comment '0.-Córdobas
             1.-Dólares',
   CANTIDAD_140         float(8,2)  comment '',
   CONCEPTO_140         varchar(200)  comment '',
   TASACAMBIO_140       float(8,2)  comment '',
   TIPO_140             numeric(1,0)  comment '0.-Efectivo
             1.-Transferencia
             2.-Depósito bancario
             3.-Tarjeta',
   primary key (PAGO_REL)
);

/*==============================================================*/
/* Table: UMO141A                                               */
/*==============================================================*/
create table UMO141A
(
   PAGO_REL             varchar(10) not null  comment '',
   MATRICULA_REL        varchar(10) not null  comment '',
   MATCURSO_REL2        varchar(20) not null  comment '',
   VALOR_141            float(8,2)  comment '',
   DESCUENTO_141        decimal(10,2)  comment '',
   primary key (PAGO_REL, MATRICULA_REL, MATCURSO_REL2)
);

/*==============================================================*/
/* Table: UMO142A                                               */
/*==============================================================*/
create table UMO142A
(
   PAGO_REL             varchar(10) not null  comment '',
   MATCURSO_REL2        varchar(20) not null  comment '',
   VALOR_142            float(8,2)  comment '',
   DESCUENTO_142        decimal(10,2)  comment '',
   primary key (PAGO_REL, MATCURSO_REL2)
);

/*==============================================================*/
/* Table: UMO150A                                               */
/*==============================================================*/
create table UMO150A
(
   ASISTENCIA_REL       varchar(10) not null  comment '',
   DOCENTE_REL          varchar(10)  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   CARRERA_REL          varchar(10)  comment '',
   FECHA_150            date  comment '',
   TURNO_150            numeric(1,0)  comment '1.-Diurno
             2.-Matutino
             3.-Verpertino
             4.-Nocturno
             5.-Sabatino
             6.-Dominical',
   ANNO_150             numeric(1,0)  comment '',
   SEMESTRE_150         numeric(1,0)  comment '',
   primary key (ASISTENCIA_REL)
);

/*==============================================================*/
/* Table: UMO151A                                               */
/*==============================================================*/
create table UMO151A
(
   ASISTENCIA_REL       varchar(10) not null  comment '',
   MATRICULA_REL        varchar(10) not null  comment '',
   ESTADO_151           char(1)  comment '',
   primary key (ASISTENCIA_REL, MATRICULA_REL)
);

/*==============================================================*/
/* Table: UMO160A                                               */
/*==============================================================*/
create table UMO160A
(
   CALIFICACION_REL     varchar(10) not null  comment '',
   DOCENTE_REL          varchar(10)  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   CARRERA_REL          varchar(10)  comment '',
   FECHA_160            date  comment '',
   ANNO_160             numeric(4,0)  comment '',
   SEMESTRE_160         numeric(1,0)  comment '',
   PERIODO_160          numeric(1,0)  comment '0.-Semestre 1
             1.-Semestre 2
             2.-Cuatrimestre 1
             3.-Cuatrimestre 2
             4.-Cuatrimestre 3',
   PARCIAL_160          numeric(1,0)  comment '0.- 1er parcial
             1.- 2do parcial
             2.- 3er. parcial
             3.- Examen extraordinario
             4.- Intersemestral
             5.- Convalidación',
   TURNO_160            numeric(1,0)  comment '1.-Diurno
             2.-Matutino
             3.-Verpertino
             4.-Nocturno
             5.-Sabatino
             6.-Dominical',
   ESTADO_160           numeric(1,0)  comment '',
   primary key (CALIFICACION_REL)
);

/*==============================================================*/
/* Table: UMO161A                                               */
/*==============================================================*/
create table UMO161A
(
   MATRICULA_REL        varchar(10)  comment '',
   CALIFICACION_REL     varchar(10)  comment '',
   NOTA_161             decimal(3,0)  comment 'Para parciales 0, 1, 2 se toma la nota literal
             
             En parciales 3, 4, 5:
             0.- No aplica
             1.- Aprobado
             2.- Reprobado'
);

/*==============================================================*/
/* Table: UMO162A                                               */
/*==============================================================*/
create table UMO162A
(
   USUARIO_162          varchar(20)  comment '',
   ANNO_162             numeric(4,0)  comment '',
   SEMESTRE_162         numeric(1,0)  comment '',
   PARCIAL_162          numeric(1,0)  comment '',
   TURNO_162            numeric(1,0)  comment '1.-Diurno
             2.-Matutino
             3.-Verpertino
             4.-Nocturno
             5.-Sabatino
             6.-Dominical',
   FECHA_162            datetime  comment ''
);

/*==============================================================*/
/* Table: UMO170A                                               */
/*==============================================================*/
create table UMO170A
(
   AVANCE_REL           varchar(10) not null  comment '',
   DOCENTE_REL          varchar(10)  comment '',
   CARRERA_REL          varchar(10)  comment '',
   SYLLABUS_REL         varchar(10)  comment '',
   ASIGNATURA_REL       varchar(10)  comment '',
   FECHA_170            date  comment '',
   ANNO_170             numeric(4,0)  comment '',
   SEMESTRE_170         numeric(1,0)  comment '',
   TURNO_170            numeric(1,0)  comment '0.-Regular
             1.-Sabatino
             2.-Dominical',
   primary key (AVANCE_REL)
);

/*==============================================================*/
/* Table: UMO171A                                               */
/*==============================================================*/
create table UMO171A
(
   AVANCE_REL           varchar(10) not null  comment '',
   DETAVANCE_REL        numeric(2,0) not null  comment '',
   FECHAP_171           date  comment '',
   FECHAE_171           date  comment '',
   UNIDADP_171          varchar(200)  comment '',
   UNIDADE_171          varchar(200)  comment '',
   CONTENIDOP_171       varchar(200)  comment '',
   CONTENIDOE_171       varchar(200)  comment '',
   OBSERVACIONES_171    varchar(500)  comment '',
   primary key (AVANCE_REL, DETAVANCE_REL)
);

/*==============================================================*/
/* Table: UMO180A                                               */
/*==============================================================*/
create table UMO180A
(
   UNIVERSIDAD_REL      varchar(20) not null  comment '',
   NOMBRE_180           varchar(100)  comment '',
   TIPO_180             numeric(1,0)  comment '',
   primary key (UNIVERSIDAD_REL)
);

/*==============================================================*/
/* Table: UMO190A                                               */
/*==============================================================*/
create table UMO190A
(
   CURSOS_REL           varchar(20) not null  comment '',
   DOCENTE_REL          varchar(10)  comment '',
   NOMBRE_190           varchar(50)  comment '',
   TURNO_190            numeric(1,0)  comment '',
   HRSINICIO_190        time  comment '',
   HRSFIN_190           time  comment '',
   FECHAINICIO_190      date  comment '',
   FECHAFIN_190         date  comment '',
   DIACLASES_190        varchar(50)  comment '',
   MODALIDAD_190        bool  comment '',
   ESTADO_190           numeric(1,0)  comment '',
   primary key (CURSOS_REL)
);

/*==============================================================*/
/* Table: UMO200A                                               */
/*==============================================================*/
create table UMO200A
(
   ALUMNO_REL           varchar(10) not null  comment '',
   UNIVERSIDAD_REL      varchar(20)  comment '',
   MUNICIPIO_REL        varchar(10)  comment '',
   NOMBRES_200          varchar(50)  comment '',
   APELLIDOS_200        varchar(50)  comment '',
   NACIONALIDAD_200     varchar(100)  comment '',
   CEDULA_200           varchar(15)  comment '',
   DEFICIENCIA_200      varchar(90)  comment '',
   ESTADOCIVIL_200      numeric(1,0)  comment '',
   HIJOS_200            numeric(8,0)  comment '',
   DISCAPACIDAD_200     varchar(80)  comment '',
   NIVELESTUDIOS_200    varchar(150)  comment '',
   CONDICIONLAB_200     varchar(90)  comment '',
   PROFESIONAL_200      varchar(100)  comment '',
   SECTOR_200           numeric(2,0)  comment '1.- Agricultura, ganadería, caza y silvicultra
             2.- Pesca
             3.- Minas y canteras
             4.- Industria manufacturas
             5.- Electricidad, gas y agua
             6.- Construcción
             7.- Comercio
             8.- Hoteles y restaurantes
             9.- Transporte, almacenamiento y counicacion
             10.- Actividades inmoviliarias, empresariales y de alquiler.
             11.- Administracio publica y defensa, planes de seguridad social.
             12.- Enseñanza
             13.- Servicios sociales y de salud
             14.- Otros servicios comunales, sociales y personales.
             15.- Hogares privados con servicio doméstico
             16.- Organizaciones y órganos extraterritoriales.',
   INGRESOMENSUAL_200   varchar(20)  comment '',
   ENTIDADLAB_200       numeric(1,0)  comment '',
   TELEFONO_200         varchar(20)  comment '',
   CELULAR_200          varchar(20)  comment '',
   EMAIL_200            varchar(90)  comment '',
   IDIOMA_200           varchar(30)  comment '',
   DOMINIOIDIOMA_200    varchar(120)  comment '',
   DIRECCION_200        varchar(250)  comment '',
   MEDIO_200            numeric(2,0)  comment '1.-Visita al colegio
             2.-Facebook
             3.-Instagram
             4.-Clinica ODM
             5.-Radio
             6.-Estudiante UMOJN
             7.-Publicidad en la calle
             8.-Amigo o familiar
             9.-Feria de salud
             10.-Tik Tok
             11.-Clinica PAMIC
             12.-Sitio web
             13.-YouTube
             14.-Otros',
   NOMBREREF_200        varchar(20)  comment '',
   CEDULAREFERENTE_200  varchar(20)  comment '',
   CELULARREFERENTE_200 varchar(20)  comment '',
   DIRECCIONREF_200     varchar(100)  comment '',
   primary key (ALUMNO_REL)
);

/*==============================================================*/
/* Table: UMO201A                                               */
/*==============================================================*/
create table UMO201A
(
   ALUMNO_REL           varchar(10) not null  comment '',
   EVIDENCIAS_REL       varchar(50) not null  comment '',
   TIPO_201             numeric(2,0)  comment '0.- Diploma de bachiller
             1.- Calificaciones de secundaria
             2.- Cédula de identidad
             3.- Acta de nacimiento
             4.- Foto
             5.- Cédula de residencia
             6.- Pasaporte
             7.- Plan de estudio
             8.- Acta de aprobación monográfica
             9.- Calificaciones universitarias
             10.- Certificación del título universitario
             11.- Datos generales del título
             12.- Publicación en la gaceta
             13.- Título universitario',
   DESC_201             varchar(100)  comment '',
   RUTA_201             varchar(255)  comment '',
   primary key (ALUMNO_REL, EVIDENCIAS_REL)
);

/*==============================================================*/
/* Table: UMO210A                                               */
/*==============================================================*/
create table UMO210A
(
   MATCURSO_REL2        varchar(20) not null  comment '',
   CURSOS_REL           varchar(20)  comment '',
   ALUMNO_REL           varchar(10)  comment '',
   FECHA_210            date  comment '',
   RECIBO_210           numeric(10,0)  comment '',
   BECA_210             bool  comment '',
   MOTIVOBECA_210       varchar(300)  comment '',
   DIPLOMA_210          bool  comment '',
   CEDULA_210           bool  comment '',
   ACTANACIMIENTO_210   bool  comment '',
   ESTADO_210           numeric(1,0)  comment '',
   ANNOACADEMICO_210    bool  comment '',
   primary key (MATCURSO_REL2)
);

/*==============================================================*/
/* Table: UMO230A                                               */
/*==============================================================*/
create table UMO230A
(
   PLANPOSGRADO_REL     varchar(10) not null  comment '',
   CARRERA_REL          varchar(10)  comment '',
   PERIODO_230          varchar(30)  comment '',
   GRADO_230            varchar(50)  comment '',
   HORAS_230            numeric(6,0)  comment '',
   CREDITOS_220         numeric(4,0)  comment '',
   TURNO_230            numeric(1,0)  comment '1.-Diurno
             2.-Matutino
             3.-Vespertino
             4.-Nocturno
             5.-Sabatino
             6.-Dominical',
   REGIMEN_230          numeric(1,0)  comment '1.-Mensual
             2.-Bimestral
             3.-Trimestral
             4.-Cuatrimestral
             5.-Semestral
             6.-Intensivo',
   MODALIDAD_230        numeric(1,0)  comment '1.-Presencial
             2.-Por encuentro
             3.-Virtual
             4.-Mixta',
   ACTIVO_230           bool  comment '',
   primary key (PLANPOSGRADO_REL)
);

/*==============================================================*/
/* Table: UMO231A                                               */
/*==============================================================*/
create table UMO231A
(
   PLANPOSGRADO_REL     varchar(10) not null  comment '',
   DETPLAN_REL          varchar(3) not null  comment '',
   CURSOPOSGRADO_REL    varchar(10)  comment '',
   PERIODO_221          numeric(2,0)  comment 'Semestre, trimestre o cuatrimestre en el que se imparte el curso',
   MODULO_221           varchar(200)  comment '',
   HPRESENCIALES_221    numeric(5,0)  comment '',
   HAUTOESTUDIO_221     numeric(5,0)  comment '',
   HTRABAJO_221         numeric(5,0)  comment '',
   HTOTALES_221         numeric(5,0)  comment '',
   CREDITOS_221         numeric(5,0)  comment '',
   primary key (PLANPOSGRADO_REL, DETPLAN_REL)
);

/*==============================================================*/
/* Table: UMO240A                                               */
/*==============================================================*/
create table UMO240A
(
   CURSOPOSGRADO_REL    varchar(10) not null  comment '',
   CARRERA_REL          varchar(10)  comment '',
   NOMBRE_240           varchar(100)  comment '',
   CODIGO_240           varchar(10)  comment '',
   ACTIVO_240           bool  comment '',
   primary key (CURSOPOSGRADO_REL)
);

/*==============================================================*/
/* Table: UMO250A                                               */
/*==============================================================*/
create table UMO250A
(
   ESTUDIANTEPOS_REL    varchar(10) not null  comment '',
   CARRERA_REL          varchar(10)  comment '',
   UNIVERSIDAD_REL      varchar(20)  comment '',
   MUNICIPIO_REL        varchar(10)  comment '',
   USUARIO_REL          varchar(20)  comment '',
   FECHA_250            date  comment '',
   ANNOACADEMICO_250    numeric(4,0)  comment '',
   CARNET_250           varchar(20)  comment '',
   NOMBRE1_250          varchar(50)  comment '',
   NOMBRE2_250          varchar(50)  comment '',
   APELLIDO1_250        varchar(50)  comment '',
   APELLIDO2_250        varchar(50)  comment '',
   GRADOACADEMICO_250   varchar(200)  comment '',
   FECHANAC_250         date  comment '',
   NACIONALIDAD_250     varchar(50)  comment '',
   PESO_250             numeric(3,0)  comment '',
   TALLA_250            numeric(3,0)  comment '',
   TIPOSANGRE_250       varchar(3)  comment '',
   CEDULA_250           varchar(20)  comment '',
   SEXO_250             char(1)  comment '',
   ESTADOCIVIL_250      numeric(1,0)  comment '0.-Soltero
             1.-Casado
             2.-Divorciado
             3.-Viudo',
   HIJOS_250            numeric(2,0)  comment '',
   TELEFONO_250         varchar(20)  comment '',
   CELULAR_250          varchar(20)  comment '',
   CORREOE_250          varchar(100)  comment '',
   CORREOI_250          varchar(100)  comment '',
   DIRECCION_250        varchar(300)  comment '',
   MEDIO_250            numeric(2,0)  comment '1.-Visita a univesidad
             2.-Facebook
             3.-Instagram
             4.-Clinica ODM
             5.-Radio
             6.-Estudiante UMOJN
             7.-Publicidad en la calle
             8.-Amigo o familiar
             9.-Feria de salud
             10.-Tik Tok
             11.-Clinica PAMIC
             12.-Sitio web
             13.-YouTube
             14.-Otros',
   EMERGENCIA_250       varchar(100)  comment '',
   TEL_EMERGENCIA_250   varchar(20)  comment '',
   CEL_EMERGENCIA_250   varchar(20)  comment '',
   CONDICIONLABORAL_250 bool  comment '',
   OCUPACION_250        varchar(200)  comment '',
   INGRESOMENSUAL_250   float(8,2)  comment '',
   CENTROTRABAJO_250    varchar(200)  comment '',
   DIRECCIONTRABAJO_250 varchar(300)  comment '',
   OTROIDIOMA_250       bool  comment '',
   IDIOMA_250           varchar(200)  comment '',
   primary key (ESTUDIANTEPOS_REL)
);

/*==============================================================*/
/* Table: UMO251A                                               */
/*==============================================================*/
create table UMO251A
(
   ESTUDIANTEPOS_REL    varchar(10) not null  comment '',
   TIPO_REL             numeric(2,0) not null  comment '0.- Curriculum vitae
             1.- Calificaciones de grado
             2.- Cédula de identidad
             3.- Título de grado
             4.- Foto
             5.- Plan de estudio
             6.- Acta de aprobación monográfica
             7.- Calificaciones universitarias
             8.- Certificación del título universitario
             9.- Datos generales del título
             10.- Publicación en la gaceta
             11.- Título de posgrado',
   ARCHIVO_251          varchar(50)  comment '',
   DESC_251             varchar(100)  comment '',
   RUTA_251             varchar(255)  comment '',
   primary key (ESTUDIANTEPOS_REL, TIPO_REL)
);

/*==============================================================*/
/* Table: UMO260A                                               */
/*==============================================================*/
create table UMO260A
(
   MATRICULAPOS_REL     varchar(10) not null  comment '',
   ESTUDIANTEPOS_REL    varchar(10)  comment '',
   CARRERA_REL          varchar(10)  comment '',
   CURSOPOSGRADO_REL    varchar(10)  comment '',
   FECHA_260            date  comment '',
   ANNOINGRESO_260      numeric(4,0)  comment '',
   COHORTE_260          varchar(5)  comment '',
   RECIBO_260           varchar(10)  comment '',
   TITULO_260           bool  comment '',
   NOTAS_260            bool  comment '',
   CEDULA_260           bool  comment '',
   CURRICULUM_260       bool  comment '',
   ESTADO_260           numeric(1,0)  comment '',
   primary key (MATRICULAPOS_REL)
);

/*==============================================================*/
/* Table: UMO320A                                               */
/*==============================================================*/
create table UMO320A
(
   ASISTENCIACL_REL     varchar(10) not null  comment '',
   DOCENTE_REL          varchar(10)  comment '',
   FECHA_320            date  comment '',
   TURNO_320            numeric(1,0)  comment '',
   ANNO_320             numeric(1,0)  comment '',
   MODULOLECTIVO_320    numeric(1,0)  comment '',
   primary key (ASISTENCIACL_REL)
);

/*==============================================================*/
/* Table: UMO321A                                               */
/*==============================================================*/
create table UMO321A
(
   ASISTENCIACL_REL     varchar(10)  comment '',
   MATCURSO_REL2        varchar(20)  comment '',
   ESTADO_321           char(1)  comment ''
);

alter table UMO002B add constraint FK_UMO002B_REL_001B__UMO001B foreign key (EXPDIGITAL_REL)
      references UMO001B (EXPDIGITAL_REL) on delete restrict on update restrict;

alter table UMO005A add constraint FK_UMO005A_REL_003_0_UMO003A foreign key (GRUPO_REL)
      references UMO003A (GRUPO_REL) on delete restrict on update restrict;

alter table UMO005A add constraint FK_UMO005A_REL_004_0_UMO004A foreign key (PAGINA_REL)
      references UMO004A (PAGINA_REL) on delete restrict on update restrict;

alter table UMO006A add constraint FK_UMO006A_UMO006A_UMO003A foreign key (GRUPO_REL)
      references UMO003A (GRUPO_REL) on delete restrict on update restrict;

alter table UMO006A add constraint FK_UMO006A_UMO006A2_UMO002A foreign key (USUARIO_REL)
      references UMO002A (USUARIO_REL) on delete restrict on update restrict;

alter table UMO010A add constraint FK_UMO010A_REL_002_0_UMO002A foreign key (USUARIO_REL)
      references UMO002A (USUARIO_REL) on delete restrict on update restrict;

alter table UMO010A add constraint FK_UMO010A_REL_020_0_UMO020A foreign key (COLEGIO_REL)
      references UMO020A (COLEGIO_REL) on delete restrict on update restrict;

alter table UMO010A add constraint FK_UMO010A_REL_040_0_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO010A add constraint FK_UMO010A_REL_120_0_UMO120A foreign key (MUNICIPIO_REL)
      references UMO120A (MUNICIPIO_REL) on delete restrict on update restrict;

alter table UMO010C add constraint FK_UMO010C_REL_010_0_UMO010C foreign key (UMO_AREA_REL)
      references UMO010C (AREA_REL) on delete restrict on update restrict;

alter table UMO011A add constraint FK_UMO011A_REL_010_0_UMO010A foreign key (ESTUDIANTE_REL)
      references UMO010A (ESTUDIANTE_REL) on delete restrict on update restrict;

alter table UMO011C add constraint FK_UMO011C_UMO011C_UMO002A foreign key (USUARIO_REL)
      references UMO002A (USUARIO_REL) on delete restrict on update restrict;

alter table UMO011C add constraint FK_UMO011C_UMO011C2_UMO010C foreign key (AREA_REL)
      references UMO010C (AREA_REL) on delete restrict on update restrict;

alter table UMO012C add constraint FK_UMO012C_REL_010_0_UMO010C foreign key (AREA_REL)
      references UMO010C (AREA_REL) on delete restrict on update restrict;

alter table UMO020A add constraint FK_UMO020A_REL_120_0_UMO120A foreign key (MUNICIPIO_REL)
      references UMO120A (MUNICIPIO_REL) on delete restrict on update restrict;

alter table UMO030A add constraint FK_UMO030A_REL_010_0_UMO010A foreign key (ESTUDIANTE_REL)
      references UMO010A (ESTUDIANTE_REL) on delete restrict on update restrict;

alter table UMO030A add constraint FK_UMO030A_REL_040_0_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO030A add constraint FK_UMO030A_REL_050_0_UMO050A foreign key (PLANESTUDIO_REL)
      references UMO050A (PLANESTUDIO_REL) on delete restrict on update restrict;

alter table UMO031A add constraint FK_UMO031A_UMO031A_UMO030A foreign key (MATRICULA_REL)
      references UMO030A (MATRICULA_REL) on delete restrict on update restrict;

alter table UMO031A add constraint FK_UMO031A_UMO031A2_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO050A add constraint FK_UMO050A_REL_040_0_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO051A add constraint FK_UMO051A_REL_050_0_UMO050A foreign key (PLANESTUDIO_REL)
      references UMO050A (PLANESTUDIO_REL) on delete restrict on update restrict;

alter table UMO051A add constraint FK_UMO051A_REL_060_0_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO051A add constraint FK_UMO051A_REL_060_0_UMO060A foreign key (UMO_ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO060A add constraint FK_UMO060A_REL_040_0_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO070A add constraint FK_UMO070A_REL_050_0_UMO050A foreign key (PLANESTUDIO_REL)
      references UMO050A (PLANESTUDIO_REL) on delete restrict on update restrict;

alter table UMO070A add constraint FK_UMO070A_REL_060_0_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO070A add constraint FK_UMO070A_REL_100_0_UMO100A foreign key (DOCENTE_REL)
      references UMO100A (DOCENTE_REL) on delete restrict on update restrict;

alter table UMO071A add constraint FK_UMO071A_REL_070_0_UMO070A foreign key (SYLLABUS_REL)
      references UMO070A (SYLLABUS_REL) on delete restrict on update restrict;

alter table UMO072A add constraint FK_UMO072A_REL_070_0_UMO070A foreign key (SYLLABUS_REL)
      references UMO070A (SYLLABUS_REL) on delete restrict on update restrict;

alter table UMO073A add constraint FK_UMO073A_REL_070_0_UMO070A foreign key (SYLLABUS_REL)
      references UMO070A (SYLLABUS_REL) on delete restrict on update restrict;

alter table UMO074A add constraint FK_UMO074A_REL_070_0_UMO070A foreign key (SYLLABUS_REL)
      references UMO070A (SYLLABUS_REL) on delete restrict on update restrict;

alter table UMO075A add constraint FK_UMO075A_REL_070_0_UMO070A foreign key (SYLLABUS_REL)
      references UMO070A (SYLLABUS_REL) on delete restrict on update restrict;

alter table UMO080A add constraint FK_UMO080A_REL_060_0_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO081A add constraint FK_UMO081A_REL_080_0_UMO080A foreign key (DIACLASE_REL)
      references UMO080A (DIACLASE_REL) on delete restrict on update restrict;

alter table UMO090A add constraint FK_UMO090A_REL_060_0_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO100A add constraint FK_UMO100A_REL_002_1_UMO002A foreign key (USUARIO_REL)
      references UMO002A (USUARIO_REL) on delete restrict on update restrict;

alter table UMO120A add constraint FK_UMO120A_REL_110_1_UMO110A foreign key (DEPARTAMENTO_REL)
      references UMO110A (DEPARTAMENTO_REL) on delete restrict on update restrict;

alter table UMO130A add constraint FK_UMO130A_REL_040_1_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO130A add constraint FK_UMO130A_REL_130_1_UMO130A foreign key (UMO_COBRO_REL)
      references UMO130A (COBRO_REL) on delete restrict on update restrict;

alter table UMO130A add constraint FK_UMO130A_REL_190_1_UMO190A foreign key (CURSOS_REL)
      references UMO190A (CURSOS_REL) on delete restrict on update restrict;

alter table UMO130A add constraint FK_UMO130A_REL_260_1_UMO260A foreign key (MATRICULAPOS_REL)
      references UMO260A (MATRICULAPOS_REL) on delete restrict on update restrict;

alter table UMO131A add constraint FK_UMO131A_REL_030_1_UMO030A foreign key (MATRICULA_REL)
      references UMO030A (MATRICULA_REL) on delete restrict on update restrict;

alter table UMO131A add constraint FK_UMO131A_REL_130_1_UMO130A foreign key (COBRO_REL)
      references UMO130A (COBRO_REL) on delete restrict on update restrict;

alter table UMO132A add constraint FK_UMO132A_REL_130_1_UMO130A foreign key (COBRO_REL)
      references UMO130A (COBRO_REL) on delete restrict on update restrict;

alter table UMO141A add constraint FK_UMO141A_REL_030_1_UMO030A foreign key (MATRICULA_REL)
      references UMO030A (MATRICULA_REL) on delete restrict on update restrict;

alter table UMO141A add constraint FK_UMO141A_REL_140_1_UMO140A foreign key (PAGO_REL)
      references UMO140A (PAGO_REL) on delete restrict on update restrict;

alter table UMO141A add constraint FK_UMO141A_REL_210_1_UMO210A foreign key (MATCURSO_REL2)
      references UMO210A (MATCURSO_REL2) on delete restrict on update restrict;

alter table UMO142A add constraint FK_UMO142A_REL_140_1_UMO140A foreign key (PAGO_REL)
      references UMO140A (PAGO_REL) on delete restrict on update restrict;

alter table UMO142A add constraint FK_UMO142A_REL_210_1_UMO210A foreign key (MATCURSO_REL2)
      references UMO210A (MATCURSO_REL2) on delete restrict on update restrict;

alter table UMO150A add constraint FK_UMO150A_REL_040_1_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO150A add constraint FK_UMO150A_REL_060_1_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO150A add constraint FK_UMO150A_REL_100_1_UMO100A foreign key (DOCENTE_REL)
      references UMO100A (DOCENTE_REL) on delete restrict on update restrict;

alter table UMO151A add constraint FK_UMO151A_REL_030_1_UMO030A foreign key (MATRICULA_REL)
      references UMO030A (MATRICULA_REL) on delete restrict on update restrict;

alter table UMO151A add constraint FK_UMO151A_REL_150_1_UMO150A foreign key (ASISTENCIA_REL)
      references UMO150A (ASISTENCIA_REL) on delete restrict on update restrict;

alter table UMO160A add constraint FK_UMO160A_REL_040_1_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO160A add constraint FK_UMO160A_REL_060_1_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO160A add constraint FK_UMO160A_REL_100_1_UMO100A foreign key (DOCENTE_REL)
      references UMO100A (DOCENTE_REL) on delete restrict on update restrict;

alter table UMO161A add constraint FK_UMO161A_REL_030_1_UMO030A foreign key (MATRICULA_REL)
      references UMO030A (MATRICULA_REL) on delete restrict on update restrict;

alter table UMO161A add constraint FK_UMO161A_REL_160_1_UMO160A foreign key (CALIFICACION_REL)
      references UMO160A (CALIFICACION_REL) on delete restrict on update restrict;

alter table UMO170A add constraint FK_UMO170A_REL_040_1_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO170A add constraint FK_UMO170A_REL_060_1_UMO060A foreign key (ASIGNATURA_REL)
      references UMO060A (ASIGNATURA_REL) on delete restrict on update restrict;

alter table UMO170A add constraint FK_UMO170A_REL_070_1_UMO070A foreign key (SYLLABUS_REL)
      references UMO070A (SYLLABUS_REL) on delete restrict on update restrict;

alter table UMO170A add constraint FK_UMO170A_REL_100_1_UMO100A foreign key (DOCENTE_REL)
      references UMO100A (DOCENTE_REL) on delete restrict on update restrict;

alter table UMO171A add constraint FK_UMO171A_REL_170_1_UMO170A foreign key (AVANCE_REL)
      references UMO170A (AVANCE_REL) on delete restrict on update restrict;

alter table UMO190A add constraint FK_UMO190A_REL_100_1_UMO100A foreign key (DOCENTE_REL)
      references UMO100A (DOCENTE_REL) on delete restrict on update restrict;

alter table UMO200A add constraint FK_UMO200A_REL_120_2_UMO120A foreign key (MUNICIPIO_REL)
      references UMO120A (MUNICIPIO_REL) on delete restrict on update restrict;

alter table UMO200A add constraint FK_UMO200A_REL_180_2_UMO180A foreign key (UNIVERSIDAD_REL)
      references UMO180A (UNIVERSIDAD_REL) on delete restrict on update restrict;

alter table UMO201A add constraint FK_UMO201A_REL_200_2_UMO200A foreign key (ALUMNO_REL)
      references UMO200A (ALUMNO_REL) on delete restrict on update restrict;

alter table UMO210A add constraint FK_UMO210A_REL_190_2_UMO190A foreign key (CURSOS_REL)
      references UMO190A (CURSOS_REL) on delete restrict on update restrict;

alter table UMO210A add constraint FK_UMO210A_REL_200_2_UMO200A foreign key (ALUMNO_REL)
      references UMO200A (ALUMNO_REL) on delete restrict on update restrict;

alter table UMO230A add constraint FK_UMO230A_REL_040_2_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO231A add constraint FK_UMO231A_REL_230_2_UMO230A foreign key (PLANPOSGRADO_REL)
      references UMO230A (PLANPOSGRADO_REL) on delete restrict on update restrict;

alter table UMO231A add constraint FK_UMO231A_REL_240_2_UMO240A foreign key (CURSOPOSGRADO_REL)
      references UMO240A (CURSOPOSGRADO_REL) on delete restrict on update restrict;

alter table UMO240A add constraint FK_UMO240A_REL_040_2_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO250A add constraint FK_UMO250A_REL_002_2_UMO002A foreign key (USUARIO_REL)
      references UMO002A (USUARIO_REL) on delete restrict on update restrict;

alter table UMO250A add constraint FK_UMO250A_REL_040_2_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO250A add constraint FK_UMO250A_REL_120_2_UMO120A foreign key (MUNICIPIO_REL)
      references UMO120A (MUNICIPIO_REL) on delete restrict on update restrict;

alter table UMO250A add constraint FK_UMO250A_REL_180_2_UMO180A foreign key (UNIVERSIDAD_REL)
      references UMO180A (UNIVERSIDAD_REL) on delete restrict on update restrict;

alter table UMO251A add constraint FK_UMO251A_REL_250_2_UMO250A foreign key (ESTUDIANTEPOS_REL)
      references UMO250A (ESTUDIANTEPOS_REL) on delete restrict on update restrict;

alter table UMO260A add constraint FK_UMO260A_REL_040_2_UMO040A foreign key (CARRERA_REL)
      references UMO040A (CARRERA_REL) on delete restrict on update restrict;

alter table UMO260A add constraint FK_UMO260A_REL_240_2_UMO240A foreign key (CURSOPOSGRADO_REL)
      references UMO240A (CURSOPOSGRADO_REL) on delete restrict on update restrict;

alter table UMO260A add constraint FK_UMO260A_REL_250_2_UMO250A foreign key (ESTUDIANTEPOS_REL)
      references UMO250A (ESTUDIANTEPOS_REL) on delete restrict on update restrict;

alter table UMO320A add constraint FK_UMO320A_REL_100_3_UMO100A foreign key (DOCENTE_REL)
      references UMO100A (DOCENTE_REL) on delete restrict on update restrict;

alter table UMO321A add constraint FK_UMO321A_REL_210_3_UMO210A foreign key (MATCURSO_REL2)
      references UMO210A (MATCURSO_REL2) on delete restrict on update restrict;

alter table UMO321A add constraint FK_UMO321A_REL_320_3_UMO320A foreign key (ASISTENCIACL_REL)
      references UMO320A (ASISTENCIACL_REL) on delete restrict on update restrict;

