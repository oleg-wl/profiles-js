
<?php session_start(); ?>
<?php require_once "pdo.php";?>


<?php
$sql_show = 'SELECT * FROM Profile';

$q = $pdo->query($sql_show);
$r=$q->fetchAll(PDO::FETCH_ASSOC);

#for test
#$_SESSION['name'] = 1;
#$_SESSION['user_id'] = 1;
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>eec2f94f</title>    
    <?php require_once 'heads.php'; ?>
</head>
<body>
<?php
// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<?php
// Flash pattern
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}
?>
<div class="container">
  <h1>List of users</h1>
  <li>

    
    <!-- блок с списком users -->
  <?php 
  if (empty($r)) {
    echo('<div class="msg"> No users </div>');
  } else  {
    echo ('<table border="0.5">' . "\n");
    echo ('<td>Name</td><td>Headline</td><td>Action</td></th>');
        foreach ($r as $user) {
            echo "<tr><td>";
            echo ('<a href="view.php?profile_id=' . $user['profile_id'] . '">' .           htmlentities($user['first_name']) . ' ' . htmlentities($user['last_name']) . '</a>');
            echo ("</td><td>");
            echo (htmlentities($user['headline']));
            echo ("</td><td>");
            if (isset($_SESSION['name']) and $_SESSION['user_id'] == $user['user_id']) {
              echo('<a href="edit.php?profile_id=' . $user['profile_id'] . '">Edit</a>');
              echo('<a href="delete.php?profile_id=' . $user['profile_id'] . '"> Delete</a>');
            } else {
              echo(' ');
            }
            echo('</td></tr>');
        }
        echo ('</table>');
    }
  ?>
  <!-- блок проверки логина -->
  <?php if (!isset($_SESSION['name'])) {
    echo('<p><a href="login.php">Please log in</a></p>');
  } elseif(isset($_SESSION['name'])) {




    echo('<p><a href="add.php">Add New Entry</a></p>');
    echo('<p><a href="logout.php">Logout</a></p>');
  } ?>
</div>  


</body>
</html>