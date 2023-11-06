<?php
namespace iutnc\touiteur\Dispatcheur;
class Dispatcheur{
    $action;
    function __construct(){
        if (isset($_GET["action"])) $this->action = $_GET["action"];
        else $this->action
    }
}