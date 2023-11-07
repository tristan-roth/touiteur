<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheTouite extends Action{

    public function execute(): string
    {
        $id = $_GET['id'];
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select message_text, date_touit, rating, images.image_path as image, utilisateur.utilisateur as id_user from touits 
                                    left join images on touits.id_image = images.id_image
                                    inner join touitsutilisateur on touits.id_touit = touitsutilisateur.id_touit
                                    inner join utilisateur on touitsutilisateur.id_utilisateur = utilisateur.id_utilisateur
                                    where touits.id_touit = $id");
        $html = "";
        while ($res=$data->fetch()){
            $message = htmlspecialchars($res['message_text']);
            $html .= "<p>$message</p>";
            if ($res['image'] !== null){
                $element = explode(".",$res['image']);
                switch($element[count($element)-1]){
                    case "mp4" :
                        $html.='<video controls width="250">
                        <source src="upload/'.$res['image'].'" type="video/mp4" />
                        <a href="upload/'.$res['image'].'"></a>
                        </video>';
                        break;
                    default :
                        $html .= "<img src='upload/".$res['image']."' width='300px' ><br>";
                }
            }
            $html .= "<p>date : ".$res['date_touit']."</p>";
            $html .= "<p>rating : ".$res['rating']."</p>";
            $html .= "<p>Auteur : ".$res['id_user']."</p>";
            $html .= <<<HTML
                <form action="index.php" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            HTML;
        }
        unset($connexion);
        return $html;
    }
}