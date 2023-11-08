<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class Accueil extends Action {
    public function execute(): string
    {
        $listTouitesAction = new AfficheListeTouites();
        return $listTouitesAction->execute();
    }
}
