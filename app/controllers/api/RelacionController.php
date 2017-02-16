<?php

ini_set('memory_limit', '-1');

class RelacionController extends BaseController {

	public function conecta_ftp(){

		try{

		//Permite conectarse al Servidor FTP
		$ftp_server = "172.17.10.9";
		$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
		$login = ftp_login($ftp_conn, 'admin', 'Med1$4_01i0');
		return $ftp_conn; //Devuelve el manejador a la función

		} catch (Exception $e){

		  echo 'Excepción capturada: ',  $e->getMessage(), "\n";

		}
	}

	public function SubirCFDI($archivo,$archivo,$clave,$usucarpeta){

	  // // $archivo_remoto = "CFDCGastos/Facturas/$archivo";
		
	  $anio = date('Y'); 
	  $mes = date('m'); 
	  $ruta = "web/FacturasPagos/";
	  $diranio =  "web/FacturasPagos/$anio";
	  $dirmes =   "web/FacturasPagos/$anio/$mes";
	  $dirfinal = "web/FacturasPagos/$anio/$mes/$clave";

	  	$ruta = FTP::connection('connection2')->getDirListing($ruta);
		$rutaanio = FTP::connection('connection2')->getDirListing($diranio);
		$rutames = FTP::connection('connection2')->getDirListing($dirmes);
		$rutafinal = FTP::connection('connection2')->getDirListing($dirfinal);

	    // ftp_chmod($id_ftp,0644,$ruta);
		if (count($rutaanio) == 0){

			FTP::connection('connection2')->makeDir($diranio);
		}

		if (count($rutames) == 0){
			FTP::connection('connection2')->makeDir($dirmes);
		}

		if (count($rutafinal) == 0){
			FTP::connection('connection2')->makeDir($dirfinal);
		}

	    $archivo_remoto = "web/FacturasPagos/$anio/$mes/$clave/$archivo";
	    $archivo_local =  "FacturasPagos/".$usucarpeta."/$archivo";

	    FTP::connection('connection2')->uploadFile($archivo_local,$archivo_remoto, FTP_ASCII);

	    $archivosFinal = FTP::connection('connection2')->getDirListing($ruta);

		// print_r($archivosFinal);
	    FTP::disconnect('connection2');

	}

    public function listadoOrdenes($id){


    $norelacionados = DB::table ('RelacionPago')       
                        ->join('ordenPago', 'RelacionPago.FLD_claveint', '=', 'ordenPago.FLD_claveint')                                       
                        ->join('FlujoDoc', 'RelacionPago.FLD_claveint', '=', 'FlujoDoc.FLD_claveint')
                        ->join('Documento', 'FlujoDoc.DOC_claveint', '=', 'Documento.DOC_claveint')
                        ->join('Empresa', 'Empresa.EMP_claveint', '=', 'Documento.EMP_claveint')
                        ->join('Unidad', 'Unidad.UNI_claveint', '=', 'Documento.UNI_claveint')
                        ->join('Usuario', 'Usuario.USU_claveint', '=', 'FlujoDoc.USU_activo')
                        ->join('AreaOperativa', 'AreaOperativa.ARO_claveint',  '=', 'FlujoDoc.ARO_activa')
                        ->join('Pase',  'Pase.PAS_folio', '=', 'Documento.DOC_folio')
                        ->join('DatosSiniestro', 'DatosSiniestro.DAS_claveint', '=', 'Pase.DAS_claveint')
                        ->join('Producto', 'Producto.PRO_claveint', '=', 'DatosSiniestro.PRO_claveint')
                        ->join('Etapa1',  'Etapa1.PAS_folio', '=', 'Pase.PAS_folio')
                        ->join('Expediente',  'Expediente.ET1_claveint', '=', 'Etapa1.ET1_claveint')
                        ->join('Reporte', 'Reporte.REP_claveint', '=', 'Etapa1.REP_claveint')
                        ->join('Lesion', 'Lesion.LES_clave', '=', 'Reporte.LES_primaria')
                        ->join('TipoLesion', 'TipoLesion.TLE_claveint', '=', 'Lesion.TLE_claveint')
                        ->join('HistorialFlujo',  'HistorialFlujo.DOC_claveint', '=', 'Documento.DOC_claveint')
                        ->leftjoin('Relacion', 'Relacion.REL_clave', '=', 'RelacionPago.REL_clave')
                        ->leftjoin('Triage',  'Triage.TRI_claveint', '=', 'Pase.TRI_claveint')
                        ->select(DB::raw('USU_nombre as USUNombre, 
											PRO_abreviatura as Producto,
											TRI_nombre as Triage,
											EMP_nombrecorto as Cliente, 
											UNI_nombrecorto as Unidad,
											Documento.UNI_claveint as claveunidad, 
											UNI_ref as referencia,
											RelacionPago.DOC_folio as Folio, 
											DOC_lesionado as Lesionado, 
											DOC_etapa as Etapa, 
											DOC_numeroentrega as Entrega, 
											REP_fechaconsulta as FAtencion,
											FLD_formaRecep as FormaRecep,
											DOC_originalfechacaptura as fechaRecepcion,             
											his_fecha as FechaRecepPag,
											TLE_nombre as Tipo,
											LES_nombre as Lesi,
											Relacion.REL_clave as Relacion,
											REL_fecha as FRelacion,
											REL_fPagada as FRelPago,
											REL_fPagadaReg as FRelPagoReg,
											CASE WHEN PAS_fechaPago IS NULL THEN 0 ELSE 1 END as PasC,  
											PAS_fechaPago  as FPasCobrado,
											CONVERT(varchar,RPA_pago,1) as Pago,
											CASE DOC_etapa WHEN 1 THEN CONVERT(varchar, EXP_pagoET1,1) WHEN 2 THEN CONVERT(varchar, EXP_pagoET2, 1) ELSE CONVERT(varchar, EXP_pagoET3, 1) END as Tabulador,
											EXP_reserva_inicial_Tot as Reserva,
											RPA_factura as FacturaRelacion,                                              
											DOC_factura as FacDoc,
											CASE WHEN REL_fPagada IS NULL THEN 0 ELSE 1 END as RelP, 
											REL_pagada as Pagado,
											PAS_pagado as Cobrado,
											PAS_penalizado as penalizado,
											Unidad.UNI_claveint as claveunidad,
											UNI_rfc as rfc'))
                        ->where('his_area', '=', 6)
                        ->where('Documento.UNI_claveint', '=',$id)
                        ->whereNull('RelacionPago.REL_clave')
                        ->distinct()
                        ->get();


    return $norelacionados;
	}



	public function buscaxProveedor($id){


    $norelacionados = DB::table ('RelacionPago')       
                        ->join('ordenPago', 'RelacionPago.FLD_claveint', '=', 'ordenPago.FLD_claveint')                                       
                        ->join('FlujoDoc', 'RelacionPago.FLD_claveint', '=', 'FlujoDoc.FLD_claveint')
                        ->join('Documento', 'FlujoDoc.DOC_claveint', '=', 'Documento.DOC_claveint')
                        ->join('Empresa', 'Empresa.EMP_claveint', '=', 'Documento.EMP_claveint')
                        ->join('Unidad', 'Unidad.UNI_claveint', '=', 'Documento.UNI_claveint')
                        ->join('Usuario', 'Usuario.USU_claveint', '=', 'FlujoDoc.USU_activo')
                        ->join('AreaOperativa', 'AreaOperativa.ARO_claveint',  '=', 'FlujoDoc.ARO_activa')
                        ->join('Pase',  'Pase.PAS_folio', '=', 'Documento.DOC_folio')
                        ->join('DatosSiniestro', 'DatosSiniestro.DAS_claveint', '=', 'Pase.DAS_claveint')
                        ->join('Producto', 'Producto.PRO_claveint', '=', 'DatosSiniestro.PRO_claveint')
                        ->join('Etapa1',  'Etapa1.PAS_folio', '=', 'Pase.PAS_folio')
                        ->join('Expediente',  'Expediente.ET1_claveint', '=', 'Etapa1.ET1_claveint')
                        ->join('Reporte', 'Reporte.REP_claveint', '=', 'Etapa1.REP_claveint')
                        ->join('Lesion', 'Lesion.LES_clave', '=', 'Reporte.LES_primaria')
                        ->join('TipoLesion', 'TipoLesion.TLE_claveint', '=', 'Lesion.TLE_claveint')
                        ->join('HistorialFlujo',  'HistorialFlujo.DOC_claveint', '=', 'Documento.DOC_claveint')
                        ->leftjoin('Relacion', 'Relacion.REL_clave', '=', 'RelacionPago.REL_clave')
                        ->leftjoin('Triage',  'Triage.TRI_claveint', '=', 'Pase.TRI_claveint')
                        ->leftjoin('TipoOrdenPago', 'ordenPago.TIO_id', '=', 'TipoOrdenPago.TIO_id')
                        ->select(DB::raw('USU_nombre as USUNombre, 
											PRO_abreviatura as Producto,
											TRI_nombre as Triage,
											EMP_nombrecorto as Cliente, 
											UNI_nombrecorto as Unidad,
											Documento.UNI_claveint as claveunidad, 
											UNI_ref as referencia,
											Documento.DOC_folio as Folio, 
											DOC_lesionado as Lesionado, 
											DOC_etapa as Etapa, 
											DOC_numeroentrega as Entrega, 
											REP_fechaconsulta as FAtencion,
											FLD_formaRecep as FormaRecep,
											DOC_originalfechacaptura as fechaRecepcion,             
											his_fecha as FechaRecepPag,
											TLE_nombre as Tipo,
											LES_nombre as Lesi,
											Relacion.REL_clave as Relacion,
											REL_fecha as FRelacion,
											REL_fPagada as FRelPago,
											REL_fPagadaReg as FRelPagoReg,
											CASE WHEN PAS_fechaPago IS NULL THEN 0 ELSE 1 END as PasC,  
											PAS_fechaPago  as FPasCobrado,
											CONVERT(varchar,RPA_pago,1) as Pago,
											CASE DOC_etapa WHEN 1 THEN CONVERT(varchar, EXP_pagoET1,1) WHEN 2 THEN CONVERT(varchar, EXP_pagoET2, 1) ELSE CONVERT(varchar, EXP_pagoET3, 1) END as Tabulador,
											EXP_reserva_inicial_Tot as Reserva,
											RPA_factura as FacturaRelacion,                                              
											DOC_factura as FacDoc,
											CASE WHEN REL_fPagada IS NULL THEN 0 ELSE 1 END as RelP, 
											REL_pagada as Pagado,
											PAS_pagado as Cobrado,
											PAS_penalizado as penalizado,
											Unidad.UNI_claveint as claveunidad,
											UNI_rfc as rfc,
											TIO_nombreorden as nombreOrden,
											ORP_foliofiscal as foliofiscal,
											ORP_nombreEmisor as nombreEmisor,
											ORP_rfcemisor as rfcemisor,
											ORP_total as total,
											ORP_factura as foliointerno'))
                        ->where('his_area', '=', 6)
                        ->where('Documento.UNI_claveint', '=',$id)
                        ->whereNull('RelacionPago.REL_clave')
                        ->distinct()
                        ->get();


    return $norelacionados;
	}

	public function insertaRelacion($usuario){

		$folios =  Input::all(); 

		$numrelacion = $folios['numrelacion'];
		$usucarpeta = $folios['usucarpeta'];
		$obs = $folios['observacion'];
		// $tipofactura = $folios['tipofactura'];
		$unidad = $folios['unidad'];
		$totales = $folios['total'];
		$fecha = date('d/m/Y');
		$fechaH = date('Y-m-d H:i:s');
		$fechaH  = date( 'd-m-y H:i:s',strtotime($fecha));

		foreach ($folios['seleccionados'] as $foliodato){
    
            // $importe = $foliodato['importe'];
            if (!isset($foliodato['total'])){
		    	$total = 0;
		    }else{
		    	$total = $foliodato['total'];
		    }

		    if (!isset($foliodato['importe'])){
		    	$importe = 0;
		    }else{
		    	$importe = $foliodato['importe'];
		    }
		    if (!isset($foliodato['subtotal'])){
		    	$subtotal = 0;
		    }else{
		    	$subtotal = $foliodato['subtotal'];
		    }
        }
                                                                                                                                                                                                                             
	 //    $relacion = DB::table('RelacionFiscal')->insert(
		//     array('REL_clave' => $numrelacion, 'REL_global' => $tipofactura, 'REL_completa' => 0, 'REL_aplicada' =>0 , 'REL_editada' => 0)
		// );

	 //    $relacionusuario = DB::table('RelacionUsuarios')->insert(
		//     array('REL_clave' => $numrelacion, 'USU_creo' => $usuario, 'USU_cancelo' => null, 'USU_aplico' => null)
		// );

		// $relacionfechas = DB::table('RelacionFechas')->insert(
		//     array('REL_clave' => $numrelacion, 'RELF_fcreada' => $fecha, 'RELF_fcompleta' => null, 'RELF_fcancelada' => null, 'RELF_faplicada' => null)
		// );

		$sql = "EXEC MV_REL_InsertaRelacion 

						@relacion = '$numrelacion',
						@fechaPago = '$fecha',
						@unidad = '$unidad',
					    @subtotalp = '$importe',
					    @impuestop = '0.00',
					    @totalp = '$totales',
						@subtotal = '$importe',
						@impuesto = '0.00',
						@total = '$totales',
						@observaciones = '$obs',
					    @conFactura = 0,  
						@usuario = '$usuario',
					    @conIVA = 0,
					    @retIVA = 0,
					    @retISR = 0,
					    @impIVA = '0.00',
					    @impISR = '0.00'";
		DB::statement($sql);
		// return DB::select

		$consecutivo = 0;

		foreach ($folios['seleccionados'] as $foliodato){

			$consecutivo+=1;
    
            $claveunidad = $foliodato['claveunidad'];


		    if (!isset($foliodato['descuento'])){
		    	$descuento = 0;
		    }else{
		    	$descuento = $foliodato['descuento'];
		    }

		    if (!isset($foliodato['concepto'])){
		    	$concepto = 0;
		    }else{
		    	$concepto = $foliodato['concepto'];
		    }

		    if (!isset($foliodato['tipotramite'])){
		    	$tipo = 0;
		    }else{
		    	$tipo = $foliodato['tipotramite'];
		    }

		    if (!isset($foliodato['foliofiscal'])){
		    	$foliofiscal = null;
		    }else{
		    	$foliofiscal = $foliodato['foliofiscal'];
		    }

		    if (!isset($foliodato['fechaemision'])){
		    	$fechaemision = 0;
		    }else{
		    	$fechaemision = $foliodato['fechaemision'];
		    }

		    if (!isset($foliodato['importe'])){
		    	$importe = 0;
		    }else{
		    	$importe = $foliodato['importe'];
		    }

		    if (!isset($foliodato['total'])){
		    	$total = 0;
		    }else{
		    	$total = $foliodato['total'];
		    }

		    if (!isset($foliodato['nombreEmisor'])){
		    	$emisor = $foliodato['nombreEmisor'];
		    }else{
		    	$emisor = $foliodato['nombreEmisor'];
		    }


		    if (!isset($foliodato['rfcemisor'])){
		    	$rfcemisor = $foliodato['rfcemisor'];
		    }else{
		    	$rfcemisor = $foliodato['rfcemisor'];
		    }

		    $folio = $foliodato['Folio'];
		    $etapa = $foliodato['Etapa'];
		    $entrega = $foliodato['Entrega'];
		    
		    $llave = $folio.$etapa.$entrega;


		// $folioetapaent = DB::table('FolioEtapaEntrega')->insert(
		//     array('FEE_clave' => $llave, 'FEE_Folio' => $folio, 'FEE_Etapa' => $etapa, 'FEE_entrega' => $entrega)
		// );

		// $cfdi = DB::table('CFDI')->insert(array('CFD_foliofiscal' => $foliofiscal, 'REL_clave' => $numrelacion, 'CFD_fechaemision' => $fechaemision,
  //                                               'CFD_emisor' => $emisor, 'CFD_importe' => $importe, 'CFD_total' => $total, 'CFD_descuento' => $descuento,
  //                                               'CFD_fechasistema' => $fechaH, 'CFD_valido' => NULL));
        $Documento = Documento::where(array('DOC_folio' => $folio,'DOC_etapa' => $etapa, 'DOC_numeroEntrega' => $entrega))->first();
        $DOC_clave = $Documento->DOC_claveint;	

        $claveflujo = Flujo::where(array('DOC_claveint' => $DOC_clave))->first()->FLD_claveint;  

        $tramite = DB::table('ordenPago')->where(array('DOC_claveint' => $DOC_clave,'FLD_claveint' => $claveflujo))
		                                 ->update(array('REL_clave' => $numrelacion));

        $rpaClave = RelacionPago::where(array('FLD_claveint' => $claveflujo))->first()->RPA_claveint;  
	    
		$sql = "EXEC MV_REL_ActualizaPago

					@rpaClave = $rpaClave,
					@relacion = '$numrelacion',
					@referencia = 1,
				    @factura = 0,
				    @importeFac = '$total',
					@siniestro = '',
					@fechaCaptura = '',
					@tipoLesion = '1',
					@diagnostico = '',
					@pago = '$total',
					@lesionado = '',
					@statusFac = 0,             
					@excepcion = 0,
				    @statusRes = '',
				    @unidad = $claveunidad,
				    @etapa = $etapa,
				    @folio = '$folio',
				    @usuario = $usuario,
				    @expediente = 1";
		DB::statement($sql);

		// 		
	    }

	    $pago = DB::table('Pago')->insert(
		        array('DOC_claveint' => $DOC_clave, 'FLD_claveint' => $claveflujo,'PAS_folio' => $folio,'PAG_etapa' => $etapa,'PAG_entrega' => $entrega,'PAG_total' => $total,'PAG_fecha' => '','PAG_transferencia' => '',
	                'PAG_factura' => '' ,'PAG_relacion' => $numrelacion,'UNI_claveint' => $unidad,'PAG_FRegistro' => $fechaH,'PAG_FPagoReg' => '','PAG_descuento' => 0,'TID_claveint' => '',
	                'PAG_observaciones' => '','USU_registro' => $usuario,'USU_paga' => ''));
	    
	    // foreach ($folios['archivos'] as $archi){

	    //          $this->SubirCFDI($archi,$archi,$numrelacion,$usucarpeta);

	    // }

    return Response::json(array('respuesta' => 'Folios Relacionados Correctamente'));

    }

    public function insertaRelacionGlo($usuario){
		$folios =  Input::all(); 

		$numrelacion = $folios['numrelacion'];
		$usucarpeta = $folios['usucarpeta'];
		$obs = $folios['observacion'];
		$tipofactura = $folios['tipofactura'];
	    $unidad = $folios['unidad'];
		$fecha = date('d/m/Y');
		$fechaH = date('Y-m-d H:i:s');
		$fechaH  = date( 'd-m-y H:i:s',strtotime($fecha));
	    foreach ($folios['seleccionados'] as $foliodato){    
            if (!isset($foliodato['total'])){
		    	$total = 0;
		    }else{
		    	$total = $foliodato['total'];
		    }

		    if (!isset($foliodato['rfcemisor'])){
		    	$rfcemisor = null;
		    }else{
		    	$rfcemisor = $foliodato['rfcemisor'];
		    }

		    if (!isset($foliodato['foliofiscal'])){
		    	$foliofiscal = null;
		    }else{
		    	$foliofiscal = $foliodato['foliofiscal'];
		    }

		    if (!isset($foliodato['nombreEmisor'])){
		    	$emisor = $foliodato['nombreEmisor'];
		    }else{
		    	$emisor = $foliodato['nombreEmisor'];
		    }
        }
		$foliofiscal = Input::get('factura')['foliofiscal'];
	    $fechaemision = Input::get('factura')['fechaemision'];
		// $emisor = Input::get('factura')['rfcemisor'];
		$importe = Input::get('factura')['importe'];
		$total = Input::get('factura')['total'];
	    if (!isset(Input::get('factura')['descuento'])){
	    	$descuento = 0;
	    }else{
	    	$descuento = Input::get('factura')['descuento'];
	    }

	    // return Response::json(array('CFD_foliofiscal' => $foliofiscal, 'REL_clave' => $numrelacion, 'CFD_fechaemision' => $fechaemision,
     //                                            'CFD_emisor' => $emisor, 'CFD_importe' => $importe, 'CFD_total' => $total, 'CFD_descuento' => $descuento,
     //                                            'CFD_fechasistema' => $fechaH, 'CFD_valido' => NULL));

	    // $cfdi = DB::table('CFDI')->insert(array('CFD_foliofiscal' => $foliofiscal, 'REL_clave' => $numrelacion, 'CFD_fechaemision' => $fechaemision,
     //                                            'CFD_emisor' => $emisor, 'CFD_importe' => $importe, 'CFD_total' => $total, 'CFD_descuento' => $descuento,
     //                                            'CFD_fechasistema' => $fechaH, 'CFD_valido' => NULL));

	    $sql = "EXEC MV_REL_InsertaRelacion 

					@relacion = '$numrelacion',
					@fechaPago = '$fecha',
					@unidad = '$unidad',
				    @subtotalp = '$importe',
				    @impuestop = '0.00',
				    @totalp = '$total',
					@subtotal = '0.00',
					@impuesto = '0.00',
					@total = '0.00',
					@observaciones = '',
				    @conFactura = 0,  
					@usuario = '$usuario',
				    @conIVA = 0,
				    @retIVA = 0,
				    @retISR = 0,
				    @impIVA = '0.00',
				    @impISR = '0.00'";
		DB::statement($sql);
 
		foreach ($folios['seleccionados'] as $foliodato){

		    $folio = $foliodato['Folio'];
		    $etapa = $foliodato['Etapa'];
		    $entrega = $foliodato['Entrega'];

		    $tipo = $foliodato['tipotramite'];
		    $concepto = $foliodato['concepto'];
		    
		    $llave = $folio.$etapa.$entrega;

		$folioetapaent = DB::table('FolioEtapaEntrega')->insert(
		    array('FEE_clave' => $llave, 'FEE_Folio' => $folio, 'FEE_Etapa' => $etapa, 'FEE_entrega' => $entrega)
		);

	 //    $tramite = DB::table('OrdenPago')->insert(
		//     array('TCO_concepto' => $concepto, 'TRA_tipo' => $tipo ,'TRA_fecha' => $fecha, 'TRA_llave' => $llave, 'TRA_foliofiscal' => $foliofiscal, 'TRA_obs' => $obs, 'TRA_total' => $total)
		// );
		}

	    $Documento = Documento::where(array('DOC_folio' => $folio,'DOC_etapa' => $etapa, 'DOC_numeroEntrega' => $entrega))->first();
        $DOC_clave = $Documento->DOC_claveint;	

        $claveflujo = Flujo::where(array('DOC_claveint' => $DOC_clave))->first()->FLD_claveint;  
	    
		$tramite = DB::table('ordenPago')->where('FLD_claveint', $claveflujo)
		    ->update(array('ORP_foliofiscal' => $foliofiscal, 'ORP_nombreEmisor' => $emisor ,'ORP_rfcemisor' => $rfcemisor, 'ORP_total' => $total));

		$rpaClave = RelacionPago::where(array('FLD_claveint' => $claveflujo))->first()->RPA_claveint;  

		$sql ="EXEC MV_REL_ActualizaPago

					@rpaClave = $rpaClave,
					@relacion = '$numrelacion',
					@referencia = 1,
				    @factura = 0,
				    @importeFac = 0.00,
					@siniestro = '',
					@fechaCaptura = '',
					@tipoLesion = '1',
					@diagnostico = '',
					@pago = '0.00',
					@lesionado = '',
					@statusFac = 0,             
					@excepcion = 0,
				    @statusRes = '',
				    @unidad = $unidad,
				    @etapa = $etapa,
				    @folio = '$folio',
				    @usuario = $usuario,
				    @expediente = 1";
		// DB::statement($sql1);
		DB::statement($sql);

	    
	    $relacion = DB::table('RelacionFiscal')->insert(
		    array('REL_clave' => $numrelacion, 'REL_global' => $tipofactura, 'REL_completa' => 0, 'REL_aplicada' =>0 , 'REL_editada' => 0)
		);

	    $relacionusuario = DB::table('RelacionUsuarios')->insert(
		    array('REL_clave' => $numrelacion, 'USU_creo' => $usuario, 'USU_cancelo' => null, 'USU_aplico' => null)
		);

		$relacionfechas = DB::table('RelacionFechas')->insert(
		    array('REL_clave' => $numrelacion, 'RELF_fcreada' => $fecha, 'RELF_fcompleta' => null, 'RELF_fcancelada' => null, 'RELF_faplicada' => null)
		);
        if (empty($folios['archivos'])) {

        	# code...
        }else{

        	$archi = $folios['archivos'];
	        $this->SubirCFDI($archi,$archi,$numrelacion,$usucarpeta);

        }

	    $tramite = DB::table('Pago')->insert(
		    array('DOC_claveint' => $DOC_clave,'PAS_folio' => $folio,'PAG_etapa' => $etapa,'PAG_entrega' => $entrega,'PAG_total' => $total,'PAG_fecha' => '','PAG_transferencia' => '',
	                'PAG_factura' => '' ,'PAG_relacion' => $numrelacion,'UNI_claveint' => $unidad,'PAG_FRegistro' => $fechaH,'PAG_FPagoReg' => '','PAG_descuento' => 0,'TID_claveint' => '',
	                'PAG_observaciones' => '','USU_registro' => $usuario,'USU_paga' => '')
		);

    	return Response::json(array('respuesta' => 'Folios Relacionados Correctamente'));

    }

// TRUNCATE TABLE FolioEtapaEntrega

// TRUNCATE TABLE CFDI

// TRUNCATE TABLE Tramite

// TRUNCATE TABLE RelacionFiscal

// TRUNCATE TABLE RelacionUsuarios

// TRUNCATE TABLE RelacionFechas


// SELECT * FROM FolioEtapaEntrega

// SELECT * FROM CFDI

// SELECT * FROM Tramite

// SELECT * FROM RelacionFiscal

// SELECT * FROM RelacionUsuarios

// SELECT * FROM RelacionFechas

    public function upload($usuario){
 
	    if (!file_exists("FacturasPagos/$usuario")) { 
	       $carpeta = mkdir("FacturasPagos/$usuario/", 0700);
	    }

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {

	        $nombre =  $_FILES['file']['name'];
	        $file = str_replace(" ","",$nombre);
      
	        copy($_FILES['file']['tmp_name'], 'FacturasPagos/'.$usuario.'/'. $file);
	        $file_name = $file;
	        $subido = true;

	        //$total_imagenes = count(glob("Facturas/{*.pdf,*.xml}",GLOB_BRACE));
	        $directorio = opendir('FacturasPagos/'.$usuario.'/'); //ruta actual
	        while ($archivo1 = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
	        {
	            if (is_dir($archivo1))//verificamos si es o no un directorio
	            {
	                //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
	            }
	            else
	            {   
	                $ext = strtolower(\Input::file('file')->getClientOriginalExtension());   //Line 32
				    // $ext = array_shift($ext);  //Line 34
	                if ($ext == 'xml' or $ext == 'XML'){
	                    
	                    $archivo[] = $archivo1;
	                }
	                
	            }
	        }

	    foreach ($archivo as $var){

            $extension = strtolower(\Input::file('file')->getClientOriginalExtension());
            //echo $extension;
            if ($extension == 'xml' or $extension == 'XML') {

                $leexml = $var;
                $bit2 = 1;
            }
	    
	        if($subido) {
	           $respuesta = array('respuesta' => "El Archivo subio con exito", 'ruta' => $file_name, 'archivo' => $archivo, 'leexml' => $leexml, 'bit2' => $bit2);
	        } else {
	           $respuesta = array('respuesta' => "Error al subir el archivo");
	        }
        } 

        return Response::json($respuesta);
}
}

	public function eliminaxml(){

		$dir = "FacturasPagos/"; 
		$handle = opendir($dir); 

		while ($file = readdir($handle))  { 

			if (is_file($dir.$file)) { 
				unlink($dir.$file); 
			}

		} 
	}

	public function eliminaxmlInd($idx){

		$dir = "FacturasPagos/"; 
		$handle = opendir($dir); 
		while ($file = readdir($handle)){
			if (is_dir($file)){ 

			}else{
				
				$archivos[] = $file;
			}

		}

		unlink('FacturasPagos/'.$idx.'.xml');
 		
	}

	public function borratemporales($usuario){

	      $files = glob('FacturasPagos/'.$usuario.'/*'); // obtiene todos los archivos
	      foreach($files as $file){
	        if(is_file($file)) // si se trata de un archivo
	           unlink($file); // lo elimina
	      }
	}

	public function busquedaRelaciones(){

		$folios =  Input::all(); 

		$relacion = $folios['numrelacion'];
		$resultado = DB::table('Relacion')->where('REL_clave', '=', $relacion)->get();

		if (count($resultado) == 1){
		    return Response::json(array('respuesta' => 1));

		}else{
			return Response::json(array('respuesta' => 0));
		}

	}

	public function consultaFolioFiscal($foliofiscal){

      $resultado = DB::table('CFDI')->select(DB::raw('count(*) as count'))->where('CFD_foliofiscal', '=', $foliofiscal)->get();
      $respuesta = array('count' => $resultado);

      return $resultado;
    }

    public function validaUnidad($rfc){

      $resultado = DB::table('Unidad')->select(DB::raw('UNI_rfc as rfc, UNI_claveWeb as unidadweb, UNI_claveint as unidad'))->where('UNI_rfc', '=', $rfc)->get();
      $respuesta = array('count' => $resultado);

      return $resultado;

    }


	public function borraxArchivo($usuario,$archivo){

		 File::delete ('FacturasPagos/'.$usuario.'/'.$archivo);

	      
	}



}


