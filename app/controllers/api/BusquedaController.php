<?php

//controlador de busquedas de toda la aplicacion para su reuso 
class BusquedaController extends BaseController {

	public function areas(){
		return Areas::where('ARO_activa','=', 1)
				->select('ARO_claveint as calve','ARO_Nombre as nombre')
				->get();
	}

	public function ajustador($cliente){
		return Ajustador::where('EMP_claveint',$cliente)->orderBy('AJU_nombre')->get();
	}


	public function buscador($consulta,$tipo){
		if ($tipo == 'lesionado') {
			$consulta = '%'.$consulta.'%';
		}
		return DB::select("EXEC MV_buscador  @consulta='$consulta', @tipo='$tipo' " );
	}

	public function editaDatos($folio){
		$folioLocal = DB::select(" EXEC MV_EDD_BuscaFolio  @folio='$folio' " );

		if (count($folioLocal) > 0) {

			$datos = $folioLocal[0];

			$respuesta = array(
				'folio' 			=> $datos->PAS_folio,
				'materno' 			=> $datos->EXW_materno,
				'paterno' 			=> $datos->EXW_paterno,
				'nombre' 			=> $datos->EXW_nombre,
				'reporte' 			=> $datos->DAS_reporte,
				'poliza' 			=> $datos->DAS_poliza,
				'cliente' 			=> $datos->EMP_claveint,
				'producto' 			=> $datos->PRO_claveint,
				'fechaNacimiento' 	=> $datos->AFE_fechaNac,
				'riesgo' 			=> $datos->RIE_clave,
				'siniestro' 		=> $datos->DAS_siniestro,
				'inciso' 			=> $datos->DAS_inciso,
				'web'				=> 0
			);

		}else{

			$folioweb = FolioWeb::join('Compania', 'Compania.CIA_clave', '=', 'Expediente.CIA_clave')
				->join('Unidad', 'Unidad.UNI_clave', '=', 'Expediente.UNI_clave')
				->join('RiesgoAfectado', 'RiesgoAfectado.RIE_clave', '=', 'Expediente.RIE_clave')
				->join('Producto', 'Producto.Pro_clave', '=', 'Expediente.Pro_clave')
				->where('Exp_folio','=', $folio)
				->first();

			
			$respuesta = array(
				'folio' 			=> $folioweb->Exp_folio,
				'materno'			=> $folioweb->Exp_materno,
				'paterno'			=> $folioweb->Exp_paterno,
				'nombre'			=> $folioweb->Exp_nombre,
				'reporte' 			=> $folioweb->Exp_reporte,
				'poliza' 			=> $folioweb->Exp_poliza,
				'cliente' 			=> $folioweb->Cia_claveMV,
				'producto' 			=> $folioweb->Pro_claveMV,
				'fechaNacimiento' 	=> $folioweb->Exp_fechaNac2,
				'riesgo' 			=> $folioweb->RIE_claveMV,
				'siniestro' 		=> $folioweb->Exp_siniestro,
				'inciso' 			=> $folioweb->Exp_inciso,
				'web'				=> 1
			);

		}

		 
		return $respuesta;

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
				->leftjoin('Producto', 'Producto.Pro_clave', '=', 'Expediente.Pro_clave')
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
				->join('Documento', 'HistorialFlujo.DOC_claveint', '=', 'Documento.DOC_claveint')
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


	public function lesiones($tipoLes){
		return Lesion::where('TLE_claveint',$tipoLes)->get();
	}


	public function lesionWeb(){
		return LesionWeb::all();
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

	public function riesgos(){
		return Riesgo::all();
	}

	public function rehabilitacion($folio,$entrega){

		//verificamos rehabilitacion en web
		return Rehabilitacion::join('Expediente','Expediente.Exp_folio','=','Rehabilitacion.Exp_folio')
						   ->join('Unidad','Unidad.Uni_clave','=','Rehabilitacion.Uni_clave')
						   ->join('Rehabilitador', function($join) {
							   $join->on('Rehabilitador.Usu_login', '=', 'Rehabilitacion.Usu_registro');
							   $join->on('Rehabilitador.Uni_clave', '=', 'Rehabilitacion.Uni_clave');
							})
						   ->select('Uni_nombrecorto AS Unidad', 'Uni_claveMV AS unidad','Reh_claveMV AS rehabilitador','UNI_propia as propia','Rehab_fecha as fecha','Rehab_obs as observaciones','Rehab_dolor as escalaDolor','Rehab_mejoria as escalaMejoria','Rehab_tipo as tipoRehabilitacion')
						   ->where('Rehabilitacion.Exp_folio',$folio)
						   ->where('Rehab_cons',$entrega)
						   ->first();

	}

	public function subsecuencia($folio,$entrega){

		//subsecuencia en web
		return Subsecuencia::join('Expediente','Subsecuencia.Exp_folio','=','Expediente.Exp_folio')
						   ->join('Unidad','Subsecuencia.Uni_clave','=','Unidad.Uni_clave')
						   ->join('Medico','Subsecuencia.Usu_registro','=','Medico.Usu_login')
						   ->select('Uni_nombrecorto AS Unidad', 'Unidad.Uni_claveMV AS unidad','Med_claveMV AS medico','UNI_propia as propia','Sub_fecha as fecha','Sub_hora as hora')
						   ->where('Expediente.Exp_folio',$folio)
						   ->where('Sub_cons',$entrega)
						   ->first();


	}

	public function tipoLesion($tipo){
		return DB::select("EXEC MV_CAP_ListaTipoLesion  @setTipoLes=$tipo" );
	}

	public function triage(){
		return Triage::all();
	}

	public function usuarios(){
		return User::all();
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
				->select('uni_claveint as id','uni_nombrecorto as nombre','UNI_propia as propia')
				->orderBy('uni_nombrecorto')
				->get();
	}

	public function unidadesRed(){
		return Unidad::join('Localidad','Localidad.LOC_claveint','=','Unidad.LOC_claveint')
				->join('Estado','Estado.EST_clave','=','Localidad.EST_claveint')
				->where(array('UNI_propia' => 0))
				->select('uni_claveint as id','uni_nombrecorto as nombreCorto','uni_nombre as nombre','UNI_rfc as rfc','EST_nombre as estado','LOC_nombre as localidad','UNI_razonsocial as razonSocial','UNI_callenum as direccion','Uni_activa as activa')
				->orderBy('uni_nombrecorto')
				->get();
	}


	public function unidadesweb(){
		return UnidadWeb::where('Uni_activa','=', 'S')
				->select('Uni_clave as UnidadClave','uni_nombreMV as Nombre')
				->orderBy('uni_nombreMV')
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

		$documento = Documento::join('Unidad','Unidad.UNI_claveint','=','Documento.UNI_claveint')
					->where('DOC_folio ', '=', $folio)
					->where( 'DOC_etapa', '=', $etapa)
					->select('DOC_original as original','DOC_claveint as clave','DOC_fax as fax','DOC_faxfecha as fechafax','DOC_FE as fe','DOC_FEFecha as fefecha','DOC_lesionado as lesionado','Unidad.UNI_claveint as unidad','UNI_propia as propia','EMP_claveint as empresa','PRO_claveint as producto','ESC_claveint as escuela','DOC_situacionoriginal as capturado')
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
