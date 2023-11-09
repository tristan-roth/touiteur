<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\AfficheListeTouites;

class TouitAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }
    public function execute(): string
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $html = "";

            if (isset($_SESSION["login"])){

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            //recupérer un fichier
            if (isset($_FILES["image"])) {
                $tmpName = $_FILES["image"]["tmp_name"];
                $name = $_FILES["image"]["name"];
                $size = $_FILES["image"]["size"];
                $error = $_FILES["image"]["error"];

                $tabExtension = explode(".", $name);
                $extension = strtolower(end($tabExtension));
                $extensions = ["jpg", "png", "jpeg", "gif", "mp4"];
                $maxSize = 100000000;

                if (in_array($extension, $extensions) &&
                    $size <= $maxSize &&
                    $error == 0) {
                    $uniqueName = uniqid("", true);
                    //uniqid génère quelque chose comme ca : 5f586bf96dcd38.73540086
                    $file = $uniqueName . "." . $extension;
                    move_uploaded_file($tmpName, "upload/" . $file);
                }

                if ($_POST["touit"] != "") {
                    $data = $connexion->query(
                        "select max(id_touit) as id_touit from touits "
                    );
                    $res = $data->fetch();
                    $id_touit = $res["id_touit"]+1;
                    if ($id_touit === null) {
                        $id_touit = 0;
                    }

                    if (!isset($file)) {
                        $id_image = null;
                    } else {
                        $data = $connexion->query(
                            "select max(id_image) as id_image from images "
                        );
                        $res = $data->fetch();
                        $id_image = $res["id_image"];
                        if ($id_image === null) $id_image = 0;
                        else $id_image +=1;
                        $data = $connexion->prepare(
                            "insert into images values (?, null, ?)"
                        );
                        $data->execute(array($id_image,$file));
                    }

                    $data = $connexion->prepare(
                        "insert into touits values (?,?, sysdate(), 0, ?)"
                    );
                    $message = $_POST["touit"];
                    $nom_uti = $_SESSION["login"];
                    $data->execute([$id_touit, $message, $id_image]);


                    preg_match_all( '/#[^ #]+/i', $message,$tags);
                    foreach ($tags[0] as $tag) {
                        $data = $connexion->prepare(
                            "select id_tag from tags where libelle_tag = ?"
                        );
                        $data->execute([$tag]);
                        $resExist = $data->fetch();
                        if ($resExist === false) {

                            $data = $connexion->query(
                                "select max(id_tag) as id_tag from tags "
                            );
                            $res = $data->fetch();
                            $id_tag = $res["id_tag"] + 1;
                            if ($id_tag === null)
                                $id_tag = 0;


                            $data = $connexion->prepare(
                                "insert into tags (id_tag,libelle_tag) values (?, ?)"
                            );
                            $data->execute([$id_tag, $tag]);

                            $data = $connexion->prepare(
                                "insert into tagstouits (id_touit, id_tag) values (?, ?)"
                            );
                            $data->execute([$id_touit, $id_tag]);
                        }
                        else {
                            $data = $connexion->prepare(
                                "insert into tagstouits (id_touit, id_tag) values (?, ?)"
                            );
                            $data->execute([$id_touit, $resExist["id_tag"]]);
                        }
                    }


                    $data = $connexion->prepare(
                        "select id_utilisateur from utilisateur where utilisateur = ?"
                    );
                    $data->execute([$nom_uti]);
                    $res = $data->fetch();
                    $id_uti = $res["id_utilisateur"];

                    $data=$connexion->prepare(
                        "insert into touitsutilisateur values (?,?)"
                    );
                    $data->execute([$id_touit, $id_uti]);
                    $html .= (new AfficheListeTouites())->execute();
                } else {
                    $html .= "<h2>Vous ne pouvez pas envoyer un touit vide</h2>";
                    return $html .= (new AfficheListeTouites)->execute();
                }
            }

        }
        else {
            $html.="<h2>Vous devez être connecté pour touiter</h2>";
            $html.=(new signinAction())->execute();
        }
            unset($connexion);
            return $html;
    }

    }
