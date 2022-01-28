<?php
include './function.php';

$view = 0;

if (isset($_GET['uri']))
{
    $uri = $_GET['uri'];
    $str = findStr('./inc/clicks.dat', $uri);
    if ($str['ind'] != -1) $view = $str[1];
}

echo 'document.write("'.$view.'");';
exit;
?>