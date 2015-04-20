<?php

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
			if ($arearecibe == 7) {
				$flujo->USU_rec =  $usuariorecibe;
				$flujo->FLD_fechaRec =  date('d/m/Y H:i:s'); 
				$flujo->FLD_AROrec =  $arearecibe;
				$flujo->USU_activo =  $usuariorecibe;
			}else{
				$flujo->FLD_porRecibir = 1;
			}

			$flujo->save();

	    	//definimos un arreglo para poder asignarle el valor 

		}

		return Response::json(array('respuesta' => 'Documento(s) enviado Correctamente'));

	}

	public function activos($usuario){
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

		$conexion = DB::connection()->getPdo();

		$query = "EXEC MV_DCU_CapturaOriginal @folio = :folio, @etapa = :etapa, @lesionado = :lesionado,  @unidad = :unidad, @empresa = :empresa, @ambulancia = 0, @fechapago = NULL,  @originalfecha = :originalfecha,  
				@usuario = :usuario, @remesa = :remesa, @numentrega = :entrega  , @producto = :producto, @folioFac = :factura, @totalFac = :totalfactura, @escolaridad = :escolaridad, @internet = 1 "; 
		
		$stmt = $conexion->prepare( $query ); 

		$stmt->bindParam('folio',$folio);
		$stmt->bindParam('etapa',$etapa);
		$stmt->bindParam('lesionado',$lesionado);
		$stmt->bindParam('unidad',$unidad);
		$stmt->bindParam('empresa',$empresa);
		$stmt->bindParam('usuario',$usuario);
		$stmt->bindParam('remesa',$remesa);
		$stmt->bindParam('entrega',$numentrega);
		$stmt->bindParam('producto',$producto);
		$stmt->bindParam('factura',$factura);
		$stmt->bindParam('totalfactura',$totalfactura);
		$stmt->bindParam('escolaridad',$escolaridad);
		$stmt->bindParam('originalfecha',$fecha);


		if ($stmt->execute()) {
			return Response::json(array('respuesta' => 'Folio Guardado Correctamente')); 
		}else{
			return Response::json(array('respuesta' => 'Hubo un error intentelo nuevamente'), 500); 
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

		$conexion = DB::connection()->getPdo();

		$query = "EXEC MV_DCU_ActualizaDocumento @claveint = :clave, @lesionado = :lesionado, @ambulancia = 0, @fechapago = '',  @originalfecha = :fecha,  
				@usuario = :usuario , @remesa = :remesa, @folioFac = :factura, @totalFac = :totalfactura";
		
		$stmt = $conexion->prepare( $query ); 

		$stmt->bindParam('clave',$claveint);
		$stmt->bindParam('folio',$folio);
		$stmt->bindParam('lesionado',$lesionado);
		$stmt->bindParam('usuario',$usuario);
		$stmt->bindParam('remesa',$remesa);
		$stmt->bindParam('factura',$factura);
		$stmt->bindParam('totalfactura',$totalfactura);
		$stmt->bindParam('originalfecha',$fecha);

		if ($stmt->execute()) {
			return Response::json(array('respuesta' => 'Folio Actualizado Correctamente')); 
		}else{
			return Response::json(array('respuesta' => 'Hubo un error intentelo nuevamente'), 500); 
		}

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

}
