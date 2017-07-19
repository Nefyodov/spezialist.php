<?php

define('FILE_NAME', '.htpasswd');

/**
 * Генерируем хеш пароля
 * @param $password
 * @return bool|string
 */
function getHash($password){
    $hash = password_hash($password, PASSWORD_BCRYPT);
    return $hash;
}

/**
 * Проверяем пароль
 * @param $password
 * @param $hash
 * @return bool
 */
function checkHash ($password, $hash){
    return password_verify($password,$hash);
}

/**
 * Создаем новую запись в файле пользователей
 * @param $login
 * @param $hash
 * @return bool
 */
function saveUser ($login, $hash){
    $str = "$login:$hash\n";
    if (file_put_contents(FILE_NAME,$str,FILE_APPEND))
        return true;
    else
        return false;
}

/**
 * Проверяем наличие юзера в списке
 * @param $login
 * @return bool
 */
function userExists ($login) {
    if(!is_file(FILE_NAME))
        return false;
    $users = file(FILE_NAME);
    foreach ($users as $user) {
        if (strpos($user, $login.':')!== false)
            return $user;
        }
        return false;
}

function logOut() {
    session_destroy();
    header('Location: secure/login.php');
    exit();
}