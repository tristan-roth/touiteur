<?php
declare(strict_types=1);
namespace iutnc\touiteur\connect;

class ConnectionFactory{

    static $tab;

    public static function setConfig(string $file) : void{
        self::$tab = parse_ini_file($file);
    }

    public static function makeConnection() : PDO{
        $driver=self::$tab["driver"];
        $username=self::$tab["username"];
        $mdp=self::$tab["mdp"];
        $host=self::$tab["host"];
        $db=self::$tab["db"];

        $db = new PDO(
            "$driver:host=$host;dbname=$db",
            "$username",
            "$mdp"
        );
        return $db;
    }
}
