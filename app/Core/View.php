<?php

namespace app\Core;


class View {

    public function view( string $viewPath, array $params = [] ) : void {

        $realPath = filePath("views") . $viewPath . ".phtml";

        $snippets = $this->loadSnippets($params);

        require_once $realPath;
    }


    public function loadSnippets( array $params = [] ) : array {

        $snippets = [
            "header",
            "navi",
            "pageHeadline"
        ];

        $title = $params["title"];

        $results = [];

        foreach($snippets as $snippetName) {
            ob_start();
            require(filePath("snippets") . $snippetName . ".phtml");
            $results[$snippetName] = ob_get_clean();
        }

        return $results;
    }
}

