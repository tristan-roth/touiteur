<?php
namespace iutnc\touiteur\action;
require_once "vendor/autoload.php";
use iutnc\touiteur\action\Action;
use iutnc\touiteur\action\SigninAction;
use iutnc\touiteur\action\AfficheListeTouites;
use iutnc\touiteur\connection\ConnectionFactory;

class FollowAction extends Action {

    public function __construct() {
        parent::__construct();
    }
    public function execute() : string {
        $html = "";
        if (isset($_SESSION["login"])){
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $idsuivre = $_POST["user"];
            var_dump($idsuivre);
            $data=$connexion->query("select id_utilisateur from utilisateur where utilisateur = '$idsuivre'");
            $res = $data->fetch();
            $idsuivre = $res["id_utilisateur"];
            var_dump($idsuivre);
            $data=$connexion->query("select id_utilisateur from utilisateur where utilisateur = '{$_SESSION['login']}'");
            $res = $data->fetch();
            $idsuit = $res["id_utilisateur"];
            var_dump($idsuit);
            if ($idsuit !== $idsuivre){
                $data = $connexion->Query("insert into utilisateursuivi values($idsuit,$idsuivre)");
                $html.="<h2>Vous suivez maintenant $idsuivre</h2>";
                $html.=(new AfficheListeTouites)->execute();
            }
            else{
                $html.="<h2>vous ne pouvez pas vous suivre vous-même</h2>";
            }
            
        }
        else{
            $html.= "<p>Connectez vous pour suivre un utilisateur</p>";
            $html.= (new SigninAction)->execute();
        }
        return $html;
    }
}