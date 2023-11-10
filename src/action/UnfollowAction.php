<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\connection\ConnectionFactory;

class UnfollowAction extends Action {

    public function __construct() {
        parent::__construct();
    }
    public function execute() : string {

        $contenuHtml = "";
        if (isset($_SESSION["login"])) {
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            $idsuivre = $_POST["user"] + 0;
            $idsuit = RequetesBd::recupererId($_SESSION["login"]);
            
            if (!RequetesBd::followDeja($idsuit, $idsuivre)) {
                $contenuHtml.="<h2>Vous suivez déjà cet utilisateur.</h2>";
                
            } else {
                $data = $connexion->query(<<<SQL
                    DELETE from UtilisateurSuivi where id_utilisateur_suit = $idsuit and id_utilisateur_suivi = $idsuivre
                    SQL);
            $contenuHtml.="<h2>Vous avez supprimé $idsuivre de vos abonnements</h2>";
            }
        } else {
            $contenuHtml.= "<p>Vous ne pouvez pas vous désabonner sans compte</p>";
            $contenuHtml.= (new SigninAction)->execute();
        }
        return $contenuHtml;
    }
}