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
                <input type="text" id="nom" name="nom" placeholder ="votre mail">
                <input type="text" id="mdp" name="mdp" placeholder ="votre mot de passe">
                <button type="submit">Valider</button>
            </form>
            HTML;
        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $mdp = password_hash($_POST["mdp"],1);
            $email = @filter_var($_POST["nom"],FILTER_SANITIZE_STRING);
            $data = $connexion->query("select email, passwd from user");       
            while ($res=$data->fetch()){
                $html .= "<p>email : {$res['email']}, mdp : {$res['passwd']}"; 
            } 
        }
        return $html;
    }
}