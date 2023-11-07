<?php
declare(strict_types=1);

namespace iutnc\touiteur;
require_once 'vendor/autoload.php';

use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\action\AfficheTouiteUtilisateur;
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
                $this->html.= (new AfficheTouite())->execute();
                break;

            case "auteur" :
                $this->html.= (new AfficheTouiteUtilisateur())->execute();
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

            $petitMenu=<<<BEGIN
                    <form action="?action=touit" method="POST" enctype="multipart/form-data">
                        <input type="text" name="touit" placeholder="Votre touite" autocomplete="off">
                        <input type="file" name="image" accept="image/*">
                        <button type="submit">Touiter</button>
                    </form>
                    BEGIN;
        }
        else {$copaco = '<a href="?action=signin">Sign In</a>
                        <a href="?action=signup">Sign Up</a>';
            $petitMenu =<<<BEGIN
                    <form action="?action=signin" method="GET" enctype="multipart/form-data">
                        <button type="submit">Touiter</button>
                    </form>
                    BEGIN;
        }

        echo <<<BEGINHTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset='UTF-8'>
            <meta charset="UTF-8">
            <title>Page Title</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <link rel="stylesheet" type='text/css' href="CSS/style.css">
        </head>
            <body>
                <header>
                    <nav class="menu-gauche">
                        $copaco
                    </nav>
                    <h1><a href="index.php">Touiteur</a></h1>
                    <nav class="menu-droite">
                    </nav>
                </header>
                
        <main class="contenu">
            <section class="touites">
            </section>
            <section class="publier-touite">
                    $petitMenu
            </section>
        </main>
                $this->html
            </body>
        </html>
        BEGINHTML;
    }
}