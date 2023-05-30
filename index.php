<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Если есть параметр save, то выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['year'] = !empty($_COOKIE['year_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['bodyparts'] = !empty($_COOKIE['bodyparts_error']);
  $errors['ability'] = !empty($_COOKIE['ability_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);

  // Выдаем сообщения об ошибках.
  if ($errors['fio']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('fio_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя.</div>';
  }
  if ($errors['email']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('email_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните email.</div>';
  }
  if ($errors['year']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('year_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните год.</div>';
  }
  if ($errors['gender']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('gender_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните пол.</div>';
  }
  if ($errors['bodyparts']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('bodyparts_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните кол-во конечностей.</div>';
  }
  if ($errors['ability']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('ability_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните суперспособность.</div>';
  }
  if ($errors['bio']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('bio_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните биографию.</div>';
  }

  // Складываем предыдущие значения полей в массив, если есть.
  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['year'] = empty($_COOKIE['year_value']) ? '' : strip_tags($_COOKIE['year_value']);
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
  $values['bodyparts'] = empty($_COOKIE['bodyparts_value']) ? '' : strip_tags($_COOKIE['bodyparts_value']);
  $values['ability'] = empty($_COOKIE['ability_value']) ? array() : json_decode($_COOKIE['ability_value']);
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
        $user = 'u52991';
        $pass = '4039190';
        $db = new PDO('mysql:host=localhost;dbname=u52991', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        try{ 
            $stmt=$db->prepare("SELECT id  FROM user WHERE login=?");
            $stmt->bindParam(1,$_SESSION['login']);
            $stmt->execute();
            $arr=$stmt->fetchAll();

            $stmt=$db->prepare("SELECT * FROM application WHERE user_id=?");
            $stmt->bindParam(1,$arr[0]['id']);
            $stmt->execute();
            $arr1=$stmt->fetchALL();
            $values['fio']=$arr1[0]['name'];
            $values['email']=$arr1[0]['email'];
            $values['year']=$arr1[0]['year'];
            $values['gender']=$arr1[0]['gender'];
            $values['bodyparts']=$arr1[0]['bodyparts'];
            $values['bio']=$arr1[0]['bio'];

            $stmt=$db->prepare("SELECT id FROM application WHERE user_id=?");
            $stmt->bindParam(1,$arr[0]['id']);
            $stmt->execute();
            $arr3=$stmt->fetchAll();

            $stmt=$db->prepare("SELECT ability_id FROM ability_application WHERE application_id=?");
            $stmt->bindParam(1,$arr3[0]['id']);
            $stmt->json_encode(execute());
            $values['ability']=$stmt->json_decode();
        }
        catch(PDOException $e){
          print('Error: '.$e->getMessage());
          exit();
          
        }
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['fio'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('fio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
  }

  if (empty($_POST['email']) || !preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9-]+.+.[A-Z]{2,4}$/i', $_POST['email'])) {
    // Выдаем куку на день с флажком об ошибке в поле email.
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }

  if (empty($_POST['year']) || !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
    // Выдаем куку на день с флажком об ошибке в поле year.
    setcookie('year_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
  }

  if (empty($_POST['gender']) || ($_POST['gender']!='m' && $_POST['gender']!='f')) {
    // Выдаем куку на день с флажком об ошибке в поле gender.
    setcookie('gender_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);
  }
  if (empty($_POST['bodyparts']) || ($_POST['bodyparts']!='2' && $_POST['bodyparts']!='3' && $_POST['bodyparts']!='4' && $_POST['bodyparts']!='cannot count')) {
    // Выдаем куку на день с флажком об ошибке в поле bodyparts.
    setcookie('bodyparts_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('bodyparts_value', $_POST['bodyparts'], time() + 30 * 24 * 60 * 60);
  }

  foreach ($_POST['ability'] as $ability) {
    if (!is_numeric($ability) || !in_array($ability, [1, 2, 3, 4])) {
      setcookie('ability_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
      break;
    }
  }
  if (!empty($_POST['ability'])) {
    setcookie('ability_value', json_encode($_POST['ability']), time() + 24 * 60 * 60);
  }

  if (empty($_POST['bio']) || !preg_match('/^[0-9A-Za-z0-9А-Яа-я,\.\s]+$/', $_POST['bio'])) {
      // Выдаем куку на день с флажком об ошибке в поле bio.
      setcookie('bio_error', '1', time() + 24 * 60 * 60);
      $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
  }

  if ($errors) {
    // При наличии ошибок завершаем работу скрипта.
    header('Location:index.php');
    exit();
  }
  else{
    setcookie('fio_error', '', 1000000);
    setcookie('year_error', '', 1000000);
    setcookie('email_error', '', 1000000);
    setcookie('gender_error', '', 1000000);
    setcookie('bodyparts_error', '', 1000000);
    setcookie('ability_error', '', 1000000);
    setcookie('bio_error', '', 1000000);
  }

  // Сохранение в базу данных.
  $user = 'u51489';
	$pass = '7565858';	
  $db = new PDO('mysql:host=localhost;dbname=u51489', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  
  if (!empty($_COOKIE[session_name()]) &&
  session_start() && !empty($_SESSION['login'])) {
    $id=$_SESSION['uid'];
    $upd=$db->prepare("UPDATE application SET name = ?, email = ?, year = ?, gender = ?, bodyparts = ?, biography = ? WHERE user_id=$id");
    $upd->execute([$_POST['fio'], $_POST['email'], $_POST['year'], $_POST['gender'], $_POST['bodyparts'], $_POST['bio']]);
    
    $stmt=$db->prepare("SELECT id FROM application WHERE user_id=$id");
    $stmt->execute();
    $app_id=$stmt->fetchAll();

    $del=$db->prepare("DELETE FROM ability_application WHERE application_id=?");
    $del->bindParam(1,$app_id[0]['id']);
    $del->execute();

    $stmt = $db->prepare("INSERT INTO ability_application SET ability_id= ?, application_id=?");
    foreach ($_POST['ability'] as $ability) {
      $stmt->execute([$ability, $app_id[0]['id']]);
    }
  }
  else{
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
    $login = 'u'.substr(uniqid(),-5);
    $pass = substr(md5(uniqid()),0,10);
    $pass_hash=password_hash($pass,PASSWORD_DEFAULT);
    setcookie('login', $login);
    setcookie('pass', $pass);
    try {
      $stmt=$db->prepare("INSERT INTO user SET login=?,pass=?");
      $stmt->bindParam(1,$login);
      $stmt->bindParam(2,$pass_hash);
      $stmt->execute();
      $user_id=$db->lastInsertId();

      $stmt = $db->prepare("INSERT INTO application SET name = ?, email = ?, year = ?, gender = ?, bodyparts = ?, biography = ?");
      $stmt->execute([$_POST['fio'], $_POST['email'], $_POST['year'], $_POST['gender'], $_POST['bodyparts'], $_POST['bio']]);
      
      $app_id = $db->lastInsertId();
      
      $stmt = $db->prepare("INSERT INTO ability_application SET application_id = ?, ability_id=?");
      foreach ($_POST['ability'] as $ability) {
        $stmt->execute([$app_id, $ability]);
      }
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
  }
  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
