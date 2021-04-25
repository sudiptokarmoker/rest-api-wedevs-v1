<?php
namespace Src\Controller;

class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function login()
    {

    }

    public function signup()
    {
        // echo json_encode([
        //     'firstname' => $_POST['firstname'],
        //     'lastname' => $_POST['lastname']
        // ]);
        // http_response_code(200);
        $firstname = '';
        $lastname = '';
        $email = '';
        $password = '';
        $conn = null;

        //$databaseService = new DatabaseService();
        $conn = $this->db;

        $data = json_decode(file_get_contents("php://input"));

        //echo json_encode($data);

        $firstname = $data->firstname;
        $lastname = $data->lastname;
        $email = $data->email;
        $password = $data->password;

        $table_name = 'Users';

        $query = "INSERT INTO " . $table_name . "
                        SET first_name = :firstname,
                            last_name = :lastname,
                            email = :email,
                            password = :password";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':firstname', $firstName);
        $stmt->bindParam(':lastname', $lastName);
        $stmt->bindParam(':email', $email);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':password', $password_hash);

        if ($stmt->execute()) {

            http_response_code(200);
            echo json_encode(array("message" => "User was successfully registered."));
        } else {
            http_response_code(400);

            echo json_encode(array("message" => "Unable to register the user."));
        }
    }

    public function logout()
    {

    }
}