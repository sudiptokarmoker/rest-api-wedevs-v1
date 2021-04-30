<?php
namespace Src\Controller;

use Firebase\JWT\JWT;
use PDO;
use PDOException;

class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function login()
    {
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $table_name = 'users';

        $query = "SELECT id, firstname, lastname, user_type, password FROM " . $table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $password2 = $row['password'];
            $user_type = $row['user_type'];

            if (password_verify($password, $password2)) {
                $secret_key = "WEDEVS";
                $issuer_claim = "THE_ISSUER"; // this can be the servername
                $audience_claim = "THE_AUDIENCE";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 10; //not before in seconds
                $expire_claim = $issuedat_claim + 600000; // expire time in seconds
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $id,
                        "firstname" => $firstname,
                        "lastname" => $lastname,
                        "email" => $email,
                        "user_type" => $user_type,
                    ));
                http_response_code(200);
                $jwt = JWT::encode($token, $secret_key);
                echo json_encode(
                    array(
                        "message" => "Successful login.",
                        "jwt" => $jwt,
                        "email" => $email,
                        "expireAt" => $expire_claim,
                    ));
            } else {
                echo json_encode([
                    'status' => 'denied',
                ]);
            }
        }
    }

    public function signup()
    {
        try {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user_type = $_POST['user_type'];

            $table_name = 'users';

            $query = "INSERT INTO " . $table_name . "
                            SET firstname=:firstname,
                                lastname=:lastname,
                                email=:email,
                                password=:password,
                                user_type=:user_type";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':user_type', $user_type);

            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array("message" => "User was successfully registered."));
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to register the user."));
            }
        } catch (\PDOException $e) {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to register the user.", "error" => $e->getMessage()));
        }
    }

    public function auth_check()
    {
        $secret_key = "WEDEVS";
        $jwt = null;
        $conn = $this->db;
        $data = json_decode(file_get_contents("php://input"));
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1];
        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
                // Access is granted. Add code of the operation here
                echo json_encode(array(
                    "message" => "Access granted:",
                    "auth" => $decoded,
                ));
            } catch (\Exception $e) {
                http_response_code(401);
                echo json_encode(array(
                    "message" => "Access denied.",
                    "error" => $e->getMessage(),
                ));
            }
        }
    }

    public function token_verify()
    {
        $secret_key = "WEDEVS";
        $jwt = null;
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1];
        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
                if ($decoded) {
                    echo json_encode(array(
                        "status" => true,
                    ));
                }
            } catch (\Exception $e) {
                http_response_code(401);
                echo json_encode(array(
                    "status" => false,
                    "error" => $e->getMessage(),
                ));
            }
        }
    }
}
