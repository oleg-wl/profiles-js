
<?php 
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}
?>

<?php

//Проверка нажатия кнопки делит
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing id";
  header('Location: index.php');
  return;
}
?>

<?php
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :id and user_id = :ui");
$stmt->execute(array(":id" => $_GET['profile_id'], ":ui" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
?>
<html>
<title>eec2f94f</title>    
<p>Confirm: Deleting <?= htmlentities($row['first_name']) ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">

<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form></html>
