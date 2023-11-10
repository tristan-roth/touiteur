<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheTouite extends Action {

    public function execute(): string
    {
        $id = $_GET['id'];
        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT message_text,
                    date_touit,
                    rating,
                    Images.image_path as image,
                    Utilisateur.utilisateur as id_user,
                    Utilisateur.id_utilisateur as userr
                FROM Touits 
                LEFT JOIN Images ON Touits.id_image = Images.id_image
                INNER JOIN TouitsUtilisateur ON Touits.id_touit = TouitsUtilisateur.id_touit
                INNER JOIN Utilisateur ON TouitsUtilisateur.id_utilisateur = Utilisateur.id_utilisateur
                WHERE Touits.id_touit = $id
            SQL);

        $contenuHtml = "";
        while ($res = $data->fetch()) {

            $message = htmlspecialchars($res['message_text']);
            $message = preg_replace('/#([^ #]+)/i', '<a href="?action=tag">$0</a>', $message);
            $contenuHtml .= "<p>$message</br></p>";

            if ($res['image'] !== null) {
                $element = explode(".",$res['image']);

                switch($element[count($element)-1]) {
                    case "mp4" :
                        $contenuHtml .= <<<HTML
                        <div class="organisation"></div>
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
                <div class="organisation">
                    <p>date : $res[date_touit]</p>
                    <p>rating : $res[rating]</p>
                    <a href="?action=auteur&user=$res[userr]">
                        <p>Auteur : $res[id_user]</p>
                    </a>
                </div>

                <form action="?action=like" method="post">
                    <input type="hidden" name="id" value="$id">
                    <input type="submit" class="test" name="type" value="like">
                    <input type="submit" class="test" name="type" value="dislike">
                </form>
            HTML;
        }
        unset($connexion);

        return $contenuHtml;
    }
}