<?php

require_once __DIR__.'/../vendor/autoload.php';
use JWT\DatabaseService;

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$firstName = '';
$lastName  = '';
$email     = '';
$password  = '';
$conn = null;

$databaseService = new DatabaseService();
$connection      = $databaseService->getConnection();

$data      = json_decode(file_get_contents("php://input"));
$firstName = $data->first_name;
$lastName  = $data->last_name;
$email     = $data->email;
$password  = $data->password;

$tableName = 'Users';
$query     = "INSERT INTO ".$tableName." 
                SET first_name = :first_name,
                last_name = :last_name,
                email = :email,
                password = :password";

$stmt = $connection->prepare($query);
$stmt->bindParam(':first_name', $firstName);
$stmt->bindParam(':last_name', $lastName);
$stmt->bindParam(':email', $email);
$hashPassword = password_hash($password, PASSWORD_BCRYPT);
$stmt->bindParam(':password', $hashPassword);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(array('message' => 'User was successfully registered.'));
} else {
    http_response_code(400);
    echo json_encode(array('message' => 'Unable to register the user.'));
}