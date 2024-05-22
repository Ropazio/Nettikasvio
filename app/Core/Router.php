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
            "login"                 => ["Authenticator", "login"],
            "herbarium/add-species" => ["Herbarium", "add"]
        ],
        "GET" => [
            ""                      => ["Home", "index"],
            "herbarium"             => ["Herbarium", "index"],
            "other"                 => ["Other", "index"],
            "identification"        => ["Identification", "index"],
            "login"                 => ["Authenticator", "index"],
            "logout"                => ["Authenticator", "logout"],
            "herbarium/add-species" => ["Herbarium", "addView"],
            "error401"              => ["Error", "error401"],
            "error404"              => ["Error", "error404"],
            "error500"              => ["Error", "error500"]
        ]
    ];

    public function __construct() {

        $url = $this->getUrl();
        // get the request method
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        // Url is split in parts by "/" and added to array
        $url = $this->parseUrl($url, $requestMethod);

        // If page url can't be found, show 404
        if (!isset(self::ROUTING_TABLE[$requestMethod][$url[0]])) {
            header("Location: " . siteUrl("error404"));
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
            header("Location: " . siteUrl("error404"));
        }
    }

    private function getUrl() : string {

        $url = "";
        if (isset($_GET["url"])) {
            $url = $_GET["url"];
        }
        return $url;
    }


    private function parseUrl( string $url, string $method ) : array {

        $partitionedUrl = explode("/", rtrim($url, "/"));

        if (count($partitionedUrl) > 1) {
            $pages = self::ROUTING_TABLE[$method];
            $tempParts = [];
            $parts = [];
            foreach ($partitionedUrl as $part) {
                array_push($tempParts, $part);
                $pageName = join("/", $tempParts);
                if (!isset($pages[$pageName])) {
                    break;
                }
                array_push($parts, $part);
            }
            $pageName = join("/", $parts);
            if (!(array_diff($partitionedUrl, $parts))) {
                return array($pageName);
            } else {
                return array($pageName, implode(array_diff($partitionedUrl, $parts)));
            }
        }

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

        $params = end($url);

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
