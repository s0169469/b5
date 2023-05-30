<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

include_once 'includes.php';

$columns = array();
$columns[] = 'full_name';
$columns[] = 'email';
$columns[] = 'birth_year';
$columns[] = 'limbs_amount';
$columns[] = 'is_male';
$columns[] = 'biography';
$columns[] = 'powers';


// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');



// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  $is_changing_data = empty($errors) && !empty($_COOKIE[session_name()]) &&
              session_start() && !empty($_SESSION['login']);

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    
    $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
      и паролем <strong>%s</strong> для изменения данных.',
      $_COOKIE['login'],
      $_COOKIE['pass']);
    
  } else if ($is_changing_data){
    $messages[] = '<a href="login.php">ВЫХОД</a>';
    $messages[] = 'Изменение данных:';
  } else {
    $messages[] = '<a href="login.php">ВХОД</a>';
  }

  // Складываем признак ошибок в массив.
  $errors = array();

  foreach ($columns as $column)
    $errors[$column] = !empty($_COOKIE[$column.'_error']);

  // Выдаем сообщения об ошибках.
  foreach ($columns as $column) 
    if ($errors[$column]) {
     // Выводим сообщение.
     $messages[] = '<div class="error">'.$_COOKIE[$column.'_error'].'</div>';
     // Удаляем куку, указывая время устаревания в прошлом.
     setcookie($column.'_error', '', 100000);
    }

  
  $values = array();
  

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {

    try {
      if ($result = $db->query(
        "SELECT * FROM Person WHERE _login='".$_POST['login']."' && password_hash='".password_hash($_POST['pass'], PASSWORD_BCRYPT)."';"
      )){
        $obj = $result->fetchAll()[0];
        foreach ($columns as $column)
          $values[$column] = empty($obj[$column]) ? '' : $obj[$column];
      }
    }
    catch(PDOException $e){
        send_error_and_exit($e->message,"500");
    }

    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  } else {
    // Складываем предыдущие значения полей в массив, если есть.
    // При этом санитизуем все данные для безопасного отображения в браузере.
    foreach ($columns as $column)
      $values[$column] = empty($_COOKIE[$column.'_value']) ? '' : json_decode($_COOKIE[$column.'_value']);
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

  if (empty($_POST['full_name'])) {
    // Выдаем куку на день с флажком об ошибке в поле full_name.
    setcookie('full_name_error', 'Enter your name, please', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    if (empty($_POST['email'])) 
      setcookie('email_error', 'Mail is not set', time() + 24 * 60 * 60);
    else
      setcookie('email_error', 'Mail is invalid', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  if (!isset($_POST['birth_year'])) {
    setcookie('birth_year_error', 'Year is not set', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  if (!isset($_POST['limbs_amount'])) {
    setcookie('limbs_amount_error', 'Limbs number is not set', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  if (!isset($_POST['is_male']) || ($_POST['is_male']!=0 && $_POST['is_male']!=1)) {
    if (!isset($_POST['is_male']))
      setcookie('is_male_error', 'Gender is not set', time() + 24 * 60 * 60);
    else
      setcookie('is_male_error', 'Gender is invalid', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  if (!isset($_POST['powers'])) {
    setcookie('powers_error', 'You have to choose minimum one power', time() + 24 * 60 * 60);
    $errors = TRUE;
  }


  // Сохраняем ранее введенное в форму значение на месяц.
  foreach ($columns as $column)
    setcookie($column.'_value', json_encode($_POST[$column]), time() + 30 * 24 * 60 * 60);
  


  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    foreach ($columns as $column)
      setcookie($column.'_error', '', 100000);
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {

    $stmt = $db->prepare(
      "UPDATE Person ".
      "SET full_name=:full_name, email=:email, birth_year=:birth_year, ".
      "is_male=:is_male, limbs_amount=:limbs_amount, biography=:biography ".
      "WHERE _login='".$_SESSION['login']."';"
      );
    $stmtErr = $stmt -> execute(
          [
          'full_name' => $_POST['full_name'],
          'email' => $_POST['email'] , 
          'birth_year' => $_POST['birth_year'], 
          'is_male' => $_POST['is_male'], 
          'limbs_amount' => $_POST['limbs_amount'], 
          'biography' => $_POST['biography']
          ]
      );
    if (!$stmtErr) 
      send_error_and_exit($stmt->errorInfo()[0].'   '.$stmt->errorInfo()[1].'   '.$stmt->errorInfo()[2],"500");


    $deleted_count = $db->exec(
        "DELETE FROM Person_Ability ".
        "WHERE person_id=".$_SESSION['uid'].";"
    );

    foreach ($_POST['powers'] as $item) {
      $stmt = $db->prepare(
        "INSERT INTO Person_Ability (person_id, ability_id) VALUES (:p, :a);"
      );
      $stmtErr = $stmt->execute(['p' => intval($_SESSION['uid']), 'a' => $item]);
      if (!$stmtErr)
        send_error_and_exit("Problem with giving ability to person","500");
    }
  }
  else {
    // Генерируем уникальный логин и пароль.
    $login = generate_random_string(15);
    $pass = generate_random_string(15);
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);



    // Сохраняем в бд
    try {

      $stmt = $db->prepare(
        "INSERT INTO Person ".
        "(full_name, _login, password_hash, email, birth_year, is_male, limbs_amount, biography) ".
        "VALUES (:full_name, :_login, :password_hash, :email, :birth_year, :is_male, :limbs_amount, :biography);"
        );
      $stmtErr = $stmt -> execute(
            [
            'full_name' => $_POST['full_name'],
            '_login' => $login,
            'password_hash' => my_password_hash($pass),
            'email' => $_POST['email'] , 
            'birth_year' => $_POST['birth_year'], 
            'is_male' => $_POST['is_male'], 
            'limbs_amount' => $_POST['limbs_amount'], 
            'biography' => $_POST['biography']
            ]
        );
      if (!$stmtErr) 
        send_error_and_exit($stmt->errorInfo()[0].'   '.$stmt->errorInfo()[1].'   '.$stmt->errorInfo()[2],"500");
      $strId = $db->lastInsertId();
      
      foreach ($_POST['powers'] as $item) {
        $stmt = $db->prepare(
          "INSERT INTO Person_Ability (person_id, ability_id) VALUES (:p, :a);"
        );
        $stmtErr = $stmt->execute(['p' => intval($strId), 'a' => $item]);
        if (!$stmtErr)
          send_error_and_exit("Problem with giving ability to person","500");
      }
      
    }
    catch(PDOException $e){
        send_error_and_exit($e->message,"500");
    }
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
