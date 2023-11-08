<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class Accueil extends Action {
    public function execute(): string
    {
        return (new AfficheListeTouites())->execute();
    }
}
