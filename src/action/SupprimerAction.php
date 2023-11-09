<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class SupprimerAction extends Action{

    public function execute(): string
    {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $id = $_GET['id'];
        $data = $connexion->query("select utilisateur from utilisateur 
                                   inner join touitsutilisateur on utilisateur.id_utilisateur = touitsutilisateur.id_utilisateur
                                   where touitsutilisateur.id_touit = $id");
        $res = $data->fetch();
        if ($res['utilisateur'] === $_SESSION['login']){
            $data = $connexion->query("delete from touits where id_touit = $id");
            $contenuHtml = "<h2>Le touite a bien été supprimé</h2>";
        } else {
            $contenuHtml = "<h2>Vous ne pouvez pas supprimer ce touite</h2>";
        }
        return $contenuHtml;
    }
}