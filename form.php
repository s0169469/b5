<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<div id = "form" style="max-width:800px;  background-color:rgb(230, 141, 206);  margin:auto; margin-bottom:5px; margin-top:5px; padding:10px;">
  <h2>HTML form</h2>
  <?php
    if (!empty($messages)) {
      print('<div id="messages">');
      // Выводим все сообщения.
      foreach ($messages as $message) {
        print($message);
      }
      print('</div>');
    }

    // Далее выводим форму отмечая элементы с ошибками классом error
    // и задавая начальные значения элементов ранее сохраненными.
    //print_r($values);
  ?>
  <form action=""
    method="POST">

    <label>
      1. Your name:<br />
      <input name="fio"
        placeholder="Ivan Ivanov" 
        <?php print($errors['fio'] ? 'class="error"' : '');?> value="<?php print $values['fio'];?>"/>
    </label><br />

    <label >
      2. Your email:<br />
      <input name="email"
        type="email"
        placeholder="user@example.com" 
        <?php print($errors['email'] ? 'class="error"' : '');?> value="<?php print $values['email'];?>"/>
    </label><br />

    <label>
      3. Date of birth:<br />
      <select name="year">
        <?php 
        for ($i = 1922; $i <= 2022; $i++) {
          $selected= ($i == $values['year']) ? 'selected="selected"' : '';
          printf('<option value="%d" %s>%d год</option>', $i, $selected, $i);
        }
        ?>
      </select><br />

      4. Your civility:<br/>
    <label><input type="radio" checked="checked"
      name="gender" value="m" 
      <?php print($errors['gender'] ? 'class="error"' : '');?>
      <?php if ($values['gender']=='m') print 'checked';?>
      />
      male</label>
    <label><input type="radio"
      name="gender" value="f" 
      <?php print($errors['gender'] ? 'class="error"' : '');?>
      <?php if ($values['gender']=='f') print 'checked';?>
      />
      female</label><br />
      5. Number of limbs:<br/>
    <label><input type="radio" name="bodyparts" value="2" 
      <?php print($errors['bodyparts'] ? 'class="error"' : '');?>
      <?php if ($values['bodyparts']=='2') print 'checked';?>
    />
      2</label>
    <label><input type="radio" name="bodyparts" value="3" 
      <?php print($errors['bodyparts'] ? 'class="error"' : '');?>
      <?php if ($values['bodyparts']=='3') print 'checked';?>
    />
      3</label><br />
    <label><input type="radio" name="bodyparts" value="4" 
      <?php print($errors['bodyparts'] ? 'class="error"' : '');?>
      <?php if ($values['bodyparts']=='4') print 'checked';?>
    />
      4</label><br />
    <label><input type="radio" name="bodyparts" value="cannot count" 
      <?php print($errors['bodyparts'] ? 'class="error"' : '');?>
      <?php if ($values['bodyparts']=='cannot count') print 'checked';?>
    />
      cannot count</label><br />
      </label>
      6. Your superpower (pick one or more):
      <br />
      <select name="ability[]"
          multiple="multiple" <?php print($errors['ability'] ? 'class="error"' : '');?>>
          <option value="1" <?php print(in_array('1', $values['ability']) ? 'selected ="selected"' : '');?>>none</option>
          <option value="2" <?php print(in_array('2', $values['ability']) ? 'selected ="selected"' : '');?>>immortality</option>
          <option value="3" <?php print(in_array('3', $values['ability']) ? 'selected ="selected"' : '');?>>invisibility</option>
          <option value="4" <?php print(in_array('4', $values['ability']) ? 'selected ="selected"' : '');?>>levitation</option>
      </select>
      </label><br />
      <label>
      7. Your biography:<br />
        <textarea name="bio" placeholder="your text..."
        <?php print($errors['bio'] ? 'class="error"' : '');?>
        value = "<?php print $values['bio'];?>"
        ></textarea>
        </label><br />
        
      8.<label><input type="checkbox" checked="checked"
        name="check" />
        read and understood</label>

      <input type="submit" value="Send" />
    </form>
  </div>
</body>
