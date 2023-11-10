<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;
use iutnc\touiteur\connection\ConnectionFactory;

class RequetesBd {

    static function recupererId(string $uti) : int {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT id_utilisateur FROM Utilisateur
                WHERE utilisateur = '$uti'
            SQL);
        $res = $data->fetch();
        return $res["id_utilisateur"]+0;
    }

    static function recupererNom(int $uti) : string {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT utilisateur FROM Utilisateur
                WHERE id_utilisateur = $uti
            SQL);
        $res = $data->fetch();
        return $res["utilisateur"];
    }

    static function followDeja(int $suiveur, int $suivi) : bool {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT id_utilisateur_suit FROM UtilisateurSuivi
                WHERE id_utilisateur_suit = $suiveur AND id_utilisateur_suivi = $suivi
            SQL);
        if ($data->rowCount()===1) return true;
        else return false;
    }

    static function alike(int $id_touit,int $id_uti) : int {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT rating FROM UtilisateurRatings
                WHERE id_touit = $id_touit AND id_utilisateur = $id_uti
            SQL);
        if ($data->rowCount() === 0) return -2; 
        else {
            $res = $data->fetch();
            return $res["rating"] + 0;
        }
    }

    static function modifRating(int $id_touit, int $id_uti, int $new_rating) {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $connexion->query(<<<SQL
            UPDATE UtilisateurRatings
                SET rating = $new_rating
                WHERE id_touit = $id_touit AND id_utilisateur = $id_uti
            SQL);
    }

    static function creerRating(int $id_touit, int $id_uti, int $new_rating) {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $connexion->query(<<<SQL
            INSERT INTO UtilisateurRatings
                VALUES ($id_touit,$id_uti,$new_rating)
            SQL);
    }

}