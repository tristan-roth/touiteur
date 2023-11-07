<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select message from touit");
        $html = "";
        while ($res=$data->fetch()){
            $html .= "<p>{$res['message']}</p>";
        }
        return $html;
    }
}