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
        $html="";

        if ($method === "GET" || !isset($_POST["nom"])) {
            $html = <<<HTML
            <h3>connexion : </h3>
            <form action="signin" id="signin" method="POST">
                <input type="text" id="nom" name="nom" placeholder="votre nom d'utilisateur">
                <input type="password" id="mdp" name="passwd" placeholder="votre mot de passe">
                <button type="submit">Valider</button>
            </form>
            HTML;
        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $mdp = $_POST["passwd"];
            $nom = @filter_var($_POST["nom"],FILTER_SANITIZE_STRING);
            $data = $connexion->prepare("select utilisateur, passwd from utilisateur where utilisateur = ?");
            $data->bindParam(1,$nom);
            $data->execute();
            if ($data->rowCount()===0){
                $html.= "<p>ce nom d'utilisateur n'existe pas. <a href=\"?action=signup\">Créez un compte</a> pour continuer</p>";
            }
            else{
                while ($res=$data->fetch()){
                    if (password_verify($mdp, $res['passwd'])) {
                        $_SESSION["login"] = $nom;
                        $html.="<h1>Vous êtes maintenant connecté</h1>";
                        $html.=(new AfficheListeTouites)->execute();
                    }
                    else{
                        $html.="<h1>Les informations ne correspondent pas</h1>";
                    }
                }
            } 
        }
        unset($connexion);
        return $html;
    }
}