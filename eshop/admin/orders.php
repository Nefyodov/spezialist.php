<?php
	require "secure/session.inc.php";
	require "../inc/lib.inc.php";
	require "../inc/config.inc.php";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Поступившие заказы</title>
	<meta charset="utf-8">
</head>
<body>
<h1>Поступившие заказы:</h1>
<?php
$orders = getOrders();
$orderCount = 1;
$orders = array_slice($orders,1);
    foreach ($orders as $order) {
        ?>
        <hr>
        <h2>Заказ номер: <?= $orderCount ?></h2>
        <p><b>Заказчик</b>: <?= $order["name"] ?></p>
        <p><b>Email</b>: <?= $order["email"] ?></p>
        <p><b>Телефон</b>: <?= $order["phone"] ?></p>
        <p><b>Адрес доставки</b>: <?= $order["address"] ?></p>
        <p><b>Дата размещения заказа</b>: <?= date('d-m-Y h:m', $order['date']) ?></p>

        <h3>Купленные товары:</h3>
        <table border="1" cellpadding="5" cellspacing="0" width="90%">
            <tr>
                <th>N п/п</th>
                <th>Название</th>
                <th>Автор</th>
                <th>Год издания</th>
                <th>Цена, руб.</th>
                <th>Количество</th>
            </tr>

            <?php
            foreach ($order['goods'] as $goods){
                $i = 1;
                echo "<tr>";
                    echo "<td>" . $i . "</td>";
                    echo "<td>" . $goods['title'] . "</td>";
                    echo "<td>" . $goods['author'] . "</td>";
                    echo "<td>" . $goods['pubyear'] . "</td>";
                    echo "<td>" . $goods['price'] . "</td>";
                    echo "<td>" . $goods['quantity'] . "</td>";
                echo "</tr>";
                $i++;
                $sum += $goods['price'] * $goods['quantity'];
            }
            ?>
        </table>
        <p>Всего товаров в заказе на сумму: <?= $sum ?> руб.</p>
        <?php
        unset($sum);
        $orderCount++;
    }
?>

</body>
</html>