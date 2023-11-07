<?php
declare(strict_types=1);

namespace iutnc\touiteur;
require_once 'vendor/autoload.php';

use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\TouitAction;
use iutnc\touiteur\action\SignupAction;
use iutnc\touiteur\action\DeconnexionAction;
use iutnc\touiteur\action\AfficheTouite;

class Dispatcheur {

    private string $action;
    private string $html;

    function __construct() {
        if (isset($_GET["action"]))
            $this->action = $_GET["action"];
        else
            $this->action = "";
        $this->html = "";
    }

    function run() : void {
        if (!isset($_SESSION)) session_start();
        switch ($this->action) {
            case "signin" : 
                $this->html.= (new SigninAction())->execute();
                break;

            case "signup" :
                $this->html.= (new SignupAction())->execute();
                break;

            case "touit" :
                if(isset($_SESSION["login"])){
                    $this->html.= (new TouitAction())->execute();
                }
                else{
                    $this->html.="<h1>Veuillez vous connecter pour touiter</h1>" . (new SigninAction())->execute();
                }
                break;

            case "detail" :
                //$this->html.= (new AfficheTouite())->execute();
                break;

            case "deconnecter" :
                $this->html.=(new DeconnexionAction)->execute();
                break;

            default : 
                $this->html .= (new AfficheListeTouites())->execute();

                break;
        }
        $this->renderer();
    }

    function renderer() : void {
        if (isset($_SESSION["login"])){
            $copaco = '<a href="?action=deconnecter">se déconnecter</a>';
        }
        else $copaco = '<a href="?action=signin">se connecter</a>
                        <a href="?action=signup">s\'inscrire</a>';
        echo <<<BEGINHTML
        <!DOCTYPE html>
        <html lang="fr">
            <meta charset="UTF-8">
            <title>Page Title</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <link rel="stylesheet" href="">

            <style>
            </style>
            <header>
            </header>
            <body>
                <a href="index.php">Accueil</a>
                <a href="?action=touit">touiter</a>
                $copaco
                $this->html
            </body>
        </html>
        BEGINHTML;
    }
}