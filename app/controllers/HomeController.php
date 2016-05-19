<?php

class HomeController extends BaseController {

	public function index(){
		// funcion que nos permite agregar folio en el flujo de documentos
		return ClasificacionLesWeb::find(3527);
	}

	public function procesos(){


		$datos =  DB::select("EXEC MV_REW_Captura  @fechaini='08/02/2016 00:00:00', @fechafin='18/02/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$folio = $dato->Folio;

			$existencia = ExpedienteInfo::find($folio);

			if ($existencia) {
				
				$registro = ExpedienteInfo::find($folio);

			}else{
				
				$registro = new ExpedienteInfo;

			}
			
			$registro->EXP_folio = $folio;
			$registro->EXP_lesionado = $dato->Lesionado;
			$registro->CIA_clave = $dato->ClienteWeb;
			$registro->UNI_clave = $dato->UnidadWeb;
			$registro->EXP_fechaExpedicion = $dato->FExpedicion;
			$registro->EXP_fechaCaptura = $dato->FCaptura;
			$registro->EXP_fechaAtencion = $dato->FAtencion;
			$registro->EXP_poliza = $dato->Poliza;
			$registro->EXP_siniestro = $dato->Siniestro;
			$registro->EXP_reporte = $dato->Reporte;
			$registro->EXP_orden = $dato->NoOrden;
			$registro->POS_claveint = $dato->PosicionWeb;
			$registro->RIE_claveint = $dato->RiesgoWeb;
			$registro->EXP_ajustador = $dato->Ajustador;
			$registro->EXP_medico = $dato->Medico;
			$registro->EXP_obsAjustador = $dato->LesionesA;
			$registro->LES_primaria = $dato->LPrimaria;
			$registro->LES_empresa = $dato->LEmpresa;
			$registro->EXP_diagnostico = $dato->DescMedica;
			$registro->FAC_folioFiscal = $dato->FolFiscal;
			$registro->FAC_serie = $dato->Serie;
			$registro->FAC_folio = $dato->Factura;
			$registro->FAC_fecha = $dato->FFactura;
			$registro->FAC_importe = $dato->Importe;
			$registro->FAC_iva = $dato->IVA;
			$registro->FAC_total = $dato->Total;
			$registro->FAC_saldo = $dato->Saldo;
			$registro->FAC_pagada = $dato->Pagada;
			$registro->FAC_fechaPago = $dato->FPagoFac;
			$registro->FAC_ultimaAplicacion = $dato->FUltApl;
			$registro->EXP_fechaRegWeb = $dato->FExp;
			$registro->EXP_costoEmpresa = $dato->CostoEmpresa;
			$registro->save();


		}

		return "Listo =) registros: ". count($datos);

	}

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

			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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
			$bitacora = new Bitacora;
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

	public function guardaUsuario(){
		return Input::all();
	}

	public function subeEt1(){


		$datos =  DB::select("EXEC MV_REW_PagosEt1ConClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoUnidad;
			
			$registro->DOC_clave = $dato->DOCClave;
			$registro->PAU_pago = $dato->Pago;
			$registro->PAU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PAU_relacion = $dato->Relacion;
			$registro->PAU_fechaRel = $dato->FRelacion;
			$registro->PAU_fechaPago = $dato->FPago;
			$registro->PAU_fechaImp = $dato->FechaExp;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos);

	}

	public function subeEt1Especial(){


		$datos =  DB::select("EXEC MV_REW_PagosEt1SinClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoEspecial;
			
			$registro->EXP_folio = $dato->Folio;
			$registro->PEU_pago = $dato->Pago;
			$registro->PEU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PEU_relacion = $dato->Relacion;
			$registro->PEU_fechaRel = $dato->FRelacion;
			$registro->PEU_fechaPago = $dato->FPago;
			$registro->PEU_fechaImp = $dato->FechaExp;
			$registro->PEU_etapa = $dato->EXPClave;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos);

	}

	public function subeEt2(){

		$datos =  DB::select("EXEC MV_REW_PagosEt2ConClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoUnidad;
			
			$registro->DOC_clave = $dato->DOCClave;
			$registro->PAU_pago = $dato->Pago;
			$registro->PAU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PAU_relacion = $dato->Relacion;
			$registro->PAU_fechaRel = $dato->FRelacion;
			$registro->PAU_fechaPago = $dato->FPago;
			$registro->PAU_fechaImp = $dato->FechaExp;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos);

	}

	public function subeEt2Especial(){


		$datos =  DB::select("EXEC MV_REW_PagosEt2SinClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoEspecial;
			
			$registro->EXP_folio = $dato->Folio;
			$registro->PEU_pago = $dato->Pago;
			$registro->PEU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PEU_relacion = $dato->Relacion;
			$registro->PEU_fechaRel = $dato->FRelacion;
			$registro->PEU_fechaPago = $dato->FPago;
			$registro->PEU_fechaImp = $dato->FechaExp;
			$registro->PEU_etapa = $dato->EXPClave;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos);

	}

	public function subeEt3(){

		$datos =  DB::select("EXEC MV_REW_PagosEt3ConClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoUnidad;
			
			$registro->DOC_clave = $dato->DOCClave;
			$registro->PAU_pago = $dato->Pago;
			$registro->PAU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PAU_relacion = $dato->Relacion;
			$registro->PAU_fechaRel = $dato->FRelacion;
			$registro->PAU_fechaPago = $dato->FPago;
			$registro->PAU_fechaImp = $dato->FechaExp;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos);

	}

	public function subeEt3Especial(){


		$datos =  DB::select("EXEC MV_REW_PagosEt3SinClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoEspecial;
			
			$registro->EXP_folio = $dato->Folio;
			$registro->PEU_pago = $dato->Pago;
			$registro->PEU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PEU_relacion = $dato->Relacion;
			$registro->PEU_fechaRel = $dato->FRelacion;
			$registro->PEU_fechaPago = $dato->FPago;
			$registro->PEU_fechaImp = $dato->FechaExp;
			$registro->PEU_etapa = $dato->EXPClave;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos);

	}


	public function subePagoCobreEspecial(){


		$datos =  DB::select("EXEC MV_REW_PagosPCEConClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato) {

			$registro = new PagoUnidad;
			
			$registro->DOC_clave = $dato->DOCClave;
			$registro->PAU_pago = $dato->Pago;
			$registro->PAU_factura = $dato->Factura;
			$registro->UNI_clave = $dato->Unidad;
			$registro->PAU_relacion = $dato->Relacion;
			$registro->PAU_fechaRel = $dato->FRelacion;
			$registro->PAU_fechaPago = $dato->FPago;
			$registro->PAU_fechaImp = $dato->FechaExp;
			$registro->save();
		}


		return "Listo =) registros: ". count($datos) . '</br>';

		$datosSinClave =  DB::select("EXEC MV_REW_PagosPCESinClave  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datosSinClave as $datosin) {

			$registro = new PagoEspecial;
			
			$registro->EXP_folio = $datosin->Folio;
			$registro->PEU_pago = $datosin->Pago;
			$registro->PEU_factura = $datosin->Factura;
			$registro->UNI_clave = $datosin->Unidad;
			$registro->PEU_relacion = $datosin->Relacion;
			$registro->PEU_fechaRel = $datosin->FRelacion;
			$registro->PEU_fechaPago = $datosin->FPago;
			$registro->PEU_fechaImp = $datosin->FechaExp;
			$registro->PEU_etapa = $datosin->PAGClave;
			$registro->save();
		}


		return "Listo =) registros: ". count($datosSinClave);


	}

	public function SubeDocumento(){

		$datos =  DB::select("EXEC MV_REW_InsertaDocWeb  @fechaini='01/01/2016 00:00:00', @fechafin='10/05/2016 23:59:58.999' " );

		foreach ($datos as $dato ){
			
			$clave = $dato->DOCClave;

			$existencia = DocumentoWeb::find($clave);

			if (!$existencia) {
				
				$registro = new DocumentoWeb;
				$registro->DOC_clave = $clave;
				$registro->DOC_folio = $dato->Folio;
				$registro->DOC_etapa = $dato->Etapa;
				$registro->DOC_entrega = $dato->NoEnt;
				$registro->UNI_clave = $dato->UNIClaveWeb;
				$registro->CIA_clave = $dato->EMPClaveWeb;
				$registro->DOC_fecha = $dato->FOriginal;
				$registro->DOC_fechaReg = $dato->FOriginalCap;
				$registro->DOC_fechaImp = $dato->FImp;
				$registro->PRO_clave = $dato->PROClave;
				$registro->save();

			}
			

		}

	}

}
