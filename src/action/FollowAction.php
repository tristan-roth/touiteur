<?php
namespace iutnc\touiteur\action;
require_once "vendor/autoload.php";
use iutnc\touiteur\action\Action;
use iutnc\touiteur\action\SigninAction;

class FollowAction extends Action {

    public function __construct() {
        parent::__construct();
    }
    public function execute() : string {
        var_dump($_POST["user"]);
        $html = "";
        if (isset($_SESSION["login"])){
        }
        else{
            $html.= "<p>Connectez vous pour suivre un utilisateur</p>";
            $html.= (new SigninAction)->execute();
        }
        return $html;
    }
}