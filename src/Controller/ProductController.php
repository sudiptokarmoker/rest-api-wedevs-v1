<?php
namespace Src\Controller;

use PDO;
use PDOException;
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
            $name = $_POST['name'];
            $sku = $_POST['sku'];
            $description = $_POST['description'];
            $category = $_POST['category'];
            $price = $_POST['price'];

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
            if (isset($_FILES['image'])) {
                $file = $_FILES['image']['name'];
                $target_dir = "../uploads/";
                $path = pathinfo($file);
                $filename = $path['filename'];
                $ext = $path['extension'];
                $temp_name = $_FILES['image']['tmp_name'];
                $path_filename_ext = $target_dir . $filename . "." . $ext;

                $filenameWithExtension = $filename . "." . $ext;
                // Check if file already exists
                if (file_exists($path_filename_ext)) {
                    $empty_file = null;
                    $stmt->bindParam(':image_file', $empty_file);
                } else {
                    move_uploaded_file($temp_name, $path_filename_ext);
                    $stmt->bindParam(':image_file', $filenameWithExtension);
                }
            } else {
                $empty_file = null;
                $stmt->bindParam(':image_file', $empty_file);
            }
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

    public function show($id)
    {
        // echo json_encode([
        //     'id' => $id
        // ]);
        //$id = intval($_id);
        // echo json_encode([
        //     'id' => $id
        // ]);
        $stmt = $this->db->prepare("SELECT * FROM product WHERE id=?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        echo json_encode([
            'product' => $product,
        ]);
        http_response_code(200);
    }

    public function update()
    {
        $isValidToken = new User();
        $this->user = $isValidToken->auth();
        if ($this->user) {
            try {
                if (isset($_FILES['image'])) {
                    /**
                     * Delete old file and upload new file
                     */
                    $stmt = $this->db->prepare("SELECT image FROM product WHERE id=? AND product_owner_user_id=?");
                    $stmt->execute(
                        [
                            $_POST['id'],
                            $this->user['id'],
                        ]);
                    $image_data = $stmt->fetch();
                    $target_dir = "../uploads/";
                    if ($image_data) {
                        unlink($target_dir . $image_data['image']);
                    }
                    /**
                     * Now insert updated image
                     */
                    $file = $_FILES['image']['name'];
                    $path = pathinfo($file);
                    $filename = $path['filename'];
                    $ext = $path['extension'];
                    $temp_name = $_FILES['image']['tmp_name'];
                    $path_filename_ext = $target_dir . $filename . "." . $ext;

                    $filenameWithExtension = $filename . "." . $ext;

                    move_uploaded_file($temp_name, $path_filename_ext);

                    $data = [
                        'name' => $_POST['name'],
                        'sku' => $_POST['sku'],
                        'description' => $_POST['description'],
                        'category' => $_POST['category'],
                        'price' => $_POST['price'],
                        'id' => $_POST['id'],
                        'product_owner_user_id' => $this->user['id'],
                        'image' => $filenameWithExtension,
                    ];
                    $sql = "UPDATE product SET name=:name, sku=:sku, description=:description, category=:category, price=:price, image=:image WHERE id=:id AND product_owner_user_id=:product_owner_user_id";
                } else {
                    $data = [
                        'name' => $_POST['name'],
                        'sku' => $_POST['sku'],
                        'description' => $_POST['description'],
                        'category' => $_POST['category'],
                        'price' => $_POST['price'],
                        'id' => $_POST['id'],
                        'product_owner_user_id' => $this->user['id'],
                    ];
                    $sql = "UPDATE product SET name=:name, sku=:sku, description=:description, category=:category, price=:price WHERE id=:id AND product_owner_user_id=:product_owner_user_id";
                }
                $stmt = $this->db->prepare($sql);
                $stmt->execute($data);
                echo json_encode([
                    'status' => true,
                ]);
                http_response_code(200);
            } catch (PDOException $e) {
                echo json_encode([
                    'status' => false,
                    'error' => $e->getMessage(),
                ]);
                http_response_code(400);
            }
        }
    }
    /**
     * Delete image
     */
    public function delete()
    {
        $isValidToken = new User();
        $this->user = $isValidToken->auth();
        if ($this->user) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM product WHERE id=? AND product_owner_user_id=?");
                $stmt->execute(
                    [
                        $_POST['id'],
                        $this->user['id'],
                    ]);
                $product = $stmt->fetch();
                if ($product) {
                    $sql = "DELETE FROM product WHERE id=" . $_POST['id'];
                    $this->db->exec($sql);
                    echo json_encode([
                        'status' => true,
                    ]);
                }
            } catch (PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        } else {
            echo json_encode([
                'status' => false,
                'error' => 'Unauthorized User',
            ]);
        }
    }
}
