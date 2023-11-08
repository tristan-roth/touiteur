<?php
namespace iutnc\touiteur\action;
require_once "vendor/autoload.php";
use iutnc\touiteur\action\Action;
use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\connection\ConnectionFactory;

class FollowAction extends Action {

    public function __construct() {
        parent::__construct();
    }
    public function execute() : string {
        var_dump($_POST["user"]);
        $html = "";
        if (isset($_SESSION["login"])){
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $idsuivre = $_POST["user"];
            $data=$connexion->query("select id_utilisateur from utilisateur where utilisateur = '{$_SESSION['login']}'");
            $res = $data->fetch();
            $idsuit = $res["id_utilisateur"];
            $data = $connexion->Query("insert into utilisateursuivi values($idsuit,$idsuivre)");
            $html.="<h2>Vous suivez maintenant $idsuivre</h2>";
        }
        else{
            $html.= "<p>Connectez vous pour suivre un utilisateur</p>";
            $html.= (new SigninAction)->execute();
        }
        return $html;
    }
}