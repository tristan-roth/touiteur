<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;

class SignupAction extends Action {

    public function __construct() {
        parent::__construct();
    }

    public function execute(): string {
        $method = $_SERVER["REQUEST_METHOD"];

        $contenuHtml = "";
        if ($method === "GET") {
            $contenuHtml = <<<HTML
                <h3>Inscription : </h3>
                <form action="" id="signup" method="POST">
                    <input type="text" id="nom" name="nom" placeholder="Votre nom">
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom">
                    <input type="text" id="uti" name="uti" placeholder="Votre nom d'utilisateur">
                    <input type="password" id="mdp" name="mdp" placeholder="Votre mot de passe">
                    <input type="password" id="mdp" name="mdp2" placeholder="Retapez votre mot de passe"><br>
                    <button type="submit">Valider</button>
                </form>
            HTML;

        } else if ($method === "POST") {
            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            $mdp = $_POST["mdp"];
            $mdp2 = $_POST["mdp2"];

            if (!$this->checkPasswordStrength($mdp, 8)) {
                $contenuHtml .= <<<HTML
                    <h2>Votre mot de passe doit comporter au moins 8 caractères, un chiffre, un caractère spécial, une majuscule et une minuscule.</h2>
                HTML;
            } else {
                $uti = @filter_var($_POST["uti"], FILTER_SANITIZE_STRING);
                $nom = @filter_var($_POST["nom"], FILTER_SANITIZE_STRING);
                $prenom = @filter_var($_POST["prenom"], FILTER_SANITIZE_STRING);

                if ($uti === "" || $nom === "" || $prenom === "") {
                    $contenuHtml .= <<<HTML
                        <h2>Remplissez tous les champs.</h2>
                    HTML;

                } else if ($mdp !== $mdp2) {
                    $contenuHtml .= <<<HTML
                        <h2>Vos mots de passe ne correspondent pas.</h2>
                    HTML;

                } else {
                    $data = $connexion->prepare(<<<SQL
                        SELECT utilisateur FROM Utilisateur
                            WHERE utilisateur = ?
                        SQL);

                    $data->bindParam(1, $uti);
                    $data->execute();

                    if ($data->rowCount() !== 0) {
                        $contenuHtml .= <<<HTML
                            <p>ce nom d'utilisateur est déjà pris. <a href="?action=signin">Connectez vous</a> pour continuer</p>
                            HTML;
                    } else {
                        try {
                            $mdpHash = password_hash($_POST["mdp"], PASSWORD_DEFAULT, [
                                "cost" => 12,
                            ]);
                            $data = $connexion->prepare(<<<SQL
                                INSERT INTO Utilisateur (
                                        utilisateur,
                                        passwd,
                                        nom,
                                        prenom,
                                        nbSuivis,
                                        nbSuiveurs
                                    ) values (?,?,?,?,0,0)
                            SQL);

                            $data->execute(array
                                ($uti,
                                $mdpHash,
                                $nom,
                                $prenom
                                ));

                            $_SESSION["login"] = $uti;
                            $contenuHtml .= "<p>Votre compte a été créé, vous y êtes maintenant connectés.</p>";
                        } catch (SQLException $e) {
                            $contenuHtml .= "<p>Une erreur est survenue.</p>";
                        }
                    }
                }
            }
        }
        return $contenuHtml;
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
