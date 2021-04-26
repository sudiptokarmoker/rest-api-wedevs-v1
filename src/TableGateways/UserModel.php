<?php
namespace Src\TableGateways;

class UserModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

   
    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO person 
                (firstname, lastname, email, password)
            VALUES
                (:firstname, :lastname, :email, :password);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'firstname' => $input['firstname'],
                'lastname'  => $input['lastname'],
                'email' => $input['email'] ?? null,
                'password' => $input['password'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}