<?php

require_once __DIR__.'/../vendor/autoload.php';

use JWT\DatabaseService;
use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$email    = '';
$password = '';

$databaseService = new DatabaseService();
$connection      = $databaseService->getConnection();

$data      = json_decode(file_get_contents("php://input"));
$email     = $data->email;
$password  = $data->password;
$tableName = 'Users';
$query = "SELECT id, first_name, last_name, password FROM ".$tableName." WHERE email= :email LIMIT 0,1";

$stmt = $connection->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id            = $row['id'];
    $firstName     = $row['first_name'];
    $lastName      = $row['last_name'];
    $hash_password = $row['password'];

    if (password_verify($password, $hash_password)) {
        $secretKey      = 'YOUR_SECRET_KEY';
        $issuerClaim    = 'THE_ISSUER';
        $audienceClaim  = 'THE_AUDIENCE';
        $issuedAtClaim  = time(); // issued at
        $notBeforeClaim = $issuedAtClaim + 10; //not before in seconds
        $expireClaim    = $issuedAtClaim + 60; // expire time in seconds

        $token = array(
            'iss' => $issuerClaim,
            'aud' => $audienceClaim,
            'iat' => $issuedAtClaim,
            'nbf' => $notBeforeClaim,
            'exp' => $expireClaim,
            'data' => array(
                'id'        => $id,
                'firstName' => $firstName,
                'lastName'  => $lastName,
                'email'     => $email
            )
        );

        http_response_code(200);
        $jwt = JWT::encode($token, $secretKey);
        echo json_encode(
            array(
                'message'  => 'Successful login.',
                'jwt'      => $jwt,
                'email'    => $email,
                'expireAt' => $expireClaim
            )
        );
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Login failed.", "password" => $password));
    }
}
