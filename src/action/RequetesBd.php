<?php
namespace iutnc\touiteur\action;
use iutnc\touiteur\connection\ConnectionFactory;
class RequetesBd {


    static function recupererId(string $uti) : string {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select id_utilisateur from Utilisateur where utilisateur = '$uti'");
        $res = $data->fetch();
        return $res["id_utilisateur"];
    }

    static function recupererNom(int $uti) : string {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select utilisateur from Utilisateur where id_utilisateur = $uti");
        $res = $data->fetch();
        return $res["utilisateur"];
    }

    static function followDeja(int $suiveur, int $suivi) : bool {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select id_utilisateur_suit from Utilisateursuivi where id_utilisateur_suit = $suiveur and id_utilisateur_suivi = $suivi");
        if ($data->rowCount()===1)
            return true;
        else
            return false;
    }

    static function alike(int $id_touit,int $id_uti) : int{
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select rating from utilisateurratings where id_utilisateur = $id_uti and id_touit = $id_touit");
        if ($data->rowCount()===0){
            return -2;
        }
        else {
            $res = $data->fetch();
            return $res["rating"]+0;
        }
    }

    static function modifRating(int $id_touit, int $id_uti, int $new_rating){
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $connexion->query("update utilisateurratings
        set rating = $new_rating 
        where id_touit = $id_touit and id_utilisateur = $id_uti");
    }

    static function creerRating(int $id_touit, int $id_uti, int $new_rating){
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $connexion->query("insert into utilisateurratings
        values ($id_touit,$id_uti,$new_rating)");
    }

}