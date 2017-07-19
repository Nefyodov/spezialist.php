<?php
	// подключение библиотек
	require "inc/lib.inc.php";
	require "inc/config.inc.php";

	if (isset($_GET['id'])) {
        $id = $_GET['id'];
        deleteItemFromBasket($id);
        header("Location: basket.php");
    }