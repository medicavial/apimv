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
	public function editaUsuario(){
		return Input::all();
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
	public function guardaUsuario(){
		return Input::all();
	}


}
