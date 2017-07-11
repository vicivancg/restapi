<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: application/json; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

//require __DIR__.'/../vendor/slim/slim/Slim/Slim.php';

require __DIR__.'/../vendor/autoload.php'; 
$app = new \Slim\Slim();

$app -> get('/auto',function()
{
	$response = array();
	$autos = array( array('make'=>'Toyota', 'model'=>'Corolla', 'year'=>'2006', 'MSRP'=>'18,000'), array('make'=>'Nissan', 'model'=>'Sentra', 'year'=>'2010', 'MSRP'=>'22,000'));
	$response["error"] = false;
	$response["message"] = "Autos cargados: ";
	$response["autos"] = $autos;
	echoResponse(200,$response);
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
	if (isset($headers['Authorization'])) {
		$token = $headers['Authorization'];
		if (!($token == API_KEY))
		{
			$response["error"] = true;
			$response["message"] = "Acceso denegado. token inválido";
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
			$response["message"] = "Acceso denegado. Falta token";
			echoResponse(400,$response);
			$app->stop();
	}
}
?>