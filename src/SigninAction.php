<?php
namespace iutnc\touiteur\action;
require_once "vendor/autoload.php";
use iutnc\touiteur\action\Action as Action;
use iutnc\touiteur\connect\ConnectionFactory;
class SigninAction extends Action{
    public function __construct(){
        parent();
    }

    public function execute() : boolean{
        $method = $_SERVER["REQUEST_METHOD"];
        $html="";
        if ($method === "GET"){
            $html = $html . "<h3>connexion : </h3>
            <form action id = signin method = \"POST\">
            <input type = \"text\" id = \"mail\" name = \"mail\" placeholder = \"votre mail\">
            <input type = \"text\" id = \"mdp\" name = \"mdp\" placeholder = \"votre mot de passe\">
            <button type = \"submit\">valider</button>
            </form>";
        }
        else{
            ConnectionFactory::setConfig("config.ini");
            $mdp = password_hash($_POST["mdp"],1);
            $email = filter_var($_POST["mail"],FILTER_SANITIZE_EMAIL);
            $connexion = ConnectionFactory::makeConnection();
            echo $email . "   " . $mdp;
    
                $data = $connexion->query("select email, passwd from db where email = $email and passwd = $mdp");        
        }
        return $html;
    }
}