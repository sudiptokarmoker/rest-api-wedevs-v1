<?php
namespace Src\Model;

use Firebase\JWT\JWT;

class User
{
    // public function __construct($_token)
    // {
    //     $this->token = $_token;
    // }
    public function auth()
    {
        $secret_key = "WEDEVS";
        $jwt = null;
        //$databaseService = new DatabaseService();
        //$conn = $databaseService->getConnection();
        $data = json_decode(file_get_contents("php://input"));
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1];
        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
                if ($decoded) {
                    return [
                        'id' => $decoded->data->id,
                        'email' => $decoded->data->id
                    ];
                    //return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                // http_response_code(401);
                // echo json_encode(array(
                //     "message" => "Access denied.",
                //     "error" => $e->getMessage(),
                // ));
                return false;
            }
        }
    }

}
