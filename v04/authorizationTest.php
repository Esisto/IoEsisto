<?php
require_once('dao/AuthorizationDao.php');


$authodao = new AuthorizationDao();
$prova = $authodao->loadPermit("admin", "read");
var_dump($prova);

echo "<br/><br/>";

$prova = $authodao->load("admin");
var_dump($prova);

echo "<br/><br/>";

$authodao->check("admin");


?>