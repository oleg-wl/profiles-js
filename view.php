<?php
session_start();
require_once 'pdo.php';
require_once 'utils.php'
?>

<?php
// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

//Общие значения из таблицы Profiles
$val = $pdo->prepare('SELECT first_name, last_name, email, headline, summary FROM Profile WHERE profile_id = :id');
$val->execute(array(':id' => $_GET['profile_id']));
$r = $val->fetch(PDO::FETCH_ASSOC);

$pid = $_GET['profile_id'];


//Значения из таблицы Positions
$val2 = $pdo->prepare('SELECT description, year FROM Position WHERE profile_id = :pid ORDER BY year DESC');
$val2->execute(array(
  ':pid'=>$pid
));
$pos = $val2->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once 'heads.php' ?>
<title>Abrosimov Oleg Vladimirovich</title>    
</head>
<body>
<div class="container">
<h1>Profile information</h1>
<p>First Name: <?= htmlentities($r['first_name'])?></p>
<p>Last Name:  <?= htmlentities($r['last_name'])?></p>
<p>Email:      <?= htmlentities($r['email'])?></p>
<p>Headline:   <?= htmlentities($r['headline'])?></p>
<p>Summary:      <?= htmlentities($r['summary'])?><p>
</p>
</p>
<div id="schoolview"></div>
<div id="positions">
  <?php 
  if ($pos !== false) {
    echo('<p>Position</p><ul>');
    foreach($pos as $v){
      echo('<li>'.htmlentities($v['description']).':'.htmlentities($v['year']).'</li>');

    }
  }
?>
</div>
<a href="index.php">Done</a>
<a href="schoolview.php">SChools</a>
</div>


</body>
<?php 
//Инлайн PHP скрипт для передачи переменной pid в js
echo('<script> var pid = '.$pid.'</script>');?>

<script type="text/javascript">
  $(document).ready(function(){
    $.getJSON('schoolview.php', {profile_id:pid}, function(data){
      if (data.length > 0) {
        console.log(data);
        console.log(data[0].name);
        $("#schoolview").append('<p>Education:</p><ul>')
        data.forEach(function(elemen){
          console.log(elemen.name);
          $("#schoolview").append('<li> Name: '+elemen.name+' Year: '+elemen.year+"</li>");
        })
        $('#schoolview').append('</ul>')
      }})

    })

</script
</html>