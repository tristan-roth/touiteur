<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\connection\ConnectionFactory;

class RatingTouites extends Action {
    public function execute(): string {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $touiteId = $_POST["touiteId"];
            $action = $_POST["action"];

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            if ($action === "like") {
                $query = "UPDATE touit SET rating = rating + 1 WHERE id = ?";
            } elseif ($action === "dislike") {
                $query = "UPDATE touit SET rating = rating - 1 WHERE id = ?";
            }
            $data = $connexion->prepare($query);
            $data->execute([$touiteId]);
            header("Location: accueil.php");
            exit;
        }

        return "fqidhfonaqdifji";
    }
}
