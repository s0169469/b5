<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  session_destroy();
  // Делаем перенаправление на форму.
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<style>
  .login{
    font-family: "Montserrat", sans-serif;
    max-width: 960px;
    text-align: center;
    margin: 0 auto;
    padding: 40px;
    width: 250px;
    background-color: rgb(253, 197, 123);
    border: 2px solid black;
  }
</style>
<div class = "login">
  <form action="login.php" method="post">
  <div class = "main">
    <input name="login" />Логин<br>
    <input name="pass" />Пароль<br>
    <input type="submit" value="Войти" />
    </div>
  </form>
</div>
<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
  $login=$_POST['login'];
  $pswrd=$_POST['pass'];
  $uid=0;
  $error=TRUE;
  $user = 'u52991';
  $pass = '4039190';
  $db1 = new PDO('mysql:host=localhost;dbname=u52991', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  if(!empty($login) and !empty($pswrd)){
    try{
      $chk=$db1->prepare("SELECT * FROM user WHERE login=?");
      $chk->bindParam(1,$login);
      $chk->execute();
      $username=$chk->fetchALL();
	  print($username[0]['pass']);
      if(password_verify($pswrd,$username[0]['pass'])){
        $uid=$username[0]['id'];
        $error=FALSE;
      }
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
  }
  if($error==TRUE){
    print('Неправильные логин или пароль? <br> Создайте нового <a href="index.php">пользователя</a> или <a href="login.php">попробовать войти снова</a> ');
    session_destroy();
    exit();
  }
  // Если все ок, то авторизуем пользователя.
  $_SESSION['login'] = $login;
  // Записываем ID пользователя.
  $_SESSION['uid'] = $uid;
  // Делаем перенаправление.
  header('Location: index.php');
}
