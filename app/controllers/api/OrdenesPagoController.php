<?php

include(app_path() . '/classes/Historiales.php');

class OrdenesPagoController extends BaseController {

	//Se crea una nueva por que es para facturacion
	public function listadoOrdenPago(){

		$listaordenes = Flujo::join('Documento', 'FlujoDoc.DOC_claveint', '=', 'Documento.DOC_claveint')
		                      ->join('Unidad', 'Unidad.UNI_claveint', '=', 'Documento.UNI_claveint')
		                      ->join('Empresa', 'Empresa.EMP_claveint', '=', 'Documento.EMP_claveint')
		                      ->join('OrdenPago', 'FlujoDoc.FLD_claveint', '=', 'OrdenPago.FLD_claveint')
		                      ->join('TipoOrdenPago', 'OrdenPago.TIO_id', '=', 'TipoOrdenPago.TIO_id')
		                      ->where(array('ARO_activa' => 16,'FLD_Rechazado' => 0))
		                      ->select('FlujoDoc.DOC_claveint',
						                 'FlujoDoc.FLD_claveint',
										 'Documento.DOC_folio as Folio',
										 'DOC_etapa as etapa',
				                         'DOC_numeroEntrega as entrega',
										 'UNI_nombrecorto',
										 'EMP_nombrecorto as EMP_nombrecorto',
										 'Documento.EMP_claveint',
										 'Documento.UNI_claveint',
										 'FLD_formaRecep',
										 'FLD_AROrec', 
										 'USU_rec',
						                 'USU_ent',
										 'ARO_activa',
										 'ARO_porRecibir',
										 'USU_recibe',
										 'USU_ent as EntregadoPor',
										 'FLD_AROent',
										 'TIO_nombreorden as tipoorden')

		                    ->get();


	    return $listaordenes;



	}

	public function aceptaOrden(){

		$folios =  Input::all(); 

	    foreach ($folios as $foliodato){

		    $usuarioentrega =  $foliodato['USU_ent']; 
		    $areaentrega =  $foliodato['FLD_AROent']; 
		    $usuariorecibe =  $foliodato['USU_rec']; 
		    $arearecibe =  6;

	    	$clave = $foliodato['FLD_claveint'];
	    	$folio = $foliodato['Folio'];
	    	$etapa = $foliodato['etapa'];
	    	$entrega = $foliodato['entrega'];

			
			$flujo = Flujo::find($clave);

			$flujo->FLD_AROent = 16;
			$flujo->USU_rec =  $usuariorecibe;
			$flujo->FLD_porRecibir = 6;
			$flujo->FLD_rechazado = 0;
			$flujo->USU_recibe =  $usuariorecibe;
			$flujo->ARO_porRecibir =  6;
			$flujo->USU_activo =  $usuariorecibe;
			$flujo->ARO_activa =  6;
			$flujo->FLD_fechaRec = date('d/m/Y H:i:s'); 

			$flujo->save();
			Historial::aceptaEntrega($clave);


			if ($arearecibe == 6){

				$flujopagos = new Flujopagos;
				$flujopagos->FLD_claveint = $clave;
				$flujopagos->PAS_folio = $folio;
				$flujopagos->RPA_etapa = $etapa;
				$flujopagos->RPA_numEnt = $entrega;
	
				$flujopagos->save();
           
			}

		}

		return Response::json(array('respuesta' => 'Aceptación con exito: Se Genero una Orden de Pago para Relación'));

	}



}
