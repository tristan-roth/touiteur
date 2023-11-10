<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\connection\ConnectionFactory;

class LikeDislikeAction extends Action {

    public function __construct() {
        parent::__construct();
    }
    public function execute() : string {

        $contenuHtml = "";
        if (isset($_SESSION["login"])) {
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $contenuHtml ="";
            $idtouit = $_POST["id"]+0;
            var_dump($idtouit);
            $idsuit = RequetesBd::recupererId($_SESSION["login"]);
            
             

        } else {
            $contenuHtml.= "<p>Connectez vous pour aimer un touite</p>";
            $contenuHtml.= (new SigninAction)->execute();
 }
        return $contenuHtml;
    }
}