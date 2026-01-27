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
        $routClass = "Models/" . $model . ".php";
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
}
?>