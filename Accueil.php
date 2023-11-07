<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;
require_once "vendor/autoload.php";
use iutnc\touiteur\action\Action;

class Accueil extends Action {
    public function execute(): string
    {
        $listTouitesAction = new AfficheListeTouites();
        return $listTouitesAction->execute();
    }
}
