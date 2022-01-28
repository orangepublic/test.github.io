<?php
include './function.php';

if(phpversion()<4.1) error('Версия PHP интерпретатора должна быть 4.1.0 или выше, но никак не ниже (ваша версия интерпретатора: '.phpversion(),'');

if (isset($_GET['uri']))
{
    $uri = $_GET['uri'];

    $str = findStr('./inc/clicks.dat', $uri);
    if ($str['ind'] != -1)
    {
        modifyFileStr('./inc/clicks.dat', $str['ind'], $uri.'::'.($str[1]+1).'::');
    }
    else modifyFileStr('./inc/clicks.dat', -1, $uri.'::1::');
}

header('Location: http://'.$uri);
?>
