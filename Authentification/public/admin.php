<?php
require '../vendor/autoload.php';
use App\App;

$user = App::getAuth()->requireRole('admin');
//dump($user);

?>

Réservé à l'admin