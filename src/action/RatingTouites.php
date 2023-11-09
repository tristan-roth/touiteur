<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class RatingTouites extends Action {
    public function execute(): string {
        $contenuHtml = "";
        if (isset($_SESSION["login"])) {
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            $data = $connexion->query(<<<SQL
                SELECT id_utilisateur FROM Utilisateur
                    WHERE utilisateur = '{$_SESSION['login']}'
            SQL);

            $res = $data->fetch();
            $idsuivre = $_POST["user"];
            $idsuit = $res["id_utilisateur"];

            $data = $connexion->query(<<<SQL
                INSERT INTO UtilisateurSuivi VALUES ($idsuit,$idsuivre)
            SQL);
            $contenuHtml.="<h2>Vous suivez maintenant $idsuivre</h2>";
        } else {
            $contenuHtml.= "<p>Connectez vous pour suivre un utilisateur</p>";
            $contenuHtml.= (new SigninAction)->execute();
        }
        return $contenuHtml;
    }
}
