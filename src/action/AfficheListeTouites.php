<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select message_text, images.image_path as image from touits inner join images on touits.id_image = images.id_image
                                     order by touits.id_touit desc");
        $html = "";
        while ($res=$data->fetch()){
            $message = htmlspecialchars($res['message_text']);
            $html .= "<p>$message</p>";
            if ($res['image'] !== "null"){
                $element = explode(".",$res['image']);
                switch($element[count($element)-1]){
                    case "mp4" :
                        $html.='<video controls width="250">
                        <source src="upload/'.$res['image'].'" type="video/mp4" />
                        <a href=""upload/'.$res['image'].'"></a>
                        </video>';
                        break;
                    default :
                        $html .= "<img src='upload/".$res['image']."' width='300px' ><br>";
                }
            
            
            $html .= <<<HTML
                <form action="index.php" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            HTML;
        }
    }
        return $html;
    }
}