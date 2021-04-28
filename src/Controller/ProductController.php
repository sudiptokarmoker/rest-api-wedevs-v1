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
            // $target_dir = dirname("\uploads");

            // echo json_encode([
            //     'file' => $target_dir
            // ]);
            // http_response_code(200);
            // die();

            $name = $_POST['name'];
            $sku = $_POST['sku'];
            $description = $_POST['description'];
            $category = $_POST['category'];
            $price = $_POST['price'];
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
                        product_owner_user_id = :user_id,
                        image = :image_file";

            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':sku', $sku);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':user_id', $this->user['id']);

            /**
             * Image upload script here
             */
            //$target_dir = "uploads";

            $target_dir = "../uploads/";
            $file = $_FILES['image']['name'];
            $path = pathinfo($file);
            $filename = $path['filename'];
            $ext = $path['extension'];
            $temp_name = $_FILES['image']['tmp_name'];
            $path_filename_ext = $target_dir . $filename . "." . $ext;

            $filenameWithExtension = $filename . "." . $ext;
            // Check if file already exists
            if (file_exists($path_filename_ext)) {
                //echo "Sorry, file already exists.";
                $stmt->bindParam(':image_file', null);
            } else {
                move_uploaded_file($temp_name, $path_filename_ext);
                $stmt->bindParam(':image_file', $filenameWithExtension);
                //echo "Congratulations! File Uploaded Successfully.";
            }

            // $target_dir = dirname("uploads");

            // $target_file = $target_dir . basename($_FILES["image"]["name"]);
            // $uploadOk = 1;
            // $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            // $check = getimagesize($_FILES["image"]["tmp_name"]);
            // $filename = basename($_FILES["image"]["name"]);
            // if ($check !== false) {
            //     if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            //         $stmt->bindParam(':image_file', $filename);
            //     }
            // } else {
            //     $stmt->bindParam(':image', null);
            // }
            /**
             * Image upload script end
             */
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
