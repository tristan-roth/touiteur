<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;

class TouitAction extends Action{

    public function __construct() {
        parent::__construct();
    }
    public function execute(): string{
        $method = $_SERVER["REQUEST_METHOD"];
        $html = "";
        if ($method === "GET") {
            $html = <<<HTML
            <h3>touit : </h3>
                <form action id =touit method ="POST">
                    <input type = "text" id = "touit" name = "touit" placeholder = "votre touit">
                    <button type = "submit">Touiter</button>   
                </form>
            HTML;
        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $data = $connexion->prepare("insert into touit (message) values (?)");
            $message = $_POST["touit"];
            $data->execute([$message]);
        }
        return $html;
    }
}