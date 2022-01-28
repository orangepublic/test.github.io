<?php
include './function.php';

error_reporting(E_ALL);

if(phpversion()<4.1) error('Версия PHP интерпретатора должна быть 4.1.0 или выше, но никак не ниже (ваша версия интерпретатора: '.phpversion(),'');

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

@session_start();

$template = '';
$errorMsg = '';
$templateFile = '';
$yesAccess = FALSE;

if (!isset($_SESSION['cl_access']) or $_SESSION['cl_access'] <> 1)
{
    if (!isset($_POST['login']) or !isset($_POST['password']))
    {
        $templateFile = './template/login.html';
    }
    elseif ($_POST['login'] == '' or strlen($_POST['login'])>16
            or $_POST['password'] == '' or strlen($_POST['password'])>16)
    {
        $errorMsg = 'Неверный формат данных';
        $templateFile = './template/login.html';
    }
    else
    {
        if (!$yesAccess = login($_POST['login'],$_POST['password']))
        {
            $errorMsg = 'Неверный логин или пароль';
            $templateFile = './template/login.html';
        }
    }

    if (!$yesAccess)
    {
        if (!is_readable($templateFile)) error('Нет прав на чтение шаблона, либо его не существует', $templateFile);
        if (!$template=file_get_contents($templateFile)) error('Файл шаблона пуст',$templateFile);
        $template = str_replace('<!-- error -->', $errorMsg, $template);
        echo $template;
        exit();
    }
    else
    {
        $_SESSION['cl_access'] = 1;
        $_SESSION['cl_login'] = strtolower($_POST['login']);
    }
}

if (!isset($_GET['sec'])) $_GET['sec'] = 'common';

switch ($_GET['sec'])
{
    case 'out':
    {
        session_destroy();
        header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        exit;
    }

    default:
    case 'common':
    {
        if (isset($_GET['action']) && $_GET['action'] == 'clear')
        {
            $flog = fopen('./inc/clicks.dat', 'w');
            fclose($flog);
            header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
            exit;
        }

        if (!is_readable('./template/top.html')) warning('Нет прав на чтение шаблона, либо его не существует', './template/admin/top.html');
        else include './template/top.html';
        echo $top;

        if (!is_readable('./template/common.html')) warning('Нет прав на чтение шаблона, либо его не существует', './template/admin/common.html');
        else include ('./template/common.html');

        if (!is_readable('./inc/clicks.dat')) warning('Не могу прочитать лог-файл [./inc/clicks.dat], либо его не существует');
        else
        {
            if ($fClicks = file('./inc/clicks.dat'))
            {
                $strCnt = count($fClicks);
                for ($i = 0; $i < $strCnt; $i++)
                {
                    $str = explode('::', trim($fClicks[$i]));
                    echo '<TR bgcolor="#779DD6">';
                    echo '<TD><a href="http://'.$_SERVER['HTTP_HOST'].'/'.$str[0].'"><font size="2" color="000000"><b>http://'.$_SERVER['HTTP_HOST'].'/'.$str[0].'</b></font></a></TD>';
                    echo '<TD align="center"><font size="2" color="000000"><b>'.$str[1].'</b></font>';
                    if ($i != ($strCnt-1))echo "</TD></TR>\n";
                }

            }
            else echo '<TR bgcolor="#779DD6"><TD colspan=2 align=center><font size="2" color="000000"><b>Лог-файл пуст</b></font>';
        }
        break;
    }
}

echo '  </TD></TR>
</TABLE>
<table width="640" align="center"><tr><td align="right">
  <font size="2"><a href="admin.php?sec=out"><b>(Закрыть сесcию)</b></a></font>
</td></tr></table>

<center>
  <font face=verdana color="#779DD6" size=1>Yugeon Web Clicks v0.1<br>Design and coding by <a href="http://phpcoder.com.ru" target="_blank">Yugeon</a></font>
</center>
</BODY>
</HTML>';
?>
