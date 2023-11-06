<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;

class touitAction extends Action{

    public function __construct() {
        parent::__construct();
    }
    public function execute(): string{
        $html = <<<HTML
            <h3>touit : </h3>
                <form action id = touit method = "POST">
                    <input type = "text" id = "touit" name = "touit" placeholder = "votre touit">
                    <button type = "submit">Touiter</button>   
                </form>
            HTML;

        ConnectionFactory::setConfig("config.ini");
        $connexion = ConnectionFactory::makeConnection();
        $data = $connexion->prepare("insert into touit (message) values (?)");
        $message = $_POST["touit"];
        $data->execute([$message]);

        return $html;
    }
}