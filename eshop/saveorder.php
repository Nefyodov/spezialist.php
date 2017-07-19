<?php
	require "inc/lib.inc.php";
	require "inc/config.inc.php";
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	    global $basket;
	    $date = time();
	    $getOrder = array(
	    trim(strip_tags($_POST['name'])),
	    trim(strip_tags($_POST['email'])),
        trim(strip_tags($_POST['phone'])),
	    trim(strip_tags($_POST['address'])),
	    $basket['orderid'],
	    $date);

        $order = implode("|",$getOrder);
        if (file_exists("admin/" . ORDERS_LOG)) {
            tempnam("/admin/", ORDERS_LOG);
        }
        $writeOrderToFile = fopen("admin/" . ORDERS_LOG,"a");
        fputs($writeOrderToFile, "\n" .$order);
        fclose($writeOrderToFile);

        saveOrder($date);
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Сохранение данных заказа</title>
</head>
<body>
	<p>Ваш заказ принят.</p>
	<p><a href="catalog.php">Вернуться в каталог товаров</a></p>
</body>
</html>