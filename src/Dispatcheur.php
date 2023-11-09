<?php
declare(strict_types=1);

namespace iutnc\touiteur;
require_once 'vendor/autoload.php';

use iutnc\touiteur\action\Accueil;
use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\action\AfficheTouiteTag;
use iutnc\touiteur\action\AfficheTouiteUtilisateur;
use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\TouitAction;
use iutnc\touiteur\action\SignupAction;
use iutnc\touiteur\action\DeconnexionAction;
use iutnc\touiteur\action\AfficheTouite;
use iutnc\touiteur\action\FollowAction;

use iutnc\touiteur\action\AlertAction;

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

            case "follow" :
                $this->contenuHtml = (new FollowAction())->execute();
                break;

            case "deconnecter" :
                $this->contenuHtml .= (new DeconnexionAction)->execute();
                break;

            case "alert" :
                $this->contenuHtml .= (new AlertAction)->execute();
                break;

            case "tag" :
                $this->contenuHtml .= (new AfficheTouiteTag)->execute();
                break;

            default :
                $this->contenuHtml .= (new Accueil())->execute();
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
                    <div class="touitActionsWrapper">
                        <label for="touitSendFile">
                            <img src="image/sendFile.png" style="width: 32px"/>
                        </label>
                        <input type="file" id="touitSendFile" name="image" accept="image/*"/>
                        <button type="submit">Touiter</button>
                    </div>
                </form>
                HTML;
        } else {
            $estConnecteTexte = <<<HTML
                <a href="?action=signin">Sign In<br></a>
                <a href="?action=signup">Sign Up<br></a>
            HTML;

            $boiteTouit = <<<HTML
                    <form action="?action=signin" method="POST" enctype="multipart/form-data">
                        <button type="submit">Touiter</button>
                    </form>
            HTML;
        }

        echo <<<BEGINHTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset='UTF-8'>
            <title>Touiteur</title>
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <link rel='stylesheet' type='text/css' href='CSS/style.css'>
        </head>
        <body>
            <div class="wrapper">   
                <div class="menu-gauche">        
                    <h1><a href="index.php">Touiteur</a></h1>  
                    $estConnecteTexte
                </div>
                
                
                <div class="contenu">
                    <div class="publier-touite">
                        $boiteTouit
                    </div>
                    <div class="tweets-container">
                        $this->contenuHtml
                    </div>
                </div>
                
                
                <div class="menu-droite">
                    $estConnecteTexte
                </div>
            </div>
            
        </body>
        </html>
        BEGINHTML;
    }
}