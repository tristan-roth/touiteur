<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\AfficheListeTouites;

class DeconnexionAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute() : string {
        if (isset($_SESSION["login"])) {
            unset($_SESSION["login"]);
            $contenuHtml = "<p>Vous êtes maintenant déconnecté</p>";
            $contenuHtml .= (new AfficheListeTouites())->execute();
        } else {
            $contenuHtml = <<<HTML
                <p>Vous n'étiez pas connectés.</p>
                <p><br>COMMENT ÊTES VOUS ARRIVÉS LÀ??</br></p>
            HTML;
        }
        return $contenuHtml;
    }
}