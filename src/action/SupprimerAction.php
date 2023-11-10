<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class SupprimerAction extends Action{

    public function execute(): string
    {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $id = $_GET['id'];
        $data = $connexion->query("select utilisateur, images.id_image as image from utilisateur 
                                   inner join touitsutilisateur on utilisateur.id_utilisateur = touitsutilisateur.id_utilisateur
                                   inner join touits on touits.id_touit = touitsutilisateur.id_touit
                                   left join images on images.id_image = touits.id_image
                                   where touitsutilisateur.id_touit = $id");
        $res = $data->fetch();
        var_dump($res);
        $image = $res['image'];
        var_dump($res['utilisateur']);
        if ($res['utilisateur'] === $_SESSION['login']){
            if ($image !== null){
                $data = $connexion->prepare("delete from images where id_image = ?");
                $data->bindParam(1,$image);
                $data->execute();
            }
            $data = $connexion->prepare("delete from touits where id_touit = ?");
            $data->execute([$id]);
            $data = $connexion->prepare("delete from touitsutilisateur where id_touit = ?");
            $data->execute([$id]);


            $contenuHtml = "<h2>Le touite a bien été supprimé</h2>";
        } else {
            $contenuHtml = "<h2>Vous ne pouvez pas supprimer ce touite</h2>";
        }
        return $contenuHtml;
    }
}