<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class SupprimerAction extends Action{

    public function execute(): string
    {
        $connexion = ConnectionFactory::makeConnection();

        $id = $_GET['id'];
        $data = $connexion->query(<<<SQL
            SELECT utilisateur,
                    Images.id_image as image
                FROM Utilisateur
                INNER JOIN TouitsUtilisateur ON Utilisateur.id_utilisateur = TouitsUtilisateur.id_utilisateur
                INNER JOIN Touits ON Touits.id_touit = TouitsUtilisateur.id_touit
                LEFT JOIN Images ON Images.id_image = Touits.id_image
                WHERE TouitsUtilisateur.id_touit = $id
            SQL);

        $res = $data->fetch();


        $image = $res['image'];
        if ($res['utilisateur'] === $_SESSION['login']) {

            $data = $connexion->prepare(<<<SQL
                DELETE FROM TouitsUtilisateur
                    WHERE id_touit = ?
                SQL);
            $data->execute([$id]);

            $data = $connexion->prepare(<<<SQL
            delete from Tagstouits where id_touit = ?
            SQL);
            $data->execute([$id]);

            $data = $connexion->prepare(<<<SQL
                DELETE FROM Touits
                    WHERE id_touit = ?
                SQL);
            $data->execute([$id]);

            if ($image !== null) {

                $data = $connexion->prepare(<<<SQL
                    DELETE FROM Images
                        WHERE id_image = ?
                    SQL);
                $data->bindParam(1,$image);
                $data->execute();
            }

            $contenuHtml = "<h2>Le touite a bien été supprimé.</h2>";
        } else {
            $contenuHtml = "<h2>Vous ne pouvez pas supprimer ce touite.</h2>";
        }
        
        return $contenuHtml;
    }
}