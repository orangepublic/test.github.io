<?php
function error($error,$file){die('<font face="verdana" size="1" color="#de0000"><b>Ошибка: '.$error.'<br>['.$file.']</b></font><br>');}

function warning($error,$file){echo('<font face="verdana" size="1" color="#de0000"><b> ПРЕДУПРЕЖДЕНИЕ: '.$error.'<br>['.$file.']</b></font><br>');}

function modifyFileStr($file, $index, $newStr)
{
    if ($newStr != '')
    {
        $newStr .= "\r\n";

        $fileBase = array();

        if (is_writable($file)) $fileBase = file($file);

        if (!$fBase = fopen($file,'w'))
            return warning('Не могу открыть или создать файл. Проверьте права доступа.',$file);

        $cnt = count($fileBase);
        if (($index < 0) or ($index >= $cnt))
        {
            $cnt = array_push($fileBase, $newStr);
        }
        else array_splice($fileBase, $index, 1, $newStr);

        fwrite($fBase, ltrim(implode('', $fileBase)));
        fclose($fBase);
    }

    return TRUE;
}

function findStr($file, $str)
{
    $fileSubStr = array();

    if (!is_readable($file)) return -1;

    $fileBase = file($file);
    $cnt = count($fileBase);

    for ($i = 0; $i < $cnt; $i++)
    {
        $fileSubStr = explode('::', $fileBase[$i]);

        if ($fileSubStr[0] == $str)
        {
            $fileSubStr['ind'] = $i;
            return $fileSubStr;
        }

    }
    $fileSubStr['ind'] = -1;
    return $fileSubStr;
}


//Аутентификация
function login($login,$password)
{
    $login = strtolower($login);
    $password = md5($password);

      if (!file_exists('./inc/login.dat'))
      {
            if (!modifyFileStr(
                            './inc/login.dat',
                            -1,
                            $login.'::'.$password.'::')
               )
            {
                return warning('Не удалось создать суперадмина','');
            }
            else return TRUE;
      }
      elseif (!is_readable('./inc/login.dat')) return warning('Нет прав прочитать базу админов','./inc/login.dat');
      elseif ( filesize('./inc/login.dat') <= 2 )
      {
            if(!modifyFileStr(
                            './inc/login.dat',
                            -1,
                            $login.'::'.$password.'::')
              )
            {
                  return warning('Не удалось создать суперадмина','');
            } else  return TRUE;
      }
      else
      {
            $fLoginBase = file('./inc/login.dat');
            $cnt = count($fLoginBase);
            for($i=0;$i<$cnt;$i++)
            {
                  //имя::хеш_пароля::
                  $tmp = explode('::',$fLoginBase[$i]);
                  if ($login == strtolower($tmp[0]) && $password == $tmp[1])
                  {
                      return TRUE;//успех
                  }
            }
            return FALSE;
      }

      return FALSE; //denied
}

?>
