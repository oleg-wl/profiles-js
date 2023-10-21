<?php session_start(); ?>
<?php require_once "pdo.php"; ?>



<?php
#Data validation
if (isset($_POST['login'])) {

    if (empty($_POST['email']) or empty($_POST['pass'])) {
        $_SESSION['error'] = 'User name and password are required';
        header('Location: login.php');
        return;

    } elseif (!empty($_POST['email']) && strpos($_POST['email'], '@') == False) {
        $_SESSION['error'] = 'Email must have an at-sign (@)';
        header('Location: login.php');

        error_log("Login fail " . $_POST['email'] . $pass . "\n", message_type: 3, destination: "log.txt");
        return;

    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        
        $check_user = 'SELECT user_id, name FROM users WHERE email = :em AND password = :pass';
        #Проверка наличия юзера в таблице
        $user = $pdo->prepare($check_user);
        $user->execute(
            array(
                ':em' => $_POST['email'],
                ':pass' => $check));
        $data = $user->fetch(PDO::FETCH_ASSOC);
        
        #если пусто
        if ($data === False) {
            $_SESSION['error'] = 'Incorrect login';
            header('Location: login.php');
            return;
        } else {
            $_SESSION['name'] = $data['name'];
            $_SESSION['user_id'] = $data['user_id'];
            $_SESSION['success'] = "Login success";
            header("Location: index.php");
            error_log("Login success " . $_POST['email'] . "\n", message_type: 3, destination: "log.txt");
            return;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>eec2f94f</title>    
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Please Log In</h1>
            <?php
            #Print error message
            if ( isset($_SESSION['error']) ) {
                echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
                unset($_SESSION['error']);
            }
            ?>
        <form method="POST" action="login.php">
            <label for="email">Email</label>
            <input type="text" name="email" id="email"><br />
            <label for="pass">Password</label>
            <input type="password" name="pass" id="pass"><br />
            <input type="submit" name="login" onclick="return doValidate();" value="Log In">
            <input type="submit" name="cancel" value="Cancel">
        </form>
</body>

</html>

<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('pass').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>