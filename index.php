<?php
declare(strict_types=1);

namespace iutnc\touiteur\Touiteur;
use iutnc\touiteur\Dispatcheur;

require_once 'vendor/autoload.php';

(new Dispatcheur())->run();