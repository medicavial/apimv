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
		return View::make('hello');
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


    //busquedas de control de documentos
	Route::group(array('prefix' => 'controldocumentos'), function()
	{
    	Route::post('folio', array('uses' => 'ControlController@folio'));
    	Route::post('lesionado', array('uses' => 'ControlController@lesionado'));
    	Route::post('fechas', array('uses' => 'ControlController@fechas'));
	});

	Route::group(array('prefix' => 'consulta'), function()
	{
		Route::get('areas', array('uses' => 'BusquedaController@areas'));
		Route::get('empresas', array('uses' => 'BusquedaController@empresas'));
	    Route::get('escolaridad', array('uses' => 'BusquedaController@escolaridad'));
	    Route::get('folioweb/{folio}', array('uses' => 'BusquedaController@folioweb'));
	    Route::get('productos', array('uses' => 'BusquedaController@productos'));
	    Route::get('productos/{empresa}', array('uses' => 'BusquedaController@productosEmp'));
	    Route::get('referencia/{unidad}', array('uses' => 'BusquedaController@referencia'));
	    Route::get('usuarios/{area}', array('uses' => 'BusquedaController@usuariosarea'));
	    Route::get('unidades', array('uses' => 'BusquedaController@unidades'));
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
		Route::post('alta', array('uses' => 'FlujoController@alta'));
		Route::post('actualiza', array('uses' => 'FlujoController@actualiza'));
		Route::get('activos/{usuario}', array('uses' => 'FlujoController@activos'));
		Route::post('elimina', array('uses' => 'FlujoController@elimina'));
		Route::get('entregas/{usuario}', array('uses' => 'FlujoController@entregas'));
		Route::get('recepcion/{usuario}', array('uses' => 'FlujoController@recepcion'));
		Route::get('rechazos/{usuario}', array('uses' => 'FlujoController@rechazos'));
		Route::get('npc/{usuario}', array('uses' => 'FlujoController@npc'));
	});

	Route::group(array('prefix' => 'flujopagos'), function()
	{
		Route::post('general', array('uses' => 'FlujopagosController@general'));
		Route::post('fecharecepcion', array('uses' => 'FlujopagosController@fecharecepcion'));
		Route::post('fechapagos', array('uses' => 'FlujopagosController@fechapagos'));
	});

	Route::group(array('prefix' => 'qualitas'), function()
	{
		Route::post('actualiza/{envio}', array('uses' => 'QualitasController@actualiza'));
		Route::get('consulta/{folio}', array('uses' => 'QualitasController@consulta'));
    	Route::post('envios', array('uses' => 'QualitasController@envios'));
		Route::get('envios/{envio}', array('uses' => 'QualitasController@envio'));
    	Route::post('genera', array('uses' => 'QualitasController@generaArchivos'));
    	Route::post('principal', array('uses' => 'QualitasController@principal'));
    	Route::get('procesado/{envio}', array('uses' => 'QualitasController@procesado'));
    	Route::post('procesa', array('uses' => 'QualitasController@procesa'));
    	Route::post('rechazos', array('uses' => 'QualitasController@rechazos'));
    	Route::post('sinarchivo', array('uses' => 'QualitasController@sinarchivo'));
    	Route::post('sinprocesar', array('uses' => 'QualitasController@sinprocesar'));
    	Route::post('incompletos', array('uses' => 'QualitasController@incompletos'));
    	Route::post('invalidos', array('uses' => 'QualitasController@invalidos'));
	});

	Route::group(array('prefix' => 'reportes'), function()
	{
    	Route::post('controldocumentos', array('uses' => 'ReportesController@control'));
    	Route::get('descargar/{archivo}', array('uses' => 'ReportesController@descargar'))->where('archivo', '[A-Za-z0-9\-\_\.]+');
	});

});

Route::get('/', function()
{

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

	$datos = Documento::find(516030)->DOC_folio;;

	return  $datos;

});

