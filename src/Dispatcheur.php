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
    private string $contenuHtml;
    private string $loginError = "<h1>Veuillez vous connecter pour touiter</h1>";

    function __construct() {
        if (isset($_GET["action"]))
            $this->action = $_GET["action"];
        else
            $this->action = "";
        $this->contenuHtml = "";
    }

    function run() : void {
        if (!isset($_SESSION)) session_start();
        switch ($this->action) {
            case "signin" : 
                $this->contenuHtml .= (new SigninAction())->execute();
                break;

            case "signup" :
                $this->contenuHtml .= (new SignupAction())->execute();
                break;

            case "touit" :
                if(isset($_SESSION["login"])) {
                    $this->contenuHtml .= (new TouitAction())->execute();
                }
                else {
                    $this->contenuHtml .= $this->loginError . (new SigninAction())->execute();
                }
                break;

            case "detail" :
                $this->contenuHtml .= (new AfficheTouite())->execute();
                break;

            case "auteur" :
                $this->contenuHtml .= (new AfficheTouiteUtilisateur())->execute();
                break;

            case "deconnecter" :
                $this->contenuHtml .= (new DeconnexionAction)->execute();
                break;

            default : 
                $this->contenuHtml .= (new AfficheListeTouites())->execute();
                break;
        }
        $this->renderer();
    }

    function renderer() : void {
        if (isset($_SESSION["login"])) {
            $estConnecteTexte = <<<HTML
                <a href="?action=deconnecter">se déconnecter</a>
            HTML;

            $boiteTouit = <<<HTML
                <form action="?action=touit" method="POST" enctype="multipart/form-data">
                    <input type="text" name="touit" placeholder="Votre touite" autocomplete="off">
                    <input type="file" name="image" accept="image/*">
                    <button type="submit">Touiter</button>
                </form>
                HTML;
        } else {
            $estConnecteTexte = <<<HTML
                <a href="?action=signin">Sign In</a>
                <a href="?action=signup">Sign Up</a>
            HTML;

            $boiteTouit = <<<HTML
                <div class="touiteform">
                    <form action="?action=signin" method="GET" enctype="multipart/form-data">
                        <button type="submit">Touiter</button>
                    </form>
                </div>
            HTML;
        }

        echo <<<BEGINHTML
        <!DOCTYPE contenuHtml>
        <contenuHtml lang="fr">
        <head>
            <meta charset='UTF-8'>
            <title>Touiteur</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <link rel="stylesheet" type='text/css' href="CSS/style.css">
        </head>
        <body>
            <header>
                <nav class="menu-gauche">
                    $estConnecteTexte
                </nav>
                <h1>
                    <a href="index.php">Touiteur</a>
                </h1>
                <nav class="menu-droite">
                </nav>
            </header>
                
            <main class="contenu">
                <div class="publier-touite">
                    $boiteTouit
                </div>

                <section class="tweets-container">
                    $this->contenuHtml
                </section>
            </main>
        </body>
        </contenuHtml>
        BEGINHTML;
    }
}