<?php
include(app_path() . '/classes/Header.php');

class QualitasController extends BaseController {

	//Actualiza y procesa todo sobre el envio los que fueron aceptados y los que no
	public function actualiza($envio){

 		$datos =  Input::all();

 		// Actualizamos el envio
		$send = Qualitas::find($envio);
		$send->ENQ_fechaprocesado = date('d/m/Y H:i:s');
		$send->ENQ_procesado = 1;
		$send->save();

		//se actualizan los folios que si fuerno validos por qualitas
 		foreach ($datos as $dato) {

 			$folio = $dato['folioSistema'];

 			DB::table('DetalleEnvio')
				->where('ENQ_claveint',$envio)
				->where('PAS_folio', $folio)
				->update(array('DEE_procesado' => 1));

			$pase = Pase::find($folio);
 			$pase->PAS_procQ = 2;
 			$pase->save();

 		}

 		//se toman los que no para poderlos actualizar en la tabla pase
 		$foliosNoprocesados = Qualitasdetalle::where('ENQ_claveint',$envio)
 											->where('DEE_procesado',0)->get();

 		//aqui se actualiza en pase para regresarlos
 		foreach ($foliosNoprocesados as $dato) {
 			$folio = $dato['PAS_folio'];
 			$pase = Pase::find($folio);
 			$pase->PAS_procQ = 0;
 			$pase->save();
 		}

 		$respuesta = array('respuesta' => 'Datos procesados Correctamente','faltantes' => $foliosNoprocesados);

 		return Response::json($respuesta);
 		
	}

	public function consulta($folio){

 
		$datos =  DB::select("EXEC MVQualitasWSconsulta @folio = '$folio'");
		$data = array();

		foreach ($datos as $dato){

			$folio = $dato->folioSistema;
			$fecha = $dato->FechaCaptura;

			$nombre = $this->nombreArchivo($folio) . "(QS07.jpg,GN19.jpg,ME02.pdf)";
			$imagenes = $this->archivos($folio,$fecha,$nombre);

			$detalle[] = $imagenes;

		    $data[] = array(
				"folioElectronico" => $dato->folioElectronico,
				"folioAdministradora" => $dato->folioAdministradora,
				"folioSistema" => $dato->folioSistema,
				"claveproovedor" => $dato->claveproovedor,
				"claveprestador" => $dato->claveprestador,
				"Siniestro" => $dato->Siniestro,
				"Reporte" => $dato->Reporte,
				"Poliza" => $dato->Poliza,
				"Lesionado" => $dato->Lesionado,
				"Afectado" => $dato->Afectado,
				"Cobertura" => $dato->Cobertura,
				"Subtotal" => $dato->Subtotal,
				"iva" => $dato->iva,
				"Descuento" => $dato->Descuento,
				"Total" => $dato->Total,
				"TipoUnidad" => $dato->TipoUnidad,
				"FechaCaptura" => $dato->FechaCaptura,
				"Unidad" => $dato->Unidad,
				"Estatus" => $dato->Estatus,
				"FechaRecepcion" => $dato->FechaRecepcion,
				"ID_asociado" => $dato->ID_asociado,
				"nombreArchivos" => $nombre,
				"imagenes" => $imagenes
		    );

		}

		return $data;
		
	}

	public function invalidos(){
 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		return DB::select("EXEC MVQualitasWSrechazado @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");
	}

	public function envio($envio){
 		return DB::select("EXEC MVQualitasWSenviado @idenvio = $envio");
	}

	public function envios(){

		$fechaini = Input::get('fechaini');
		$fechafin = Input::get('fechafin') . ' 23:59:59';

 		$envios = DB::select("SELECT EnviosQualitas.ENQ_claveint, ENQ_fechaenvio,ENQ_procesado, (SELECT COUNT(*) from DetalleEnvio where DetalleEnvio.ENQ_claveint = EnviosQualitas.ENQ_claveint) as Cuenta FROM EnviosQualitas WHERE ENQ_fechaenvio BETWEEN '$fechaini' and '$fechafin'");

 		return $envios;	
	}

	public function incompletos(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		$datos =  DB::select("EXEC MVQualitasWS @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");

		$data = array();
		
		foreach ($datos as $dato) {
			
            $valor5 = $dato->claveprestador;
			$valor6 = $dato->Siniestro;
            $valor7 = $dato->Reporte;
            $valor10 = $dato->Cobertura;
            $valor15 = $dato->Afectado;
            $valor18 = $dato->Unidad;

            if ($valor6 == $valor7) {
				$motivo = 'Siniestro igual a reporte';
			}elseif ($valor10 == 99 || $valor15 == 99) {
				$motivo = 'Cobertura o Afectado Invalido';
			}elseif ($valor5 == '07370' && $valor18 != 33) {
				$motivo = 'Falta Clave de Provedor Qualitas';
			}

    		if ($valor6 == $valor7 || $valor10 == 99 || $valor15 == 99 || ($valor5 == '07370' && $valor18 != 33) ) {

    			$data[] = array(
			        "folioElectronico" => $dato->folioElectronico,
		            "folioAdministradora" => $dato->folioAdministradora,
		            "folioSistema" =>$dato->folioSistema,
		            "claveproovedor" =>$dato->claveproovedor,
		            "claveprestador" => $dato->claveprestador,
		            "Siniestro" => $dato->Siniestro,
		            "Reporte" => $dato->Reporte,
		            "Poliza" =>$dato->Poliza,
					"Lesionado" =>$dato->Lesionado,
					"Afectado" => $dato->Afectado,
					"Cobertura" => $dato->Cobertura,
					"Subtotal" => $dato->Subtotal,
					"iva" => $dato->iva,
					"Descuento" => $dato->Descuento,
					"Total" => $dato->Total,
					"TipoUnidad" => $dato->TipoUnidad,
					"FechaCaptura" => $dato->FechaCaptura,
					"Motivo" => $motivo
			    );

    		}
            


		}

		return $data;
	
	}

	public function generaArchivos(){

		$datos = Input::all();

		$correctos = array();
		$incorrectos = array();
		$archivos = array();
		$correctos = array();
		$imagenes = array();
		
		$Dia = date('d');
		$Mes = date('m');
		$Anyo = date('Y');


		$clave = $this->generar_clave();
		$fechacarpeta =  $Dia . "-" . $Mes . "-" . $Anyo;
		$filename = "archivo-". $fechacarpeta . "-". $clave . ".zip";
		$carpetaexporta = public_path().'/exports/'.$filename;

		// $zip = new ZipArchive();
		// $zip->open($filename, ZipArchive::CREATE);

		$pesototal = 14900000;//equivale a 15Mb peso total del zip
		$peso = 0;
		$pesofolio = 0;
		$maximofolios = 50;//maximo de folios por archivo
		$cuentaFolio = 0;

		//verificamos folio x folio para ver si cumople con 
		foreach ($datos as $dato) {
			
			$folio = $dato['folioSistema'];
			$fecha = $dato['FechaCaptura'];
			$fe = isset($dato['FacturaEx']) == null ? 'NO' : $dato['FacturaEx'];

			$MesNro = date('m', strtotime($fecha));
			$DiaNro = date('d', strtotime($fecha));
			$AnyoNro = date('Y', strtotime($fecha));
			
			if($MesNro=='01'){ 
				$MesNro="1"; 
			} 

			if($MesNro=='02'){ 
				$MesNro="2"; 
			} 

			if($MesNro=='03'){ 
				$MesNro="3"; 
			} 

			if($MesNro=='04'){ 
				$MesNro="4"; 
			} 

			if($MesNro=='05'){ 
				$MesNro="5"; 
			} 

			if($MesNro=='06'){ 
				$MesNro="6"; 
			} 

			if($MesNro=='07'){ 
				$MesNro="7"; 
			} 

			if($MesNro=='08'){ 
				$MesNro="8"; 
			} 

			if($MesNro=='09'){ 
				$MesNro="9"; 
			} 

			$nombre = $this->nombreArchivo($folio);

			//$ruta = "C:\\Users\\salcala.MEDICAVIAL\\Desktop\\MV\\QUALITAS\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;
			//ruta en producción
			$ruta = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;


			$archivo1 = $ruta . "\\" . $nombre . "QS07.jpg";
			$archivo2 = $ruta . "\\" . $nombre . "GN19.jpg";
			$archivo3 = $ruta . "\\" . $nombre . "ME02.pdf";
			
			if (File::exists($archivo1) && File::exists($archivo2) && File::exists($archivo3)) {


				if ($cuentaFolio < $maximofolios) {

					if ($peso < $pesototal ) {
						
						$pesoarchivo1 = filesize($archivo1);
						$pesoarchivo2 = filesize($archivo2);
						$pesoarchivo3 = filesize($archivo3);

						//sumamos el peso de los tres archivos del folio
						$pesofolio = $pesoarchivo1 + $pesoarchivo2 + $pesoarchivo3;
					    
					    $peso = $pesofolio + $peso;

					    array_push($imagenes, $archivo1);
					    array_push($imagenes, $archivo2);
					    array_push($imagenes, $archivo3);

					    array_push($archivos, $dato);

					    $cuentaFolio = $cuentaFolio + 1;

					}

				}  

				array_push($correctos, $dato);
			   
			}else {

				array_push($incorrectos, $dato);

			}

		}

		// $zip->close();
		Zipper::make($carpetaexporta)->add($imagenes);

		$resultado = array('correctos' => $correctos,'incorrectos' => $incorrectos, 'archivo' =>  $filename,'comprimidos' => $archivos);

 		return Response::json($resultado);
	
	}

	public function general(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		
		$datos =  DB::select("EXEC MVQualitasWSgeneral @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");

		$data = array();

		foreach ($datos as $dato) {
			
            $valor5 = $dato->claveprestador;
			$valor6 = $dato->Siniestro;
            $valor7 = $dato->Reporte;
            $valor10 = $dato->Cobertura;
            $valor18 = $dato->Unidad;

            if ($valor6 != $valor7) {

            	if ($valor10 != 99) {

            		if ($valor5 == '07370' && $valor18 == 33 ) {

            			$data[] = array(
					        "folioElectronico" => $dato->folioElectronico,
				            "folioAdministradora" => $dato->folioAdministradora,
				            "folioSistema" =>$dato->folioSistema,
				            "claveproovedor" =>$dato->claveproovedor,
				            "claveprestador" => $dato->claveprestador,
				            "Siniestro" => $dato->Siniestro,
				            "Reporte" => $dato->Reporte,
				            "Poliza" =>$dato->Poliza,
							"Lesionado" =>$dato->Lesionado,
							"Afectado" => $dato->Afectado,
							"Cobertura" => $dato->Cobertura,
							"Subtotal" => $dato->Subtotal,
							"iva" => $dato->iva,
							"Descuento" => $dato->Descuento,
							"Total" => $dato->Total,
							"TipoUnidad" => $dato->TipoUnidad,
							"FechaCaptura" => $dato->FechaCaptura
					    );

            		}else{

						$data[] = array(
					        "folioElectronico" => $dato->folioElectronico,
				            "folioAdministradora" => $dato->folioAdministradora,
				            "folioSistema" =>$dato->folioSistema,
				            "claveproovedor" =>$dato->claveproovedor,
				            "claveprestador" => $dato->claveprestador,
				            "Siniestro" => $dato->Siniestro,
				            "Reporte" => $dato->Reporte,
				            "Poliza" =>$dato->Poliza,
							"Lesionado" =>$dato->Lesionado,
							"Afectado" => $dato->Afectado,
							"Cobertura" => $dato->Cobertura,
							"Subtotal" => $dato->Subtotal,
							"iva" => $dato->iva,
							"Descuento" => $dato->Descuento,
							"Total" => $dato->Total,
							"TipoUnidad" => $dato->TipoUnidad,
							"FechaCaptura" => $dato->FechaCaptura
					    );
            			
            		}

            	}
            }


		}

		return $data;

	}

	public function principal(){

 		$datos =  Input::all();
		
 		//aqui se actualiza en pase para mandarlos a rechazos
 		foreach ($datos as $dato) {
 			$folio = $dato['folioSistema'];
 			$pase = Pase::find($folio);
 			$pase->PAS_procQ = 0;
 			$pase->save();
 		}

 		return Response::json(array('respuesta' => 'Datos procesados Correctamente'));

	}

	public function procesa(){

 		$correctos =  Input::get('correctos'); 
 		$incorrectos =  Input::get('incorrectos'); 
	    
	    $envio = new Qualitas;

	    $envio->ENQ_fechaenvio = date('d/m/Y H:i:s');
	    $envio->ENQ_procesado = 0;
	    $envio->save();

	    //toma el ultimo id insertado
	    $ultimo = $envio->ENQ_claveint;

	    foreach ($correctos as $dato) {
				
				$folio = $dato['folioSistema'];

				$detalle = new Qualitasdetalle;

				$detalle->ENQ_claveint = $ultimo;
				$detalle->PAS_folio = $folio;
				$detalle->DEE_procesado = 0;
				$detalle->save(); 

				$folio = Pase::find($folio);
				$folio->PAS_procQ = 1;
				$folio->save();

		}


		foreach ($incorrectos as $data) {
				
				$folio = $data['folioSistema'];

				$pase = Pase::find($folio);
				$pase->PAS_procQ = 3;
				$pase->save();

		}


		return Response::json(array('respuesta' => 'Proceso Exitoso'));
	
	}

	public function rechazos(){

 		$datos =  Input::all();
		
 		//aqui se actualiza en pase para mandarlos a rechazos
 		foreach ($datos as $dato) {
 			$folio = $dato['PAS_folio'];
 			$motivo = $dato['Motivo'];
 			$pase = Pase::find($folio);
 			$pase->PAS_procQ = 4;
 			$pase->PAS_procQObs = $motivo;
 			$pase->save();
 		}

 		return Response::json(array('respuesta' => 'Datos procesados Correctamente'));

	}

	public function reporte($reporte){

		$wsdl = "http://201.151.239.105:8070/wsProveedores/siniestros?wsdl";
	    $username = '07370';
	    $password = '535466';


        try {

		    $client = new SoapClient($wsdl,array('trace' => 1));

	        $wsse_header = new WsseAuthHeader($username, $password);
	        $client->__setSoapHeaders(array($wsse_header));
	        $param = array('reporte' => $reporte);
	        $respuesta = $client->getSiniestroByReporte($param);
		    
	        foreach ($respuesta as $key => $value) {

	        	$data = array(
	                    'reporte' => $reporte,
	                    'siniestro' => $value->numeroSiniestro,
	                    'poliza' => $value->numeroPoliza,
	                    'causa' => $value->causa
				);

	        	// echo $reporte . '<br>';
	        	// echo $value->numeroSiniestro.  '<br>';
	        	// echo $value->numeroPoliza . '<br>';
	        	// echo $value->causa.  '<br>';



       		}

		} catch (Exception $e) {
	        $data = array('respuesta' => 'Reporte invalido');
	        // echo 'error';
		}

		return $data;
        // print_r($data);

	}

	public function sinarchivo(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		$datos =  DB::select("EXEC MVQualitasWSarchivos @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");

		$data = array();

		foreach ($datos as $dato){

			$folio = $dato->folioSistema;
			$fecha = $dato->FechaCaptura;

			$nombre = $this->nombreArchivo($folio);
			$imagenes = $this->archivos($folio,$fecha,$nombre);

			$detalle[] = $imagenes;

		    $data[] = array(
		        "folioElectronico" => $dato->folioElectronico,
	            "folioAdministradora" => $dato->folioAdministradora,
	            "folioSistema" =>$dato->folioSistema,
	            "claveproovedor" =>$dato->claveproovedor,
	            "claveprestador" => $dato->claveprestador,
	            "Siniestro" => $dato->Siniestro,
	            "Reporte" => $dato->Reporte,
	            "Poliza" =>$dato->Poliza,
				"Lesionado" =>$dato->Lesionado,
				"Afectado" => $dato->Afectado,
				"Cobertura" => $dato->Cobertura,
				"Subtotal" => $dato->Subtotal,
				"iva" => $dato->iva,
				"Descuento" => $dato->Descuento,
				"Total" => $dato->Total,
				"TipoUnidad" => $dato->TipoUnidad,
				"FechaCaptura" => $dato->FechaCaptura
		    );

		}

		$respuesta = array('listado' => $data,'detalle' => $detalle);

		return $respuesta;
	
	
	}

	public function sinprocesar(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		$datos =  DB::select("EXEC MVQualitasWS @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");

		$data = array();

		foreach ($datos as $dato) {
			
            $valor5 = $dato->claveprestador;
			$valor6 = $dato->Siniestro;
            $valor7 = $dato->Reporte;
            $valor10 = $dato->Cobertura;
            $valor18 = $dato->Unidad;

            if ($valor6 != $valor7) {

            	if ($valor10 != 99) {

            		if ($valor5 == '07370' && $valor18 == 33 ) {

            			$data[] = array(
					        "folioElectronico" => $dato->folioElectronico,
				            "folioAdministradora" => $dato->folioAdministradora,
				            "folioSistema" =>$dato->folioSistema,
				            "claveproovedor" =>$dato->claveproovedor,
				            "claveprestador" => $dato->claveprestador,
				            "Siniestro" => $dato->Siniestro,
				            "Reporte" => $dato->Reporte,
				            "Poliza" =>$dato->Poliza,
							"Lesionado" =>$dato->Lesionado,
							"Afectado" => $dato->Afectado,
							"Cobertura" => $dato->Cobertura,
							"Subtotal" => $dato->Subtotal,
							"iva" => $dato->iva,
							"Descuento" => $dato->Descuento,
							"Total" => $dato->Total,
							"TipoUnidad" => $dato->TipoUnidad,
							"FacturaEx" => $dato->FacturaEx,
							"FechaCaptura" => $dato->FechaCaptura
					    );

            		}else{

						$data[] = array(
					        "folioElectronico" => $dato->folioElectronico,
				            "folioAdministradora" => $dato->folioAdministradora,
				            "folioSistema" =>$dato->folioSistema,
				            "claveproovedor" =>$dato->claveproovedor,
				            "claveprestador" => $dato->claveprestador,
				            "Siniestro" => $dato->Siniestro,
				            "Reporte" => $dato->Reporte,
				            "Poliza" =>$dato->Poliza,
							"Lesionado" =>$dato->Lesionado,
							"Afectado" => $dato->Afectado,
							"Cobertura" => $dato->Cobertura,
							"Subtotal" => $dato->Subtotal,
							"iva" => $dato->iva,
							"Descuento" => $dato->Descuento,
							"Total" => $dato->Total,
							"TipoUnidad" => $dato->TipoUnidad,
							"FacturaEx" => $dato->FacturaEx,
							"FechaCaptura" => $dato->FechaCaptura
					    );
            			
            		}

            	}
            }


		}

		return $data;
	
	}

	public function sinprocesarFE(){

 		$fechaini =  Input::get('fechaini'); 
	    $fechafin =  Input::get('fechafin'); 
		$datos =  DB::select("EXEC MVQualitasFEWS @fechaini = '$fechaini', @fechafin = '$fechafin 23:59:58.999'");

		$data = array();

		foreach ($datos as $dato) {
			
            $valor5 = $dato->claveprestador;
			$valor6 = $dato->Siniestro;
            $valor7 = $dato->Reporte;
            $valor10 = $dato->Cobertura;
            $valor18 = $dato->Unidad;

            if ($valor6 != $valor7) {

            	if ($valor10 != 99) {

            		if ($valor5 == '07370' && $valor18 == 33 ) {

            			$data[] = array(
					        "folioElectronico" => $dato->folioElectronico,
				            "folioAdministradora" => $dato->folioAdministradora,
				            "folioSistema" =>$dato->folioSistema,
				            "claveproovedor" =>$dato->claveproovedor,
				            "claveprestador" => $dato->claveprestador,
				            "Siniestro" => $dato->Siniestro,
				            "Reporte" => $dato->Reporte,
				            "Poliza" =>$dato->Poliza,
							"Lesionado" =>$dato->Lesionado,
							"Afectado" => $dato->Afectado,
							"Cobertura" => $dato->Cobertura,
							"Subtotal" => $dato->Subtotal,
							"iva" => $dato->iva,
							"Descuento" => $dato->Descuento,
							"Total" => $dato->Total,
							"TipoUnidad" => $dato->TipoUnidad,
							"FacturaEx" => $dato->FacturaEx,
							"FechaCaptura" => $dato->FechaCaptura
					    );

            		}else{

						$data[] = array(
					        "folioElectronico" => $dato->folioElectronico,
				            "folioAdministradora" => $dato->folioAdministradora,
				            "folioSistema" =>$dato->folioSistema,
				            "claveproovedor" =>$dato->claveproovedor,
				            "claveprestador" => $dato->claveprestador,
				            "Siniestro" => $dato->Siniestro,
				            "Reporte" => $dato->Reporte,
				            "Poliza" =>$dato->Poliza,
							"Lesionado" =>$dato->Lesionado,
							"Afectado" => $dato->Afectado,
							"Cobertura" => $dato->Cobertura,
							"Subtotal" => $dato->Subtotal,
							"iva" => $dato->iva,
							"Descuento" => $dato->Descuento,
							"Total" => $dato->Total,
							"TipoUnidad" => $dato->TipoUnidad,
							"FacturaEx" => $dato->FacturaEx,
							"FechaCaptura" => $dato->FechaCaptura
					    );
            			
            		}

            	}
            }


		}

		return $data;
	
	}

	public function renombrar(){

 		$datos =  Input::all(); 
 		$detalle = array();
	   	
		foreach ($datos as $dato){

			$folio = $dato['folioSistema'];
			$fecha = $dato['FechaCaptura'];

			$nombreEsperado = $this->nombreArchivo($folio);
			$renombres = $this->archivoRenombra($folio,$fecha,$nombreEsperado);

			$detalle[] = $renombres;

		}

		return $detalle;
	
	
	}

	public function renombrarIndividual($folio){

		$fecha = Pase::where('PAS_folio',$folio)->first()->PAS_fechaCaptura;
		$nombreEsperado = $this->nombreArchivo($folio);
		$renombres = $this->archivoRenombra($folio,$fecha,$nombreEsperado);

		return Response::json(array('respuesta' => 'Renombre Completo'));

	}

	public function test($folio){
		$fecha = Pase::where('PAS_folio',$folio)->first()->PAS_fechaCaptura;
		$nombre = $this->nombreArchivo($folio);

		return $this->archivos($folio, $fecha, $nombre);
	}

	//funciones privadas
	private function generar_clave(){ 

       	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<12;$i++) {
			$cad .= substr($str,rand(0,62),1);
		}
		return $cad;

	}

	public function nombreArchivo($folio){
		
		$archivo = DB::select("EXEC MVImgs_Datos @folio = '$folio'");
		foreach ($archivo as $data) {
			$nombre = $data->Archivo;
		}

		return $nombre;

	}

	private function archivos($folio, $fecha, $nombre){

		$MesNro = date('m', strtotime($fecha));
		$DiaNro = date('d', strtotime($fecha));
		$AnyoNro = date('Y', strtotime($fecha));
		

		if($MesNro=='01'){ 
			$MesNro="1"; 
		} 

		if($MesNro=='02'){ 
			$MesNro="2"; 
		} 

		if($MesNro=='03'){ 
			$MesNro="3"; 
		} 

		if($MesNro=='04'){ 
			$MesNro="4"; 
		} 

		if($MesNro=='05'){ 
			$MesNro="5"; 
		} 

		if($MesNro=='06'){ 
			$MesNro="6"; 
		} 

		if($MesNro=='07'){ 
			$MesNro="7"; 
		} 

		if($MesNro=='08'){ 
			$MesNro="8"; 
		} 

		if($MesNro=='09'){ 
			$MesNro="9"; 
		} 

		//$ruta = "C:\\Users\\salcala.MEDICAVIAL\\Desktop\\MV\\QUALITAS\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;
		//ruta en producción
		$ruta = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;

		$encontrados = array();

		$encontrados['folio'] = $folio;
		$encontrados['Fecha_Captura'] = $fecha;
		$encontrados['nombreEsperado'] = $nombre;

		//verifica 
		if (file_exists($ruta)){

			$directorio = opendir($ruta); //ruta actual

			$encontrados['QS_07'] = 0;
			$encontrados['ME_024'] = 0;
			$encontrados['ME_023'] = 0;
			$encontrados['ME_022'] = 0;
			$encontrados['ME_021'] = 0;
			$encontrados['GN_19'] = 0;
			$encontrados['QS07'] = 0;
			$encontrados['ME021'] = 0;
			$encontrados['ME022'] = 0;
			$encontrados['ME023'] = 0;
			$encontrados['ME024'] = 0;
			$encontrados['ME02'] = 0;
			$encontrados['GN19'] = 0;
			$encontrados['nombreActual'] = '';

			while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
			{
			    if (!is_dir($archivo))//verificamos si es o no un directorio
			    {	
			    	//originales

			    	if (preg_match('/QS_07.jpg/' , $archivo)) {
			    		$encontrados['QS_07'] = 1;
			    		//$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME_024.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_024'] = 1;
			    		//$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME_023.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_023'] = 1;
			    		//$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME_022.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_022'] = 1;
			    		//$encontrados['nombreActual'] = $archivo;
			    	}


			    	if (preg_match('/ME_021.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_021'] = 1;
			    		//$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/GN_19.jpg/' , $archivo) == 1) {
			    		$encontrados['GN_19'] = 1;
			    		//$encontrados['nombreActual'] = $archivo;
			    	}

			    	//comprimidas

			    	if (preg_match('/QS07.jpg/' , $archivo) == 1) {
			    		$encontrados['QS07'] = 1;
			    		$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME021.jpg/' , $archivo) == 1) {
			    		$encontrados['ME021'] = 1;
			    		$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME022.jpg/' , $archivo) == 1) {
			    		$encontrados['ME022'] = 1;
			    		$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME023.jpg/' , $archivo) == 1) {
			    		$encontrados['ME023'] = 1;
			    		$encontrados['nombreActual'] = $archivo;
			    	}

			    	if (preg_match('/ME024.jpg/' , $archivo) == 1) {
			    		$encontrados['ME024'] = 1;
			    		$encontrados['nombreActual'] = $archivo;
			    	}


			    	if (preg_match('/GN19.jpg/' , $archivo) == 1) {
			    		$encontrados['GN19'] = 1;
			    		$encontrados['nombre'] = $archivo;
			    	}

			    	if (preg_match( '/ME02.pdf/' , $archivo) == 1) {
			    		$encontrados['ME02'] = 1;
			    		$encontrados['nombre'] = $archivo;
			    	}

			    }


			}

		}else{

			$encontrados['QS_07'] = 0;
			$encontrados['ME_024'] = 0;
			$encontrados['ME_023'] = 0;
			$encontrados['ME_022'] = 0;
			$encontrados['ME_021'] = 0;
			$encontrados['GN_19'] = 0;
			$encontrados['QS07'] = 0;
			$encontrados['ME021'] = 0;
			$encontrados['ME022'] = 0;
			$encontrados['ME023'] = 0;
			$encontrados['ME024'] = 0;
			$encontrados['ME02'] = 0;
			$encontrados['GN19'] = 0;
			$encontrados['nombreActual'] = 'No existe carpeta';
		}

		return $encontrados;

	}

	private function archivoRenombra($folio, $fecha, $nombreEsperado){

		$MesNro = date('m', strtotime($fecha));
		$DiaNro = date('d', strtotime($fecha));
		$AnyoNro = date('Y', strtotime($fecha));
		

		if($MesNro=='01'){ 
			$MesNro="1"; 
		} 

		if($MesNro=='02'){ 
			$MesNro="2"; 
		} 

		if($MesNro=='03'){ 
			$MesNro="3"; 
		} 

		if($MesNro=='04'){ 
			$MesNro="4"; 
		} 

		if($MesNro=='05'){ 
			$MesNro="5"; 
		} 

		if($MesNro=='06'){ 
			$MesNro="6"; 
		} 

		if($MesNro=='07'){ 
			$MesNro="7"; 
		} 

		if($MesNro=='08'){ 
			$MesNro="8"; 
		} 

		if($MesNro=='09'){ 
			$MesNro="9"; 
		} 

		//$ruta = "C:\\Users\\salcala.MEDICAVIAL\\Desktop\\MV\\QUALITAS\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;
		//ruta en producción
		$ruta = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro . "\\" . $MesNro . "\\". $folio;

		$rutaRenombre = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro . "\\" . $MesNro . "\\". $folio . "\\";

		$encontrados = array();

		$encontrados['folio'] = $folio;
		$encontrados['Fecha_Captura'] = $fecha;
		$encontrados['nombreNuevo'] = '';
    	$encontrados['nombreAnterior'] = '';

		$encontrados['QS_07'] = 0;
		$encontrados['ME_024'] = 0;
		$encontrados['ME_023'] = 0;
		$encontrados['ME_022'] = 0;
		$encontrados['ME_021'] = 0;
		$encontrados['GN_19'] = 0;
		$encontrados['QS07']  = 0;
		$encontrados['ME021'] = 0;
		$encontrados['ME022'] = 0;
		$encontrados['ME023'] = 0;
		$encontrados['ME024'] = 0;
		$encontrados['ME02'] = 0;
		$encontrados['GN19'] = 0;

		$encontrados['QS_07/Renombrado'] = 0;
		$encontrados['ME_024/Renombrado'] = 0;
		$encontrados['ME_023/Renombrado'] = 0;
		$encontrados['ME_022/Renombrado'] = 0;
		$encontrados['ME_021/Renombrado'] = 0;
		$encontrados['GN_19/Renombrado'] = 0;
		$encontrados['QS07/Renombrado']  = 0;
		$encontrados['ME021/Renombrado'] = 0;
		$encontrados['ME022/Renombrado'] = 0;
		$encontrados['ME023/Renombrado'] = 0;
		$encontrados['ME024/Renombrado'] = 0;
		$encontrados['ME02/Renombrado'] = 0;
		$encontrados['GN19/Renombrado'] = 0;


		$encontrados['nombreAnterior'] = '';

		//verifica 
		if (file_exists($ruta)){

			$directorio = opendir($ruta); //ruta actual


			while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
			{
			    if (!is_dir($archivo))//verificamos si es o no un directorio
			    {	
			    	//originales
			    	$archivoNuevo = $rutaRenombre . $nombreEsperado;
			    	$encontrados['nombreNuevo'] = $nombreEsperado;
			    	$encontrados['nombreAnterior'] = $archivo;

			    	if (preg_match('/QS_07.jpg/' , $archivo)) {
			    		$encontrados['QS_07'] = 1;
			    		if ($archivo != $nombreEsperado) {
							$encontrados['QS_07/Renombrado'] = 1;
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'QS_07.jpg');
			    		}
			    	}

			    	if (preg_match('/ME_024.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_024'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME_024/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME_024.jpg');
			    		}
			    	}

			    	if (preg_match('/ME_023.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_023'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME_023/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME_023.jpg');
			    		}
			    	}

			    	if (preg_match('/ME_022.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_022'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME_022/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME_022.jpg');
			    		}
			    	}


			    	if (preg_match('/ME_021.jpg/' , $archivo) == 1) {
			    		$encontrados['ME_021'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME_021/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME_021.jpg');
			    		}
			    	}

			    	if (preg_match('/GN_19.jpg/' , $archivo) == 1) {
			    		$encontrados['GN_19'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['GN_19/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'GN_19.jpg');
			    		}
			    	}

			    	//comprimidas

			    	if (preg_match('/QS07.jpg/' , $archivo) == 1) {
			    		$encontrados['QS07'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['QS07/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'QS07.jpg');
			    		}
			    	}

			    	if (preg_match('/ME021.jpg/' , $archivo) == 1) {
			    		$encontrados['ME021'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME021/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME021.jpg');
			    		}
			    	}

			    	if (preg_match('/ME022.jpg/' , $archivo) == 1) {
			    		$encontrados['ME022'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME022/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME022.jpg');
			    		}
			    	}

			    	if (preg_match('/ME023.jpg/' , $archivo) == 1) {
			    		$encontrados['ME023'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME023/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME023.jpg');
			    		}
			    	}

			    	if (preg_match('/ME024.jpg/' , $archivo) == 1) {
			    		$encontrados['ME024'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME024/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME024.jpg');
			    		}
			    	}


			    	if (preg_match('/GN19.jpg/' , $archivo) == 1) {
			    		$encontrados['GN19'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['GN19/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'GN19.jpg');
			    		}
			    	}

			    	if (preg_match( '/ME02.pdf/' , $archivo) == 1) {
			    		$encontrados['ME02'] = 1;
			    		if ($archivo != $nombreEsperado) {
			    			$encontrados['ME02/Renombrado'] = 1;	
			    			rename ($rutaRenombre . $archivo, $archivoNuevo . 'ME02.pdf');
			    		}
			    	}

			    }


			}

		}else{

			$encontrados['nombreActual'] = 'No existe carpeta';
		}

		return $encontrados;

	}

}
