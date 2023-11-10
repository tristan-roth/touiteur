<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class LikeDislikeAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute() : string {

        $contenuHtml = "";
        if (isset($_SESSION["login"])) {
            
            $idTouit = $_POST["id"] + 0;
            $type = $_POST["type"];

            $idLike = RequetesBd::recupererId($_SESSION["login"]) + 0;
            $ancien = RequetesBd::alike($idTouit,$idLike);

            if ($type === "like") {
                switch ($ancien) {
                    case -2 :
                        RequetesBd::creerRating($idTouit, $idLike, 1);
                        break;

                    case 1 : 
                        RequetesBd::modifRating($idTouit, $idLike, 0);
                        break;

                    default :
                        RequetesBd::modifRating($idTouit, $idLike, 1);
                        break;
                }
            } else {
                switch ($ancien) {
                    case -2 :
                        RequetesBd::creerRating($idTouit, $idLike, -1);
                        break;

                    case -1 : 
                        RequetesBd::modifRating($idTouit, $idLike, 0);
                        break;

                    default :
                        RequetesBd::modifRating($idTouit, $idLike, -1);
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