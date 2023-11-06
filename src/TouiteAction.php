<?php

namespace iutnc\touiteur;

use iutnc\touiteur\action\Action;

class TouiteAction extends Action{
    public function __construct(){
        parent();
    }

    public function execute(): string
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $html="";

        return $html;
    }
}