<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        if (isset($_SESSION["login"])) {
            $utilisateur = $_SESSION["login"];
        } else {
            $utilisateur = "";
        }

        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT Touits.id_touit,
                    message_text,
                    Images.image_path as image,
                    TouitsUtilisateur.id_utilisateur as id_user
                FROM Touits 
                LEFT JOIN Images on Touits.id_image = Images.id_image
                INNER JOIN TouitsUtilisateur on Touits.id_touit = TouitsUtilisateur.id_touit
                ORDER BY Touits.id_touit DESC
            SQL);

        $contenuHtml ='';
        while ($res=$data->fetch()) {

            $message = htmlspecialchars($res['message_text']);
            $id = $res['id_touit'];
            $user = $res['id_user'];
            $contenuHtml = <<<HTML
                <div class="tweet-box">
                <a href="?action=detail&id=$id&user=$user">
                <p>$message</p>
                <form action="?action=follow" class="suivre" method="POST">
                    <input type="hidden" name="user" value="'.$user.'">
                    <input type="submit" value="Suivre" name="mybutton">
                </form>
            HTML;

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
                </a>
                <form action="" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            </div>
            HTML;
        }
        unset($connexion);
        return $contenuHtml;
    }
}