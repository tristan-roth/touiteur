<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;

class SigninAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute() : string {
        $method = $_SERVER["REQUEST_METHOD"];
        $html="";

        if ($method === "GET") {
            $html = <<<HTML
            <h3>connexion : </h3>
            <form action id=signin method ="POST">
                <input type="text" id="nom" name="nom" placeholder="votre nom d'utilisateur">
                <input type="text" id="mdp" name="mdp" placeholder="votre mot de passe">
                <button type="submit">Valider</button>
            </form>
            HTML;
        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $mdp = $_POST["mdp"];
            $nom = @filter_var($_POST["nom"],FILTER_SANITIZE_STRING);
            $data = $connexion->query("select utilisateur, passwd from user where utilisateur = '$nom'");
            if ($data->rowCount()===0){
                $html.= "<p>ce nom d'utilisateur n'existe pas. <a href=\"?action=signup\">Créez un compte</a> pour continuer</p>";
<<<<<<< Updated upstream
            }    
            while ($res=$data->fetch()){
                if (password_verify($mdp, $res['passwd'])) {
                    echo "ca marche";
                    session_start();
                    $_SESSION["login"] = "$nom";
                   }
=======
            }
            else{
                while ($res=$data->fetch()){
                    if (password_verify($mdp, $res['passwd'])) {
                        session_start();
                        $_SESSION["login"] = "$nom";
                        $html.="<h1>Vous êtes maintenant connectés</h1>";
                    }
                    else{
                        $html.="<h1>Les informations ne correspondent pas</h1>";
                    }
                }
>>>>>>> Stashed changes
            } 
        }
        return $html;
    }
}