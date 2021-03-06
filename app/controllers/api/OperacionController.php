<?php

class OperacionController extends BaseController {

	//funcion para editar datos
	public function editaDatos(){
		
		//primero obtenemos todos los datos del post que manda angular
		$web = Input::get('web');
		$folio = Input::get('folio');
		$poliza = Input::get('poliza');
		$reporte = Input::get('reporte');
		$siniestro = Input::get('siniestro');
		$riesgo = Riesgo::find(Input::get('riesgo'));
		$producto = Producto::find(Input::get('producto'));
		$cliente = Empresa::find(Input::get('cliente'));
		$materno = Input::get('materno');
		$paterno = Input::get('paterno');
		$nombre = Input::get('nombre');
		$lesionado = $paterno . ' ' . $materno . ' ' . $nombre;
		$fechaNacimiento = Input::get('fechaNacimiento');

		// tomamos los valores que existian antes de guardar los cambios
		$folioWebAntes = folioWeb::find($folio);
		$polizaAntes = $folioWebAntes->Exp_poliza;
		$reporteAntes = $folioWebAntes->Exp_reporte;
		$siniestroAntes = $folioWebAntes->Exp_siniestro;
		$riesgoAntes = $folioWebAntes->RIE_clave;
		$productoAntes = $folioWebAntes->Pro_clave;
		$clienteAntes = $folioWebAntes->Cia_clave;
		$lesionadoAntes = $folioWebAntes->Exp_completo;
		$fechaNacimientoAntes = $folioWebAntes->Exp_fechaNac2;

		//verificamos si esta en MV o solo en Web
		if ($web == 0) {
			

			// DB::select("EXEC MV_REW_Captura  @fechaini='08/02/2016 00:00:00', @fechafin='18/02/2016 23:59:58.999' " );

		}

		// guardamos en web 
		$folioWeb = folioWeb::find($folio);
		$folioWeb->Exp_poliza = $poliza;
		$folioWeb->Exp_reporte = $reporte;
		$folioWeb->Exp_siniestro = $siniestro;
		$folioWeb->RIE_clave = $riesgo->RIE_claveWeb;
		$folioWeb->Pro_clave = $producto->PRO_claveWeb;
		$folioWeb->Cia_clave = $cliente->EMP_claveWeb;
		$folioWeb->Exp_fechaNac2 = $fechaNacimiento;
		$folioWeb->Exp_materno = $materno;
		$folioWeb->Exp_paterno = $paterno;
		$folioWeb->Exp_nombre = $nombre;
		$folioWeb->Exp_completo = $lesionado;
		$folioWeb->save();

		//expedienteInfo
		$ExpedienteInfo = ExpedienteInfo::find($EXP_folio);
		if ($ExpedienteInfo->count() > 0) {
			$ExpedienteInfo->EXP_poliza = $poliza;
			$ExpedienteInfo->EXP_reporte= $reporte;
			$ExpedienteInfo->EXP_siniestro = $siniestro;
			$ExpedienteInfo->RIE_claveint = $riesgo->RIE_claveWeb;
			$ExpedienteInfo->CIA_clave = $cliente->EMP_claveWeb;
			$ExpedienteInfo->EXP_lesionado = $lesionado;
			$ExpedienteInfo->save();
		}

		//Documento
		$Documento = Documento::where('DOC_folio',$folio)->first();
		if (count($Documento) > 0) {

			$Documento->PRO_clave = $producto->PRO_claveWeb;
			$Documento->CIA_clave = $cliente->EMP_claveWeb;
			$Documento->save();
			# code...
		}
		
		//ClasificacionLes
		$ClasificacionLes = ClasificacionLes::where('Exp_folio',$folio)->first();
		if (count($ClasificacionLes) > 0) {
			# code...
			$ClasificacionLes->Exp_poliza = $poliza;
			$ClasificacionLes->Exp_reporte = $reporte;
			$ClasificacionLes->Exp_siniestro = $siniestro;
			$ClasificacionLes->Cia_clave = $cliente->EMP_claveWeb;
			$ClasificacionLes->Exp_nombre = $nombre;
			$ClasificacionLes->save();
		}

		// verificamos que este en local de tabla que importa de web
		$folioWebLocal = ExpedienteWebLocal::find($folio);
		if($folioWebLocal->count() > 0){

			$folioWebLocal->EXW_poliza = $poliza;
			$folioWebLocal->EXW_siniestro = $siniestro;
			$folioWebLocal->EXW_reporte = $reporte;

			$folioWebLocal->EXW_riesgo = $riesgo->RIE_claveWeb;
			$folioWebLocal->EXW_producto = $producto->PRO_claveWeb;

			$folioWebLocal->Cia_clave = $cliente->EMP_claveWeb;
			$folioWebLocal->EMP_claveint = $cliente->EMP_claveint;

			$folioWebLocal->EXW_materno = $materno;
			$folioWebLocal->EXW_paterno = $paterno;
			$folioWebLocal->EXW_nombre = $nombre;

			$folioWebLocal->save();
		}

		//guardamos el log de cambios por cada campo que se modifico en caso de existir modificación
		if ($polizaAntes != $poliza){

			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'EXP_poliza';
			$bitacora->BIT_anterior = $polizaAntes;
			$bitacora->BIT_nuevo = $poliza;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

		if ($reporteAntes != $reporte){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'EXP_reporte';
			$bitacora->BIT_anterior = $reporteAntes;
			$bitacora->BIT_nuevo = $reporte;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

		if ($siniestroAntes != $siniestro){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'EXP_siniestro';
			$bitacora->BIT_anterior = $siniestroAntes;
			$bitacora->BIT_nuevo = $siniestro;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

		if ($riesgoAntes != $riesgo->RIE_claveWeb){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'RIE_clave';
			$bitacora->BIT_anterior = $riesgoAntes;
			$bitacora->BIT_nuevo = $riesgo->RIE_claveWeb;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}


		if ($productoAntes != $producto->PRO_claveWeb){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'Pro_clave';
			$bitacora->BIT_anterior = $productoAntes;
			$bitacora->BIT_nuevo = $producto->PRO_claveWeb;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

		if ($clienteAntes != $cliente->EMP_claveWeb){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'Cia_clave';
			$bitacora->BIT_anterior = $clienteAntes;
			$bitacora->BIT_nuevo = $cliente->EMP_claveWeb;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

		if ($lesionadoAntes != $lesionado){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'EXP_completo';
			$bitacora->BIT_anterior = $lesionadoAntes;
			$bitacora->BIT_nuevo = $lesionado;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

		if ($fechaNacimientoAntes != $fechaNacimiento){
			$bitacora = new BitacoraWeb;
			$bitacora->EXP_folio = $folio;
			$bitacora->BIT_tabla = 'Expediente';
			$bitacora->BIT_campo = 'Exp_fechaNac2';
			$bitacora->BIT_anterior = $fechaNacimientoAntes;
			$bitacora->BIT_nuevo = $fechaNacimiento;
			$bitacora->BIT_fecha = date('Y-m-d H:i:s');
			$bitacora->USU_login = 'algo';
			$bitacora->save();
		}

	}

	//edita informacion del usuario en flujo
	public function editaUsuario($id){

		$editaPass = Input::get('editaPassword');

		$usuario = User::find($id);


		if ($editaPass == true ){

			$usuario->USU_password = md5(Input::get('password'));
		}

		$usuario->USU_nombre = Input::get('nombre');
		$usuario->USU_login = Input::get('login');
	    $usuario->USU_usuarioWeb = Input::get('userweb');
	    $usuario->USU_factivo = Input::get('usuactivo');
	    $usuario->USU_fcaptura = Input::get('captura');
	    $usuario->USU_fcontrolDocumentos = Input::get('controldoc');
	    $usuario->USU_fconsultaIndividual = Input::get('consulindiv');
	    $usuario->USU_ftickets = Input::get('tickets');
	    $usuario->USU_fmanual = Input::get('flujomanual');
	    $usuario->USU_fqualitas = Input::get('qualitas');
	    $usuario->USU_freportes = Input::get('reportes');
	    $usuario->USU_fpagos = Input::get('flujopagos');
	    $usuario->USU_fdocumentos = Input::get('flujodoc');
	    $usuario->USU_fusuarios = Input::get('usuarios');
	    $usuario->USU_fticketPagos = Input::get('ticketspagos');
	    $usuario->USU_fexpress = Input::get('facexpress');
	    $usuario->save();

	    $areaUsuario = UsuarioArea::where('USU_claveint', '=', $id )->update(array('ARO_claveint'=>Input::get('areaOp')));
	
			return Response::json(array('respuesta' => 'Usuario Guardado Correctamente'));
	}


	//funcion para guardar si la unidad esta activa 
	public function estatusUnidad($unidad,$bit,$usuario){

		//datos de sql server
		// $datosUnidad = Unidad::find($unidad);
		// $datosUnidad->UNI_activa = $bit;
		// $datosUnidad->save();

		// $bitacora = new Bitacora;
		// $bitacora->PAS_folio = $unidad;
		// $bitacora->BIT_tabla = 'Unidad';
		// $bitacora->BIT_campo = 'UNI_activa';
		// $bitacora->BIT_anterior = $bit == 0 ? 1:0;
		// $bitacora->BIT_nuevo = $bit;
		// $bitacora->BIT_fecha = date('d/m/Y H:i:s');
		// $bitacora->USU_cambio = $usuario;
		// $bitacora->save();

		// datos de web
		$usuarioWeb = User::find($usuario)->USU_usuarioWeb;
		$unidadWeb = Unidad::find($unidad)->UNI_claveWeb;

		$datosUnidadWeb = UnidadWeb::find($unidadWeb);
		if ($bit == 0) {
			$bitWebAnterior = 'N';
			$bitWeb = 'S';
		}else{
			$bitWebAnterior = 'S';
			$bitWeb = 'N';
		}
		$datosUnidadWeb->Uni_activa = $bitWeb;
		$datosUnidadWeb->save();

		$bitacoraWeb = new BitacoraWeb;
		$bitacoraWeb->EXP_folio = $unidadWeb;
		$bitacoraWeb->BIT_tabla = 'Unidad';
		$bitacoraWeb->BIT_campo = 'Uni_activa';
		$bitacoraWeb->BIT_anterior = $bitWebAnterior;
		$bitacoraWeb->BIT_nuevo = $bitWeb;
		$bitacoraWeb->BIT_fecha = date('Y-m-d H:i:s');
		$bitacoraWeb->USU_login = $usuarioWeb;
		$bitacoraWeb->save();

		return Response::json(array('respuesta' => 'Cambio con exito'));

	}

	//funcion para guardar usuario nuevo en flujo
	public function guardaTramite(){
		
		$usuario = Input::get('usuario');
		$fecha = date('Y-m-d H:i');
		$lesionado = Input::get('lesionado');
		$hospital = Input::get('hospital');

	    return Response::json(array('respuesta' => 'Usuario Guardado Correctamente'));
	
	}

	//funcion para guardar usuario nuevo en flujo
	public function guardaUsuario(){
		$usuario = new User;

		$usuario->USU_password = md5(Input::get('password'));

		$usuario->USU_nombre = Input::get('nombre');
		$usuario->USU_login = Input::get('login');
	    $usuario->USU_usuarioWeb = Input::get('userweb');
	    $usuario->USU_factivo = Input::get('usuactivo');
	    $usuario->USU_fcaptura = Input::get('captura');
	    $usuario->USU_fcontrolDocumentos = Input::get('controldoc');
	    $usuario->USU_fconsultaIndividual = Input::get('consulindiv');
	    $usuario->USU_ftickets = Input::get('tickets');
	    $usuario->USU_fmanual = Input::get('flujomanual');
	    $usuario->USU_fqualitas = Input::get('qualitas');
	    $usuario->USU_freportes = Input::get('reportes');
	    $usuario->USU_fpagos = Input::get('flujopagos');
	    $usuario->USU_fdocumentos = Input::get('flujodoc');
	    $usuario->USU_fusuarios = Input::get('usuarios');
	    $usuario->USU_fticketPagos = Input::get('ticketspagos');
	    $usuario->USU_fexpress = Input::get('facexpress');
	    $usuario->save();

	    $claveUsuario = $usuario->USU_claveint;	

	    $areaUsuario = new UsuarioArea;

	    $areaUsuario->USU_claveint = $claveUsuario;
	    $areaUsuario->ARO_claveint = Input::get('areaOp');
	    $areaUsuario->save();


	    return Response::json(array('respuesta' => 'Usuario Guardado Correctamente'));
	
	}

	//funcion para generar factura manual
	//solo usar en caso de emergencia y para FE y qualitas
	public function generaFactura($folio){

		$fecha = date('Y-m-d H:i');
		$usuario = 'algo';

		$datos = FolioWeb::find($folio);
		$datos->Exp_autorizado = 1;
		$datos->USU_solicito = $usuario;
		$datos->Exp_fechaSolicitud = $fecha;
		$datos->save();

		$cliente = FolioWeb::find($folio)->Cia_clave;

		$importe = ExpedienteInfo::find($folio)->EXP_costoEmpresa;
		$iva = round($importe * 0.16, 2);
		$total = $importe  + $iva;

		// //consultamos consecutivo de factura
		$facFolio = FacturaWeb::max('FAC_folio') + 1;
		$fechaIni = FacturaWeb::max('FAC_fecha');

		if ($fechaIni != null) {
    
	        $ultimaFechaFac = date("Y-m-d", strtotime( $fechaIni ));
	        $ultimaHoraFac =  date('H:i', strtotime( '+1 minutes' , strtotime($fechaIni) ) );
	        $fechaActual = date('Y-m-d');

	        if ( $fechaActual == $ultimaFechaFac) {
	          $fechaFactura = date('Y-m-d') . " " . $ultimaHoraFac;
	        }else{
	          $fechaFactura = date('Y-m-d') . ' 21:00';
	        }

	    // si no reseteamos la fecha APARTIR DE las 9 de la noche 
	    }else{
        	$fechaFactura = date('Y-m-d') . ' 21:00';
	    }

	    if(FacturaExpedienteWeb::where('Exp_folio',$folio)->count() == 0){
		    //insertamos 
			$facturacion = new FacturaWeb;
			$facturacion->CIA_clave = $cliente;
			$facturacion->FAC_serie = 'FW';
			$facturacion->FAC_folio = $facFolio;
			$facturacion->FAC_fecha = $fechaFactura;
			$facturacion->FAC_global = 0;
			$facturacion->FAC_importe = $importe;
			$facturacion->FAC_iva = $iva;
			$facturacion->FAC_total = $total;
			$facturacion->FAC_saldo = $total;
			$facturacion->FAC_fechaReg = $fecha;
			$facturacion->USU_registro = $usuario;

			$facturacion->save();

			$factura = $facturacion->FAC_clave;

			$facturacionExpediente = new FacturaExpedienteWeb;
			$facturacionExpediente->EXP_folio = $folio;
			$facturacionExpediente->FAC_clave = $factura;
			$facturacionExpediente->save();

			return Response::json(array('flash' => 'Factura generada'));
	    	
	    }else{

	    	return Response::json(array('flash' => 'Factura Ya Existente'),500);
	    }
	}

	public function guardaImagenes($folio){

		if ($this->importaImagenes($folio)) {
			return Response::json(array('respuesta' => 'Imagenes Guardadas Correctamente'));
		}else{
			return Response::json(array('respuesta' => 'Imagenes No Guardadas'),500);
		}

	}

	public function expedienteInfo($folio){
		
		$datosCentrales =  DB::select(" EXEC MV_REW_Captura_folio  @folio='$folio' " )[0];

		if (ExpedienteInfo::find($folio)->count() > 0) {
			$registro = ExpedienteInfo::find($folio);
		}else{
			$registro = new ExpedienteInfo;
			
		}
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

		return Response::json(array('respuesta' => 'Datos actualizados'));
								
	}

	//funcion para importar imagenes del ftp
	public function importaImagenes($folio){

		//fecha de registro
		$fechaRegistro = FolioWeb::find($folio)->Exp_fecreg;

		$unidad = Documento::where(array('DOC_folio'=>$folio,'DOC_etapa' => 1))->first();

		$propia = Unidad::find($unidad->UNI_claveint)->UNI_propia;

		//fecha de captura
		$fechaCaptura = Pase::find($folio)->PAS_fechaCaptura;

		// tomamos el mes y el año de cada fecha
		$AnyoNro = date('Y',strtotime($fechaCaptura));
		$MesNro = date('n',strtotime($fechaCaptura));

		$AnioReg = date('Y',strtotime($fechaRegistro));
		$MesReg = date('F',strtotime($fechaRegistro));


		//ruta del convertidor pdf jpg

		$convert = "C:\\Program Files\\ImageMagick-6.8.9-Q16\\convert";

		//armamos las rutas prueba
		// $rutaLocalAnioCarpeta = "C:\\Users\\SISTEMAS2\\Documents\\Qualitas\\". $AnyoNro;

		// if(!is_dir($rutaLocalAnioCarpeta)) mkdir($rutaLocalAnioCarpeta);

		// $rutaLocalMesCarpeta = $rutaLocalAnioCarpeta . "\\" . $MesNro;

		// if(!is_dir($rutaLocalMesCarpeta)) mkdir($rutaLocalMesCarpeta);

		// $rutaLocal =  $rutaLocalMesCarpeta . "\\". $folio;


		//armamos las rutas produccion
		$rutaLocalAnioCarpeta = "\\\\Eaa\\RENAUT\\10\\". $AnyoNro;

		if(!is_dir($rutaLocalAnioCarpeta)) mkdir($rutaLocalAnioCarpeta);

		$rutaLocalMesCarpeta = $rutaLocalAnioCarpeta . "\\" . $MesNro;

		if(!is_dir($rutaLocalMesCarpeta)) mkdir($rutaLocalMesCarpeta);

		$rutaLocal =  $rutaLocalMesCarpeta . "\\". $folio;


		//verificamos si es propia o red
		if ($propia == 1) {
			$rutaWeb = "/public_html/registro/Digitales/". $AnioReg . "/" . $MesReg . "/". $folio;
		}else{
			$rutaWeb = "/public_html/registro/Digitales/". $AnioReg . "/" . $MesReg . "/". $folio . '/1';
		}

		$archivos = File::files($rutaLocal);

		if (count($archivos) > 0) File::deleteDirectory($rutaLocal);
				
		//conectamos al ftp del folio
		$files =  FTP::connection()->getDirListing($rutaWeb);

		$archivosPDF = array();
		$folioAutorizacion = '';

		//recorremos cada archivo que encuentra
		foreach ($files as $file) {


			$formato = File::extension($file);

			//esta validacion es por que el ftp toma como puntos como un archivo y no deja pasar
			if (strlen($file) > 3) {


				// si no existe ruta la crea
				if(!is_dir($rutaLocal)) mkdir($rutaLocal);

				FTP::connection()->downloadFile( $rutaWeb . '/' . $file, $rutaLocal . '/' . $file );
				// mandamos a llamar la accion del controlador para generar codigos
		    	$nombreArchivo = App::make('QualitasController')->nombreArchivo($folio);

				if ($formato == 'pdf') {

					//obtenemos la ubicacion del pdf descargado
		    		$imagenPDF = $rutaLocal . '/' . $file;
		    		//obtenemos solo en nombre del archivo
		    		$nombrePDF = explode(".", $file ); 
		    		$imagenJPG = $rutaLocal . '/' . $nombrePDF[0] . '.jpg';
					exec('"' . $convert . '" -density 600 -quality 100 ' . $imagenPDF . '[0] '. $imagenJPG .'');
					File::delete($imagenPDF);

					$file = $nombrePDF[0] . '.jpg';

				}

		    	//es pase medico
		    	if (preg_match('/_pa_' . $folio . '/' , $file)) {

		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/pa_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'QS07.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);


		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);

		    		// echo "Encontrado pase medico " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	}

		    	//es aviso de privacidad
		    	if (preg_match('/_AP_' . $folio . '/' , $file)) {
		    		// echo "Encontrado aviso de privacidad " . $file . '<br>';
		    		File::move($rutaLocal . '/' . $file, $rutaLocal . '/ap_' . $folio . '.jpg');

		    	}

		    	// cuestionario
		    	if (preg_match('/_CA_' . $folio . '/' , $file)) {
		    		// echo "Encontrado cuestionario " . $file . '<br>';
		    		File::move($rutaLocal . '/' . $file, $rutaLocal . '/cu_' . $folio . '.jpg');
		    		
		    	}

		    	//es folio de autorizacion
		    	if (preg_match('/_CE_' . $folio . '/' , $file)) {
		    		
		    		// generamos el nombre que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/fa_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'ME020.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);


		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen

		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);

		    		$folioAutorizacion = $nombreQualitas;

		    		// echo "folio autorizacion " . $nombreQualitas . ' y pesa ' . $peso .'<br>';

		    	}

		    	//es identificacion
		    	if (preg_match('/_ID_' . $folio . '/' , $file)) {
		    		
		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/id_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'GN19.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);


		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);

		    		// echo "Encontrado identificacion " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	}

		    	//es nota medica
		    	if (preg_match('/1_NM_' . $folio . '/' , $file)) {
		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/im_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'ME021.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);

		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);
		    		//agregamos a documento que ira en el pdf
		    		array_push($archivosPDF, $nombreQualitas);

		    		// echo "Encontrado nota medica " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	}

		    	//es nota medica
		    	if (preg_match('/2_NM_' . $folio . '/' , $file)) {

		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/i2_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'ME022.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);


		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					//omprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);
		    		//agregamos a documento que ira en el pdf
		    		array_push($archivosPDF, $nombreQualitas);

		    		// echo "Encontrado nota medica " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	}

		    	//es nota medica
		    	if (preg_match('/3_NM_' . $folio . '/' , $file)) {

		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/i3_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'ME023.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);


		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);
		    		//agregamos a documento que ira en el pdf
		    		array_push($archivosPDF, $nombreQualitas);

		    		// echo "Encontrado nota medica " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	}

		    	//es nota medica
		    	if (preg_match('/4_NM_' . $folio . '/' , $file)) {

		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/i4_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'ME024.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);


		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);
		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);
		    		//agregamos a documento que ira en el pdf
		    		array_push($archivosPDF, $nombreQualitas);

		    		// echo "Encontrado nota medica " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	}

		    	//es nota medica
		    	if (preg_match('/5_NM_' . $folio . '/' , $file)) {

		    		//generamos los nombres que debe tener el archivo
		    		$nombreNuevo = $rutaLocal . '/i5_' . $folio . '.jpg';
		    		$nombreQualitas = $rutaLocal . '/' . $nombreArchivo . 'ME025.jpg';

		    		//renombramos el archivo que descargamos
		    		File::move($rutaLocal . '/' . $file, $nombreNuevo);

		    		//generamos una copia de ese archivo para nombrarlo como identiicador qualitas
		    		File::copy($nombreNuevo, $nombreQualitas);

		    		//medimos el peso para que segun sea el peso calcule el porcentaje de archivo
		    		$peso = File::size($nombreQualitas);

		    		//comprimimos imagen
		    		// cambiamos tamaño para bajar peso
					$img = Image::make($nombreQualitas)->resize(850,900);

					// comprimimos la calidad al 40%
					$img->save($nombreQualitas, 40);

		    		//agregamos a documento que ira en el pdf
		    		array_push($archivosPDF, $nombreQualitas);

		    		// echo "Encontrado nota medica " . $nombreQualitas . ' y pesa ' . $peso .'<br>';
		    	
		    	}
		    	
			}
		}


		/// generamos el PDF con las imagenes
		PDF::setPrintHeader(false);
		PDF::setPrintFooter(false);

		// set margins
		PDF::SetMargins(0, 0, 0, true);

		// set auto page breaks false
		PDF::SetAutoPageBreak(false, 0);

		PDF::AddPage('P', 'A4');

		foreach ($archivosPDF as $imagen) {

			// Display image on full page
			PDF::Image($imagen, 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);

		}

		// la ultima debe ser el folio de autorización

		// add a page
		if ($folioAutorizacion != '') {
			// add a page
			PDF::AddPage('P', 'A4');
			// Display image on full page
			PDF::Image($folioAutorizacion, 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
		}

		//Guardamos en la misma ubicacion
		PDF::Output( __DIR__ . '/' .$nombreArchivo . 'ME02.pdf', 'F' );

		File::copy( __DIR__ . '/' . $nombreArchivo . 'ME02.pdf', $rutaLocal . '/' .$nombreArchivo . 'ME02.pdf');

		File::delete(__DIR__ . '/' .$nombreArchivo . 'ME02.pdf');
		
		return true;


	}

	public function editaRiesgo($folio,$valor){

		$pase = Pase::find($folio);
		$pase->RIE_clave = $valor;
		$pase->save();

		return Response::json(array('respuesta' => 'Riesgo Guardado'));

	}


	public function pdfImagen($archivo){

		$convert = "C:\\Program Files\\ImageMagick-6.8.9-Q16\\convert";
		//obtenemos la ubicacion del pdf descargado
		$imagenPDF = public_path('pdf/' . $archivo . '.pdf');
		//obtenemos solo en nombre del archivo
		$imagenJPG = public_path('pdf/' . $archivo . '.jpg');
		exec('"' . $convert . '" -density 600 -quality 100 ' . $imagenPDF . ' '. $imagenJPG .'');

		return Response::json(array('respuesta' => 'Imagen Separada'));
	}


}