<?php

ini_set('memory_limit', '-1');
include(app_path() . '/classes/Historiales.php');


class FacturaUnidadesController extends BaseController {

	public function listadofacturas(){

		// $fechaini = DateTime::createFromFormat('d/m/Y', Input::get('fechaini') )->format('Y-m-d') . ' 00:00:00';
		// $fechafin = DateTime::createFromFormat('d/m/Y', Input::get('fechafin') )->format('Y-m-d') . ' 23:59:59';


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
	                        ->skip(10)
	                        ->take(5)
	                        ->distinct()
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
		    

		}
		return $respuesta;
  	    
	}

public function listadofacturasxfecha(){

		$fechaini = DateTime::createFromFormat('d/m/Y', Input::get('fechaini') )->format('Y-m-d') . ' 00:00:00';
		$fechafin = DateTime::createFromFormat('d/m/Y', Input::get('fechafin') )->format('Y-m-d') . ' 23:59:59';

		$folios = Atenciones::join('Expediente','Expediente.Exp_folio','=','Atenciones.Exp_folio')
	                        ->join('TipoAtencion','TipoAtencion.TIA_clave','=','Atenciones.TIA_clave')
	                        ->join('Compania','Compania.Cia_clave','=','Expediente.Cia_clave')
	                        ->join('Unidad', 'Expediente.Uni_clave','=', 'Unidad.Uni_clave')
	                        ->join('TriageAutorizacion', 'Expediente.Exp_triageActual', '=', 'TriageAutorizacion.Triage_id')
	                        ->join('Producto','Expediente.Pro_clave', '=', 'Producto.Pro_clave')
	                        ->where(array('Exp_cancelado' => 0,'ATN_estatus' => 5))
	                        ->whereBetween('ATN_fecreg', array($fechaini, $fechafin))
	                        ->select('Expediente.Exp_folio')
	                        ->distinct()
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
	    					->get()
	    					->toArray();
     
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

		$folio = Input::get('folio');
		$fecha = date('Y-m-d');


 		    $actualiza = DocumentoDigital::where('REG_folio', $folio)->update(array('Arc_autorizado' => 1, 'USU_autorizo' => $usuario, 'Arc_fechaAut' => $fecha));
			$inserta = Atenciones::where('Exp_folio', $folio)->update(array('ATN_estatus' => 7));

			$archivo = DocumentoDigital::where(array('REG_folio' => $folio,'Arc_tipo' => 29))
							->select('Arc_archivo as url', 'Arc_clave as nombre')
							->get()
							->toArray();

		    $ruta = "/public_html/registro/";
		    $url = $archivo[0]['url'];
		    $nombre = $archivo[0]['nombre'];
			$archivo_remoto = $ruta."/".$url;
			$listing = FTP::connection()->getDirListing($archivo_remoto);
            $archivo_local =  "FacturasPagos/xmltemporal";

		    FTP::connection()->downloadFile($archivo_remoto. '/' .$nombre,  $archivo_local. '/' .$nombre,FTP_ASCII);
            // array_push($folios, $folio);
            // array_push($archivos, $nombre);

 		// }

 		$respuesta = array('Folios' => $folio,'Nombre' => $nombre);
 		return $respuesta;

	}


	public function actualiza(){

    	$folio =  Input::get('folio'); 
	    $usuarioentrega =  Input::get('usuarioentrega'); 
	    $areaentrega =  Input::get('areaentrega'); 
	    $usuariorecibe =  Input::get('usuariorecibe'); 
	    $arearecibe =  Input::get('arearecibe');
	    $observaciones = "Atencion Autorizada por FE";

            $Documento = Documento::where(array('DOC_folio' => $folio,'DOC_etapa' => 1, 'DOC_numeroEntrega' => 1))
                                    ->first();
            $DOC_clave = $Documento->DOC_claveint;	

            $claveflujo = Flujo::where(array('FLD_formaRecep' => 'FE', 'DOC_claveint' => $DOC_clave))->first()->FLD_claveint;    	

			Historial::altaOrden($claveflujo);

			$flujo = Flujo::find($claveflujo);

			$flujo->FLD_AROent = $arearecibe;
			$flujo->USU_rec =  $usuariorecibe;
			$flujo->FLD_porRecibir = 0;
			$flujo->FLD_rechazado = 0;
			$flujo->USU_recibe =  NULL;
			$flujo->ARO_porRecibir =  NULL;
			$flujo->USU_activo =  $usuariorecibe;
			$flujo->ARO_activa =  $arearecibe;
			$flujo->FLD_fechaRec = date('d/m/Y H:i:s'); 
			$flujo->save();
			
	    	//definimos un arreglo para poder asignarle el valor 


		return Response::json(array('respuesta' => "Folio(s) Enviado"));

	}

	public function ordenPago(){

		$datos = Input::all(); 

	    	$orden = Input::get('tipoorden');
	    	$folio = Input::get('folio');
	    	$foliofiscal = Input::get('foliofiscal');
	    	$nombreEmisor = Input::get('emisor');
	    	$rfcemisor = Input::get('rfc_emisor');
	    	$total = Input::get('total');
	    	$usuario = Input::get('usuarioentrega');
	    	$factura = Input::get('foliointerno');
	    	$serie = Input::get('serie');
	    	$subtotal = Input::get('subtotal');

	        $Documento = Documento::where(array('DOC_folio' => $folio,'DOC_etapa' => 1, 'DOC_numeroEntrega' => 1))
                                    ->first();
            $DOC_clave = $Documento->DOC_claveint;	

            $claveflujo = Flujo::where(array('FLD_formaRecep' => 'FE', 'DOC_claveint' => $DOC_clave))->first()->FLD_claveint;    	


			$ordenpago = new OrdenPago;

			$ordenpago->TIO_id = $orden;
			$ordenpago->DOC_folio = $folio;
			$ordenpago->FLD_claveint = $claveflujo;
			$ordenpago->DOC_claveint = $DOC_clave;
			$ordenpago->ORP_foliofiscal =  $foliofiscal;
			$ordenpago->ORP_nombreEmisor = $nombreEmisor;
			$ordenpago->ORP_rfcemisor = $rfcemisor;
			$ordenpago->ORP_importe = $subtotal;
			$ordenpago->ORP_iva = NULL;
			$ordenpago->ORP_total= $total;
			$ordenpago->ORP_fechaemision = date('d/m/Y H:i:s'); 
			$ordenpago->ORP_usuregistro = $usuario;
			$ordenpago->ORP_factura = $factura.'-'.$serie;


			$ordenpago->save();

			$flujopagos = new Flujopagos;

			$flujopagos->FLD_claveint = $claveflujo;
			$flujopagos->PAS_folio = $folio;
			$flujopagos->RPA_etapa = 1;
			$flujopagos->RPA_numEnt = 1;

            $flujopagos->save();


			// Historial::altaEntrega($claveflujo);

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

	public function borratemporales() {

      $files = glob('FacturasPagos/xmltemporal/*'); // obtiene todos los archivos
      foreach($files as $file){
        if(is_file($file)) // si se trata de un archivo
          unlink($file); // lo elimina
      }

    }

    public function tipoorden(){

		return TipoOrdenPago::where('TIO_activo','=', 1)
				->select('TIO_id as id','TIO_nombreorden as nombre', 'TIO_nombrelargo as nombrelargo')
				->orderBy('TIO_nombrelargo')
				->get();

	}


}