<?php
namespace iutnc\touiteur\action;
require_once "vendor/autoload.php";
use iutnc\touiteur\action\Action;

class DeconnexionAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute() : string {
        if (isset($_SESSION["login"])){
            unset($_SESSION["login"]);
            $html = "<p>Vous êtes maintenant déconnecté</p>";
        }
        else{
            $html = "<p>Vous n'étiez pas connectés.</p><p><br>COMMENT ÊTES VOUS ARRIVÉS LÀ??</br></p>";
        }
        return $html;
    }
}