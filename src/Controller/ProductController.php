<?php
namespace Src\Controller;

class ProductController
{
    public function index()
    {
        http_response_code(200);
        echo json_encode([
            'status' => "called",
        ]);
    }
}
