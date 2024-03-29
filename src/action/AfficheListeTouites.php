<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\RequetesBd;
/**
 * affiche tous les touites
 */
class AfficheListeTouites extends Action
{
    public function execute(): string
    {
        $contenuHtml = "";
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

        //recuperationde tous les tweets pour leur affichage
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
            $id_connecte = RequetesBd::RecupererId($utilisateur);
            $recherche = $connexion->query(
                <<<SQL
            SELECT count(id_utilisateur_suivi) as nombre FROM UtilisateurSuivi
            INNER JOIN Utilisateur ON UtilisateurSuivi.id_utilisateur_suivi = Utilisateur.id_utilisateur
            WHERE Utilisateur.id_utilisateur = '$utilisateur'
            SQL
            );

            $res = $recherche->fetch();
            $uti = $res["nombre"];


        } else {
            $utilisateur = "";
        }
        $precedent = -1;
        $requete = $connexion->query($data);

        while ($res=$requete->fetch()) {
            //$message = htmlspecialchars($res['message_text']);
            $id = $res['id_touit'];
            $user = $res['id_user'];
            $tag = $res['id_tag'];
            if ($precedent!==$id){
                $replacement = <<<HTML
                <a href="?action=tag&tag=$tag">$0</a>
                HTML;


                $message = htmlspecialchars_decode($res['message_text']);
                $message = preg_replace('/#([^ #]+)/i',$replacement, $message);
                //$message = htmlspecialchars($message);


                $contenuHtml .= <<<HTML
                                <a href="?action=detail&id=$id&user=$user" style="width: 100%">
                                <div class="touit-box">
                                
                                <p>$message</p>
                                <div class="touit-actions">
                                <div class="AlignButton">
                                    <div class="rating">
                                        <form action="?action=like" method="post">
                                            <input type="hidden" name="id" value="$id">
                                            <input type="submit" class="test" name="type" value="like">
                                            <input type="submit" class="test" name="type" value="dislike">
                                        </form>
                                    </div>
                                HTML;
                if (!$connecte) {
                    $memeuti = false;
                } else {
                    $id_connecte = RequetesBd::RecupererId($utilisateur);
                    $memeuti = $user === $id_connecte;
                }
                //affichage des boutons en fonction de la relation entre l'utilisateur et l'auteur du tweet. 
                if ($connecte) {
                    if($memeuti){
                    $contenuHtml .= <<<HTML
                    <div class="Delete">
                        <form action="?action=supprimer&id=$id" class="supprimer" method="POST">
                            <input type="hidden" name="id" value="$id">
                            <input type="submit" class="test" value="Supprimer" name="button">
                        </form>
                    </div>
                    HTML;
                } else if (RequetesBd::followDeja($id_connecte,$user)){
                    $contenuHtml .= <<<HTML
                    <div class="Follow">
                        <form action="?action=delete" class="suivre" method="POST">
                            <input type="hidden" name="user" value="$user">
                            <input type="submit" class="test" value="se désabonner" name="mybutton">
                        </form>
                    </div>
                    HTML;
                }
                else{
                    $contenuHtml .= <<<HTML
                    <div class="Follow">
                        <form action="?action=follow" class="suivre" method="POST">
                            <input type="hidden" name="user" value="$user">
                            <input type="submit" class="test" value="suivre" name="mybutton">
                        </form>
                    </div>
                    HTML;
                }
            }
            else{
                $contenuHtml .= <<<HTML
                <div class="Follow">
                    <form action="?action=follow" class="suivre" method="POST">
                        <input type="hidden" name="user" value="$user">
                        <input type="submit" class="test" value="suivre" name="mybutton">
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
                            </div>
                            </div>
                            </a>
                            HTML;
                $precedent = $id;
            }
        }
        unset($connexion);
        return $contenuHtml;
    }
}
