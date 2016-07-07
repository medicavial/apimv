<?php

/*
|
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
|
*/

set_time_limit(3600);
ini_set('memory_limit', '-1');


Route::group(array('prefix' => 'api'), function()
{
	Route::get('/', function()
	{
		// $datosCentrales =  DB::select(" EXEC MV_REW_Captura_folio  @folio='PEMV035563' " )[0];

		// $registro = new ExpedienteInfo;
		// $registro->EXP_folio = $datosCentrales->Folio;
		// $registro->EXP_lesionado = $datosCentrales->Lesionado;
		// $registro->CIA_clave = $datosCentrales->ClienteWeb;
		// $registro->UNI_clave = $datosCentrales->UnidadWeb;
		// $registro->EXP_fechaExpedicion = $datosCentrales->FExpedicion;
		// $registro->EXP_fechaCaptura = $datosCentrales->FCaptura;
		// $registro->EXP_fechaAtencion = $datosCentrales->FAtencion;
		// $registro->EXP_poliza = $datosCentrales->Poliza;
		// $registro->EXP_siniestro = $datosCentrales->Siniestro;
		// $registro->EXP_reporte = $datosCentrales->Reporte;
		// $registro->EXP_orden = $datosCentrales->NoOrden;
		// $registro->POS_claveint = $datosCentrales->PosicionWeb;
		// $registro->RIE_claveint = $datosCentrales->RiesgoWeb;
		// $registro->EXP_ajustador = $datosCentrales->Ajustador;
		// $registro->EXP_medico = $datosCentrales->Medico;
		// $registro->EXP_obsAjustador = $datosCentrales->LesionesA;
		// $registro->LES_primaria = $datosCentrales->LPrimaria;
		// $registro->LES_empresa = $datosCentrales->LEmpresa;
		// $registro->EXP_diagnostico = $datosCentrales->DescMedica;
		// $registro->FAC_folioFiscal = $datosCentrales->FolFiscal;
		// $registro->FAC_serie = $datosCentrales->Serie;
		// $registro->FAC_folio = $datosCentrales->Factura;
		// $registro->FAC_fecha = $datosCentrales->FFactura;
		// $registro->FAC_importe = $datosCentrales->Importe;
		// $registro->FAC_iva = $datosCentrales->IVA;
		// $registro->FAC_total = $datosCentrales->Total;
		// $registro->FAC_saldo = $datosCentrales->Saldo;
		// $registro->FAC_pagada = $datosCentrales->Pagada;
		// $registro->FAC_fechaPago = $datosCentrales->FPagoFac;
		// $registro->FAC_ultimaAplicacion = $datosCentrales->FUltApl;
		// $registro->EXP_fechaRegWeb = $datosCentrales->FExp;
		// $registro->EXP_costoEmpresa = $datosCentrales->CostoEmpresa;

		// $registro->save();

		// return View::make('hello');

		return User::all();
	});

	Route::get('/monitor', function(){
		return DB::select("EXEC MVMonitorCaptura");
	});

	Route::get('/multiasistencia', function(){
		return DB::select("EXEC MV_MON_Multiasistencia");
	});

    Route::post('login', array('uses' => 'UserController@login'));
    Route::get('logout', array('uses' => 'UserController@logout'));

	//movimientos dentro del flujo para el alta de un nuevo folio registrado
    Route::post('altaoriginal', array('uses' => 'FlujoController@altaoriginal'));
    //movimiento que se utiliza para actualizar de factrua express a original
    Route::post('actializaoriginal', array('uses' => 'FlujoController@actializaoriginal'));
    //movimiento que cambia un bit para que sea reconocido como folio para "no pagar hasta cobrar"
    Route::post('insertanpc', array('uses' => 'FlujoController@insertanpc'));
    Route::post('eliminanpc', array('uses' => 'FlujoController@eliminanpc'));
    Route::post('posiblenp', array('uses' => 'FlujoController@posiblenp'));

    //busquedas de control de documentos
	Route::group(array('prefix' => 'controldocumentos'), function()
	{
    	Route::post('folio', array('uses' => 'ControlController@folio'));
    	Route::post('lesionado', array('uses' => 'ControlController@lesionado'));
    	Route::post('fechas', array('uses' => 'ControlController@fechas'));
	});

	//busquedas de tickets normales y de pagos
	Route::group(array('prefix' => 'tickets'), function()
	{
		Route::post('', array('uses' => 'TicketController@altaticket'));
		Route::put('', array('uses' => 'TicketController@actualizaticket'));
    	Route::post('consulta', array('uses' => 'TicketController@consulta'));
    	Route::get('categorias', array('uses' => 'TicketController@categorias'));
    	Route::get('detalle/{foliointerno}/{folio}', array('uses' => 'TicketController@detalle'));
    	Route::get('folio/{folio}', array('uses' => 'TicketController@folio'));
    	Route::get('foliointerno/{folio}', array('uses' => 'TicketController@foliointerno'));
    	Route::get('maximo', array('uses' => 'TicketController@maximo'));
    	Route::get('status', array('uses' => 'TicketController@status'));
    	Route::get('subcategorias/{categoria}', array('uses' => 'TicketController@subcategorias'));

    	Route::group(array('prefix' => 'pagos'), function()
		{
			Route::post('', array('uses' => 'TicketController@altaticketpagos'));
			Route::put('', array('uses' => 'TicketController@actualizaticketpagos'));
	    	Route::post('consulta', array('uses' => 'TicketController@consultapagos'));
	    	Route::get('categorias', array('uses' => 'TicketController@categoriaspagos'));
	    	Route::get('detalle/{foliointerno}/{folio}', array('uses' => 'TicketController@detallepagos'));
	    	Route::get('folio/{folio}', array('uses' => 'TicketController@foliopagos'));
	    	Route::get('foliointerno/{folio}', array('uses' => 'TicketController@foliointernopagos'));
	    	Route::get('status', array('uses' => 'TicketController@statuspagos'));
	    	Route::get('subcategorias/{categoria}', array('uses' => 'TicketController@subcategoriaspagos'));
		});

	});

	Route::group(array('prefix' => 'consulta'), function()
	{
		Route::get('areas', array('uses' => 'BusquedaController@areas'));
		Route::get('ajustadores/{cliente}', array('uses' => 'BusquedaController@ajustador'));
		Route::get('buscador/{consulta}/{tipo}', array('uses' => 'BusquedaController@buscador'));
		Route::get('editaDatos/{folio}', array('uses' => 'BusquedaController@editaDatos'));
		Route::get('empresas', array('uses' => 'BusquedaController@empresas'));
		Route::get('empresasweb', array('uses' => 'BusquedaController@empresasweb'));
	    Route::get('escolaridad', array('uses' => 'BusquedaController@escolaridad'));
	    Route::get('folio/{folio}', array('uses' => 'BusquedaController@folio'));
	    Route::get('folioweb/{folio}', array('uses' => 'BusquedaController@folioweb'));
	    Route::get('flujo/{folio}', array('uses' => 'BusquedaController@flujo'));
	    Route::get('historial/{folio}/{etapa}/{entrega}', array('uses' => 'BusquedaController@historial'));
	    Route::get('lesionado/{lesionado}', array('uses' => 'BusquedaController@lesionado'));
	    Route::get('medicos/{unidad}', array('uses' => 'BusquedaController@medicos'));
	    Route::get('posiciones', array('uses' => 'BusquedaController@posiciones'));
	    Route::get('productos', array('uses' => 'BusquedaController@productos'));
	    Route::get('productos/{empresa}', array('uses' => 'BusquedaController@productosEmp'));
	    Route::get('referencia/{unidad}', array('uses' => 'BusquedaController@referencia'));
	    Route::get('riesgos', array('uses' => 'BusquedaController@riesgos'));
	    Route::get('subsecuencia/{folio}/{entrega}', array('uses' => 'BusquedaController@subsecuencia'));
	    Route::get('rehabilitacion/{folio}/{entrega}', array('uses' => 'BusquedaController@rehabilitacion'));
	    Route::get('tipoLesion/{tipo}', array('uses' => 'BusquedaController@tipoLesion'));
	    Route::get('triage', array('uses' => 'BusquedaController@triage'));
	    Route::get('usuarios', array('uses' => 'BusquedaController@usuarios'));
	    Route::get('usuarios/{area}', array('uses' => 'BusquedaController@usuariosarea'));
	    Route::get('usuariostodos/{area}', array('uses' => 'BusquedaController@usuariostodosarea'));
	    Route::get('usuariosweb', array('uses' => 'BusquedaController@usuariosweb'));
	    Route::get('unidades', array('uses' => 'BusquedaController@unidades'));
	    Route::get('unidades/red', array('uses' => 'BusquedaController@unidadesRed'));
	    Route::get('unidadesweb', array('uses' => 'BusquedaController@unidadesweb'));
	    Route::get('lesiones/{tipoLes}', 'BusquedaController@lesiones');
	    Route::get('lesionweb', 'BusquedaController@lesionWeb');
	    Route::get('verificaetapaentrega/{folio}/{etapa}', array('uses' => 'BusquedaController@verificaetapaentrega'));
	    Route::get('verificafolio/{folio}/{etapa}', array('uses' => 'BusquedaController@verificafolio'));
	    Route::get('verificafoliopase/{folio}', array('uses' => 'BusquedaController@verificafoliopase'));
	    Route::get('verificaprefijo/{prefijo}/{empresa}', array('uses' => 'BusquedaController@verificaprefijo'));
	    
	    
	});

	Route::group(array('prefix' => 'entregas'), function()
	{
    	Route::post('acepta', array('uses' => 'EntregasController@acepta'));
    	Route::post('rechaza', array('uses' => 'EntregasController@rechaza'));
	});


    Route::group(array('prefix' => 'flujo'), function()
	{
		Route::get('consulta/{usuario}', array('uses' => 'FlujoController@consulta'));

		Route::post('alta', array('uses' => 'FlujoController@alta'));
		Route::post('actualiza', array('uses' => 'FlujoController@actualiza'));
		Route::get('activos/{usuario}', array('uses' => 'FlujoController@activos'));
		Route::post('elimina', array('uses' => 'FlujoController@elimina'));
		Route::get('entregas/{usuario}', array('uses' => 'FlujoController@entregas'));
		Route::get('recepcion/{usuario}', array('uses' => 'FlujoController@recepcion'));
		Route::get('rechazos/{usuario}', array('uses' => 'FlujoController@rechazos'));
		Route::get('npc/{usuario}', array('uses' => 'FlujoController@npc'));

		Route::get('activosarea/{area}', array('uses' => 'FlujoController@activosarea'));
		Route::get('entregasarea/{area}', array('uses' => 'FlujoController@entregasarea'));
		Route::get('recepcionarea/{area}', array('uses' => 'FlujoController@recepcionarea'));
		Route::get('rechazosarea/{area}', array('uses' => 'FlujoController@rechazosarea'));

	});

	Route::group(array('prefix' => 'flujopagos'), function()
	{

		Route::get('general', array('uses' => 'FlujopagosController@general'));
		Route::post('fecharecepcion', array('uses' => 'FlujopagosController@fecharecepcion'));
		Route::post('fechapagos', array('uses' => 'FlujopagosController@fechapagos'));

	});

	Route::group(array('prefix' => 'operacion'), function()
	{
		Route::post('editaDatos', array('uses' => 'OperacionController@editaDatos'));
		Route::post('usuarios', array('uses' => 'OperacionController@guardaUsuario'));
		Route::get('estatusUnidad/{unidad}/{bit}/{usuario}', array('uses' => 'OperacionController@estatusUnidad'));
		Route::put('usuarios', array('uses' => 'OperacionController@editaUsuario'));
		Route::get('prueba', array('uses' => 'OperacionController@prueba'));

	});

	Route::group(array('prefix' => 'qualitas'), function()
	{
		Route::post('actualiza/{envio}', array('uses' => 'QualitasController@actualiza'));
		Route::get('consulta/{folio}', array('uses' => 'QualitasController@consulta'));
    	Route::post('envios', array('uses' => 'QualitasController@envios'));
		Route::get('envios/{envio}', array('uses' => 'QualitasController@envio'));
    	Route::post('genera', array('uses' => 'QualitasController@generaArchivos'));
    	Route::post('general', array('uses' => 'QualitasController@general'));
    	Route::post('principal', array('uses' => 'QualitasController@principal'));
    	Route::get('procesado/{envio}', array('uses' => 'QualitasController@procesado'));
    	Route::post('procesa', array('uses' => 'QualitasController@procesa'));
    	Route::post('rechazos', array('uses' => 'QualitasController@rechazos'));
    	Route::get('reporte/{reporte}', array('uses' => 'QualitasController@reporte'));
    	Route::post('renombrar', array('uses' => 'QualitasController@renombrar'));
    	Route::post('sinarchivo', array('uses' => 'QualitasController@sinarchivo'));
    	Route::post('sinprocesar', array('uses' => 'QualitasController@sinprocesar'));
    	Route::post('incompletos', array('uses' => 'QualitasController@incompletos'));
    	Route::post('invalidos', array('uses' => 'QualitasController@invalidos'));
	});

	Route::group(array('prefix' => 'reportes'), function()
	{
    	Route::post('controldocumentos', array('uses' => 'ReportesController@control'));
    	Route::get('descargar/{archivo}', array('uses' => 'ReportesController@descargar'))->where('archivo', '[A-Za-z0-9\-\_\.]+');
    	Route::post('facturas', array('uses' => 'ReportesController@facturas'));
    	Route::post('pago/unidades', array('uses' => 'ReportesController@pagoUnidad'));
    	Route::get('tickets', array('uses' => 'ReportesController@tickets'));
    	Route::get('ticketsdia', array('uses' => 'ReportesController@ticketsdia'));
    	Route::post('ticketsdia', array('uses' => 'ReportesController@ticketsdiaespecifico'));
	});

	Route::group(array('prefix' => 'facturacionExpress'), function()
	{
    	Route::post('autorizados', array('uses' => 'FacturacionExpressController@autorizados'));
    	Route::put('actualizaFolio', array('uses' => 'FacturacionExpressController@actualizaFolio'));
    	Route::post('captura', array('uses' => 'FacturacionExpressController@captura'));
    	Route::post('capturaCuestionario', array('uses' => 'FacturacionExpressController@capturaCuestionario'));
    	Route::post('capturaAjustador', array('uses' => 'FacturacionExpressController@capturaAjustador'));
		Route::get('detalleFolio/{folio}', array('uses' => 'FacturacionExpressController@detalleFolio'));
    	Route::post('pendientes', array('uses' => 'FacturacionExpressController@pendientes'));
    	Route::post('rechazados', array('uses' => 'FacturacionExpressController@rechazados'));
    	Route::post('solicitarAutorizacion', array('uses' => 'FacturacionExpressController@solicitarAutorizacion'));
    	Route::post('solicitarAutorizacionRechazos', array('uses' => 'FacturacionExpressController@solicitarAutorizacionRechazos'));
    	Route::post('solicitados', array('uses' => 'FacturacionExpressController@solicitados'));
	});

    	Route::group(array('prefix' => 'relacion'), function()
	{
		Route::post('entrega', array('uses' => 'PagosController@entrega'));

	});

});

Route::get('/', array('uses' => 'HomeController@index'));
Route::get('/procesos', array('uses' => 'HomeController@procesos'));
Route::get('/et1', array('uses' => 'HomeController@subeEt1'));
Route::get('/et2', array('uses' => 'HomeController@subeEt2'));
Route::get('/et3', array('uses' => 'HomeController@subeEt3'));

Route::get('/et1Especial', array('uses' => 'HomeController@subeEt1Especial'));
Route::get('/et2Especial', array('uses' => 'HomeController@subeEt2Especial'));
Route::get('/et3Especial', array('uses' => 'HomeController@subeEt3Especial'));

Route::get('/especial', array('uses' => 'HomeController@subePagoCobreEspecial'));

Route::get('/documento', array('uses' => 'HomeController@SubeDocumento'));

Route::get('/info', function()
{
	phpinfo();
});

	// $fechaini = '01/01/2015';
	// $fechafin = '01/04/2015';
	// $result = DB::select("EXEC MV_DOC_ListadoDocumentosXFecha  @fechaIni='$fechaini', @fechaFin='$fechafin'");

	// foreach ($result as $dato){

	//     $data[] = array(
	//         "folio"=> $dato->Folio,
	//         "etapa" => $dato->Etapa,
	//         "fax"=> $dato->Fax,
	//         "original"=> $dato->Original,
	//         "facExp"=> $dato->FacExp,
	//         "situacion"=> $dato->Situacion,
	//         "unidad"=> $dato->Unidad,
	//         "fechacaptura"=> $dato->FechaCaptura,
	//     );
	// }

	// Excel::create('Filename', function($excel) use($data) {

	//     $excel->sheet('Sheetname', function($sheet) use($data) {

	//         $sheet->fromArray($data);

	//     });

	// })->export('xls');


	// $datos = DB::select("EXEC MVImgs_Datos @folio = 'ROMV000005'");

	// foreach ($datos as $dato) {
	// 	$nombre = $dato->Archivo;
	// }
	// $envios = DB::select("SELECT EnviosQualitas.ENQ_claveint, ENQ_fechaenvio,ENQ_procesado, (SELECT COUNT(*) from DetalleEnvio where DetalleEnvio.ENQ_claveint = EnviosQualitas.ENQ_claveint) as Cuenta
	// 			FROM EnviosQualitas  WHERE ENQ_fechaenvio BETWEEN '01/04/2015' and '22/04/2015' ");

	// return $envios;
	// $folio = 'ROMV000005';
	// $etapa = 1;
	// $entrega = 1;

	// $datos = Documento::where('DOC_folio',$folio)->where('DOC_etapa',$etapa)->where('DOC_numeroEntrega',$entrega)->get();

	// foreach ($datos as $dato) {
	// 	$documento = $dato['DOC_claveint'];
	// }

	// $datos = Documento::find(516030)->DOC_folio;

	// $reportes = '04140615280';

	
 //    $wsdl = "http://201.151.239.105:8081/wsProveedores/siniestros?wsdl";
 //    $username = '11555';
 //    $password = '2WXH59';

 //    $client = new SoapClient($wsdl,array('trace' => 1));

 //    $wsse_header = new WsseAuthHeader($username, $password);
 //    $client->__setSoapHeaders(array($wsse_header));
 //    $param = array('reporte' => $reporte);
 //    $respuesta = $client->getSiniestroByReporte($param);

	// $reportes

	// return  $datos;

