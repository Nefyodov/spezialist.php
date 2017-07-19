<?php

define("DB_HOST","localhost");
define("DB_LOGIN","nefyodov");
define("DB_PASSWORD","46225778");
define("DB_NAME","eshop");

define("ORDERS_LOG", "orders.log");

$basket = array();
$count = 0;

/**
 * Подключение к БД
 */
$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
if (!$link) {
    echo 'Ошибка: '
        . mysqli_connect_errno()
        . ':'
        . mysqli_connect_error();
}

basketInit();
