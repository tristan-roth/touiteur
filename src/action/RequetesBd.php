<?php
namespace iutnc\touiteur\action;
use iutnc\touiteur\connection\ConnectionFactory;
class RequetesBd {


    static function recupererId(string $uti) : int {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select id_utilisateur from Utilisateur where utilisateur = '$uti'");
        $res = $data->fetch();
        return $res["id_utilisateur"];
    }

    static function followDeja(int $suiveur, int $suivi) : bool {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select id_utilisateur_suit from Utilisateursuivi where id_utilisateur_suit = $suiveur and id_utilisateur_suivi = $suivi");
        if ($data->rowCount()===1)
            return true;
        else
            return false;
    }

}