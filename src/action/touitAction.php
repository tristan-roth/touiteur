<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;

class touitAction extends Action{

    public function __construct() {
        parent::__construct();
    }
    public function execute(): string{
        $html = <<<HTML
            <h3>touit : </h3>
                <form action id = touit method = \"POST\">
                    <input type = "text" id = "touit" name = "touit" placeholder = "votre touit">
                    <button type = "submit">Touiter</button>   
                </form>
            HTML;
        return $html;
    }
}