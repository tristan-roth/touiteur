<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select message, image from touit");
        $html = "";
        while ($res=$data->fetch()){
            $message = htmlspecialchars($res['message']);
            $html .= "<p>$message</p>";
            if ($res['image'] != "null")
                $html .= "<img src='upload/".$res['image']."' width='300px' ><br>";
            var_dump($res['image']);
            $html .= <<<HTML
                <form action="noter_touite.php" method="post">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            HTML;
        }

        return $html;
    }
}