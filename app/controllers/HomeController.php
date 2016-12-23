<?php

class HomeController extends BaseController {

	public function index(){

		return View::make('hello');
	}

	public function procesos(){


		$datos =  DB::select("EXEC MV_REW_Captura  @fechaini='01/12/2016 00:00:00', @fechafin='21/12/2016 23:59:58.999' " );

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
