<?php

namespace app\Models;

use app\Models\DatabaseModel;


class TextModel extends DatabaseModel {

    public function __construct() {

        parent::__construct();
    }


    public function getPageText( string $textName ) : array {

        $sth = $this->pdo->prepare("SELECT id, content FROM pageTexts WHERE name = ?");
        $sth->execute([$textName]);

        $text = $sth->fetch(\PDO::FETCH_ASSOC);
        $text = [
                "id"        => $text["id"],
                "content"   => $text["content"]
        ];

        return $text;
    }


    public function update( string $text, int $textId ) : void {

        // Update text with given text id
        $query = "UPDATE pageTexts SET content = ? WHERE id = ?";
        $sth = $this->pdo->prepare($query);
        $sth->execute([$text, $textId]);
    }
}