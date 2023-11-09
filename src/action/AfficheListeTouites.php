<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        $contenuHtml = '';
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        
        $data = $connexion->query(<<<SQL
            SELECT Touits.id_touit,
                    message_text,
                    Images.image_path as image,
                    TouitsUtilisateur.id_utilisateur as id_user,
                    tagstouits.id_tag as id_tag
                FROM Touits 
                LEFT JOIN Images on Touits.id_image = Images.id_image
                INNER JOIN TouitsUtilisateur on Touits.id_touit = TouitsUtilisateur.id_touit
                INNER JOIN tagstouits on Touits.id_touit = tagstouits.id_touit
                ORDER BY Touits.id_touit DESC
            SQL);


        if (isset($_SESSION["login"])) {

            $utilisateur = $_SESSION["login"];
            $recherche = $connexion->query(<<<SQL
                SELECT COUNT(id_utilisateur_suivi) as nombre FROM utilisateursuivi
                    INNER JOIN utilisateur ON id_utilisateur_suit = utilisateur.id_utilisateur
                    WHERE utilisateur.utilisateur = '$utilisateur'
                SQL);

            if ($recherche->rowCount() !== 0) {
                $res = $recherche->fetch();
                $contenuHtml.="<h2>{$res["nombre"]}</h2>";
            }
        }

        while ($res=$data->fetch()) {
            $message = htmlspecialchars($res['message_text']);
            //$message = preg_replace('/#([^#\'\s]+)/i', '<a href="?action=tag">$0</a>', $message);

            $message = preg_replace('/#([^ #]+)/i', '<a href="?action=tag">$0</a>', $message);


            $id = $res['id_touit'];
            $user = $res['id_user'];
            $tag = $res['id_tag'];
            $replacement = <<<HTML
                <a href="?action=tag&tag=$tag">$0</a>
            HTML;

            $message = preg_replace('/#([^ #]+)/i', $replacement, $message);
            $contenuHtml .= <<<HTML
                <div class="tweet-box">
                <a href="?action=detail&id=$id&user=$user">
                <p>$message</p>
                <form action="?action=follow" class="suivre" method="POST">
                    <input type="hidden" name="user" value="$user">
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