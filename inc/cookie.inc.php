<?php
/* Проверяем кол-во посещений из кукис, выводим счетчик и дату последнего посещения*/
    $visitCounter = 0;
    if (isset($_COOKIE["visitCounter"]))
        $visitCounter = $_COOKIE["visitCounter"];
    $visitCounter++;
    $lastVisit = "";
    if (isset($_COOKIE["lastVisit"]))
        $lastVisit = date("d-m-Y H-i-s", $_COOKIE["lastVisit"]);
    //
    if (date ("d-m-Y", $_COOKIE['lastVisit']) != date ("d-m-Y"))
    {
        setcookie("visitCounter", $visitCounter, 0x7FFFFFFF);
        setcookie("lastVisit", time(), 0x7FFFFFFF);
    }
