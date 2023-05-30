<?php

function send_error_and_exit($error_message, $error_code="400"){
    header("HTTP/1.1 " . $error_code . " " . $error_message);
    exit();
}

function generate_random_string($length) {
    $symbols = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $symbols_amount = strlen($symbols);
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $symbols[random_int(0, $symbols_amount - 1)];
    }
    return $str;
}

function my_password_hash($pass){
    return password_hash($pass, PASSWORD_BCRYPT);
}

function my_verify_password($pass, $hash){
    return password_verify($pass, $hash);
}

$user = 'u51489';
$pass = '7565858';
$db = new PDO('mysql:host=localhost;dbname='.$user, $user, $pass, [PDO::ATTR_PERSISTENT => true]);

?>
