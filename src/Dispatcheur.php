<?php
declare(strict_types=1);

namespace iutnc\touiteur;
require_once 'vendor/autoload.php';
use iutnc\touiteur\action\SigninAction;

class Dispatcheur {

    private string $action;
    private string $html = "";

    function __construct() {
        if (isset($_GET["action"]))
            $this->action = $_GET["action"];
        else
            $this->action = "";
    }

    function run() : void {
        switch ($this->action) {
            case "signin" : 
                $this->html = (new SigninAction())->execute();
                break;
            default : 
                $this->html = "coucou";
        }
        $this->renderer();
    }

    function renderer() : void {
        echo <<<BEGINHTML
        <!DOCTYPE html>
        <html lang="fr">
            <meta charset="UTF-8">
            <title>Page Title</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <link rel="stylesheet" href="">

            <style>
            </style>

            <body>
                <a href="?action=signin">se connecter</a>
                $this->html
            </body>
        </html>
        BEGINHTML;
    }
}