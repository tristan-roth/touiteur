<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;

class SignupAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $html = "";

        if ($method === "GET") {
            $html = <<<HTML
<h3>Inscription : </h3>
<form action id=signup method ="POST">
    <input type="text" id="nom" name="nom" placeholder="votre nom">
    <input type="text" id="prenom" name="prenom" placeholder="votre prénom">
    <input type="text" id="uti" name="uti" placeholder="votre nom d'utilisateur">
    <input type="text" id="mdp" name="mdp" placeholder="votre mot de passe">
    <button type="submit">Valider</button>
</form>
HTML;
        } elseif ($method === "POST") {
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();
            $mdp = $_POST["mdp"];
            if (!$this->checkPasswordStrength($mdp, 8)) {
                $html .=
                    "<h2>Votre mot de passe doit comporter au moins 8 caractères, un chiffre, un caractère spécial, une majuscule, une minuscule</h2>";
            } else {
                $mdpHash = password_hash($_POST["mdp"], PASSWORD_DEFAULT, [
                    "cost" => 12,
                ]);
                $uti = @filter_var($_POST["uti"], FILTER_SANITIZE_STRING);
                $nom = @filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
                $prenom = @filter_var($_POST["prenom"], FILTER_SANITIZE_STRING);
                if ($uti==="" ||$nom==="" ||$prenom==="" ){
                    $html.="<h2>Remplissez tous les champs</h2>";
                }
                else{
                $data = $connexion->prepare(
                    "select utilisateur from utilisateur where utilisateur = ?"
                );
                $data->bindParam(1,$uti);
                $data->execute();
                if ($data->rowCount() !== 0) {
                    $html .=
                        "<p>ce nom d'utilisateur est déjà pris. <a href=\"?action=signin\">Connectez vous</a> pour continuer</p>";
                } else {
                    try {
                        $data=$connexion->prepare(
                            "insert into utilisateur (utilisateur,passwd,nom,prenom,nbSuivis,nbSuiveurs) values (?,?,?,?,0,0)"
                        );
                        $data->execute(array($uti,$mdpHash,$nom,$prenom));
                        $_SESSION["login"] = $uti;
                        $html .= "<p>Votre compte a été créé, vous y êtes maintenant connectés</p>";
                    } catch (SQLException) {
                        $html .= "<p>une erreur est survenue</p>";
                    }
                }
            }
        }
        }
        return $html;
    }

    public function checkPasswordStrength(
        string $pass,
        int $minimumLength
    ): bool {
        $length = strlen($pass) >= $minimumLength; // longueur minimale
        $digit = preg_match("#[\d]#", $pass); // au moins un digit
        $special = preg_match("#[\W]#", $pass); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if (!$length || !$digit || !$special || !$lower || !$upper) {
            return false;
        }
        return true;
    }
}
