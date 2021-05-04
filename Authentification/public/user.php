<?php
require '../vendor/autoload.php';
use App\App;

$user = App::getAuth()->requireRole('user', 'admin');
//dump($user);

?>

Réservé à l'utilisateur