<?php

namespace app\Core;

use app\{
    Core\Sessions,
    Controllers
};


class Router {

    // ROUTING TABLE = ["page url" => [controller name, method name/function]]
    const ROUTING_TABLE = [
        "POST" => [
            "herbarium"             => ["Herbarium", "update"],
            "login"                 => ["Authenticator", "login"]
        ],
        "GET" => [
            ""                      => ["Home", "index"],
            "herbarium"             => ["Herbarium", "index"],
            "other"                 => ["Other", "index"],
            "identification"        => ["Identification", "index"],
            "login"                 => ["Authenticator", "index"],
            "logout"                 => ["Authenticator", "logout"]
        ]
    ];

    public function __construct() {

        $url = $this->getUrl();
        // Url is split in parts by "/" and added to array
        $url = $this->parseUrl($url);
        // get the request method
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        // If page url can't be found, show 404
        if (!isset(self::ROUTING_TABLE[$requestMethod][$url[0]])) {
            //header("Location: " . site_url("error-404"));
        }

        // Based on the page url, get controller name and method name
        $controllerName = $this->getControllerName($url, $requestMethod);
        $methodName = $this->getMethodName($url, $requestMethod);

        // If page url has no action, continue to page
        if (count($url) == 1) {
            $this->continueToPage($controllerName, $methodName);
        // If page url has parameters, save parameters and continue to page and pass on the parameters
        } elseif (count($url) == 2) {
            $params = $this->getParams($url, $requestMethod);
            $this->continueToPage($controllerName, $methodName, $params);
        } else {
            //header("Location: " . site_url("error-404"));
        }
    }

    private function getUrl() : string {

        $url = "";
        if (isset($_GET["url"])) {
            $url = $_GET["url"];
        }
        return $url;
    }


    private function parseUrl( string $url ) : array {

        $partitionedUrl = explode("/", rtrim($url, "/"));
        return $partitionedUrl;
    }


    protected function getControllerName( array $url, string $method ) : string {

        $pageName = $url[0];
        $controllerName = self::ROUTING_TABLE[$method][$pageName][0];

        return $controllerName;
    }

    protected function getMethodName( array $url, string $method ) : string {

        $pageName = $url[0];
        $methodName = self::ROUTING_TABLE[$method][$pageName][1];

        return $methodName;
    }


    protected function getParams( array $url, string $method ) : string {

        $params = $url[1];

        return $params;
    }


    protected function continueToPage( string $controllerName, string $methodName, string $params = "" ) : void {

        Sessions::startSession();

        $controller = new ("app\Controllers\\" . $controllerName)();

        if ($methodName == "delete") {
            $controller->$methodName($params);
        } else {
            $controller->$methodName();
        }
    }
}
