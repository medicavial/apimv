<?php

ini_set('memory_limit', '-1');

class RelacionController extends BaseController {

	public function buscaxProveedor($id){

	 //    DB::disableQueryLog();
		// $fechaini =  Input::get('fechainiEnt'); 
	 //    $fechafin =  Input::get('fechafinEnt'). ' 23:59:58.999'; 

	// $norelacionados =DB::table('RelacionPago')
	//                   ->join('FlujoDoc', 'RelacionPago.FLD_claveint', '=', 'FlujoDoc.FLD_claveint')
	 //                      ->join('Documento', 'FlujoDoc.DOC_claveint', '=', 'Documento.DOC_claveint') 
	 //                      ->join('Pase', 'Pase.PAS_folio', '=', 'Documento.DOC_folio') 
	 //                      ->join('Usuario', 'FlujoDoc.USU_ent', '=', 'Usuario.USU_claveint')  
	 //                      ->join('Producto', 'Documento.PRO_claveint', '=', 'Producto.PRO_claveint')
	 //                      ->join('Empresa', 'Documento.EMP_claveint', '=', 'Empresa.EMP_claveint')
	 //                      ->join('Unidad', 'Documento.UNI_claveint', '=', 'Unidad.UNI_claveint') 
	 //                      ->join('Etapa1', 'Etapa1.PAS_folio', '=', 'Pase.PAS_folio')
	 //                      ->join('Reporte', 'Reporte.REP_claveint', '=', 'Etapa1.REP_claveint')
	 //                      ->join('Lesion', 'Lesion.LES_clave', '=', 'Reporte.LES_primaria') 
	 //                      ->join('TipoLesion', 'TipoLesion.TLE_claveint', '=', 'Lesion.TLE_claveint')
	 //                      ->join('HistorialFlujo','HistorialFlujo.DOC_claveint', '=', 'Documento.DOC_claveint')  
	 //                      ->whereNull('REL_clave')
	 //                      ->where('his_area', '=', 6)
	 //                      ->whereBetween('DOC_originalfecha', array($fechaini, $fechafin))
	 //                      ->get();

  //       return $norelacionados;

    $norelacionados = DB::table ('RelacionPago')                                              
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
											DOC_folio as Folio, 
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
											PAS_penalizado as penalizado'))
                        ->where('his_accion', '=', 'Recepcion')
                        ->where('his_area', '=', 6)
                        ->where('Documento.UNI_claveint', '=',$id)
                        ->whereNull('RelacionPago.REL_clave')
	               //      ->whereNotExists(function($query)
				            // {
				            //     $query->select(DB::raw('FEE_Folio'))
				            //           ->from('FolioEtapaEntrega');
				            // })
                        ->get();


    return $norelacionados;
	}

	// public function relacionFechaRec(){

	//     DB::disableQueryLog();
	// 	$fechaini =  Input::get('fechainiRec'); 
	//     $fechafin =  Input::get('fechafinRec'). ' 23:59:58.999'; 		

 //    $norelacionados = DB::table ('RelacionPago')                                              
 //                        ->join('FlujoDoc', 'RelacionPago.FLD_claveint', '=', 'FlujoDoc.FLD_claveint')
 //                        ->join('Documento', 'FlujoDoc.DOC_claveint', '=', 'Documento.DOC_claveint')
 //                        ->join('Empresa', 'Empresa.EMP_claveint', '=', 'Documento.EMP_claveint')
 //                        ->join('Unidad', 'Unidad.UNI_claveint', '=', 'Documento.UNI_claveint')
 //                        ->join('Usuario', 'Usuario.USU_claveint', '=', 'FlujoDoc.USU_activo')
 //                        ->join('AreaOperativa', 'AreaOperativa.ARO_claveint',  '=', 'FlujoDoc.ARO_activa')
 //                        ->join('Pase',  'Pase.PAS_folio', '=', 'Documento.DOC_folio')
 //                        ->join('DatosSiniestro', 'DatosSiniestro.DAS_claveint', '=', 'Pase.DAS_claveint')
 //                        ->join('Producto', 'Producto.PRO_claveint', '=', 'DatosSiniestro.PRO_claveint')
 //                        ->join('Etapa1',  'Etapa1.PAS_folio', '=', 'Pase.PAS_folio')
 //                        ->join('Expediente',  'Expediente.ET1_claveint', '=', 'Etapa1.ET1_claveint')
 //                        ->join('Reporte', 'Reporte.REP_claveint', '=', 'Etapa1.REP_claveint')
 //                        ->join('Lesion', 'Lesion.LES_clave', '=', 'Reporte.LES_primaria')
 //                        ->join('TipoLesion', 'TipoLesion.TLE_claveint', '=', 'Lesion.TLE_claveint')
 //                        ->join('HistorialFlujo',  'HistorialFlujo.DOC_claveint', '=', 'Documento.DOC_claveint')
 //                        ->leftjoin('Relacion', 'Relacion.REL_clave', '=', 'RelacionPago.REL_clave')
 //                        ->leftjoin('Triage',  'Triage.TRI_claveint', '=', 'Pase.TRI_claveint')
 //                        ->select(DB::raw('USU_nombre as USUNombre, 
	// 										PRO_abreviatura as Producto,
	// 										TRI_nombre as Triage,
	// 										EMP_nombrecorto as Cliente, 
	// 										UNI_nombrecorto as Unidad, 
	// 										Documento.UNI_claveint as claveunidad, 
	// 										DOC_folio as Folio, 
	// 										DOC_lesionado as Lesionado, 
	// 										DOC_etapa as Etapa, 
	// 										DOC_numeroentrega as Entrega, 
	// 										REP_fechaconsulta as FAtencion,
	// 										FLD_formaRecep as FormaRecep,
	// 										DOC_originalfechacaptura as fechaRecepcion,             
	// 										his_fecha as FechaRecepPag,
	// 										TLE_nombre as Tipo,
	// 										LES_nombre as Lesion,
	// 										Relacion.REL_clave as Relacion,
	// 										REL_fecha as FRelacion,
	// 										REL_fPagada as FRelPago,
	// 										REL_fPagadaReg as FRelPagoReg,
	// 										CASE WHEN PAS_fechaPago IS NULL THEN 0 ELSE 1 END as PasC,  
	// 										PAS_fechaPago  as FPasCobrado,
	// 										CONVERT(varchar,RPA_pago,1) as Pago,
	// 										CASE DOC_etapa WHEN 1 THEN CONVERT(varchar, EXP_pagoET1,1) WHEN 2 THEN CONVERT(varchar, EXP_pagoET2, 1) ELSE CONVERT(varchar, EXP_pagoET3, 1) END as Tabulador,
	// 										EXP_reserva_inicial_Tot as Reserva,
	// 										RPA_factura as FacturaRelacion,                                              
	// 										DOC_factura as FacDoc,
	// 										CASE WHEN REL_fPagada IS NULL THEN 0 ELSE 1 END as RelP, 
	// 										REL_pagada as Pagado,
	// 										PAS_pagado as Cobrado,
	// 										PAS_penalizado as penalizado'))
 //                        ->where('his_accion', '=', 'Recepcion')
 //                        ->where('his_area', '=', 6)
 //                        ->whereBetween('his_fecha', array($fechaini, $fechafin))
 //                        ->whereNull('RelacionPago.REL_clave')
 //                        ->get();

 //        return $norelacionados;
	// }

	public function insertaRelacion($usuario){

		$folios =  Input::all(); 

		$numrelacion = $folios['numrelacion'];
		$obs = $folios['observacion'];
		$tipofactura = $folios['tipofactura'];
		$fecha = date('d/m/Y');
		$fechaH = date('Y-m-d H:i:s');

		foreach ($folios['seleccionados'] as $foliodato){

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

		    if (!isset($foliodato['emisor'])){
		    	$emisor = 0;
		    }else{
		    	$emisor = $foliodato['emisor'];
		    }


		    $folio = $foliodato['Folio'];
		    $etapa = $foliodato['Etapa'];
		    $entrega = $foliodato['Entrega'];
		    
		    $llave = $folio.$etapa.$entrega;

		$folioetapaent = DB::table('FolioEtapaEntrega')->insert(
		    array('FEE_clave' => $llave, 'FEE_Folio' => $folio, 'FEE_Etapa' => $etapa, 'FEE_entrega' => $entrega)
		);

	    $tramite = DB::table('Tramite')->insert(
		    array('TCO_concepto' => $concepto, 'TRA_tipo' => $tipo ,'TRA_fecha' => $fecha, 'TRA_llave' => $llave, 'TRA_foliofiscal' => $foliofiscal, 'TRA_obs' => $obs)
		);
		// $max_tramite = DB::table('Tramite')->max('TRA_clave');

	 //    $tramiteRelacion = DB::table('TramiteRelacion')->insert(
		//     array('TRA_clave' => $max_tramite, 'REL_clave' => $numrelacion)
		// );
		// 
		$cfdi = DB::table('CFDI')->insert(array('CFD_foliofiscal' => $foliofiscal, 'REL_clave' => $numrelacion, 'CFD_fechaemision' => $fechaemision,
                                                'CFD_emisor' => $emisor, 'CFD_importe' => $importe, 'CFD_total' => $total, 'CFD_descuento' => $descuento,
                                                'CFD_fechasistema' => $fechaH, 'CFD_valido' => NULL));

	    }                                                                                                                                                                                                                               

	    $relacion = DB::table('RelacionFiscal')->insert(
		    array('REL_clave' => $numrelacion, 'REL_global' => $tipofactura, 'REL_completa' => 0, 'REL_aplicada' =>0 , 'REL_editada' => 0)
		);

	    $relacionusuario = DB::table('RelacionUsuarios')->insert(
		    array('REL_clave' => $numrelacion, 'USU_creo' => $usuario, 'USU_cancelo' => null, 'USU_aplico' => null)
		);

		$relacionfechas = DB::table('RelacionFechas')->insert(
		    array('REL_clave' => $numrelacion, 'RELF_fcreada' => $fecha, 'RELF_fcompleta' => null, 'RELF_fcancelada' => null, 'RELF_faplicada' => null)
		);


    return Response::json(array('respuesta' => 'Folios Relacionados Correctamente'));

    }

    public function insertaRelacionGlo($usuario){
		$folios =  Input::all(); 

		$numrelacion = $folios['numrelacion'];
		$obs = $folios['observacion'];
		$tipofactura = $folios['tipofactura'];
		$fecha = date('d/m/Y');
		$fechaH = date('Y-m-d H:i:s');


		$foliofiscal = Input::get('factura')['foliofiscal'];
	    $fechaemision = Input::get('factura')['fechaemision'];
		$emisor = Input::get('factura')['emisor'];
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

	    $cfdi = DB::table('CFDI')->insert(array('CFD_foliofiscal' => $foliofiscal, 'REL_clave' => $numrelacion, 'CFD_fechaemision' => $fechaemision,
                                                'CFD_emisor' => $emisor, 'CFD_importe' => $importe, 'CFD_total' => $total, 'CFD_descuento' => $descuento,
                                                'CFD_fechasistema' => $fecha, 'CFD_valido' => NULL));

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

	    $tramite = DB::table('Tramite')->insert(
		    array('TCO_concepto' => $concepto, 'TRA_tipo' => $tipo ,'TRA_fecha' => $fecha, 'TRA_llave' => $llave, 'TRA_foliofiscal' => $foliofiscal, 'TRA_obs' => $obs)
		);
	    }
                                                                                                                                                                                                               
	    $relacion = DB::table('RelacionFiscal')->insert(
		    array('REL_clave' => $numrelacion, 'REL_global' => $tipofactura, 'REL_completa' => 0, 'REL_aplicada' =>0 , 'REL_editada' => 0)
		);

	    $relacionusuario = DB::table('RelacionUsuarios')->insert(
		    array('REL_clave' => $numrelacion, 'USU_creo' => $usuario, 'USU_cancelo' => null, 'USU_aplico' => null)
		);

		$relacionfechas = DB::table('RelacionFechas')->insert(
		    array('REL_clave' => $numrelacion, 'RELF_fcreada' => $fecha, 'RELF_fcompleta' => null, 'RELF_fcancelada' => null, 'RELF_faplicada' => null)
		);


    	return Response::json(array('respuesta' => 'Folios Relacionados Correctamente'));

    }

    	public function upload($idx)
	{
	    // if(!\Input::file("file"))
	    // {
	    //     return redirect('upload')->with('error-message', 'File has required field');
	    // }else{
	    // 	return Response::json(array('respuesta' => 'Bien'));
	    // }
	    $fecha = date('Y-m-d');
	    $mime = \Input::file('file')->getMimeType();
	    $extension = strtolower(\Input::file('file')->getClientOriginalExtension());
	//    $fileName = $fecha.'-'.uniqid().'.'.$extension;
	    $fileName = $idx.'.'.$extension;
	    $path = "Complementos/";

	    if (\Request::file('file')->isValid())
	    {
	        \Request::file('file')->move($path, $fileName);
	    }
	    $directorio = opendir($path); //ruta actual
	    while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
	    {
	        if (is_dir($archivo))//verificamos si es o no un directorio
	        {
	            //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
	        }
	        else
	        {   
				$archivos[] = $archivo;  
	            
	        }
	    }

	    return Response::json(array('respuesta' => $archivos));

	}

	public function eliminaxml(){

		$dir = "Complementos/"; 
		$handle = opendir($dir); 

		while ($file = readdir($handle))  { 

			if (is_file($dir.$file)) { 
				unlink($dir.$file); 
			}

		} 
	}

	public function eliminaxmlInd($idx){

		$dir = "Complementos/"; 
		$handle = opendir($dir); 
		while ($file = readdir($handle)){
			if (is_dir($file)){ 

			}else{
				
				$archivos[] = $file;
			}

		}

		unlink('Complementos/'.$idx.'.xml');
 

		
	}



}


