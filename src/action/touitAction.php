<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\connection\ConnectionFactory;
use iutnc\touiteur\action\AfficheListeTouites;

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
                <link rel="stylesheet" type="text/css" href="../../CSS/style.css">
                <div class="touite-form">
                    <form action id="touit" method="POST" enctype="multipart/form-data">
                        <input type="text" id="touit" name="touit" placeholder="votre touit">
                        <input type="file" id="image" name="image" placeholder="votre image">
                        <button type="submit">Touiter</button>
                    </form>
                </div>
            HTML;

        }
        else if ($method === "POST") {

            ConnectionFactory::setConfig("config.ini");
            $connexion = ConnectionFactory::makeConnection();

            //recupérer un fichier
            if (isset($_FILES['image'])) {
                var_dump($_FILES['image']);
                $tmpName = $_FILES['image']['tmp_name'];
                $name = $_FILES['image']['name'];
                $size = $_FILES['image']['size'];
                $error = $_FILES['image']['error'];

                $tabExtension = explode('.', $name);
                $extension = strtolower(end($tabExtension));
                $extensions = ['jpg', 'png', 'jpeg', 'gif', 'mp4'];
                $maxSize = 100000000;

                if(in_array($extension, $extensions) && $size <= $maxSize && $error == 0){
                    $uniqueName = uniqid('', true);
                    //uniqid génère quelque chose comme ca : 5f586bf96dcd38.73540086
                    $file = $uniqueName.".".$extension;
                    move_uploaded_file($tmpName, 'upload/'.$file);
                }
            }
            if (!isset($file))
                $file = "null";

            if ($_POST["touit"] != "") {
                $data = $connexion->prepare("insert into touits(message_text, date_Touit, rating, image) values (?, sysdate(), ?, ?)");
                $message = $_POST["touit"];
                var_dump($file);
                $data->execute(array($message, 0, $file));
                $data->closeCursor();
                $html.= (new AfficheListeTouites())->execute();
            }
            else{
                $html.="<p>Vous ne pouvez pas envoyer un touit vide</p>";
                return $html.=$this->execute();
            }


        }
        return $html;
    }
}