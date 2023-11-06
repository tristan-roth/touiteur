<?php
declare(strict_types=1);

namespace iutnc\touiteur;

require_once 'vendor/autoload.php';

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
                $this->html
            </body>
        </html>
        BEGINHTML;
    }
}