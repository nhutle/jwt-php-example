<?php


require_once __DIR__.'/../vendor/autoload.php';
use JWT\DatabaseService;
use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$secretKey = 'YOUR_SECRET_KEY';
$jwt       = null;

$databaseService = new DatabaseService();
$connection      = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);

$jwt = $arr[1];

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $secretKey, array('HS256'));
        // Access is granted. Add code of the operation here

        echo json_encode(array(
            'message' => 'Access granted.'
        ));
    } catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            'message' => 'Access denied.',
            'error'   => $e->getMessage()
        ));
    }
}
