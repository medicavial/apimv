<?php

ini_set('memory_limit', '-1');
include(app_path() . '/classes/Historiales.php');

class FacturaZimaController extends BaseController {


	public function unidades(){

		return UnidadZima::where('UNI_activa','=', 'S')
				->select('UNI_clave as id','UNI_nomCorto as nombre')
				->orderBy('UNI_nomCorto')
				->get();
	}

	public function listadofacturas(){

		$folios = PUFacturaRef::join('Unidad', 'PUFacturaRef.UNI_clave', '=', 'Unidad.UNI_clave')
	                        ->leftJoin('PUProductoZima', 'PUFacturaRef.Pro_clave', '=', 'PUProductoZima.ProdZima_Clave')
	                        ->join('PURegistro',  'PUFacturaRef.REG_folio', '=', 'PURegistro.REG_folio')
	                        ->join('Aseguradora',  'PURegistro.ASE_clave', '=', 'Aseguradora.ASE_clave')
	                        ->join('PUFacEstatusMV', 'PUFacturaRef.estatus', '=', 'PUFacEstatusMV.claveEstatus')
	                        ->where(array('estatus' => 0))
	                        ->orWhere(array('estatus' => 1))
	                        ->select('PUFacturaRef.REG_folio as Folio','UNI_nomCorto as Unidad', 'REG_nombrecompleto as Lesionado',
	                        	     'ProdZima_Desc as Producto','REG_fechahora as FechaReg', 'SOL_clave as Solicitud', 
	                        	     'ASE_nomcorto as Aseguradora', 'estatus as estatus', 'descripcion as nombreestatus' )
	                        ->distinct()
	                        ->get()
	                        ->toArray();
     
		return $folios;
  	    
	}



}