<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Content-type: application/json; charset=utf-8");
require __DIR__ . '/vendor/autoload.php';
use josegonzalez\Dotenv\Loader;
// use Auth0\SDK\JWTVerifier;
// use Auth0\SDK\Helpers\Cache\FileSystemCacheHandler;

use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Exception\CoreException;
// Setup environment vars
$Dotenv = new Loader(__DIR__ . '/.env');
$Dotenv->parse()->putenv(true);
$token = null;
$headers = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
	if($headers){
	 $matches = explode(' ',$headers);
	if(isset($matches[1])){
	  $token = $matches[1];
	}
}else{
	if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] == '/api/battles/private'){
		echo '';
		exit();
	}else{
		$data = file_get_contents(__DIR__.'/data_public.json');
	    // echo '<pre>'.print_r($decoded_token, true).'</pre>';
		echo $data;
		exit();
	}
}


try {
	
	$config = [

		'supported_algs'  => ['RS256'],

	    'valid_audiences' => ['http://localhost:8080/'],

	    'authorized_iss'  => ['https://dev-stp54a0d.auth0.com/']

	];
	
    $verifier      = new JWTVerifier($config);
    $decoded_token = $verifier->verifyAndDecode($token);
    $data = file_get_contents(__DIR__.'/data_private.json');
    // echo '<pre>'.print_r($decoded_token, true).'</pre>';
    
	echo $data;
	exit();
} catch (InvalidTokenException $e) {
	header('HTTP/1.1 500 Caught: InvalidTokenException - '.$e->getMessage());
} catch (CoreException $e) {
	header('HTTP/1.1 500 CoreException - '.$e->getMessage());
} catch (\Exception $e) {
	header('HTTP/1.1 500 Caught: Exception - '.$e->getMessage());
}