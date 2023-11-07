<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheTouiteUtilisateur extends Action {

    public function execute(): string
    {
        $id = $_GET['id'];
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select id_touit, message_text, images.image_path as image from touits left join images on touits.id_image = images.id_image
                                   order by touits.id_touit desc
                                   where touits.id_utilisateur = $id");
    }
}