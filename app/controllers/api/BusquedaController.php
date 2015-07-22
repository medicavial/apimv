<?php

//controlador de busquedas de toda la aplicacion para su reuso 
class BusquedaController extends BaseController {

	public function areas(){
		return Areas::where('ARO_activa','=', 1)
				->select('ARO_claveint as calve','ARO_Nombre as nombre')
				->get();
	}

	public function empresas(){
		return Empresa::where('emp_activa','=', 0)
				->select('emp_claveint as id','emp_nombrecorto as nombre')
				->orderBy('emp_nombrecorto')
				->get();
	}

	public function empresasweb(){
		return EmpresaWeb::where('Cia_activa','=', 'S')
				->select('Cia_clave as Clave', 'Cia_nombrecorto as Nombre')
				->orderBy('Cia_nombrecorto')
				->get();
	}

	public function escolaridad(){
		return Escolaridad::select('ESC_claveint as id','ESC_nombre as nombre')->get();
	}

	public function folio($folio){

		$datos = DB::select("EXEC MV_DCU_ListadoDocumentosXFolio  @folio='$folio'");
		$respuesta = array();
		
		foreach ($datos as $dato) {

			$respuesta[] = array(

				'Folio' =>  $dato->Folio,
	            'Etapa' =>  $dato->Etapa,
	            'Fax' =>  $dato->Fax,
	            'FechaFax' =>  $dato->FechaFax,
	            'Original' =>  $dato->Original,
	            'FechaOriginal' =>  $dato->FechaOriginal,
	            'F.E.' =>  $dato->FacExp,
	            'FechaF.E.' =>  $dato->FechaFacExp,
	            'Situacion' =>  $dato->Situacion,
	            'Unidad' =>  $dato->Unidad,
	            'FechaPago' =>  $dato->FechaPago,
	            'FechaCaptura' =>  $dato->FechaCaptura,
	            'Lesionado' =>  $dato->DOC_lesionado,
	            'Factura' =>  $dato->DOC_factura,
	            'Remesa' =>  $dato->DOC_remesa,
	            'Producto' =>  $dato->PRO_nombre,
	            'Escolaridad' =>  $dato->ESC_nombre,
	            'Cancelado' =>  $dato->Cancelado,
	            'Numero' =>  $dato->etapaEntrega,
	            'Empresa' =>  $dato->EMP_nombrecorto
			);

		}

		return $respuesta;

	}

	public function folioweb($folio){
		$folioweb = DB::connection('mysql')->table('Expediente')
				->join('Compania', 'Compania.CIA_clave', '=', 'Expediente.CIA_clave')
				->join('Unidad', 'Unidad.UNI_clave', '=', 'Expediente.UNI_clave')
				->join('Producto', 'Producto.Pro_clave', '=', 'Expediente.Pro_clave')
				->leftjoin('Escolaridad', 'Escolaridad.ESC_clave', '=', 'Expediente.ESC_clave')
				->where('Exp_folio','=', $folio)
				->get();
		return $folioweb;
	}

	public function flujo($folio){
		return DB::select("EXEC MV_FLU_BuscaDocumento  @folio='$folio'");
	}

	public function historial($folio,$etapa,$entrega){

		$datos = DB::table('HistorialFlujo')
		->join('Documento', 'HistorialFlujo.his_folio', '=', 'Documento.DOC_folio')
		->join('Empresa', 'Empresa.EMP_claveint', '=', 'Documento.EMP_claveint')
		->join('Unidad', 'Unidad.UNI_claveint', '=', 'Documento.UNI_claveint')
		->select('his_folio' , 'his_fecha' , 'his_etapa' , 'his_entrega' , 'his_hora' , 'his_titulo' , 'his_descripcion' , 'his_accion' , 'EMP_NombreCorto' , 'DOC_lesionado' , 'UNI_nombrecorto')
		->where('His_folio', '=', $folio)
		->where('His_etapa', '=', $etapa)
		->where('his_entrega', '=', $entrega)
		->get();

		$historial = array();

		foreach ($datos as $dato => $value) {

			$historial[] = array(

				'His_folio' =>  $value->his_folio,
	            'His_etapa' =>  $value->his_etapa,
	            'his_entrega' =>  $value->his_entrega,
	            'his_fecha' =>  $value->his_fecha,'d/m/Y',
	            'his_hora' =>  $value->his_hora,'G:ia',
	            'his_titulo' =>  $value->his_titulo,
	            'his_descripcion' =>  $value->his_descripcion,
	            'his_accion' =>  $value->his_accion,
	            'EMP_NombreCorto' =>  $value->EMP_NombreCorto,
	            'DOC_lesionado' =>  $value->DOC_lesionado,
	            'UNI_nombrecorto' =>  $value->UNI_nombrecorto
			);

		}



		return $historial;
	}

	public function lesionado($lesionado){

		$datos = DB::select("EXEC MV_DCU_ListadoDocumentosXLesionado  @lesionado='%$lesionado%'");
		$respuesta = array();
		foreach ($datos as $dato) {

			$respuesta[] = array(

				'Folio' =>  $dato->Folio,
	            'Etapa' =>  $dato->Etapa,
	            'Fax' =>  $dato->Fax,
	            'FechaFax' =>  $dato->FechaFax,
	            'Original' =>  $dato->Original,
	            'FechaOriginal' =>  $dato->FechaOriginal,
	            'F.E.' =>  $dato->FacExp,
	            'FechaF.E.' =>  $dato->FechaFacExp,
	            'Situacion' =>  $dato->Situacion,
	            'Unidad' =>  $dato->Unidad,
	            'FechaPago' =>  $dato->FechaPago,
	            'FechaCaptura' =>  $dato->FechaCaptura,
	            'Lesionado' =>  $dato->DOC_lesionado,
	            'Factura' =>  $dato->DOC_factura,
	            'Remesa' =>  $dato->DOC_remesa,
	            'Producto' =>  $dato->PRO_nombre,
	            'Escolaridad' =>  $dato->ESC_nombre,
	            'Cancelado' =>  $dato->Cancelado,
	            'Numero' =>  $dato->etapaEntrega,
	            'Empresa' =>  $dato->EMP_nombrecorto
			);

		}

		return $respuesta;
		
	}

	public function productos(){
		return Producto::select('PRO_claveint as id','PRO_nombre as nombre')->get();
	}

	public function productosEmp($empresa){
		$productos = DB::table('EmpresaProducto')
		->join('Producto', 'EmpresaProducto.PRO_claveint', '=', 'Producto.PRO_claveint')
		->select('Producto.PRO_claveint as id','PRO_nombre as nombre')
		->where('EMP_claveint', '=', $empresa)
		->where('EPR_activo','=', 1)
		->get();
		return $productos;
	}

	public function referencia($unidad){
		return Unidad::where('uni_activa','=', 0)
				->where('UNI_claveint','=', $unidad)
				->select('uni_claveint as id','uni_nombrecorto as nombre','uni_ref as referencia')
				->get();
	}


	public function usuariosarea($area){

		$usuarios = DB::table('Usuario')
		->join('UsuarioArea', 'Usuario.USU_claveint', '=', 'UsuarioArea.USU_claveint')
		->select('Usuario.USU_claveint as id','USU_login as nombre')
		->where('ARO_claveint', '=', $area)
		->where('USU_factivo','=', 1)
		->get();
		
		return $usuarios;
	}

	public function usuariostodosarea($area){

		$usuarios = DB::table('Usuario')
		->join('UsuarioArea', 'Usuario.USU_claveint', '=', 'UsuarioArea.USU_claveint')
		->select('Usuario.USU_claveint as id','USU_login as nombre')
		->where('ARO_claveint', '=', $area)
		->get();
		
		return $usuarios;
	}

	public function usuariosweb(){
		return UsuarioWeb::join('Permiso','Permiso.Usu_login' ,'=','Usuario.Usu_login')
				->select('Usuario.Usu_login as Clave','Usu_nombre as Nombre')
				->where('Usu_activo','S')
				->whereNotIn('Usuario.Usu_login', ['algo','lendex'])
				->where('Per_seguimiento','S')
				->orderBy('Usu_nombre')
				->get();
	}



	public function unidades(){
		return Unidad::where('uni_activa','=', 0)
				->select('uni_claveint as id','uni_nombrecorto as nombre')
				->orderBy('uni_nombrecorto')
				->get();
	}


	public function unidadesweb(){
		return UnidadWeb::where('Uni_activa','=', 'S')
				->select('Uni_clave as UnidadClave','Uni_nombre as Nombre')
				->orderBy('Uni_nombre')
				->get();
	}
	

	public function verificaetapaentrega($folio , $etapa){

		$documento = Documento::where('DOC_folio ', '=', $folio)
					->where( 'DOC_etapa', '=', $etapa)
					->select('DOC_original as original','DOC_claveint as clave','DOC_fax as fax','DOC_faxfecha as fechafax','DOC_FE as fe','DOC_FEFecha as fefecha','DOC_lesionado as lesionado','UNI_claveint as unidad','EMP_claveint as empresa','PRO_claveint as producto','ESC_claveint as escuela')
					->count();
				
		return $documento;
	}


	public function verificafolio($folio , $etapa){

		$documento = Documento::where('DOC_folio ', '=', $folio)
					->where( 'DOC_etapa', '=', $etapa)
					->select('DOC_original as original','DOC_claveint as clave','DOC_fax as fax','DOC_faxfecha as fechafax','DOC_FE as fe','DOC_FEFecha as fefecha','DOC_lesionado as lesionado','UNI_claveint as unidad','EMP_claveint as empresa','PRO_claveint as producto','ESC_claveint as escuela')
					->get();
				
		return $documento;
	}

	public function verificafoliopase($folio){

		$pases = Pase::where('PAS_folio ', '=', $folio)
					->count();
				
		return $pases;
	}

	public function verificaprefijo($prefijo , $empresa){
		return DB::select("EXEC MV_DCU_ValidaPrefijoFolio  @prefijo='$prefijo', @empresa='$empresa'");
	}

}
