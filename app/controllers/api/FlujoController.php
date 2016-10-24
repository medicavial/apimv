<?php

ini_set('memory_limit', '-1');

include(app_path() . '/classes/Historiales.php');

class FlujoController extends BaseController {

	//Se crea una nueva por que es para facturacion
	public function alta(){
		
		$folios =  Input::get('folios'); 
	    $usuarioentrega =  Input::get('usuarioentrega'); 
	    $areaentrega =  Input::get('areaentrega'); 
	    $usuariorecibe =  Input::get('usuariorecibe'); 
	    $arearecibe =  Input::get('arearecibe');

	    foreach ($folios as $foliodato) {

	    	$documento = $foliodato['DOC_claveint'];
	    	$observaciones = $foliodato['FLD_observaciones'];

			$flujo = new Flujo;

			$flujo->FLD_formaRecep = 'JF'; 
			$flujo->USU_ent = $usuarioentrega;
			$flujo->FLD_fechaent = date('d/m/Y H:i:s');  
			$flujo->USU_activo =  $usuarioentrega;
			$flujo->ARO_activa = $areaentrega;
			$flujo->FLD_porRecibir = 1; 
			$flujo->USU_recibe =  $usuariorecibe;
			$flujo->ARO_porRecibir =  $arearecibe;
			$flujo->DOC_claveint = $documento;
			$flujo->FLD_AROent = $areaentrega;
			$flujo->FLD_observaciones = $observaciones;

			$flujo->save();

			$clave = $flujo->FLD_claveint;
			Historial::altaEntrega($clave);
		}

		return Response::json(array('respuesta' => 'Documento(s) enviado Correctamente'));

	}
		
	//alta de entregas de un area a otra se actualiza en el flujo
	public function actualiza(){
		
		$folios =  Input::get('folios'); 
	    $usuarioentrega =  Input::get('usuarioentrega'); 
	    $areaentrega =  Input::get('areaentrega'); 
	    $usuariorecibe =  Input::get('usuariorecibe'); 
	    $arearecibe =  Input::get('arearecibe');

	    foreach ($folios as $foliodato) {

	    	$clave = $foliodato['FLD_claveint'];
	    	$observaciones = $foliodato['FLD_observaciones'];

	    	$flujo = Flujo::find($clave);

	    	$flujo->USU_ent = $usuarioentrega;
			$flujo->FLD_fechaent = date('d/m/Y H:i:s');  
			$flujo->FLD_AROent = $areaentrega;
			$flujo->ARO_porRecibir =  $arearecibe;
			$flujo->USU_recibe =  $usuariorecibe;
			$flujo->FLD_rechazado = 0;
			$flujo->FLD_observaciones = $observaciones;
			///Si es archivo se autorecibe el documento con usuario recepcionautomatica que es 36 
			if ($arearecibe == 7 || $arearecibe == 9) {

				$flujo->USU_rec =  $usuariorecibe;
				$flujo->FLD_fechaRec =  date('d/m/Y H:i:s'); 
				$flujo->FLD_AROrec =  $arearecibe;
				$flujo->USU_activo =  $usuariorecibe;
				$flujo->ARO_activa =  $arearecibe;
			}
			else{
				$flujo->FLD_porRecibir = 1;
			}

			$flujo->save();
	    	//definimos un arreglo para poder asignarle el valor 
			Historial::altaEntrega($clave);
		}

		return Response::json(array('respuesta' => 'Documento(s) enviado Correctamente'));

	}

	public function activos($usuario){

		DB::disableQueryLog();
		return DB::select("EXEC MV_FLU_ListaGralXUsu @usuario=$usuario");

	}

	public function altaoriginal(){

		$usuario =  Input::get('usuario'); 
	    $folio =  Input::get('folio'); 
	    $etapa =  Input::get('tipoDoc');
	    $lesionado = Input::get('lesionado'); 
	    $unidad =  Input::get('unidad'); 
	    $fecha =  Input::get('fecha'); 
	    $empresa =  Input::get('cliente'); 
	    $producto =  Input::get('producto'); 
	    $remesa =  Input::get('remesa'); 
	    $escolaridad =  Input::get('escolaridad'); 
	    $internet =  Input::get('internet'); 
	    $numentrega = Input::get('numentrega');
	    $totalfactura = Input::get('totalfactura');
	    $factura = Input::get('factura');
	    $propia = Input::has('propia') ? Input::get('propia') : 0;


		if ( $usuario == '' || $usuario == null ) {
			
			return Response::json(array('respuesta' => 'Usuario no detectado, vuelve a loguearte para restablecer los valores de tu usuario'), 500); 	
		
		}else{

			//verificamos que no este capturado el documento
			$existencia = documento::where('DOC_folio',$folio)
									 ->where('DOC_etapa',$etapa)
									 ->where('DOC_numeroentrega',$numentrega)
									 ->count();

			if ($existencia == 0) {


				//guardamos en tabla documento
				$documento = new documento;

				$documento->DOC_folio = $folio;
				$documento->DOC_etapa = $etapa;
				$documento->DOC_lesionado = $lesionado;                                
				$documento->UNI_claveint = $unidad;
				$documento->DOC_ambulancia = 0;
				$documento->DOC_fechapago = NULL; 
				$documento->DOC_original = 1; 
				$documento->DOC_originalfecha = $fecha; 
				$documento->DOC_originalfechacaptura = date('d/m/Y H:i:s'); 
				$documento->EMP_claveint = $empresa; 
				$documento->USU_original = $usuario; 
				$documento->DOC_remesa = $remesa; 
				$documento->DOC_numeroentrega = $numentrega;              
				$documento->PRO_claveint = $producto; 
				$documento->DOC_factura = $factura; 
				$documento->DOC_totalFac = $totalfactura; 
				$documento->ESC_claveint = $escolaridad; 

				$documento->save();


				$clave = $documento->DOC_claveint;

				//guardamos en flujo de documentos
				$flujo = new Flujo;

				$flujo->FLD_formaRecep = 'O'; 
			    $flujo->FLD_AROrec = 1; 		  
			    $flujo->USU_rec = $usuario;
			    $flujo->FLD_fechaRec = date('d/m/Y H:i:s');
		        $flujo->USU_activo = $usuario;
		        $flujo->DOC_claveint = $clave; 

		        if (Input::has('npc')) {
		        	$flujo->FLD_envNPC = Input::get('npc');
		        }

		        $flujo->save();

		        // capturamos en caso de ser segunda etapa y unidad propia
		        if ($propia == 1 && $etapa == 2) {

		        	$fecha = Input::get('captura')['fecha'];
		        	$hora = Input::get('captura')['hora'];
		        	$medico = Input::get('captura')['medico'];


		        	$fechaSub = date('d/m/Y', strtotime(str_replace('-', '/', $fecha ))) . ' '. $hora;

		        	//captura el pase de segunda solo unidad propia
					$sql = "EXEC MV_GenerarSuministrosEt2  
							@fechadocs = '$fechaSub',
							@observaciones = '',
							@consultas = 1,
							@usuario = $usuario,
							@folio = '$folio',
							@docClave = $clave,
							@medicoTratante = $medico,
							@interconsulta = 0,
							@EN2Clave = 0
						";

					DB::statement($sql);
		        }

		        //capturamos la tercera etapa
		        if ($propia == 1 && $etapa == 3) {

		        	$fecha = Input::get('captura')['fecha'];
		        	$medico = Input::get('captura')['medico'];
		        	$escalaDolor = Input::get('captura')['escalaDolor'];
		        	$escalaMejoria = Input::get('captura')['escalaMejoria'];
		        	$tipoRehabilitacion = Input::get('captura')['tipoRehabilitacion'];
		        	$observaciones = Input::get('captura')['observaciones'];

		        	$fechaReh = date('d/m/Y', strtotime(str_replace('-', '/', $fecha )));

		        	//captura el pase de segunda solo unidad propia
					$sql = "EXEC MV_GenerarRehabilitacion  
							@folio = '$folio',
							@firdoc = 1,
							@fechadocs = '$fechaReh',
							@medreh = $medico,
							@presenta = '$observaciones',
							@sesiones = 1,
							@ffirres = 1,
							@fechafirres = '$fechaReh',
							@ffirreh = 1,
							@fechafirreh = '$fechaReh',
							@usuario = $usuario,
							@docClave = $clave,
							@tipoTerapia = '$tipoRehabilitacion',
							@escalaMejoria = '$escalaMejoria',
							@escalaDolor = '$escalaDolor'
						";

					DB::statement($sql);
		        }

				Historial::altaOriginal($folio,$etapa,$numentrega);
				
				return Response::json(array('respuesta' => 'Folio Guardado Correctamente')); 	
			
			}else{

				return Response::json(array('respuesta' => 'Esta etapa con esta entrega ya fue capturada verificalo en Control de Documentos','entrega' => $existencia), 500); 	

			}

			
		}

	}

	public function actualizaoriginal(){
		
		$folio =  Input::get('folio'); 
		$claveint =  Input::get('documento');
	    $usuario =  Input::get('usuario'); 
	    $fecha =  Input::get('fecha');  
	    $remesa =  Input::get('remesa'); 
	    $lesionado =  Input::get('lesionado');
	    $totalfactura = Input::get('totalfactura');
	    $factura = Input::get('factura');


	    $documento =  documento::find($claveint);	    

		$documento->DOC_lesionado = $lesionado;                                
		$documento->DOC_ambulancia = 0;
		$documento->DOC_fechapago = NULL; 
		$documento->DOC_original = 1; 
		$documento->DOC_originalfecha = $fecha; 
		$documento->DOC_originalfechacaptura = date('d/m/Y H:i:s'); 
		$documento->USU_original = $usuario; 
		$documento->DOC_remesa = $remesa; 
		$documento->DOC_factura = $factura; 
		$documento->DOC_totalFac = $totalfactura; 

		$documento->save();

		$flujo = new Flujo;

		$flujo->FLD_formaRecep = 'O'; 
	    $flujo->FLD_AROrec = 1; 		  
	    $flujo->USU_rec = $usuario;
	    $flujo->FLD_fechaRec = date('d/m/Y H:i:s');
        $flujo->USU_activo = $usuario;
        $flujo->DOC_claveint = $claveint; 

        $flujo->save();

		// $conexion = DB::connection()->getPdo();

		// $query = "EXEC MV_DCU_ActualizaDocumento @claveint = :clave, @lesionado = :lesionado, @ambulancia = 0, @fechapago = '',  @originalfecha = :fecha,  
		// 		@usuario = :usuario , @remesa = :remesa, @folioFac = :factura, @totalFac = :totalfactura";
		
		// $stmt = $conexion->prepare( $query ); 

		// $stmt->bindParam('clave',$claveint);
		// $stmt->bindParam('folio',$folio);
		// $stmt->bindParam('lesionado',$lesionado);
		// $stmt->bindParam('usuario',$usuario);
		// $stmt->bindParam('remesa',$remesa);
		// $stmt->bindParam('factura',$factura);
		// $stmt->bindParam('totalfactura',$totalfactura);
		// $stmt->bindParam('originalfecha',$fecha);

		return Response::json(array('respuesta' => 'Folio Actualizado Correctamente')); 

	}

	public function elimina(){
		
		$folios =  Input::all(); 

	    foreach ($folios as $dato) {

	    	$clave = $dato['FLD_claveint'];
			$folio = $dato['PAS_folio'];
			$etapa = $dato['FLD_etapa'];
			$cantidad = $dato['FLD_numeroEntrega'];
			$area = $dato['ARO_porRecibir'];
			$documento = $dato['DOC_claveint'];

			Historial::quitaEntrega($clave);

			if($dato['FLD_formaRecep'] == 'JF'){	
				Flujo::find($clave)->delete();
			}else{
		    	//definimos un arreglo para poder asignarle el valor 
				$flujo = Flujo::find($clave);
				$flujo->USU_ent = NULL;
				$flujo->FLD_fechaent = NULL;  
				$flujo->USU_recibe =  NULL;
				$flujo->ARO_porRecibir =  NULL;
				$flujo->FLD_porRecibir = 0; 
				$flujo->save();
			}

		}

		return Response::json(array('respuesta' => 'Folio(s) Removido Correctamente'));

	}

	public function eliminanpc(){
		
		$folios =  Input::all(); 

	    foreach ($folios as $foliodato) {

	    	$clave = $foliodato['FLD_claveint'];
			$flujo = Flujo::find($clave);
			$flujo->FLD_envNPC = 0;
			$flujo->save();

			Historial::bajaNPC($clave);
		}

		return Response::json(array('respuesta' => 'Documento(s) removidos Correctamente'));

	}

	public function entregas($usuario){
		return DB::select("EXEC MV_FLU_ListaEntregaXUsu @usuario=$usuario");
	}

	public function insertanpc(){
		
		$folios =  Input::all(); 

	    foreach ($folios as $foliodato) {

	    	$clave = $foliodato['FLD_claveint'];
			$flujo = Flujo::find($clave);
			$flujo->FLD_envNPC = 1;
			$flujo->save();
			Historial::altaNPC($clave);

		}

		return Response::json(array('respuesta' => 'Documento(s) enviado Correctamente'));

	}

	public function posiblenp(){
		
		$folios =  Input::all(); 

	    foreach ($folios as $foliodato) {

	    	$clave = $foliodato['FLD_claveint'];
			$flujo = Flujo::find($clave);
			$flujo->FLD_envNPC = 2;
			$flujo->save();

			Historial::altaNPC($clave);

		}

		return Response::json(array('respuesta' => 'Documento(s) enviado Correctamente'));

	}

	public function recepcion($usuario){
		return DB::select("EXEC MV_FLU_ListaRecepcionXUsu @usuario=$usuario");
	}

	public function rechazos($usuario){
		return DB::select("EXEC MV_FLU_ListaGralXUsuRechazo @usuario=$usuario");
	}

	public function npc($usuario){
		return DB::select("EXEC MV_FLU_ListaGralXUsuNPC @usuario=$usuario");
	}

	public function consulta($usuario){

		$respuesta = array();

		$condicionRechazo = ['USU_activo' => $usuario, 'FLD_porRecibir' => 0, 'FLD_rechazado' => 1];
		$condicionXRecibir = ['USU_recibe' => $usuario, 'FLD_rechazado' => 0];
		
		$respuesta['rechazos'] = Flujo::where($condicionRechazo)->count();
		$respuesta['xrecibir'] = Flujo::where($condicionXRecibir)->count();

		return $respuesta;

	}	

	public function activosarea($area){
		return DB::select("EXEC MV_FLU_ListaGralXArea @area=$area");
	}

	public function entregasarea($area){
		return DB::select("EXEC MV_FLU_ListaEntregaXArea @area=$area");
	}

	public function recepcionarea($area){
		return DB::select("EXEC MV_FLU_ListaRecepcionXArea @area=$area");
	}

	public function rechazosarea($area){
		return DB::select("EXEC MV_FLU_ListaGralXAreaRechazo @area=$area");
	}

}
