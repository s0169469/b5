<html>
  <head>
    <style>
    /* Сообщения об ошибках и поля с ошибками выводим с красным бордюром. */
    .error {
      border: 2px solid red;
    }

    td{
      margin: 10px;
      border: 2px solid black;
    }
    </style>
  </head>
  <body>

<?php

  include_once 'includes.php';

  if (!empty($messages)) {
    print('<div id="messages">');
    // Выводим все сообщения.
    foreach ($messages as $message) {
      print($message.'<br>');
    }
    print('</div>');
  }

  print '<br>';

  if (isset($_SESSION['is_error'])){
    if ($_SESSION['is_error'] == 1){
      print ("<div class='error'>");
      print ($_SESSION['error_message']);
      print ("</div>");
    }
  }

  print '<br>';

// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>

<form action="" method="POST">
              <label >
                Имя:<br>
                <input 
                  <?php if ($errors['full_name']) {print 'class="error"';} ?>
                  name="full_name"
                  value="<?php print $values['full_name']; ?>"
                  placeholder="Имя" required>
              </label><br>
        
              <label>
                E-mail:<br>
                <input 
                  <?php if ($errors['email']) {print 'class="error"';} ?>
                  name="email"
                  type="email"
                  value="<?php print $values['email']; ?>"
                  placeholder="e-mail" required>
              </label><br>
        
              <label>
                Год рождения:<br>
                <select 
                  <?php if ($errors['birth_year']) {print 'class="error"';} ?>
                  name="birth_year">
                    <?php 
                      for ($i = 1923; $i <= 2023; $i++) {
                        if ($values['birth_year'] == $i)
                          printf('<option value="%d" selected="selected">%d год</option>', $i, $i);
                        else
                          printf('<option value="%d">%d год</option>', $i, $i);
                      }
                    ?>
                  </select>
              </label><br>
              
              <div <?php if ($errors['is_male']) {print 'class="error"';} ?>>
                Пол: <br>
                <label><input type="radio"
                  name="is_male" value="1" <?php if ($values['is_male'] == 1) print "checked"; ?> required>
                  Мужской</label>
                <label><input type="radio"
                  name="is_male" value="0" <?php if ($values['is_male'] == 0) print "checked"; ?> required>
                  Женский</label><br>
              </div>

              <div <?php if ($errors['limbs_amount']) {print 'class="error"';} ?>>
                Количество конечностей: <br>

                <?php 
                  for ($i = 1; $i <= 4; $i++) {
                    if ($i == $values['limbs_amount'])
                      print '<label><input type="radio" name="limbs_amount" value="'.$i.'"'.' required checked>'.$i.'</label>';
                    else
                      print '<label><input type="radio" name="limbs_amount" value="'.$i.'"'.' required>'.$i.'</label>';
                  }
                ?>
              </div>


            <label>
                Суперсилы:
                <br>
                <select
                  <?php if ($errors['powers']) {print 'class="error"';} ?>
                  name="powers[]"
                  multiple="multiple">
                  <?php
                    try {
                      foreach ($db->query("SELECT * FROM Ability;") as $row){
                        if (isset($values['powers'])){
                          if (in_array($row['id'], $values['powers'])) // if contains - then selected
                            print '<option value="'.intval($row['id']).'" selected>'.$row['_name'].'</option>';
                          else
                            print '<option value="'.intval($row['id']).'">'.$row['_name'].'</option>';
                        } else {
                          print '<option value="'.intval($row['id']).'">'.$row['_name'].'</option>';
                        }
                      }
                    } catch(PDOException $e){
                      send_error_and_exit("Db connection error", "500");
                    }
                  ?>
                </select>
              </label><br>
        
              <label >
                Биография:
                <br>
                <textarea 
                  <?php if ($errors['biography']) {print 'class="error"';} ?>
                  name="biography"><?php print trim($values['biography']) ?></textarea>
              </label><br>



              <?php
                if (!$is_changing_data) {
              ?>
                    Согласие c лицензионным соглашением:<br>
                    <label><input type="checkbox"
                      name="check" required>
                      Да</label><br>
              <?php
                }
              ?>

              <input type="submit" value="Отправить">
            </form>

            <table>
            <?php
                   try {
                      foreach ($db->query("SELECT * FROM Person;") as $person){
                        $abilities = '';
                        foreach ($db->query('SELECT * FROM Person_Ability WHERE person_id='.intval($person['id']).';') as $pa){
                          foreach ($db->query('SELECT _name FROM Ability WHERE id='.intval($pa['ability_id']).';') as $a){
                            $abilities = $abilities.$a['_name'].', ';
                          }
                        }
                        print '<tr><td>'.$person['full_name'].'</td><td>'.$person['email'].'</td><td>'.$person['birth_year'].'</td><td>'.$person['is_male'].'</td><td>'.$person['limbs_amount'].'</td><td>'.$person['biography'].'</td><td>'.$abilities.'</td></tr>';
                      }
                    } catch(PDOException $e){
                      send_error_and_exit("Db connection error", "500");
                    }
             ?>
            </table>
  </body>
</html>
