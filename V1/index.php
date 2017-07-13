<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

require __DIR__.'/../vendor/autoload.php';
include_once(__DIR__.'/../include/Config.php');
$app = new \Slim\Slim();

//metodo get

$app -> get('/auto',function()
{
	$response = array();
	$autos = array( array('make'=>'Toyota', 'model'=>'Corolla', 'year'=>'2006', 'MSRP'=>'18,000'), array('make'=>'Nissan', 'model'=>'Sentra', 'year'=>'2010', 'MSRP'=>'22,000'));
	$response["error"] = false;
	$response["message"] = "Autos cargados: ";
	$response["autos"] = $autos;
	echoResponse(200,$response);
});

//metodo post

$app->post('/auto','autenticar',function() use ($app)
{
	verificarParametrosRequeridos(array('make', 'model', 'year', 'msrp'));
	$response = array();
	$param['make'] = $app->request->post('make');
	$param['model'] = $app->request->post('model');
	$param['year'] = $app->request->post('year');
	$param['msrp'] = $app->request->post('msrp');

	if (is_array($param)) 
	{
		$response["error"] = false;
		$response["message"] = "Auto creado satisfactoriamente";
		$response = $param;
	}
	else
	{
		$response["error"] = true;
		$response["message"] = "Error al crear el auto";
	}
	echoResponse(201,$response);
});

$app->run();



//funciones//

function echoResponse($codigoStatus, $response)
{
	$app = \Slim\Slim::getInstance();
	$app->status($response);
	$app->contentType('application/json');
	echo json_encode($response);
}

function autenticar(\Slim\Route $ruta)
{
	$headers = apache_request_headers();
	$response = array();
	$app = \Slim\Slim::getInstance();
	$cad = "";
	foreach ($headers as $header => $value) 
	{
		$cad .= $header.' : '.$value;
	}
	if (isset($headers['authorization'])) {
		$token = $headers['authorization'];
		if (!($token == API_KEY))
		{
			$response["error"] = true;
			$response["message"] = "Acceso denegado. token inválido v ". API_KEY;
			echoResponse(401,$response);
			$app->stop();
		}
		else
		{
			//procede a ejecutar el metodo correspondiente =)
		}
	}
	else
	{
		$response["error"] = true;
			$response["message"] = "Acceso denegado. Falta token " . $cad ;
			echoResponse(400,$response);
			$app->stop();
	}
}

function verificarParametrosRequeridos($camposReq)
{
	$error = false;
	$error_fields = "";
	$request_params = array();
	$request_params = $_REQUEST;
	if ($_SERVER['REQUEST_METHOD'] == 'PUT')
	{
		$app = \Slim\Slim::getInstance();
		parse_str($app->request()->getBody(), $request_params);
	}
	foreach ($camposReq as $campo)
	{
		if (! isset($request_params[$campo]) || strlen(trim($request_params[$campo])) <= 0)
		{
			$error = true;
			$error_fields .= $campo . ', ';
		}
	}

	if ($error)
	{
		$response = array();
		$app = Slim\Slim::getInstance();
		$response["error"] = true;
		$response["message"] = 'Campos requeridos :' . substr($error_fields, 0, -2). 'faltan o estan vacios';
		echoResponse(400,$response);
		$app->stop();
	}
}
?>