<?php
require_once 'pdo.php';
require_once 'utils.php';

session_start();
if (!isset($_SESSION['name'])) {
    die('Not logged in');
} ?>

<?php
#Блок для обновления записи


//DATA VALIDATION
if (isset($_POST["add"])) {

    //validate input
    $msg = validate_input();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    //validate positions
    $msg = validate_pos();
    $msg = validate_edu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }



    $val = $pdo->prepare('INSERT INTO Profile
    (user_id, first_name, last_name, email, headline, summary)
    VALUES ( :uid, :fn, :ln, :em, :he, :su)');

    $val->execute(
        array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        )
    );
    $profile_id = $pdo->lastInsertId();

    //insert new positions utils.php
    insertPositions($pdo, $profile_id);
    //insert new edu utils.php
    insertEdu($pdo, $profile_id);


    $_SESSION['success'] = "added";
    header('Location:index.php');
    return;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}
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
    <div class="container">

        <?php flash_msg() ?>

        <h1>Adding Profile for UMSI</h1>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" />
            </p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" />
            </p>
            <p>Email:
                <input type="text" name="email" size="30" />
            </p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" />
            </p>
            <p>Summary:<br />
                <textarea name="summary" rows="8" cols="80">
</textarea>
            <p>Education: <input type="submit" id="addEdu" , value="+">
            <div id=edu_fields>

            </div>
            <p>Position: <input type="submit" id="addPos" , value="+">
            <div id="position_fields">

            </div>
            </p>
            <p>
                <input type="submit" value="Add" name='add'>
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>
</body>
<script type="text/javascript">
    countPos = 0;
    countEdu = 0;

    $(document).ready(function() {
        window.console && console.log('Document readu called');
        $('#addPos').click(function(event) {
            event.preventDefault();
            if (countPos >= 9) {
                alert('Max positions');
                return;
            }
            countPos++
            console.log('Adding pos N' + countPos);
            $('#position_fields').append(
                '<div id="position' + countPos + '"> \
                <p> Year: <input type="text" name="year' + countPos + '" value=""  /> \
                <input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
                <textarea name="desc' + countPos + '" rows="8" clos="80"></textarea></div>'
            );
        });

        $('#addEdu').click(function(event) {
            event.preventDefault();
            if (countEdu >= 9) {
                alert('Max Education');
                return;
            }
            countEdu++
            console.log('Adding education N' + countEdu);
            $('#edu_fields').append(
                '<div id="edu' + countEdu + '"> \
            <p> Year: <input type="text" name="edu_year' + countEdu + '" value=""  /> \
            <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"></p> \
            <textarea name="edu_school' + countEdu + '" rows="8" clos="80" class="school"></textarea></div>'
            );
            $('.school').autocomplete({
            source: "school.php"
        });
        })
    })
</script>
</html>