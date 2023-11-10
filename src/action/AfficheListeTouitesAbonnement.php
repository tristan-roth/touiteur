<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\RequetesBd;

class AfficheListeTouitesAbonnement extends Action {
    public function execute(): string
    {
        $contenuHtml = '';
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();

        $connecte = isset($_SESSION["login"]);
        $data =<<<SQL
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
            SQL;


        if ($connecte) {

            $utilisateur = $_SESSION["login"];
            $id_uti = RequetesBd::recupererId($utilisateur)+0;
            $recherche = $connexion->query("select count(id_utilisateur_suivi) as nombre from utilisateursuivi
                                        inner join utilisateur on id_utilisateur_suit = utilisateur.id_utilisateur
                                        where utilisateur.utilisateur = '$utilisateur'");
            $res = $recherche->fetch();
            $uti = $res["nombre"];
            if ($uti!==0){
                $data = <<<SQL
                SELECT Touits.id_touit,
                    message_text,
                    Images.image_path as image,
                    TouitsUtilisateur.id_utilisateur as id_user,
                    tagstouits.id_tag as id_tag
                FROM Touits 
                LEFT JOIN Images on Touits.id_image = Images.id_image
                INNER JOIN TouitsUtilisateur on Touits.id_touit = TouitsUtilisateur.id_touit
                left join tagstouits on Touits.id_touit = tagstouits.id_touit
                inner join utilisateursuivi on TouitsUtilisateur.id_utilisateur = utilisateursuivi.id_utilisateur_suivi
                where utilisateursuivi.id_utilisateur_suivi in (SELECt id_utilisateur_suivi from utilisateursuivi where id_utilisateur_suit = $id_uti)
                ORDER BY Touits.id_touit DESC
                SQL;


            }
        }
        else{
            $utilisateur = "";
        }
        $precedent =-1;
        $requete = $connexion->query($data);
        while ($res=$requete->fetch()) {
            $message = htmlspecialchars($res['message_text']);

            $id = $res['id_touit'];
            $user = $res['id_user'];
            $tag = $res['id_tag'];
            if ($precedent!==$id){
            $replacement = <<<HTML
                <a href="?action=tag&tag=$tag">$0</a><a href="?action=detail&id=$id&user=$user">
            HTML;

            $message = preg_replace('/#([^ #]+)/i',$replacement, $message);

            $contenuHtml .=<<<HTML
                    <div class="touit-box">
                        <a href="?action=detail&id=$id&user=$user">
                        <p>$message</p></a>
                        <div class="touit-actions">
                    HTML;

                    if (!$connecte){
                        $memeuti = false;
                    }
                    else{
                        $id_connecte = RequetesBd::RecupererId($utilisateur);
                        $memeuti = $user === $id_connecte;
                    }
                    if ($memeuti){
                    $contenuHtml.=<<<HTML
                <div class="Delete">
                    <form action="?action=supprimer&id=$id" class="supprimer" method="POST">
                        <input type="hidden" name="id" value="$id">
                        <input type="submit" value="Supprimer" name="button">
                    </form>
                </div>
                HTML;
                    }
                    else{
                $contenuHtml.=<<<HTML
                <div class="Follow">
                    <form action="?action=follow" class="suivre" method="POST">
                        <input type="hidden" name="user" value="$user">
                        <input type="submit" value="Suivre" name="mybutton">
                    </form>
                </div>
                HTML;
                    }
        

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
                <div class="rating">
                <form action="" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
                </div>
                </div>
            </div>
            HTML;
            $precedent = $id;
        }
    }
        unset($connexion);
        return $contenuHtml;
    }
}