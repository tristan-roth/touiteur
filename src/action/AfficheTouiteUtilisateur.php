<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\RequetesBd;

class AfficheTouiteUtilisateur extends Action {

    public function execute(): string {
        $id = $_GET['user'];
        $uti = RequetesBd::recupererNom($id);

        if(isset($_SESSION["login"])){

        }

        $connexion = ConnectionFactory::makeConnection();

        $data = $connexion->query(<<<SQL
            SELECT Touits.id_touit,
                    message_text, 
                    Images.image_path as image
                FROM Touits 
                LEFT JOIN Images ON Touits.id_image = Images.id_image
                INNER JOIN TouitsUtilisateur ON Touits.id_touit = TouitsUtilisateur.id_touit
                WHERE TouitsUtilisateur.id_utilisateur = $id
                ORDER BY Touits.id_touit DESC
            SQL);

        $contenuHtml = "<h2>Touites de $uti</h2>";
        while ($res = $data->fetch()) {

            $idTouit = $res['id_touit'];
            $message = htmlspecialchars($res['message_text']);
            $contenuHtml .= <<<HTML
                <div class="touit-box">
                    <a href="?action=detail&id=$idTouit">
                        <p>$message</p>
                    </a>
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
                <div class="Delete">
                    <form action="?action=supprimer&id=$id" class="supprimer" method="POST">
                        <input type="hidden"  name="id" value="$id">
                        <input type="submit" class="test" value="Supprimer" name="button">
                    </form>
                </div>
                HTML;
            $contenuHtml .= <<<HTML
                <form action="?action=like" method="post">
                    <input type="hidden" name="id" value="$id">
                    <input type="submit" class="test name="type" value="like">
                    <input type="submit" class="test" name="type" value="dislike">
                </form>
            </div>
            HTML;
        }
        unset($connexion);
        
        return $contenuHtml;
    }
}