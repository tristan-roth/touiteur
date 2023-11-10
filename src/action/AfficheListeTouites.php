<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\RequetesBd;

class AfficheListeTouites extends Action
{
    public function execute(): string
    {
        $contenuHtml = "";
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        // On détermine sur quelle page on se trouve
        if (isset($_GET["page"]) && !empty($_GET["page"])) {
            $currentPage = (int) strip_tags($_GET["page"]);
        } else {
            $currentPage = 1;
        }

        // On détermine le nombre total de touits
        $query = $connexion->prepare(
            <<<SQL
SELECT COUNT(*) AS nb_touits FROM Touits
SQL
        );
        $query->execute();
        $result = $query->fetch();
        $nbTouits = (int) $result["nb_touits"];

        //nb d'article par page
        $parPage = 15;
        //pages totales
        $pages = ceil($nbTouits / $parPage);
        //premier touit de la page
        $premier = $currentPage * $parPage - $parPage;

        $connecte = isset($_SESSION["login"]);
        $data = <<<SQL
SELECT Touits.id_touit,
        message_text,
        Images.image_path as image,
        TouitsUtilisateur.id_utilisateur as id_user,
        TagsTouits.id_tag as id_tag
    FROM Touits 
    LEFT JOIN Images on Touits.id_image = Images.id_image
    INNER JOIN TouitsUtilisateur on Touits.id_touit = TouitsUtilisateur.id_touit
    LEFT JOIN TagsTouits on Touits.id_touit = TagsTouits.id_touit
    ORDER BY Touits.id_touit DESC LIMIT $premier, $parPage
SQL;

        if ($connecte) {
            $utilisateur = $_SESSION["login"];

            $recherche = $connexion->query(
                <<<SQL
SELECT count(id_utilisateur_suivi) as nombre FROM UtilisateurSuivi
    INNER JOIN Utilisateur ON UtilisateurSuivi.id_utilisateur_suivi = Utilisateur.id_utilisateur
    WHERE Utilisateur.id_utilisateur = '$utilisateur'
SQL
            );

            $res = $recherche->fetch();
            $uti = $res["nombre"];

            if ($uti !== 0) {
                $data = <<<SQL
SELECT Touits.id_touit,
    message_text,
    Images.image_path as image,
    TouitsUtilisateur.id_utilisateur as id_user,
    TagsTouits.id_tag as id_tag
FROM Touits 
LEFT JOIN Images on Touits.id_image = Images.id_image
INNER JOIN TouitsUtilisateur on Touits.id_touit = TouitsUtilisateur.id_touit
LEFT JOIN TagsTouits on Touits.id_touit = TagsTouits.id_touit
ORDER BY Touits.id_touit DESC LIMIT $premier, $parPage
SQL;
            }
        } else {
            $utilisateur = "";
        }
        $precedent = -1;
        $requete = $connexion->query($data);
        while ($res = $requete->fetch()) {
            $message = htmlspecialchars($res["message_text"]);
            $id = $res["id_touit"];
            $user = $res["id_user"];
            $tag = $res["id_tag"];

            if ($precedent !== $id) {
                $replacement = <<<HTML
<a href="?action=tag&tag=$tag">$0</a>
<a href="?action=detail&id=$id&user=$user">
HTML;

                //$message = htmlspecialchars_decode($message);
                $message = htmlspecialchars(
                    preg_replace(
                        "/#([^ #]+)/i",
                        $replacement,
                        htmlspecialchars_decode($message)
                    )
                );
                // $message = htmlspecialchars($message);

                $contenuHtml .= <<<HTML
<div class="touit-box">
    <a href="?action=detail&id=$id&user=$user">
    <p>$message</p>
    <div class="touit-actions">
        <div class="rating">
            <form action="?action=like" method="post">
                <input type="hidden" name="id" value="$id">
                <input type="submit" name="type" value="like">
                <input type="submit" name="type" value="dislike">
            </form>
        </div>
HTML;
                if (!$connecte) {
                    $memeuti = false;
                } else {
                    $id_connecte = RequetesBd::RecupererId($utilisateur);
                    $memeuti = $user === $id_connecte;
                }
                if ($memeuti) {
                    $contenuHtml .= <<<HTML
<div class="Delete">
    <form action="?action=supprimer&id=$id" class="supprimer" method="POST">
        <input type="hidden" name="id" value="$id">
        <input type="submit" value="Supprimer" name="button">
    </form>
</div>
HTML;
                } else {
                    $contenuHtml .= <<<HTML
<div class="Follow">
    <form action="?action=follow" class="suivre" method="POST">
        <input type="hidden" name="user" value="$user">
        <input type="submit" value="Suivre" name="mybutton">
    </form>
</div>
HTML;
                }

                if ($res["image"] !== null) {
                    $element = explode(".", $res["image"]);

                    switch ($element[count($element) - 1]) {
                        case "mp4":
                            $contenuHtml .= <<<HTML
<video controls width="250">
    <source src="upload/$res[image]" type="video/mp4" />
    <a href="upload/$res[image]"></a>
</video>
HTML;
                            break;

                        default:
                            $contenuHtml .= <<<HTML
    <img src="upload/$res[image]" width="300px" ><br>
HTML;
                            break;
                    }
                }
                $contenuHtml .= <<<HTML
    </div>
    </a>
</div>
HTML;
                $precedent = $id;
            }
        }
        unset($connexion);

        return $contenuHtml;
    }
}
