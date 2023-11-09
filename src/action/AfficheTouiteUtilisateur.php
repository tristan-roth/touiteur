<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheTouiteUtilisateur extends Action {

    public function execute(): string
    {
        $id = $_GET['user'];
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT Touits.id_touit,
                    message_text, 
                    Images.image_path as image
                FROM Touits 
                LEFT JOIN images ON touits.id_image = images.id_image
                INNER JOIN touitsutilisateur ON touits.id_touit = touitsutilisateur.id_touit
                WHERE touitsutilisateur.id_utilisateur = $id
                ORDER BY touits.id_touit DESC
            SQL);

        $contenuHtml = "";
        while ($res=$data->fetch()) {
            $message = htmlspecialchars($res['message_text']);
            $contenuHtml .= "<p>$message</p>";

            if ($res['image'] !== null) {
                $element = explode(".",$res['image']);

                switch($element[count($element)-1]) {
                    case "mp4" :
                        $contenuHtml .= <<<HTML
                            <video controls width="250">
                                <source src="upload/$res[image]" type="video/mp4" />
                                <a href="upload/$res[image]"></a>
                            </video>
                        HTML;
                        break;

                    default :
                        $contenuHtml .= <<<HTML
                            <img src="upload/$res[image]" width="300px" ><br>
                        HTML;
                        break;
                }
            }
            $contenuHtml .= <<<HTML
                <form action="" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            HTML;
        }
        unset($connexion);
        
        return $contenuHtml;
    }
}