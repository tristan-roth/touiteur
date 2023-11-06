<?php
namespace iutnc\touiter\action;
require_once "vendor/autoload.php";
use iutnc\touiter\action\Action as Action;
class SigninAction extends Action{
    public function __construct(){
        parent();
    }

    static function execute() : boolean{
        $method = $_SERVER["REQUEST_METHOD"];
        if ($method === "GET"){
            $html = this->$html . "<h3>connexion : </h3>
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
                if ($data->rowCount()===1)
                    return true;
                else return false;
        
        }
    }
}