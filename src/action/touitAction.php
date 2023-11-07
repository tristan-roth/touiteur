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
            if (isset($_FILES['file'])) {
                $tmpName = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $size = $_FILES['file']['size'];
                $error = $_FILES['file']['error'];

                $tabExtension = explode('.', $name);
                $extension = strtolower(end($tabExtension));
                $extensions = ['jpg', 'png', 'jpeg', 'gif', 'mp4'];
                $maxSize = 400000;

                if(in_array($extension, $extensions) && $size <= $maxSize && $error == 0){
                    $uniqueName = uniqid('', true);
                    //uniqid génère quelque chose comme ca : 5f586bf96dcd38.73540086
                    $file = $uniqueName.".".$extension;
                    //move_uploaded_file($tmpName, 'upload/'.$file);
                }
                else{
                    echo "Erreur frérot";
                }
            }

            $dataId = $connexion->query("select max(id)+1 from touit");
            $id = $dataId->fetch();

            $data = $connexion->prepare("insert into touit(id, message, dateTouit, rating, image, utilisateur) values (?,?, ?, ?, ?, ?)");
            $message = $_POST["touit"];
            $data->execute(array($id, $message, sydate(), 0, $file, $_SESSION["login"]));
        }
        return $html;
    }
}