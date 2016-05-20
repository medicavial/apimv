<?php

include(app_path() . '/classes/Historiales.php');

class EntregasController extends BaseController {

	//Se crea una nueva por que es para facturacion
	public function acepta(){

		$folios =  Input::all(); 

	    foreach ($folios as $foliodato) {

		    $usuarioentrega =  $foliodato['USU_ent']; 
		    $areaentrega =  $foliodato['FLD_AROent']; 
		    $usuariorecibe =  $foliodato['USU_recibe']; 
		    $arearecibe =  $foliodato['ARO_porRecibir']; 

	    	$clave = $foliodato['FLD_claveint'];
	    	$folio = $foliodato['PAS_folio'];
	    	$etapa = $foliodato['FLD_etapa'];
	    	$entrega = $foliodato['FLD_numeroEntrega'];

			Historial::aceptaEntrega($clave);
			
			$flujo = Flujo::find($clave);

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

			if ($arearecibe == 6) {

				$flujopagos = new Flujopagos;
				$flujopagos->FLD_claveint = $clave;
				$flujopagos->PAS_folio = $folio;
				$flujopagos->RPA_etapa = $etapa;
				$flujopagos->RPA_numEnt = $entrega;
	
				$flujopagos->save();

			}


		}

		return Response::json(array('respuesta' => 'Aceptación con exito'));

	}

	//alta de entregas de un area a otra se actualiza en el flujo
	public function rechaza(){
		
		$folios =  Input::all(); 

	    foreach ($folios as $foliodato) {

		    $usuarioentrega =  $foliodato['USU_ent']; 
		    $areaentrega =  $foliodato['FLD_AROent']; 
		    $usuariorecibe =  $foliodato['USU_recibe']; 
		    $arearecibe =  $foliodato['ARO_porRecibir'];
		    $motivo =  $foliodato['FLD_motivo']; 

	    	$clave = $foliodato['FLD_claveint'];
	    	$folio = $foliodato['PAS_folio'];
	    	$etapa = $foliodato['FLD_etapa'];
	    	$entrega = $foliodato['FLD_numeroEntrega'];

	    	Historial::rechazaEntrega($clave, $motivo);

			$flujo = Flujo::find($clave);

			$flujo->USU_ent = NULL;
			$flujo->FLD_fechaEnt =  NULL;
			$flujo->FLD_porRecibir = 0;
			$flujo->FLD_rechazado = 0;
			$flujo->USU_recibe =  NULL;
			$flujo->ARO_porRecibir =  NULL;
			$flujo->FLD_rechazado =  1;
			$flujo->USU_Rechazo =  $usuariorecibe;
			$flujo->FLD_fechaRechazo = date('d/m/Y H:i:s');
			$flujo->FLD_MotivoRechazo =  $motivo;
			$flujo->save();

		}

		return Response::json(array('respuesta' => 'Documento(s) rechazados Correctamente'));
		
	}

}
