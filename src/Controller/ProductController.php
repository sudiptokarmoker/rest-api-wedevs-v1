<?php
namespace Src\Controller;

use PDO;
use Src\Model\User;

class ProductController
{
    private $db, $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function index()
    {
        http_response_code(200);
        echo json_encode([
            'status' => "called",
        ]);
    }
    public function insert()
    {
        /**
         * Check auth here
         */
        $isValidToken = new User();
        $this->user = $isValidToken->auth();
        if ($this->user) {

            // echo json_encode([
            //     'user' => intval($user['id'])
            // ]);
            // http_response_code(200);
            // die();

            $name = $_REQUEST['name'];
            $sku = $_REQUEST['sku'];
            $description = $_REQUEST['description'];
            $category = $_REQUEST['category'];
            $price = $_REQUEST['price'];
            //$image = $_REQUEST['image'];
            //$product_owner_user_id =

            // echo json_encode([
            //     'name' => $_REQUEST['name'],
            //     'sku' => $_REQUEST['sku']
            // ]);
            // http_response_code(200);
            // die();

            $table_name = 'product';

            $query = "INSERT INTO " . $table_name . "
                        SET name = :name,
                        sku = :sku,
                        description = :description,
                        category = :category,
                        price = :price,
                        product_owner_user_id = :user_id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':sku', $sku);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':user_id', $this->user['id']);

            try {
                $stmt->execute();
                echo json_encode(array("message" => "Product successfully created"));
                http_response_code(200);
            } catch (\PDOException $e) {
                echo json_encode(array("message" => "Unable to create product", "error" => $e->getMessage()));
                http_response_code(400);
            }
        } else {
            echo json_encode(array("message" => "Unathorized User"));
            http_response_code(403);
        }
    }

    public function view_product()
    {

    }
}
