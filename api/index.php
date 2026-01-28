<?php
require_once(__DIR__ . "/Config/Config.php");
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/Libraries/Core/Conexion.php");
require_once(__DIR__ . "/Libraries/Core/Mysql.php");
require_once(__DIR__ . "/Libraries/Core/Controllers.php");
require_once(__DIR__ . "/Libraries/Utils/JwtHandler.php");
require_once(__DIR__ . "/Libraries/Utils/Email.php");
require_once(__DIR__ . "/Libraries/Utils/PDFHelper.php");

$url = !empty($_GET['url']) ? $_GET['url'] : 'Home/home';
$arrUrl = explode("/", $url);
$controller = $arrUrl[0];
$method = $arrUrl[0];
$params = "";

if (!empty($arrUrl[1])) {
    if ($arrUrl[1] != "") {
        $method = $arrUrl[1];
    }
}

if (!empty($arrUrl[2])) {
    if ($arrUrl[2] != "") {
        for ($i = 2; $i < count($arrUrl); $i++) {
            $params .= $arrUrl[$i] . ',';
        }
        $params = trim($params, ',');
    }
}

$controllerFile = __DIR__ . "/Controllers/" . $controller . ".php";
if (file_exists($controllerFile)) {
    require_once($controllerFile);
    $objController = new $controller();
    if (method_exists($objController, $method)) {
        $objController->{$method}($params);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["status" => false, "msg" => "Endpoint no encontrado"]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => false, "msg" => "Controlador no encontrado"]);
}
