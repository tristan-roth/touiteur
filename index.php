<?php
declare(strict_types=1);
namespace iutnc\touiteur\Touiteur;
require_once 'vendor/autoload.php';
use iutnc\touiteur\Dispatcheur;
use iutnc\touiteur\connection\ConnectionFactory;
ConnectionFactory::setConfig("config.ini");
(new Dispatcheur())->run();