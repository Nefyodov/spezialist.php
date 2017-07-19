<?php

/**
 * Добавление записей в БД, необходимо дополнительно забирать данные в аргументы из $_POST
 * @param $title Название книги
 * @param $author Автор книги
 * @param $pubyear Год издания книги
 * @param $price Цена книги
 * @return bool
 */
function addItemToCatalog($title, $author, $pubyear, $price) {
    $sql = 'INSERT INTO catalog (title, author, pubyear, price)
            VALUE (?,?,?,?)';
    global $link;

    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "ssii", $title, $author, $pubyear, $price);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
}

/**
 * Вывод содержимого таблицы БД в виде ассоциативного массива
 * @return array|bool|null
 */
function selectAllItems(){
    $sql = 'SELECT id, title, author, pubyear, price FROM catalog';
    global $link;

    if(!$result = mysqli_query($link,$sql))
        return false;
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $items;

}

/**
 * Сохраняет корзину с товарами в куки
 */
function saveBasket(){
    global $basket;
    $basket = base64_encode(serialize($basket));
    setcookie('basket', $basket, 0x7FFFFFFF);
}

/**
 * Создает либо загружает в перемененную $basket корзину с товарами,
 * либо создает новую корзину с идентификатором заказа
 */
function basketInit(){
    global $basket, $count;
    if (!isset($_COOKIE['basket'])){
        $basket = ['orderid' => uniqid()];
        saveBasket();
    } else {
        $basket = unserialize(base64_decode($_COOKIE['basket']));
        $count = count($basket) - 1;
    }
}

/**
 * Сохранение товара в корзину
 * @param $id id товара, берется из БД, в корзину ложится через $_GET
 */
function add2Basket($id){
    global $basket;
    $basket[$id] = 1;
    saveBasket();
}

/**
 * Возвращает всю пользовательскую корзину в виде ассоциативного массива
 * @return bool
 */
function myBasket(){
    global $link, $basket;
    $goods = array_keys($basket);
    array_shift($goods);
    if (!$goods)
        return false;
    $ids = implode(",", $goods);
    $sql =  "SELECT id, author, title, pubyear, price FROM catalog WHERE id IN ($ids)";

    if (!$result = mysqli_query($link, $sql))
        return false;
    $items = result2Array($result);
    mysqli_free_result($result);
    return $items;
}

/**
 * Возвращает ассоциативный массив товаров, дополненный их количеством
 * @param $data
 * @return array
 */
function result2Array($data){
    global $basket;
    $arr = [];
    while ($row = mysqli_fetch_assoc($data)){
        $row['quantity'] = $basket[$row['id']];
        $arr[] = $row;
    }
    return $arr;
}

/**
 * Удаляет товар из корзины по id товара
 * @param $id уникальный идентификатор товара
 */
function deleteItemFromBasket($id){
    global $basket;
    unset($basket[$id]);
    saveBasket();
}

/**
 * Выводит содержание массива и прекращает выполнение скрипта
 * @param $var массив для просмотра
 */
function debug($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
}

/**
 * Сохраняет данные о товаре в заказе в БД таблицу orders
 * данные о клиенте сохраняются в /admin/orders.log
 * @param $datetime метка времени формирования заказа
 * @return bool
 */
function saveOrder($datetime){
    global $link, $basket;
    $goods = myBasket();
    $stmt = mysqli_stmt_init($link);
    $sql = 'INSERT INTO orders(
                          title,
                          author,
                          pubyear,
                          price,
                          quantity,
                          orderid,
                          datetime)
                    VALUES (?,?,?,?,?,?,?)';
    if  (!mysqli_stmt_prepare($stmt, $sql))
        return false;
    foreach ($goods as $item) {
        mysqli_stmt_bind_param($stmt, "ssiiisi",
            $item['title'], $item['author'],
            $item['pubyear'], $item['price'],
            $item['quantity'], $basket['orderid'],
            $datetime);
    mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    setcookie('basket', "", time()-3600);
    return true;
}

/**
 * Возвращает многомерный массив с информацией о всех заказах,
 * включая персональные данные пользователя и список его товаров
 */
function getOrders() {
    global $link;
    if(!is_file(ORDERS_LOG))
        return false;
    /*Получаем в виде массива персональные данные пользователя из файла */
    $orders = file(ORDERS_LOG);
    /*Массив, который будет возвращен функцией */
    $allorders = [];
    foreach ($orders as $order) {
        list($name, $email, $phone, $address, $orderid, $date) = explode("|", $order);
        /*Промежуточный массив для хранения информации о конкретном заказе */
        $orderinfo = [];
        /*Сохранение информации о конкретном пользователе*/
        $orderinfo['name'] = $name;
        $orderinfo['email'] = $email;
        $orderinfo['phone'] = $phone;
        $orderinfo['address'] = $address;
        $orderinfo['orderid'] = $orderid;
        $orderinfo['date'] = $date;
        /*SQL-запрос на выборку из таблицы orders всех товаров для конкретного покупателя*/
        $sql = "SELECT title, author, pubyear, price, quantity
                FROM orders
                WHERE orderid = '$orderid' AND datetime = '$date'";
        /*Получение результатов выборки */
        if (!$result = mysqli_query($link, $sql))
            return false;
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        /*Сохранение результатов в промежуточном массиве*/
        $orderinfo['goods'] = $items;
        /*Добавление промежуточного массива в возвращаемый массив*/
        $allorders[] = $orderinfo;
    }
    return $allorders;
}