<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\AfficheListeTouites;

class SigninAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute() : string {
        $method = $_SERVER["REQUEST_METHOD"];
        $contenuHtml="";

        if ($method === "GET" || !isset($_POST["nom"])) {
            $contenuHtml = <<<HTML
            <h3>Connexion : </h3>
            <form action="" id="signin" method="POST">
                <input type="text" id="nom" name="nom" placeholder="Votre nom d'utilisateur" autocomplete="off">
                <input type="password" id="mdp" name="passwd" placeholder="Votre mot de passe" autocomplete="off">
                <button type="submit">Valider</button>
            </form>
            HTML;
        }
        else if ($method === "POST") {

            $connexion = ConnectionFactory::makeConnection();

            $data = $connexion->prepare(<<<SQL
                SELECT utilisateur,
                        passwd
                    FROM Utilisateur
                    WHERE utilisateur = ?
                SQL);
            
            $mdp = $_POST["passwd"];
            $nom = @filter_var($_POST["nom"], FILTER_SANITIZE_STRING);

            $data->bindParam(1,$nom);
            $data->execute();

            if ($data->rowCount() === 0) {
                $contenuHtml .= <<<HTML
                    <p>Ce nom d'utilisateur n'existe pas. <a href="?action=signup">Créez un compte</a> pour continuer.</p>
                HTML;

            } else {
                while ($res = $data->fetch()) {
                    if (password_verify($mdp, $res['passwd'])) {
                        $_SESSION["login"] = $nom;
                        $contenuHtml .= "<h1>Vous êtes maintenant connecté.</h1>";
                        $contenuHtml .= (new AfficheListeTouites)->execute();
                    } else {
                        $contenuHtml .= "<h1>Les informations ne correspondent pas.</h1>";
                    }
                }
            } 
        }
        unset($connexion);

        return $contenuHtml;
    }
}