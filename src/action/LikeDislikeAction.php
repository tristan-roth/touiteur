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
            $type = $_POST["type"];
            $idlike = RequetesBd::recupererId($_SESSION["login"])+0;
            $ancien = RequetesBd::alike($idtouit,$idlike);
            if ($type==="like"){
                switch ($ancien){
                    case -2 :
                        RequetesBd::creerRating($idtouit,$idlike,1);
                        break;
                    case 1 : 
                        RequetesBd::modifRating($idtouit,$idlike,0);
                        break;
                    default :
                        RequetesBd::modifRating($idtouit,$idlike,1);
                        break;
                }
            }
            else {
                switch ($ancien){
                    case -2 :
                        RequetesBd::creerRating($idtouit,$idlike,-1);
                        break;
                    case -1 : 
                        RequetesBd::modifRating($idtouit,$idlike,0);
                        break;
                    default :
                        RequetesBd::modifRating($idtouit,$idlike,-1);
                        break;
                }
            }
             

        } else {
            $contenuHtml.= "<p>Connectez vous pour aimer un touite</p>";
            $contenuHtml.= (new SigninAction)->execute();
 }
        return $contenuHtml;
    }
}