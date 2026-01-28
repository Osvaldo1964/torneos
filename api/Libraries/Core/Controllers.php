<?php
class Controllers
{
    public $model;
    public $userData;
    public function __construct()
    {
        $this->loadModel();
    }

    public function loadModel()
    {
        $model = get_class($this) . "Model";
        $routClass = dirname(__DIR__, 2) . "/Models/" . $model . ".php";
        if (file_exists($routClass)) {
            require_once($routClass);
            $this->model = new $model();
        }
    }


    public function res($status, $msg, $data = null)
    {
        $response = [
            "status" => $status,
            "msg" => $msg,
            "data" => $data
        ];
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function validateToken()
    {
        $headers = getallheaders();
        // Handle lower-cased headers if server processes them that way
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? "";

        $token = str_replace('Bearer ', '', $authHeader);

        // Lazy load JwtHandler if not present
        if (!class_exists('JwtHandler')) {
            require_once(dirname(__DIR__, 2) . "/api/Libraries/Core/JwtHandler.php");
        }

        $jwt = new JwtHandler();
        $this->userData = $jwt->validateToken($token);

        if (!$this->userData) {
            $this->res(false, "Token inválido o expirado");
            exit;
        }

        return $this->userData;
    }
}

