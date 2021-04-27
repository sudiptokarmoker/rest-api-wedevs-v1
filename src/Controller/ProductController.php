<?php
namespace Src\Controller;
use Src\Model\User;

class ProductController
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function index()
    {
        http_response_code(200);
        echo json_encode([
            'status' => "called",
        ]);
    }
    public function insert_product()
    {
        /**
         * Check auth here
         */
        $isValidToken = new User();
        $user = $isValidToken->auth();
        echo json_encode([
            'user' => intval($user['id'])
        ]);
        http_response_code(200);
        die();

        $name = $_REQUEST['name'];
        $description = $_REQUEST['description'];
        $category_id = $_REQUEST['category_id'];
        $price = $_REQUEST['price'];
        $image = $_REQUEST['image'];
        //$product_owner_user_id = 

        $table_name = 'prodcut';


        $query = "INSERT INTO " . $table_name . "
                        SET name = :name,
                        description = :description,
                        category_id = :category_id,
                        price = :price,
                            user_type =: user_type";
    }
    public function view_product()
    {

    }
}
