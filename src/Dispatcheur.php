<?php
declare(strict_types=1);

namespace iutnc\touiteur;
require_once 'vendor/autoload.php';

use iutnc\touiteur\action\Accueil;
use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\action\AfficheListeTouitesAbonnement;
use iutnc\touiteur\action\AfficheTouiteTag;
use iutnc\touiteur\action\AfficheTouiteUtilisateur;
use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\SupprimerAction;
use iutnc\touiteur\action\TouitAction;
use iutnc\touiteur\action\SignupAction;
use iutnc\touiteur\action\DeconnexionAction;
use iutnc\touiteur\action\AfficheTouite;
use iutnc\touiteur\action\FollowAction;
use iutnc\touiteur\action\LikeDislikeAction;
use iutnc\touiteur\action\AlertAction;
use iutnc\touiteur\connection\ConnectionFactory;

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

            case "abo" : 
                $this->contenuHtml.=(new AfficheListetouitesAbonnement())->execute();
                break;
            case "supprimer" :
                $this->contenuHtml.=(new SupprimerAction())->execute();
                break;

            case 'like' :
                $this->contenuHtml.=(new LikeDislikeAction())->execute();

            default :
                $this->contenuHtml .= (new Accueil())->execute();
                break;
        }
        $this->renderer();
    }

    function renderer() : void {

        $droiteWeb = <<<HTML
                <div class="menu-droite">
                    <h3>Utilisateurs à suivre</h3>
                    <ul>
                        <li><a href="?action=auteur&user=1">Jean</a></li>
                        <li><a href="?action=auteur&user=2">Paul</a></li>
                        <li><a href="?action=auteur&user=3">Jacques</a></li>
                    </ul>
                </div>  
            HTML;

        if (isset($_SESSION["login"])) {
            $estConnecteTexte = <<<HTML
                <a href="?action=deconnecter">Se déconnecter</a>
            HTML;

            $boiteTouit = <<<HTML
                <form action="?action=touit" method="POST" enctype="multipart/form-data">
                    <input type="text" name="touit" placeholder="Votre touite" autocomplete="off" style="width: 400px; height: 30px; border-radius: 15px; ">
                    <div class="touitActionsWrapper">
                        <div class="icone">
                            <label for="touitSendFile">
                                <img src="image/sendicone.png" style="width: 50px" title="Envoyer une image/vidéo" alt="Envoyer une image/vidéo">
                            </label>
                            <input type="file" id="touitSendFile" name="image" accept="image/*" style="display: none">
                        </div>
                        <button type="submit">Touiter</button>
                    </div>
                </form>
                HTML;


        } else {
                    
            $estConnecteTexte = <<<HTML
                <a href="?action=signin">Se connecter<br></a>
                <a href="?action=signup">S'inscrire<br></a>
            HTML;

            $boiteTouit = <<<HTML
                    <form action="?action=signin" method="POST" enctype="multipart/form-data">
                        <button type="submit">Touiter</button>
                    </form>
            HTML;
        }
        $pagination = Dispatcheur::pagination();
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
                    <div class="type-liste">
                        <a href="?action=">Tendances</a>
                        <p>|</p>
                        <a href="?action=abo">Abonnements</a>
                    </div>
                    <div class="publier-touite">
                        $boiteTouit
                    </div>
                    <div class="touits-container">
                        $this->contenuHtml
                    </div>
                    $pagination
                </div>
                
                
                <div class="menu-droite">
                    $droiteWeb
                </div>
            </div>
            
        </body>
        </html>
        BEGINHTML;
    }

    public static function pagination() {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        // On détermine sur quelle page on se trouve
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $currentPage = (int) strip_tags($_GET['page']);
        } else {
            $currentPage = 1;
        }

        // On détermine le nombre total de touits
        $query = $connexion->prepare(<<<SQL
            SELECT COUNT(*) AS nb_touits FROM Touits
            SQL);
        $query->execute();
        $result = $query->fetch();
        $nbTouits = (int) $result['nb_touits'];

        //nb d'article par page
        $parPage = 15;
        //pages totales
        $pages = ceil($nbTouits / $parPage);
        //premier touit de la page
        $premier = ($currentPage * $parPage) - $parPage;

        $query = $connexion->prepare(<<<SQL
            SELECT * FROM Touits
            ORDER BY id_touit DESC
            LIMIT $premier, $parPage
            SQL);
        $query->execute();
        $nbTouits = $query->fetchAll();

        $precedent = $currentPage - 1;
        $suivant = $currentPage + 1;

        $contenuHtml = <<<HTML
        <nav>
            <ul class="pagination">
        HTML;
        if ($currentPage > 1) {
            $contenuHtml .= <<<HTML
            <li class="page-item">
                <a href="?page=$precedent" class="page-link">Précédente</a>
            </li>
            HTML;
        }
        for ($page = 1; $page <= $pages; $page++) {
            $contenuHtml .= <<<HTML
                <li class="page-item">
                    <a href="?page=$page" class="page-link">
                        $page
                    </a>
                </li>
            HTML;
        }
        if ($currentPage <= $pages - 1) {
            $contenuHtml .= <<<HTML
                <li class="page-item">
                    <a href="?page=$suivant" class="page-link">Suivante</a>
                </li>
                </ul>
            </nav>
        HTML;
        }
        return $contenuHtml;
    }
}