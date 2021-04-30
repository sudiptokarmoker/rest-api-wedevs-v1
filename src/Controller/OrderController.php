<?php
namespace Src\Controller;

use PDO;
use PDOException;
use Src\Model\User;

class OrderController
{
    private $db, $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function create()
    {
        try {
            /**
             * Check auth here
             */
            $isValidToken = new User();
            $this->user = $isValidToken->auth();
            if ($this->user) {
                /**
                 * Get the product id
                 */
                $productId = (int) $_POST['product_id'];
                /**
                 * Check if this is available
                 */
                $stmt = $this->db->prepare("SELECT * FROM product WHERE id=?");
                $stmt->execute([$productId]);
                $productLoad = $stmt->fetch();
                if ($productLoad) {
                    /**
                     * Now insert into DB this order information
                     */
                    $tableName = 'order';
                    $orderStatus = 'PAID';
                    $userId = intval($this->user['id']);

                    $queryInsert = "INSERT INTO `order` SET product_id = :product_id, order_by_user_id = :order_by_user_id,order_status = :order_status";

                    $stmtInsert = $this->db->prepare($queryInsert);

                    $stmtInsert->bindParam(':product_id', $productId);
                    $stmtInsert->bindParam(':order_by_user_id', $userId);
                    $stmtInsert->bindParam(':order_status', $orderStatus);

                    if ($stmtInsert->execute()) {
                        echo json_encode(array("message" => "Order successfully created", "status" => true));
                        http_response_code(200);
                    } else {
                        echo json_encode(array("message" => "error while create order", "status" => false));
                        http_response_code(400);
                    }
                }
            }
        } catch (\PDOException $e) {
            echo json_encode(array("message" => "error while create order", "status" => false, "error" => $e->getMessage()));
            http_response_code(400);
        }
    }

    public function order_status()
    {
        /**
         * Check auth here
         */
        $isValidToken = new User();
        $this->user = $isValidToken->auth();
        if ($this->user) {
            /**
             * Get the order id
             */
            $orderID = intval($_GET['id']);
            /**
             * Check if this is available
             */
            $stmt = $this->db->prepare("SELECT * FROM `order` WHERE id=?");
            $stmt->execute([$orderID]);
            $orderStatusLoad = $stmt->fetch();
            if ($orderStatusLoad) {
                echo json_encode(array("message" => "Order status", "status" => $orderStatusLoad['order_status']));
                http_response_code(200);
            } else {
                echo json_encode(array("message" => "error while get order status", "status" => false));
                http_response_code(400);
            }
        }
    }

    public function order_status_update()
    {
        try {
            /**
             * Check auth here
             */
            $isValidToken = new User();
            $this->user = $isValidToken->auth();
            if ($this->user && $this->user['user_type'] == 'admin') {
                $data = [
                    'id' => intval($_POST['order_id']),
                    //'product_owner_user_id' => intval($_POST['user_id']),
                    'order_status' => $_POST['order_status']
                ];
                $sql = "UPDATE `order` SET order_status=:order_status WHERE id=:id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($data);
                echo json_encode([
                    'status' => true
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => false,
                    'error' => 'Unauthorized',
                ]);
                http_response_code(401);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'status' => false,
                'error' => $e->getMessage(),
            ]);
            http_response_code(400);
        }
    }
}
