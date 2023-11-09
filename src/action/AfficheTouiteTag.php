<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheTouiteTag extends Action{

    public function execute(): string {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select touits.id_touit, message_text, images.image_path as image from touits 
                                   left join images on touits.id_image = images.id_image
                                   inner join tagstuits on touits.id_touit = tagstuits.id_touit
                                   where tagstuits.id_tag = 1");
    }

}