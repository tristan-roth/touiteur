<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheTouiteTag extends Action{

    public function execute(): string {
        $tag = $_GET['tag'];
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select touits.id_touit, message_text, images.image_path as image from touits 
                                   left join images on touits.id_image = images.id_image
                                   inner join tagstouits on touits.id_touit = tagstouits.id_touit
                                   where tagstouits.id_tag = $tag");
        $html = "";
        while ($res=$data->fetch()){
            $message = htmlspecialchars($res['message_text']);
            $html .= "<p>$message</p>";
            if ($res['image'] !== null){
                $element = explode(".",$res['image']);
                switch($element[count($element)-1]){
                    case "mp4" :
                        $html.='<video controls width="250">
                        <source src="upload/'.$res['image'].'" type="video/mp4" />
                        <a href="upload/'.$res['image'].'"></a>
                        </video>';
                        break;
                    default :
                        $html .= "<img src='upload/".$res['image']."' width='300px' ><br>";
                }
            }
            $html .= <<<HTML
                <form action="" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            HTML;
        }
        unset($connexion);
        return $html;
    }

}