<?php

namespace app\Models;

use app\Models\DatabaseModel;


class TextModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function getPageText( string $textName ) : string {

        $sth = $this->pdo->prepare("SELECT content FROM pageTexts WHERE name = ?");
        $sth->execute([$textName]);

        $content = stripslashes($sth->fetch(\PDO::FETCH_COLUMN));

        return $content;
    }
}