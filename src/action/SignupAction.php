<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;

class SignupAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute() : string {
        $method = $_SERVER["REQUEST_METHOD"];
        $html="";

        if ($method === "GET") {
            $html = <<<HTML
            <h3>Inscription : </h3>
            <form action id=signup method ="POST">
                <input type="text" id="nom" name="nom" placeholder="votre nom d'utilisateur">
                <input type="text" id="mdp" name="mdp" placeholder="votre mot de passe">
                <button type="submit">Valider</button>
            </form>
            HTML;
        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $mdp = password_hash($_POST["mdp"], PASSWORD_DEFAULT, ['cost'=> 12]);
            $nom = @filter_var($_POST["nom"],FILTER_SANITIZE_STRING);
            $data = $connexion->query("select utilisateur from user where utilisateur = '$nom'");
            if ($data->rowCount()!==0){
                $html.= "<p>ce nom d'utilisateur est déjà pris. <a href=\"?action=signin\">Connectez vous</a> pour continuer</p>";
            }    
            else{
                try{
                    $connexion->query("insert into user values ('$nom','$mdp')");
                    $html.= "<p>Votre compte a été créé</p>";
                }
                catch (SQLException){
                    $html.="<p>une erreur est survenue</p>";
                }
            }
        }
        return $html;
    }
}