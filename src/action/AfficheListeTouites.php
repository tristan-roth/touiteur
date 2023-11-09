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
                left join tagstouits on Touits.id_touit = tagstouits.id_touit
                ORDER BY Touits.id_touit DESC
            SQL);
        //demander a raf car left join affiche en deux fois quand on met deux # dans un touit


        if (isset($_SESSION["login"])) {

            $utilisateur = $_SESSION["login"];
            $recherche = $connexion->query(<<<SQL
                SELECT count(id_utilisateur_suivi) as nombre FROM UtilisateurSuivi
                    INNER JOIN Utilisateur ON id_utilisateur_suit = Utilisateur.id_utilisateur
                    WHERE Utilisateur.utilisateur = '$utilisateur'
                SQL);

            if ($recherche->rowCount() !== 0) {
                $res = $recherche->fetch();
                $contenuHtml.="<h2>{$res["nombre"]}</h2>";
            }
        }
        
        while ($res=$data->fetch()) {

            $message = htmlspecialchars($res['message_text']);

            $id = $res['id_touit'];
            $user = $res['id_user'];
            $tag = $res['id_tag'];
            $replacement = <<<HTML
                <a href="?action=tag&tag=$tag">$0</a><a href="?action=detail&id=$id&user=$user">
            HTML;

            $message = preg_replace('/#([^ #]+)/i',$replacement, $message);

            $contenuHtml .= <<<HTML
                <div class="tweet-box">
                <a href="?action=detail&id=$id&user=$user">
                <p>$message</p></a>
                <form action="?action=follow" class="suivre" method="POST">
                    <input type="hidden" name="user" value="$user">
                    <input type="submit" value="Suivre" name="mybutton">
                </form>
                
                <form action="?action=supprimer&id=$id" class="supprimer" method="POST">
                    <input type="hidden" name="id" value="$id">
                    <input type="submit" value="Supprimer" name="button">
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