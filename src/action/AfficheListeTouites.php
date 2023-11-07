<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class AfficheListeTouites extends Action {
    public function execute(): string
    {
        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->query("select message from touit");
        $html = "";
        while ($res=$data->fetch()){
            $touiteId = $res['id'];
            $message = htmlspecialchars($res['message']);

            $html .= "<p>$message</p>";

            $html .= <<<HTML
                <form action="noter_touite.php" method="post">
                    <input type="hidden" name="touiteId" value="$touiteId">
                    <input type="submit" name="action" value="like">
                    <input type="submit" name="action" value="dislike">
                </form>
            HTML;
        }

        return $html;
    }
}