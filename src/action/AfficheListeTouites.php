<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        if (isset($_SESSION["login"])) $perso = $_SESSION["login"];
        else $perso = "";
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select touits.id_touit, message_text, images.image_path as image, touitsutilisateur.id_utilisateur as id_user from touits 
                                     left join images on touits.id_image = images.id_image
                                     inner join touitsutilisateur on touits.id_touit = touitsutilisateur.id_touit
                                     order by touits.id_touit desc");
        $html ='';
        while ($res=$data->fetch()){
            $message = htmlspecialchars($res['message_text']);
            $id = $res['id_touit'];
            $user = $res['id_user'];
            $html .= '<div class="tweets"><a href="?action=detail&id='.$id.'&user='.$user.'">';
            $html .= '<p>' . $message . '</p>
            <form action="?action=follow" method="POST" enctype="multipart/form-data">
            <button class="follow" type="submit">Suivre</button>
        </form>';
            if ($res['image'] !== null){
                $element = explode(".",$res['image']);
                switch($element[count($element)-1]){
                    case "mp4" :
                        $html.='<video controls width="250">
                        <source src="upload/'.$res['image'].'" type="video/mp4" />
                        <a href="upload/'.$res['image'].'"></a>
                        </video>';
                        $html .= "</a>";
                        break;
                    default :
                        $html .= "<img src='upload/".$res['image']."' width='300px' ><br>";
                }
         }
         $html .= <<<HTML
                <form action="index.php" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
                </a>
            </div>
            HTML;
        }
        unset($connexion);
        return $html;
    }
}