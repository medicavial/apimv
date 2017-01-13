<?php

ini_set('memory_limit', '-1');
include(app_path() . '/classes/Historiales.php');


class FacturaUnidadesController extends BaseController {


	public function buscaFolios(){

		$fechaini = DateTime::createFromFormat('d/m/Y', Input::get('fechaini') )->format('Y-m-d') . ' 00:00:00';
		$fechafin = DateTime::createFromFormat('d/m/Y', Input::get('fechafin') )->format('Y-m-d') . ' 23:59:59';


		$folios = Atenciones::join('Expediente','Expediente.Exp_folio','=','Atenciones.Exp_folio')
	                        ->join('TipoAtencion','TipoAtencion.TIA_clave','=','Atenciones.TIA_clave')
	                        ->join('Compania','Compania.Cia_clave','=','Expediente.Cia_clave')
	                        ->join('Unidad', 'Expediente.Uni_clave','=', 'Unidad.Uni_clave')
	                        ->join('TriageAutorizacion', 'Expediente.Exp_triageActual', '=', 'TriageAutorizacion.Triage_id')
	                        ->join('Producto','Expediente.Pro_clave', '=', 'Producto.Pro_clave')
	                        // ->select('Expediente.Exp_folio as Folio','Exp_completo As Lesionado',
	                        //       	 'Unidad.Uni_nombrecorto as Unidad','Triage_nombre as Triage','Cia_nombrecorto as Cliente',
	                        //       	 'Pro_nombre as Producto', 'Exp_fecreg as fechaReg')
	                        ->where(array(
	                            'Exp_cancelado' => 0,
	                            'ATN_estatus' => 5
	                        ))
	                        ->select('Expediente.Exp_folio as folio')
	                        ->whereBetween('ATN_fecreg', array($fechaini,$fechafin))
	                        // ->skip(10)
	                        // ->take(5)
	                        ->get()
	                        ->toArray();

		    $validos = Documento::join('Unidad','Unidad.UNI_claveint','=','Documento.UNI_claveint')
		                        ->join('Producto','Producto.PRO_claveint','=','Documento.PRO_claveint')
		                        ->join('Empresa','Empresa.EMP_claveint','=','Documento.EMP_claveint')
		                        ->whereIn('DOC_folio',$folios)
		    					->where('DOC_etapa',1)
		    					->where('DOC_situacionOriginal',1)
		    					->select('UNI_nombrecorto as Unidad', 'DOC_folio as Folio', 'DOC_lesionado as Lesionado','PRO_nombre AS Producto',
		    						    'EMP_NombreCorto as Cliente', 'DOC_fechacapturado as fechaReg', 'DOC_claveint as claveDoc')
		    					// ->select('DOC_folio as folio')
		    					->get()
		    					->toArray();

		    // return $validos;
		// function array_push_assoc(array &$arrayDatos, array $values){
  //           $arrayDatos = array_merge($arrayDatos, $values);
  //       }
  //       

        $respuesta = array();

	    foreach ($validos as $valido){

	    	$clave = $valido['claveDoc'];
	    	$claveflujo = Flujo::where(array('DOC_claveint' => $clave))->first();
	    	// print_r($claveflujo['FLD_formaRecep']);    	
		    if($claveflujo['FLD_formaRecep'] == 'FE'){

		    	$tiporecep = "Facturacion Express";
		        $valido['tiporecepcion'] = $tiporecep;
		        array_push($respuesta, $valido);


		    }else{

		    	$tiporecep = "Verifica Pago";
		        $valido['tiporecepcion'] = $tiporecep;
		        array_push($respuesta, $valido);

		    }
		}
		return $respuesta;
  	    
	}

public function buscaxUnidad($id){

		$folios = Atenciones::join('Expediente','Expediente.Exp_folio','=','Atenciones.Exp_folio')
	                        ->join('TipoAtencion','TipoAtencion.TIA_clave','=','Atenciones.TIA_clave')
	                        ->join('Compania','Compania.Cia_clave','=','Expediente.Cia_clave')
	                        ->join('Unidad', 'Expediente.Uni_clave','=', 'Unidad.Uni_clave')
	                        ->join('TriageAutorizacion', 'Expediente.Exp_triageActual', '=', 'TriageAutorizacion.Triage_id')
	                        ->join('Producto','Expediente.Pro_clave', '=', 'Producto.Pro_clave')
	                        // ->select('Expediente.Exp_folio as Folio','Exp_completo As Lesionado',
	                        //       	 'Unidad.Uni_nombrecorto as Unidad','Triage_nombre as Triage','Cia_nombrecorto as Cliente',
	                        //       	 'Pro_nombre as Producto', 'Exp_fecreg as fechaReg')
	                        ->where(array('Exp_cancelado' => 0,'ATN_estatus' => 5))
	                        ->select('Expediente.Exp_folio')
	                        ->where('Uni_ClaveActual', '=' , $id)
	                        ->distinct()
	                        // ->skip(10)
	                        // ->take(5)
	                        ->get()
	                        ->toArray();

	                        // print_r($folios);

	    $validos = Documento::join('Unidad','Unidad.UNI_claveint','=','Documento.UNI_claveint')
	                        ->join('Producto','Producto.PRO_claveint','=','Documento.PRO_claveint')
	                        ->join('Empresa','Empresa.EMP_claveint','=','Documento.EMP_claveint')
	                        ->whereIn('DOC_folio',$folios)
	    					->where('DOC_etapa',1)
	    					->where('DOC_situacionOriginal',1)
	    					->select('UNI_nombrecorto as Unidad', 'DOC_folio as Folio', 'DOC_lesionado as Lesionado','PRO_nombre AS Producto',
		    						    'EMP_NombreCorto as Cliente', 'DOC_fechacapturado as fechaReg', 'DOC_claveint as claveDoc')
	    					->get()
	    					->toArray();
	    // print_r($folios)
	    // $fol = RelacionPago::whereIn('PAS_folio',$folios)->whereNull('REL_clave')->select('PAS_folio as folio')->get()->toArray();  
					// print_r($fol);

		$respuesta = array();

	    foreach ($validos as $valido){

	        $clave = $valido['claveDoc'];
	        $folio = $valido['Folio'];

	    	$claveflujo = Flujo::where(array('DOC_claveint' => $clave,'FLD_formaRecep' => 'FE'))->first();
            $revisado = Atenciones::where(array('Exp_folio' => $folio))->first();
            

		    if($claveflujo['FLD_formaRecep'] == 'FE'){

		    	$tiporecep = "Facturacion Express";
		    	$rev = "En Revision";
		        $valido['tiporecepcion'] = $tiporecep;
		        $valido['revisado'] = "";
		        $valido['bitrevisado'] = 0;
		        if ($revisado['ATN_estatusrevision'] == 1){
		        	$valido['revisado'] = $rev;
		        	$valido['bitrevisado'] = 1;
                }

		        $clave_flujo = $claveflujo['FLD_claveint'];
	    	    $existe_relacion = RelacionPago::where('FLD_claveint',$clave_flujo)->count();
	    	    // print_r($relacion);
	    	    if($existe_relacion == 0){

		          array_push($respuesta, $valido);
		        }

		    }
		    // elseif($claveflujo['FLD_formaRecep'] = 'O'){

		    // 	$tiporecep = "Verifica Pago";
		    //     $valido['tiporecepcion'] = $tiporecep;
		    //     array_push($respuesta, $valido);

		    // }
		    

		}

		return $respuesta;

}


	public function enviaFolios($usuario){

		$datos = Input::all();
		$fecha = date('Y-m-d');
		foreach ($datos as $dato){

 			$folio = $dato['Folio'];

 		    $actualiza = DocumentoDigital::where('REG_folio', $folio)->update(array('Arc_autorizado' => 1, 'USU_autorizo' => $usuario, 'Arc_fechaAut' => $fecha));
			$inserta = Atenciones::where('Exp_folio', $folio)->update(array('ATN_estatus' => 7));

 		}

	}


	public function actualiza(){

		$folios =  Input::get('folios'); 
	    $i = 0;

	    foreach ($folios as $dato){

	    	$folio =  $dato[$i]; 
		    $usuarioentrega =  Input::get('usuarioentrega'); 
		    $areaentrega =  Input::get('areaentrega'); 
		    $usuariorecibe =  Input::get('usuariorecibe'); 
		    $arearecibe =  Input::get('arearecibe');
		    $observaciones = "Atencion Autorizada por FE";

            $Documento = Documento::where(array('DOC_folio' => $folio,'DOC_etapa' => 1, 'DOC_numeroEntrega' => 1))
                                    ->first();
            $DOC_clave = $Documento->DOC_claveint;	

            $claveflujo = Flujo::where(array('FLD_formaRecep' => 'FE', 'DOC_claveint' => $DOC_clave))->first()->FLD_claveint;    	

			DB::table('FlujoDoc')
		            ->where('DOC_claveint', $DOC_clave)
		            ->where('FLD_formaRecep', 'FE')
		            ->update(array('USU_ent' => $usuarioentrega, 'FLD_fechaent' => date('d/m/Y H:i:s'), 'FLD_AROent' => $areaentrega,
		            	     'ARO_porRecibir' => $arearecibe, 'USU_recibe' => $usuariorecibe, 'FLD_rechazado' => 0, 'FLD_observaciones' => $observaciones));
	    	//definimos un arreglo para poder asignarle el valor 
			Historial::altaEntrega($claveflujo);
			$i++;
		}

		return Response::json(array('respuesta' => "Folio(s) Enviado"));

	}


	public function listadoFactura(){

		$folios = Atenciones::join('Expediente','Expediente.Exp_folio','=','Atenciones.Exp_folio')
	                        ->join('TipoAtencion','TipoAtencion.TIA_clave','=','Atenciones.TIA_clave')
	                        ->join('Compania','Compania.Cia_clave','=','Expediente.Cia_clave')
	                        ->join('Unidad', 'Expediente.Uni_clave','=', 'Unidad.Uni_clave')
	                        ->join('TriageAutorizacion', 'Expediente.Exp_triageActual', '=', 'TriageAutorizacion.Triage_id')
	                        ->join('Producto','Expediente.Pro_clave', '=', 'Producto.Pro_clave')
	                        ->select('Expediente.Exp_folio as Folio','Exp_completo As Lesionado',
	                              	 'Unidad.Uni_nombrecorto as Unidad','Triage_nombre as Triage','Cia_nombrecorto as Cliente',
	                              	 'Pro_nombre as Producto', 'Exp_fecreg as fechaReg')
	                        ->where(array(
	                            'Exp_cancelado' => 0,
	                            'ATN_estatus' => 5
	                        ))
	                        ->distinct()
	                        ->get();

	    return $folios;
	}

	public function unidades(){

		return UnidadWeb::where('Uni_activa','=', 'S')
				->select('Uni_clave as id','Uni_nombrecorto as nombre','Uni_propia as propia', 'Uni_ref as referencia')
				->orderBy('Uni_nombrecorto')
				->get();

	}



}