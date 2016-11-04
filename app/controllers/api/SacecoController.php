<?php


class SacecoController extends BaseController {

	public function pendientes(){

		$id = Input::get('cliente');
		$fechaini = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechaini') ))) . ' 00:00:00';
		$fechafin = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechafin') ))) . ' 23:59:59';

		$resultado = array();
		$porCompania = '';

		if($id>0){
			$porCompania = ' and Compania.Cia_clave='.$id;
		}

		/*$sql = "SELECT Expediente.Exp_folio, UNI_nombreMV, Expediente.Exp_poliza,
	            Expediente.Exp_siniestro,Expediente.Exp_reporte,
	            Exp_completo, Exp_fecreg, Cia_nombrecorto, RIE_nombre,
	            CASE WHEN Expediente.Exp_folio in (SELECT REG_folio FROM DocumentosDigitales where REG_folio = Expediente.Exp_folio) THEN 'DIGITALIZADO' ELSE 'SIN DIGITALIZAR' END as DocumentosDigitales,
	            EXP_fechaCaptura,EXP_costoEmpresa,Triage_nombre,UNI_propia,
	            CASE WHEN DATE_FORMAT(Exp_fecPaseMed, '%d/%m/%Y')  = '00-00-0000' THEN NULL ELSE DATE_FORMAT(Exp_fecPaseMed, '%d/%m/%Y')  END as FExpedicion
	            FROM Expediente
	                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave
	                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
	                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave
	                left join TriageAutorizacion on TriageAutorizacion.Triage_id = Expediente.Exp_triageActual
	                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
	                left join ExpedienteInfo on ExpedienteInfo.Exp_folio = Expediente.Exp_folio
	            WHERE Exp_fecreg BETWEEN '$fechaini' and '$fechafin' and Compania.Cia_clave in ($id) and Exp_FE = 1 and Unidad.Uni_clave <> 8 and Exp_cancelado = 0  AND Exp_solicitado = 0 AND Exp_autorizado = 0 AND ( FAC_folio = 0 OR FAC_folio IS NULL ) AND Expediente.Exp_fecreg >= '2016-02-08 00:00:00' and Expediente.Exp_triageActual NOT IN (4,5) and PRO_clave <> 13 ORDER BY Exp_fecreg DESC";*/

	     $sql="SELECT Expediente.Exp_folio, UNI_nombreMV, Expediente.Exp_poliza,
            Expediente.Exp_siniestro,Expediente.Exp_reporte,
            Exp_completo, Exp_fecreg , ATN_fecreg, Cia_nombrecorto, RIE_nombre,
            'DIGITAL',
            'FECHA_CAPTURA', 'COSTO_EMPRESA','TRIAGE NOMBRE',UNI_propia,
            'FECHA EXPEDICION', ATN_clave, TIA_nombre
            FROM Expediente
                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave
                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave                
                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
                inner join Atenciones on Expediente.Exp_folio = Atenciones.Exp_folio 
                inner join TipoAtencion on Atenciones.TIA_clave=TipoAtencion.TIA_clave               
            WHERE ATN_fecreg BETWEEN '$fechaini' and '".$fechafin." 23:59:59'  and Exp_cancelado = 0  AND Expediente.Exp_fecreg >= 
            '2016-02-08 00:00:00' and PRO_clave <> 13 and ATN_estatus=1 ".$porCompania."  ORDER BY Exp_fecreg DESC";       



		/*$sql = "SELECT Expediente.Exp_folio, UNI_nombreMV, Expediente.Exp_poliza,
	            Expediente.Exp_siniestro,Expediente.Exp_reporte,
	            Exp_completo, Exp_fecreg, Cia_nombrecorto, RIE_nombre,
	            CASE WHEN Expediente.Exp_folio in (SELECT REG_folio FROM DocumentosDigitales where REG_folio = Expediente.Exp_folio) THEN 'DIGITALIZADO' ELSE 'SIN DIGITALIZAR' END as DocumentosDigitales,
	            EXP_fechaCaptura,EXP_costoEmpresa,Triage_nombre,UNI_propia,
	            CASE WHEN DATE_FORMAT(Exp_fecPaseMed, '%d/%m/%Y')  = '00-00-0000' THEN NULL ELSE DATE_FORMAT(Exp_fecPaseMed, '%d/%m/%Y')  END as FExpedicion
	            FROM Expediente
	                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave
	                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
	                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave
	                left join TriageAutorizacion on TriageAutorizacion.Triage_id = Expediente.Exp_triageActual
	                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
	                left join ExpedienteInfo on ExpedienteInfo.Exp_folio = Expediente.Exp_folio
	            WHERE Exp_fecreg BETWEEN '$fechaini' and '$fechafin' and Compania.Cia_clave in ($id) and Exp_FE = 1 and Unidad.Uni_clave <> 8 and Exp_cancelado = 0  AND Exp_solicitado = 0 AND Exp_autorizado = 0 AND ( FAC_folio = 0 OR FAC_folio IS NULL ) AND Expediente.Exp_fecreg >= '2016-02-08 00:00:00' and Expediente.Exp_triageActual NOT IN (4,5) and PRO_clave <> 13 ORDER BY Exp_fecreg DESC";
*/
        //conteo general
	    $sqlAnalisis = "SELECT  
	            count(*) as Total,
	            SUM( IF(FAC_folio > 0 AND FAC_pagada = 1,1,0) ) as Pagados,
	            SUM( IF(FAC_folio > 0 AND FAC_pagada = 0,1,0) ) as Facturados,
	            SUM( IF(Exp_autorizado = 1,1,0) ) as AutorizadoNoFacturado,
	            SUM( IF(Exp_autorizado = 0 AND Exp_solicitado = 1 AND Exp_rechazado = 1,1,0) ) as Rechazados,
	            SUM( IF(EXP_autorizado = 0 AND Exp_solicitado = 1 AND Exp_rechazado = 0  and EXP_cancelado = 0,1,0) ) as XAutorizar,
	            SUM( IF(Exp_cancelado = 0 AND EXP_fechaCaptura is not null and FAC_folio = 0 AND Exp_solicitado = 0 AND Exp_solicitado = 0,1,0) ) as ConDcoumentos,
	            SUM( IF(Exp_cancelado = 0 AND EXP_fechaCaptura is null  AND Exp_solicitado = 0,1,0) ) as Registrados,
	            SUM( IF(Exp_cancelado = 0,1,0) ) as Totales,
	            SUM( IF(Exp_cancelado = 1,1,0) ) as Cancelados
	            FROM Expediente
	            LEFT JOIN ExpedienteInfo ON ExpedienteInfo.Exp_folio = Expediente.Exp_folio 
	            WHERE Exp_fecreg BETWEEN '$fechaini 00:00:00' and '$fechafin 23:59:59' and Expediente.Cia_clave in ($id) AND Expediente.Exp_fecreg >= '2016-02-08 00:00:00' and Expediente.Exp_triageActual NOT IN (4,5) and PRO_clave <> 13 and Expediente.Exp_FE = 1 and Expediente.Uni_clave <> 8 ";

		$resultado['listado'] =  DB::connection('mysql')->select($sql);

		$resultado['numeros'] =  DB::connection('mysql')->select($sqlAnalisis)[0];

		return $resultado;
	
	}

	public function detalleFolio($folio,$atencion){

		$resultado = array();

		$sql = "SELECT  Expediente.Exp_folio as folio,Exp_completo as lesionado,Exp_cveAjustador as cveAjustador ,Unidad.Uni_clave as UniClave, UNI_nombreMV as unidad, 
                    Expediente.Exp_poliza as poliza, Expediente.Exp_siniestro as siniestro ,Expediente.Exp_reporte as reporte,
                    Exp_fecreg as fechaatencion , Expediente.EXP_edad as edad, Expediente.EXP_sexo as sexo,EXP_fechaCaptura as fechacaptura,
                    ExpedienteInfo.EXP_fechaExpedicion as fechaexpedicion, Expediente.EXP_orden as orden,  RIE_nombre as riesgo, 
                    POS_nombre  as posicion, Expediente.Exp_ajustador as ajustador, EXP_obsAjustador as observaciones, TLE_nombre as lesion, 
                    EXP_diagnostico as descripcion, FAC_folioFiscal as sat, CONCAT(FAC_serie,FAC_folio) as foliointerno,
                    FAC_fecha as fechafactura, FAC_importe as importe, FAC_iva as iva, FAC_total as total, Cia_rfc as rfc,
                    Cia_nombrecorto as empresa, Compania.Cia_clave  as claveEmpresa,Exp_telefono as telefono,Exp_mail as mail,
                    EXP_costoEmpresa as costo,
                    Pro_clave as producto             
            FROM Expediente
                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave 
                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave
                left join NotaMedica on NotaMedica.Exp_folio = Expediente.Exp_folio
                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
                left join ExpedienteInfo on ExpedienteInfo.EXP_folio = Expediente.EXP_folio
                left join PosicionAcc on PosicionAcc.id = NotaMedica.Posicion_clave
                left join Posicion on Posicion.POS_clave = PosicionAcc.POS_claveMV
                left join LesionMV on LesionMV.LES_clave = ExpedienteInfo.LES_empresa
                left join TipoLesion on TipoLesion.TLE_claveint = LesionMV.TLE_claveint                
            WHERE  Expediente.EXP_folio = '$folio' and EXP_cancelado = 0";

        $resultado['expediente'] = DB::connection('mysql')->select($sql)[0];

        $productoCve = $resultado['expediente'];                
        foreach ($productoCve as $key => $value) {
        	if($key='producto'){
        		$prodCve=$value;
        	}
        }
		
        $sql="SELECT Distinct atn.TID_clave, Atenciones.ATN_clave, Exp_folio, atn.TIA_clave, ATN_cons, ATN_estatus, ATN_fecreg, Producto.Pro_clave,  TID_nombre,Arc_cons, Arc_clave, Arc_archivo, Arc_tipo, ATD_requerido, ATD_estatus, ATD_motivo, Arc_estatus, Arc_motivo, Arc_motivoCat, Arc_autorizado from Atenciones
				inner join AtencionDocumento atn on Atenciones.TIA_clave = atn.TIA_clave
				inner join TipoDocumento on TipoDocumento.TID_claveint = atn.TID_clave
				inner join Producto on Producto.Pro_clave = atn.Pro_clave
				inner join AtencionTipoDocumento on Atenciones.ATN_clave=AtencionTipoDocumento.ATN_clave and TipoDocumento.TID_claveint=AtencionTipoDocumento.TID_clave				
				left join DocumentosDigitales DIG  on Atenciones.ATN_clave= DIG.ATN_clave and  atn.TID_clave=DIG.Arc_tipo
				where Atenciones.ATN_clave=".$atencion." and Producto.Pro_clave=".$prodCve;

		$resultado['documentos'] = DB::connection('mysql')->select($sql);

		$sql="SELECT REQ_valor, ANT_valor FROM Anotaciones
				 inner join Requisitos on Anotaciones.REQ_clave = Requisitos.REQ_clave
				 WHERE ATN_clave=".$atencion;
		$resultado['anotaciones'] = DB::connection('mysql')->select($sql);

		$sqlCat="SELECT MRE_CVE, MRE_nombre FROM MotivoRechazo";
		$resultado['motivoRechazo'] = DB::connection('mysql')->select($sqlCat);

		$sqlCat="SELECT TIA_nombre FROM Atenciones
				 INNER JOIN TipoAtencion ON Atenciones.TIA_clave=TipoAtencion.TIA_clave
				 WHERE ATN_clave=".$atencion;
		$resultado['atencion'] = DB::connection('mysql')->select($sqlCat)[0];

		$cont = "SELECT count(*) as con FROM ExpedienteLesion WHERE Exp_folio='".$folio."'";
		$con = DB::connection('mysql')->select($cont)[0];
		if($con->con>0){
			$sqlLes="SELECT  ExpedienteLesion.TLE_claveint, LesionCodificada.CIE_cve, CIE_descripcion, Clave_lesionCia as lesionCIA, LesE_clave FROM ExpedienteLesion
					 INNER JOIN LesionCodificada on ExpedienteLesion.LCO_cve = LesionCodificada.LCO_cve				 
					 INNER JOIN CieOrtopedico on LesionCodificada.CIE_cve = CieOrtopedico.CIE_cve
                     INNER JOIN LesionEquivalencia on ExpedienteLesion.LES_clave = LesionEquivalencia.Clave_lesionMV
					 WHERE Exp_folio='".$folio."'";

			$resultado['lesion'] = DB::connection('mysql')->select($sqlLes)[0];
		}else{
			$resultado['lesion']='sinLesion';
		}



		/*$resultado['documentos'] = DB::connection('mysql')->table('Atenciones')
				->join('AtencionDocumento','Atenciones.TIA_clave','=','AtencionDocumento.TIA_clave')
				->join('TipoDocumento', 'TipoDocumento.TID_claveint', '=', 'AtencionDocumento.TID_clave')
				->join('Producto', 'Producto.Pro_clave', '=', 'AtencionDocumento.Pro_clave')
				->leftjoin('DocumentosDigitales','Atenciones.ATN_clave','=','DocumentosDigitales.ATN_clave')
				->leftjoin('DocumentosDigitales','TipoDocumento.TID_claveint','=','DocumentosDigitales.Arc_tipo')
				//->leftjoin('DocumentosDigitales', 'AtencionDocumento.Tia_clave', '=', 'AtencionDocumento.Pro_clave')				
				->where('Atenciones.ATN_clave','=', $atencion)
				->get();
		*/

        //preparo los archivos disponibles
	    $notaMedica = array();
	    $pase = array();
	    $identificacion = array();
	    $informe = array();
	    $historiaClinica = array();
	    $cuestionario = array();
	    $anexos = array();
	    $todos = array();

	    $nombreExp = strtolower($folio);

	    //qery para captura
	    $sqlCaptura =  "SELECT CIA_claveMV as EMPClave,
						PRO_claveMV as PROClave,
						EXP_nombre as Nombre,
						EXP_paterno as Paterno,
						EXP_materno as Materno,
						Exp_completo as Nombre,
						CASE WHEN DATE_FORMAT(EXP_fechaNac2, '%d/%m/%Y')  = '00-00-0000' THEN NULL ELSE DATE_FORMAT(EXP_fechaNac2, '%d/%m/%Y')  END as FNacimiento,
						EXP_edad as Edad,
						EXP_sexo as Sexo,
						RIE_claveMV as RIEClave,
						RIE_nombre as Riesgo,
						POS_claveMV as POSClave,
						opcion as Posicion,
						EXP_poliza as Poliza,
						EXP_reporte as Reporte,
						EXP_siniestro as Siniestro,
						UNI_claveMV as UNIClave,
						EXP_regCompania as RegCompania,
						EXP_cancelado as Cancelado,
						CASE WHEN DATE_FORMAT(EXP_fecreg, '%d/%m/%Y %H:%i:%s')  = '00-00-0000' THEN NULL ELSE DATE_FORMAT(EXP_fecreg, '%d/%m/%Y %H:%i:%s')  END as FAtencion,
						CASE WHEN DATE_FORMAT(Exp_fecPaseMed, '%d/%m/%Y')  = '00-00-0000' THEN NULL ELSE DATE_FORMAT(Exp_fecPaseMed, '%d/%m/%Y')  END as FExpedicion,
						LOC_claveint as Localidad,
						Exp_inciso as Inciso,
						Exp_orden as Orden,
						'' as Lesion,
						'' as Ajustador,
						'' as Tipo,
						'' as PagoUnidad,
						'' as motivoNoPago,
						REPLACE(REPLACE(ObsNot_expF,char(10),''),char(13),'') as ExpFis,
						UPPER(MED_nombre + ' ' + MED_paterno + ' ' + MED_materno) as Medico,
						REPLACE(REPLACE(LesE_clave,char(10),''),char(13),'') as LesRep,
						REPLACE(REPLACE(ObsNot_obs,char(10),''),char(13),'') as ObsRep,
						REPLACE(REPLACE(ObsNot_diagnosticoRx,char(10),''),char(13),'/') as Dx,
						DATE_FORMAT(EXP_fecreg, '%H:%i:%s') as HAtencion,
						REPLACE(REPLACE(CUR_curaciones ,char(10),''),char(13),'') as Curacion,
						CASE WHEN Expediente.PRO_clave = 4 THEN (CASE Expediente.UNI_clave WHEN 1 THEN 9437 WHEN 2 THEN 9438 WHEN 3 THEN 9439 WHEN 4 THEN 9440 WHEN 5 THEN 9441 WHEN 6 THEN 9442 WHEN 7 THEN 9443 WHEN 86 THEN 9444 WHEN 184 THEN 9445 WHEN 186 THEN  9446  END)
						WHEN ISNULL(MED_claveMV) THEN (CASE Expediente.UNI_clave WHEN 1 THEN 3138 WHEN 2 THEN 3145 WHEN 3 THEN 3140 WHEN 4 THEN 3139 WHEN 5 THEN 3146 WHEN 6 THEN 5082 WHEN 7 THEN 3141 WHEN 86 THEN 5373 WHEN 184 THEN 5343 WHEN 186 THEN  5417 END)                    
						ELSE Med_claveMV END as MedicoMV,
						TRI_claveMV as triage,
						Compania.CIA_clave as cliente
			FROM Expediente INNER JOIN Compania ON Compania.CIA_clave=Expediente.CIA_clave
			                              INNER JOIN Unidad     ON  Unidad.UNI_clave=Expediente.UNI_clave
			                              LEFT   JOIN RiesgoAfectado ON RiesgoAfectado.RIE_clave = Expediente.RIE_clave
			                              LEFT   JOIN NotaMedica ON NotaMedica.EXP_folio = Expediente.EXP_folio
			                              LEFT   JOIN PosicionAcc ON PosicionAcc.id = NotaMedica.Posicion_clave
			                              LEFT   JOIN ObsNotaMed ON ObsNotaMed.EXP_folio = Expediente.EXP_folio
			                              LEFT   JOIN Medico ON Medico.USU_login = NotaMedica.USU_nombre
			                              LEFT   JOIN Curaciones ON Curaciones.EXP_folio = Expediente.EXP_folio
			                              LEFT   JOIN Producto ON Producto.PRO_clave = Expediente.PRO_clave
			                              LEFT   JOIN TriageAutorizacion on TriageAutorizacion.Triage_id = Expediente.Exp_triageActual                          
			WHERE Expediente.EXP_folio = '$folio' ";

	    $resultado['captura'] = DB::connection('mysql')->select($sqlCaptura)[0];

	    //suministros
		$sqlSuministros = "SELECT Rx_Nombre as Nombre,
						1 as Cantidad,
						'' as Motivo,
						1 as Tipo
					FROM RxSolicitados INNER JOIN Rx ON RxSolicitados.Rx_clave = Rx.Rx_Clave
					WHERE EXP_folio = '$folio'
					UNION
					SELECT Sym_medicamento as Nombre,
					       Nsum_Cantidad as Cantidad,
					       '' as Motivo,
					       2 as Tipo
					FROM NotaSuministro INNER JOIN SymioCuadroBasico ON NotaSuministro.SUM_clave = SymioCuadroBasico.Clave_producto
					WHERE EXP_folio = '$folio'
					UNION
					SELECT  Sym_medicamento as Nombre,
					       Notor_Cantidad as Cantidad,
					       '' as Motivo,
					       3 as Tipo
					FROM NotaOrtesis INNER JOIN SymioCuadroBasico ON NotaOrtesis.ORT_clave = SymioCuadroBasico.Clave_producto
					WHERE EXP_folio = '$folio'
					UNION
					SELECT ESTU_nombre as Nombre,
					       0 as Cantidad,
					       REPLACE(REPLACE(EstuS_Obs,char(10),''),char(13),'') as Motivo,
					       4 as Tipo
					FROM EstSolicitados INNER JOIN Estudios ON EstSolicitados.Estu_clave = Estudios.Estu_clave
					WHERE EXP_folio = '$folio'";
		
		$resultado['suministros'] = DB::connection('mysql')->select($sqlSuministros);    

	    $sqlArchivos = "SELECT Arc_clave, Arc_archivo,TID_nombre,TID_claveint FROM DocumentosDigitales
	                    INNER JOIN TipoDocumento on TipoDocumento.TID_claveint = DocumentosDigitales.Arc_tipo
	                    WHERE REG_folio = '$folio' ";


	    $archivos = DB::connection('mysql')->select($sqlArchivos);

	    //obtenemos datos para la busqueda de la factura
	    foreach ($archivos as $archivo) {

	        $ruta = $archivo->Arc_archivo;
	        $clave = $archivo->TID_claveint;

	        if($clave == 18) array_push($notaMedica, $ruta);
	        if($clave == 17) array_push($historiaClinica, $ruta);
	        if($clave == 15) array_push($cuestionario, $ruta);
	        if($clave == 1) array_push($pase, $ruta);
	        if($clave == 16) array_push($identificacion, $ruta);
	        if($clave == 2) array_push($informe, $ruta);
	        if($clave == 4) array_push($anexos, $ruta);

	        array_push($todos, $ruta);

	    }

	    $hospitalario = HospitalarioWeb::where('EXP_folio',$folio)->get();

	    $archivos['notaMedica'] = $notaMedica;
	    $archivos['hisotriaClinica'] = $historiaClinica;
	    $archivos['cuestionario'] = $cuestionario;
	    $archivos['pase'] = $pase;
	    $archivos['identificacion'] = $identificacion;
	    $archivos['informe'] = $informe;
	    $archivos['otros'] = $anexos;
	    $archivos['todos'] = $todos;

	    $resultado['hospitalario'] = $hospitalario;
	    $resultado['archivos'] = $archivos;

	    return $resultado;

	}

	public function doctosValidados(){

		$listDoctosValidados 	= Input::get('doctos');	
		$usr 					= Input::get('user');
		$datosExp				= Input::get('expediente');	
		$usr = str_replace('"','',$usr);		

		$titulo 		= 'Validacion de documentos digitales';
		$descripcion 	= '';
		$atencionEstatus= 2;
		$atencion=0;
		$miFolio = array();
		$mensaje='';
		
		$contTipoDoc=0;
		try{
			foreach ($listDoctosValidados as $dato) {
				$atencion=$dato['ATN_clave'];
				if($dato['Arc_estatus']!=''||$dato['Arc_estatus']!=null){
					if($dato['ATD_estatus']==1){
						$dato['Arc_estatus']=1;
						$dato['Arc_motivo']='';
						$dato['ATD_motivo']='';						
					}
					if($dato['Arc_estatus']==1)  $dato['Arc_motivoCat']=0;
					$sqlDoctos="UPDATE DocumentosDigitales SET Arc_estatus = ".$dato['Arc_estatus'].", Arc_motivo ='".$dato['Arc_motivo']."', Arc_motivoCat=".$dato['Arc_motivoCat'].", Arc_autorizado=1, USU_autorizo='".$usr."' WHERE ATN_clave=".$dato['ATN_clave']." AND Arc_tipo=".$dato['TID_clave']." and Arc_clave='".$dato['Arc_clave']."'";
						$archivos = DB::connection('mysql')->update($sqlDoctos);
					if ($dato['ATD_estatus']>0) {							
						$sqlDoctosAtn="UPDATE AtencionTipoDocumento SET ATD_estatus = ".$dato['ATD_estatus'].", ATD_motivo ='".$dato['ATD_motivo']."' WHERE ATN_clave=".$dato['ATN_clave']." AND TID_clave=".$dato['TID_clave'];
						$archivos = DB::connection('mysql')->update($sqlDoctosAtn);	

						if($dato['ATD_estatus']==2){
							/**********    modulo de creación de tickets ***************************/
							switch ($dato['TID_clave']) {
								case '1':
									$subcategoria=1;
									break;
								case '18':
									$subcategoria=2;
									break;
								case '15':
									$subcategoria=3;
									break;
								case '16':
									$subcategoria=4;
									break;
								default:
									$subcategoria=5;
									break;
							}

							$existe= Tickets::where('Exp_folio','=',$datosExp['folio'])
							->where('TSub_clave','=',$subcategoria)
							->select('TSub_clave')
							->get();
							$contTicket = count($existe);
							if($contTicket==0){

								$folioin  = Tickets::max('TSeg_clave');
								$folioin++;
								$ticket = new Tickets;

								$ticket->TSeg_clave = $folioin;
								$ticket->Exp_folio = $datosExp['folio'];
								$ticket->TSeg_etapa = 1;
								$ticket->TCat_clave = 1;
								$ticket->TSub_clave = $subcategoria;
								$ticket->TSeg_obs = $dato['ATD_motivo'];
								$ticket->TStatus_clave = 1;
								$ticket->Uni_clave = $datosExp['UniClave'];
								$ticket->Cia_clave = $datosExp['claveEmpresa'];
								$ticket->TSeg_asignado = '';
								$ticket->TSeg_asignadofecha = '';
								$ticket->TSeg_fechareg = date('Y-m-d H:i:s');
								$ticket->TSeg_fechaactualizacion = date('Y-m-d H:i:s');
								$ticket->Usu_registro = $usr;
								$ticket->Usu_actualiza = $usr;

								$ticket->save();
									$nota = new Ticketnotas;
						            $nota->TSeg_clave =  $folioin;
						            $nota->Exp_folio =  $datosExp['folio'];
						            $nota->TN_descripcion =  $dato['ATD_motivo'];
						            $nota->TN_fechareg =  date('Y-m-d H:i:s');
						            $nota->Usu_registro =  $usr;

						            $nota->save();				            				        
					        }
							/************ fin de módulo de tickets    ******************************/
						}
					}
					if($dato['ATD_requerido']==1){
						if($dato['ATD_estatus']==2||$dato['ATD_estatus']==0) $atencionEstatus=3;
						//elseif($dato['ATD_estatus']==1)$atencionEstatus=1;										
					}
					$estatus='';
					if($dato['Arc_estatus']==1) {
						$descripcion ='Documento aprobado: '.$dato['TID_nombre'];
						$estatus='aprobado';
					}
					elseif($dato['Arc_estatus']==2){
						$descripcion ='Documento rechazado: '.$dato['TID_nombre'].' Motivo: '.$dato['Arc_motivo'];
						$estatus='rechazado';						
					}else{
						$atencionEstatus=3;
						$mensaje='Faltan documentos requeridos';
					}
					$titulo= 'Documento '.$estatus.': '.$dato['TID_nombre'];
					if($dato['Arc_autorizado']==0){
						$descripcion ='Documento '.$estatus.': '.$dato['TID_nombre'].' Motivo: '.$dato['Arc_motivo'];
						$historial = new Historial;
						$historial->titulo=$titulo;
						$historial->descripcion = $descripcion;
						$historial->folio = $datosExp['folio'];
						$historial->usuario = $usr;
						$historial->etapa = 1;
						$historial->entrega = 1;
						$historial->guardar();
					}
				}else{
					$atencionEstatus=3;
					$mensaje='Faltan documentos requeridos';
				}				
			}	
			$sqlDoctosAtn="UPDATE Atenciones SET ATN_estatus = ".$atencionEstatus.", ATN_mensaje='".$mensaje."' WHERE ATN_clave=".$atencion;
			$archivos = DB::connection('mysql')->update($sqlDoctosAtn);

			if($atencionEstatus==3){							 	
	    		$fecha = date('Y-m-d H:i');
				$qry = "UPDATE Expediente SET  Exp_rechazado = 1, USU_rechazo = '".$usr."', Exp_fechaRechazo ='".$fecha."'  where Exp_folio = '".$datosExp['folio']."'";
				$archivos = DB::connection('mysql')->update($qry);
			}elseif ($atencionEstatus==2) {	
				$tickets= Tickets::where('Exp_folio',$datosExp['folio'])->get();
				if($tickets){
					foreach ($tickets as $dato) {
			 			$nofol = $dato['TSeg_clave'];
			 			$pase = Tickets::find($nofol);
			 			$pase->TStatus_clave = 7;
			 			$pase->save();
			 		} 
		 		}											

				if($datosExp['claveEmpresa']==7){
					$datos = FolioWeb::find($datosExp['folio']);
					$datos->Exp_solicitado = 1;
					$datos->USU_solicito = $usr;
					$datos->Exp_fechaSolicitud = date('Y-m-d H:i');
					$datos->save();
				}elseif($datosExp['claveEmpresa']==19){	
				$fecha = date('Y-m-d H:i');
				$qry = "UPDATE Expediente SET Exp_solicitado = 0,EXP_rechazado = 0, EXP_autorizado = 1, USU_autorizo = '".$usr."', Exp_fechaAutorizado = '".$fecha."'  where Exp_folio = '".$datosExp['folio']."'";
				$archivos = DB::connection('mysql')->update($qry);


				$importe = ExpedienteInfo::find($datosExp['folio'])->EXP_costoEmpresa;
				$iva = round($importe * 0.16, 2);
	    		$total = $importe  + $iva;

				$query = "SELECT IFNULL( MAX(FAC_folio) , 0) AS numero, MAX(FAC_fecha) as fecha FROM Factura";
				$existe = DB::connection('mysql')->select($query)[0];				

			 	$facFolio = $existe->numero + 1;
  				$fechaIni = $existe->fecha;

  				if ($fechaIni != null) {
        
			        $ultimaFechaFac = date("Y-m-d", strtotime( $fechaIni ));
			        $ultimaHoraFac = date('H', strtotime( $fechaIni ) ) ;
			        $ultimaSecFac = date('i', strtotime( $fechaIni ) )  + 1;
			        $fechaActual = date('Y-m-d');

			        if ( $fechaActual == $ultimaFechaFac && $ultimaHoraFac >= '21' ) {
			          	$fechaFactura = date('Y-m-d') . " " . $ultimaHoraFac . ":" . $ultimaSecFac;
			        }else{
			          	$fechaFactura = date('Y-m-d') . ' 21:00';
			        }

			      // si no reseteamos la fecha APARTIR DE las 9 de la noche 
				}else{
					$fechaFactura = date('Y-m-d') . ' 21:00';
				}	 

				$factura = new Factura;				
				$factura ->CIA_clave  		= $datosExp['claveEmpresa'];
				$factura ->FAC_serie  		= 'FW';
				$factura ->FAC_folio  		= $facFolio;
				$factura ->FAC_fecha  		= $fechaFactura;
				$factura ->FAC_global 		= 0;
				$factura ->FAC_importe		= $importe;
				$factura ->FAC_iva    		= $iva;
				$factura ->FAC_total  		= $total;
				$factura ->FAC_saldo  	 	= $total;
				$factura ->FAC_fechaReg		= $fecha;
				$factura ->USU_registro		= $datosExp['usrMV'];
				$factura ->save();

				$noFactura  = Factura::max('FAC_clave');

				$facExp = new FacturaExpediente;
				$facExp ->Exp_folio 		= $datosExp['folio'];
				$facExp ->FAC_clave 		= $noFactura;
				$facExp ->save();
			}
		}
			$respuesta = array('respuesta' => 'exito'); 		
	 	}catch(Exception $e){
	 		$respuesta = array('respuesta' => $e->getmessage());	
	 	}
 	return Response::json($respuesta);
	}

	public function solicitarAutorizacion(){

		$folio = Input::get('folio');
		$usuario = Input::get('usuario');

		$datos = FolioWeb::find($folio);
		$datos->Exp_solicitado = 1;
		$datos->USU_solicito = $usuario;
		$datos->Exp_fechaSolicitud = date('Y-m-d H:i');
		$datos->save();

		$respuesta = array('respuesta' => 'Solicitud Generada Correctamente');

 		return Response::json($respuesta);

	}

	public function solicitarAutorizacionRechazos(){

		$datos = Input::get('folio');
		$usuario = Input::get('usuario');

		foreach ($datos as $dato){

			$folio = $dato['Exp_folio'];
			$reporte = $dato['Exp_reporte'];
			$poliza = $dato['Exp_poliza'];
			$siniestro = $dato['Exp_siniestro'];

			$claveSiniestro = Pase::find($folio)->DAS_claveint;

			$datosSiniestro = DatosSiniestro::find($claveSiniestro);
			$datosSiniestro->DAS_poliza = $poliza;
			$datosSiniestro->DAS_siniestro = $siniestro;
			$datosSiniestro->DAS_reporte = $reporte;
			$datosSiniestro->save();

			$datos = FolioWeb::find($folio);
			$datos->Exp_solicitado = 1;
			$datos->Exp_rechazado = 0;
			$datos->Exp_reporte = $reporte;
			$datos->Exp_poliza = $poliza;
			$datos->Exp_siniestro = $siniestro;
			$datos->USU_solicito = $usuario;
			$datos->Exp_fechaSolicitud = date('Y-m-d H:i');
			$datos->save();

		}

		$respuesta = array('respuesta' => 'Solicitud(es) Generada(s) Correctamente');

 		return Response::json($respuesta);

	}

	public function actualizaFolio(){

		$folio = Input::get('folio');

		$datos = FolioWeb::find($folio);
		$datos->Exp_poliza = Input::get('poliza');
		$datos->Exp_siniestro = Input::get('siniestro');
		$datos->Exp_reporte = Input::get('reporte');
		$datos->Exp_completo = Input::get('lesionado');
		$datos->save();

		$respuesta = array('respuesta' => 'Cambios efectuados Correctamente');

 		return Response::json($respuesta);
 		
	}

	public function captura(){

		$datosCaptura = Input::get('captura');
		$datosSuministros = Input::get('suministros');
		$folio = Input::get('folio');
		$usuario = Input::get('usuario');

		$lesionado = $datosCaptura['Nombre'];
		$ajustador = $datosCaptura['Ajustador'];
		$unidad = $datosCaptura['UNIClave'];
		$cliente = $datosCaptura['EMPClave'];	
		$producto = $datosCaptura['PROClave'];
		$reporte = $datosCaptura['Reporte'];
		$poliza = $datosCaptura['Poliza'];
		$inciso = $datosCaptura['Inciso'];
		$siniestro = $datosCaptura['Siniestro'];
		$localidad = $datosCaptura['Localidad'];
		$noOrden = $datosCaptura['Orden'];
		$folioElect = $datosCaptura['RegCompania'];
		$sexo = $datosCaptura['Sexo'];
		$edad = $datosCaptura['Edad'];
		$fechaNac = $datosCaptura['FNacimiento'];
		$fechaExpedicion = $datosCaptura['FExpedicion'];
		$riesgo = $datosCaptura['RIEClave'] == null ? 3 : $datosCaptura['RIEClave'];
		$posicion = $datosCaptura['POSClave'] == null ? 4 : $datosCaptura['POSClave'];
		$primaria = $datosCaptura['Lesion']['LES_clave'];
		$lesempresa = $datosCaptura['Lesion']['LES_claveEmp'];
		// $tabUnidad = $datosCaptura['tabuladorUnidad'];////// se agrego manual
		$tabEmpresa = $datosCaptura['claveTabulador'];
		$obsRep = $datosCaptura['ObsRep'];
		$tratante = $datosCaptura['MedicoMV'];
		$fechaconsulta = $datosCaptura['FAtencion'];
		$descmed = $datosCaptura['Dx'];
		$lesionunidad = $datosCaptura['Dx'];
		$triage = $datosCaptura['triage'];

		$tipo = $datosCaptura['Tipo'];//////falta agregar html formato o tiket
		$pagoUnidad = $datosCaptura['pagoUnidad'];//////falta agregar html
		$motivoNoPago = $datosCaptura['motivoNoPago'];//////falta agregar html

		$fecha = date('d/m/Y');

		///captura el pase 
		$sql = "EXEC MV_CAP_GuardaCapturaWeb  
			@folio = '$folio',
			@lesionado = '$lesionado',
			@unidad = $unidad,
			@cliente = $cliente,
			@fechaRecep = '$fecha',
			@usuario = $usuario,
			@producto = $producto,
			@reporte = '$reporte',
			@poliza = '$poliza',
			@inciso = '$inciso',
			@siniestro = '$siniestro',
			@asegurado = '',
			@ajustador = $ajustador,
			@localidad = $localidad,
			@matricula = '',
			@ROC = 0,
			@autorizacion = '',
			@noOrden = '$noOrden',
			@folioElect = '$folioElect',
			@sexo = '$sexo',
			@edad = $edad,
			@fechaNac = '$fechaNac',
			@fechaExpedicion = '$fechaExpedicion',
			@lesionesAparentes = '',
			@observaciones = '',
			@riesgo = $riesgo,
			@posicion = $posicion,
			@tipo = '$tipo',
			@clienteRef = '',
			@ref = '',
			@factor = 1,
			@iva = 0.16,
			@primaria = '$primaria',
			@lesempresa = '$lesempresa',
			@tabUnidad = 21,
			@tabEmpresa = $tabEmpresa,
			@deducible = 0,
			@identificacion = '',
			@obsRep = '$obsRep',
			@semanas = 0,
			@tratante = $tratante,
			@fechaconsulta = '$fechaconsulta',
			@secundaria = NULL,
			@terciaria = NULL,
			@descmed = '$descmed',
			@lesionunidad = '$lesionunidad',
			@lesionesPost = '',
			@expFisica = '',
			@pagoUnidad = $pagoUnidad,
			@motivoNoPago = '$motivoNoPago',
			@curacion = '',
			@facExpress = 0,
			@escolaridad = NULL,
			@colegio = NULL,
			@triage = $triage,
			@validado = 0,
			@bitacora = '',
			@tiempoCap = 0,
			@sesEnv = 0,
			@cant1 = 0,
			@tabEmp2 = 1,
			@cant2 = 0,
			@tabEmp3 = 1,
			@cant3 = 0,
			@consulta = 0,
			@facExp = 0,
			@errores = 0,
			@SE1Clave = 0
		";

		DB::statement($sql);

		//verificamos que sea unidad propia para captura de suministros
		$unidadpropia = Unidad::find($unidad)->UNI_propia;

		if ($unidadpropia == 1) {
			
			//obtenemos la clave de suministro aplicado
			$claveSuministro = Pase::join('Etapa1','Etapa1.PAS_folio','=','Pase.PAS_folio')
								   ->where('Pase.PAS_folio',$folio)
								   ->first();


			foreach ($datosSuministros as $suministro) {
				
				$tipoSuministro = $suministro['Tipo'];
				
				//radiografias
				if ($tipoSuministro == 1) {
					DB::table('Sum1Rad')->insert(
					    array( 
					    	'SE1_claveint' => $claveSuministro->SE1_claveint, 
					    	'Rad_claveint' => 4,
					    	'S1R_cantidad' => $suministro['Cantidad'],
					    	'S1R_descripcion' => $suministro['Nombre']
					    )
					);
				
				//medicamentos	
				}elseif ($tipoSuministro == 2) {
					# code...
					DB::table('Sum1MDI')->insert(
					    array( 
					    	'SE1_claveint' => $claveSuministro->SE1_claveint, 
					    	'MDI_claveint' => 5,
					    	'S1M_cantidad' => $suministro['Cantidad'],
					    	'S1M_descripcion' => $suministro['Nombre']
					    )
					);

				//ortesis
				}elseif ($tipoSuministro == 3) {
					
					DB::table('Sum1Ort')->insert(
					    array( 
					    	'SE1_claveint' => $claveSuministro->SE1_claveint, 
					    	'ORT_claveint' => 4,
					    	'S1O_Nombre' => $suministro['Nombre'],
					    	'S1O_descripcion' => ''
					    )
					);

				}
			
			}

		}


		$datosCentrales =  DB::select(" EXEC MV_REW_Captura_folio  @folio='$folio' " )[0];

		$registro = new ExpedienteInfo;
		$registro->EXP_folio = $datosCentrales->Folio;
		$registro->EXP_lesionado = $datosCentrales->Lesionado;
		$registro->CIA_clave = $datosCentrales->ClienteWeb;
		$registro->UNI_clave = $datosCentrales->UnidadWeb;
		$registro->EXP_fechaExpedicion = $datosCentrales->FExpedicion;
		$registro->EXP_fechaCaptura = $datosCentrales->FCaptura;
		$registro->EXP_fechaAtencion = $datosCentrales->FAtencion;
		$registro->EXP_poliza = $datosCentrales->Poliza;
		$registro->EXP_siniestro = $datosCentrales->Siniestro;
		$registro->EXP_reporte = $datosCentrales->Reporte;
		$registro->EXP_orden = $datosCentrales->NoOrden;
		$registro->POS_claveint = $datosCentrales->PosicionWeb;
		$registro->RIE_claveint = $datosCentrales->RiesgoWeb;
		$registro->EXP_ajustador = $datosCentrales->Ajustador;
		$registro->EXP_medico = $datosCentrales->Medico;
		$registro->EXP_obsAjustador = $datosCentrales->LesionesA;
		$registro->LES_primaria = $datosCentrales->LPrimaria;
		$registro->LES_empresa = $datosCentrales->LEmpresa;
		$registro->EXP_diagnostico = $datosCentrales->DescMedica;
		$registro->FAC_folioFiscal = $datosCentrales->FolFiscal;
		$registro->FAC_serie = $datosCentrales->Serie;
		$registro->FAC_folio = $datosCentrales->Factura;
		$registro->FAC_fecha = $datosCentrales->FFactura;
		$registro->FAC_importe = $datosCentrales->Importe;
		$registro->FAC_iva = $datosCentrales->IVA;
		$registro->FAC_total = $datosCentrales->Total;
		$registro->FAC_saldo = $datosCentrales->Saldo;
		$registro->FAC_pagada = $datosCentrales->Pagada;
		$registro->FAC_fechaPago = $datosCentrales->FPagoFac;
		$registro->FAC_ultimaAplicacion = $datosCentrales->FUltApl;
		$registro->EXP_fechaRegWeb = $datosCentrales->FExp;
		$registro->EXP_costoEmpresa = $datosCentrales->CostoEmpresa;

		$registro->save();

		$respuesta = array('respuesta' => 'Folio Capturado Correctamente');

 		return Response::json($respuesta);

	}

	public function capturaCuestionario(){

		DB::select();

	}

	public function capturaAjustador(){

		$ajustador = new Ajustador;

		$ajustador->AJU_nombre = Input::get('nombre');
		$ajustador->EMP_claveint = Input::get('cliente');
		$ajustador->AJU_clavease = '';
		$ajustador->save();

		return $ajustador->AJU_claveint;

	}

	public function rechazados(){

		$id = Input::get('cliente');
		$fechaini = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechaini') ))) . ' 00:00:00';
		$fechafin = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechafin') ))) . ' 23:59:59';

		//listado de folios x autorizar
	    $sqlRechazado = "SELECT Expediente.Exp_folio, UNI_nombreMV, Expediente.Exp_poliza,
	            Expediente.Exp_siniestro,Expediente.Exp_reporte,Exp_motivoRechazo,Exp_fechaRechazo,
	            Exp_completo, Exp_fecreg, Cia_nombrecorto , RIE_nombre
	            EXP_fechaCaptura,EXP_costoEmpresa,Triage_nombre,Exp_fechaSolicitud,DATEDIFF(Exp_fechaSolicitud,Exp_fecreg) as semaforo
	            FROM Expediente
	                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave
	                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
	                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave
	                left join TriageAutorizacion on TriageAutorizacion.Triage_id = Expediente.Exp_triageActual
	                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
	                left join ExpedienteInfo on ExpedienteInfo.Exp_folio = Expediente.Exp_folio
	            WHERE Exp_fecreg BETWEEN '$fechaini 00:00:00' and '$fechafin 23:59:59' and Compania.Cia_clave in ($id)  and Exp_FE = 1 and Unidad.Uni_clave <> 8   AND EXP_autorizado = 0 AND Exp_solicitado = 1 AND Exp_rechazado = 1  and Exp_cancelado = 0";

	    return DB::connection('mysql')->select($sqlRechazado);
	}

	public function solicitados(){

		$id = Input::get('cliente');
		$fechaini = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechaini') ))) . ' 00:00:00';
		$fechafin = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechafin') ))) . ' 23:59:59';

		//listado de folios x autorizar
	    $sqlXAutorizar = "SELECT Expediente.Exp_folio, UNI_nombreMV, Expediente.Exp_poliza,
	            Expediente.Exp_siniestro,Expediente.Exp_reporte,
	            Exp_completo, Exp_fecreg, Cia_nombrecorto , RIE_nombre
	            EXP_fechaCaptura,EXP_costoEmpresa,Triage_nombre,Exp_fechaSolicitud,DATEDIFF(Exp_fechaSolicitud,Exp_fecreg) as semaforo
	            FROM Expediente
	                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave
	                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
	                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave
	                left join TriageAutorizacion on TriageAutorizacion.Triage_id = Expediente.Exp_triageActual
	                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
	                left join ExpedienteInfo on ExpedienteInfo.Exp_folio = Expediente.Exp_folio
	            WHERE Exp_fecreg BETWEEN '$fechaini 00:00:00' and '$fechafin 23:59:59' and Compania.Cia_clave in ($id)  and Exp_FE = 1 and Unidad.Uni_clave <> 8   AND EXP_autorizado = 0 AND Exp_solicitado = 1 AND Exp_rechazado = 0  and EXP_cancelado = 0  AND Expediente.Exp_fecreg >= '2016-02-08 00:00:00'" ;

	    return DB::connection('mysql')->select($sqlXAutorizar);
	}

	public function autorizados(){

		$id = Input::get('cliente');
		$fechaini = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechaini') ))) . ' 00:00:00';
		$fechafin = date('Y-m-d', strtotime(str_replace('/', '-', Input::get('fechafin') ))) . ' 23:59:59';

		//listado de folios x autorizar
	    $sqlAutorizadoNoFac = "SELECT Expediente.Exp_folio, UNI_nombreMV, Expediente.Exp_poliza,
	            Expediente.Exp_siniestro,Expediente.Exp_reporte,
	            Exp_completo, Exp_fecreg, Cia_nombrecorto , RIE_nombre,Exp_fechaAutorizado
	            FROM Expediente
	                inner join Unidad on Unidad.Uni_clave = Expediente.Uni_clave
	                inner join Localidad on Unidad.LOC_claveint = Localidad.LOC_claveint
	                inner join Compania on Compania.Cia_clave = Expediente.Cia_clave
	                left join RiesgoAfectado on RiesgoAfectado.RIE_clave = Expediente.RIE_clave
	                left join ExpedienteInfo on ExpedienteInfo.Exp_folio = Expediente.Exp_folio
	            WHERE Exp_fecreg BETWEEN '$fechaini 00:00:00' and '$fechafin 23:59:59' and Compania.Cia_clave in ($id) and Exp_FE = 1 and Unidad.Uni_clave <> 8  and Exp_autorizado = 1  and EXP_cancelado = 0 " ;

	    return DB::connection('mysql')->select($sqlAutorizadoNoFac);

	}
}
