<?php
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

// Get environment variables
// echo 'My Auth0 domain is ' . getenv('AUTH0_DOMAIN');


// $verifier = new JWTVerifier([
// 	'client_secret' =>'MGp9-mQ9pB3Y4B4YbEloFMVwk7nAwG8BAZGAJDA-18a_0A5hCiyJgYaFLr58uCFF',
//     'valid_audiences' => ['http://localhost:8080/'],
//     'authorized_iss' => ['https://dev-stp54a0d.auth0.com'],
//     'cache' => new FileSystemCacheHandler() // This parameter is optional. By default no cache is used to fetch the JSON Web Keys.
// ]);
// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ik1rVXdRa1pDTXpGQk9FTkVSa1k0UkVNeU5rSkROalZGTURVNVFrSXdPVUU1UVRSRU5EWkNOdyJ9.eyJpc3MiOiJodHRwczovL2Rldi1zdHA1NGEwZC5hdXRoMC5jb20vIiwic3ViIjoiZ29vZ2xlLW9hdXRoMnwxMTAxMTI0MTA5MTI3MzU4MjM2MjIiLCJhdWQiOlsiaHR0cDovL2xvY2FsaG9zdDo4MDgwLyIsImh0dHBzOi8vZGV2LXN0cDU0YTBkLmF1dGgwLmNvbS91c2VyaW5mbyJdLCJpYXQiOjE1NjAyNzAwMzksImV4cCI6MTU2MDI3NzIzOSwiYXpwIjoidWFuQ1g1N1hHMGpaSmI3NzN5RjdkYmR1R1FzcVprY1kiLCJzY29wZSI6Im9wZW5pZCBwcm9maWxlIn0.z885iGGCSQlJH1danVSBxR3wunULvv5WZB8dWA7t2DoPHK8w8KKPwoyWlr_iQd7IvVoWI-NJzLJyWiBkPuWv_xPlGpKxrxSek5UKagfhMvt3N5Dy6w1gxfDr3TqVLfJVNcIgSjvbhF-aP2vci4HOYbB8HFIenNEuEOSx2Y1CHyKvtAxqDQpgse1QjnwDPPsIdSxW16Qj-yT22mlHWJgoo5rUSAZ5Uuknxc0_E3LI64Y0VOJdikbf6iOvuQYSDv4vBYrwS9Z2xOhNNCT4wAHeYy2uc2CbeuXpDKL4t0sCPbdL6xfSKqeb4vSzFNH60QJXcZyevmuFEPA-215Vl2foyg';
// $decoded = $verifier->verifyAndDecode($token);
// var_dump($decoded);

if (empty($_GET['id_token'])) {
    die( 'No `id_token` URL parameter' );
}

if (empty($_GET['token_alg']) || ! in_array($_GET['token_alg'], [ 'HS256', 'RS256' ])) {
    die( 'Missing or invalid `token_alg` URL parameter' );
}

$idToken  = rawurldecode($_GET['id_token']);
$tokenAlg = rawurldecode($_GET['token_alg']);

$config = [
    // Array of allowed algorithms; never pass more than what is expected.
    'supported_algs' => [ $tokenAlg ],
    // Array of allowed "aud" values.
    'valid_audiences' => [ getenv('AUTH0_CLIENT_ID') ],
];

if ('HS256' === $tokenAlg) {
    // HS256 tokens require the Client Secret to decode.
    $config['client_secret']         = getenv('AUTH0_CLIENT_SECRET');
    $config['secret_base64_encoded'] = false;
} else {
    // RS256 tokens require a valid issuer.
    $config['authorized_iss'] = [ 'https://'.getenv('AUTH0_DOMAIN').'/' ];
}
// echo '<pre>'.print_r($config, true).'</pre>';

try {
    $verifier      = new JWTVerifier($config);
    $decoded_token = $verifier->verifyAndDecode($idToken);
    $data = file_get_contents(__DIR__.'/data_private.json');
    echo '<pre>'.print_r($config, true).'</pre>';
    // header('Content-Type: application/json');
	echo json_encode($data);
    exit();
} catch (InvalidTokenException $e) {
    echo 'Caught: InvalidTokenException - '.$e->getMessage();
} catch (CoreException $e) {
    echo 'Caught: CoreException - '.$e->getMessage();
} catch (\Exception $e) {
    echo 'Caught: Exception - '.$e->getMessage();
}