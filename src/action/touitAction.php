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
                <form action id =touit method ="POST" enctype="multipart/form-data">
                    <input type = "text" id = "touit" name = "touit" placeholder = "votre touit">
                    <input type = "file" id = "image" name = "image" placeholder = "votre image">
                    <button type = "submit">Touiter</button>   
                </form>
            HTML;

        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            //recupérer un fichier
            echo "coucou";
            if (isset($_FILES['image'])) {
                echo "coucou";
                $tmpName = $_FILES['image']['tmp_name'];
                $name = $_FILES['image']['name'];
                $size = $_FILES['image']['size'];
                $error = $_FILES['image']['error'];

                $tabExtension = explode('.', $name);
                $extension = strtolower(end($tabExtension));
                $extensions = ['jpg', 'png', 'jpeg', 'gif', 'mp4'];
                $maxSize = 400000;

                if(in_array($extension, $extensions) && $size <= $maxSize && $error == 0){
                    $uniqueName = uniqid('', true);
                    //uniqid génère quelque chose comme ca : 5f586bf96dcd38.73540086
                    $file = $uniqueName.".".$extension;
                    move_uploaded_file($tmpName, 'upload/'.$file);
                }
                else{
                    echo "Erreur frérot";
                }
            }

            $dataId = $connexion->query("select max(id) as id from touit");
            $res = $dataId->fetch();
            $id = $res['id']+1;

            $data = $connexion->prepare("insert into touit(id, message, dateTouit, rating, image, utilisateur) values (?,?, sysdate(), ?, ?, ?)");
            $message = $_POST["touit"];
            $data->execute(array($id, $message, 0, $file, $_SESSION["login"]));
            $data->closeCursor();
        }
        return $html;
    }
}