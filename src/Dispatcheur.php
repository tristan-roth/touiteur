<?php
declare(strict_types=1);
namespace iutnc\touiteur;
require 'vendor/autoload.php';
class Dispatcheur{
    private string $action;
    private string $html;
    function __construct(){
        if (isset($_GET["action"])) $this->action = $_GET["action"];
        else $this->action = "";
    }

    function run(){
        $html="";
        switch ($this->action){
            default : 
                $html = "coucou";
        }
        $this->renderer($html);

    }

    function renderer(string $html) : void {
        echo '
        <!DOCTYPE html>
            <html lang="fr">
            <meta charset="UTF-8">
            <title>Page Title</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <link rel="stylesheet" href="">
            <style>
            </style>
            <body>
                '. $html .'
            </body>
            </html>';
    }
}