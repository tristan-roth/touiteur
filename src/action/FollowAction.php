<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\connection\ConnectionFactory;

class FollowAction extends Action {

    public function __construct() {
        parent::__construct();
    }
    public function execute() : string {

        $contenuHtml = "";
        if (isset($_SESSION["login"])) {
            $connexion = ConnectionFactory::makeConnection();

            $idsuivre = $_POST["user"] + 0;
            $idsuit = RequetesBd::recupererId($_SESSION["login"]);
            if($idsuit === $idsuivre){
                $contenuHtml.="<h2>Vous ne pouvez pas vous suivre vous-mêmes.</h2>";
            }
            else{
                if (RequetesBd::followDeja($idsuit, $idsuivre)) {
                    $contenuHtml.="<h2>Vous suivez déjà cet utilisateur.</h2>";
                    
                } else {
                    $data = $connexion->query(<<<SQL
                        INSERT INTO UtilisateurSuivi VALUES ($idsuit, $idsuivre)
                        SQL);
                        $nomfollow = RequetesBd::recupererNom($idsuivre);
                $contenuHtml.="<h2>Vous suivez maintenant $nomfollow.</h2>";
                }
            }
            
            
        } else {
            $contenuHtml.= "<p>Connectez vous pour suivre un utilisateur.</p>";
            $contenuHtml.= (new SigninAction)->execute();
        }
        return $contenuHtml;
    }
}