<?php
session_start();
require_once 'pdo.php';
require_once 'utils.php';
if (!isset($_SESSION['name'])) {
    die('Not logged in');
} ?>


<?php
$t = $_GET['profile_id'];
echo ($t);

//Проверить что юзер совпадает с тем, что он правит
$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid');
$stmt->execute(array(':pid' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}
//Загрузить данные чтобы показать в форме values
$vals = $pdo->prepare("SELECT profile_id, first_name, last_name, email, headline, summary FROM Profile WHERE profile_id = :id");
$vals->execute(array(":id" => $_REQUEST['profile_id']));
$row = $vals->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$pid = htmlentities($row['profile_id']);


$positions = loadPos($pdo, $_GET['profile_id']);
var_dump($positions);
$edus = loadEdu($pdo, $_GET['profile_id']);


// Editing data
if (isset($_POST["edit"])) {

    //VAL input
    $msg = validate_input();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }
    //validate positions and edu
    $msg = validate_pos();
    $msg = validate_edu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
        return;
    }

    //Upating заменяем данные
    $sql = 'UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hm, summary = :sm WHERE profile_id = :pid';
    $edit = $pdo->prepare($sql);
    $edit->execute(
        array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':hm' => $_POST['headline'],
            ':sm' => $_POST['summary'],
            ':pid' => $_POST['profile_id']
        )
    );

    //Delete all positions and insert new
    $sql = 'DELETE FROM Position WHERE profile_id = :pid';
    $del = $pdo->prepare($sql);
    $del->execute(array(':pid' => $_POST['profile_id']));

    //Insert new Positions
    insertPositions($pdo, $_POST['profile_id']);

    //Аналогично с таблицей Education
    $sql = 'DELETE FROM Education WHERE profile_id = :pid';
    $del = $pdo->prepare($sql);
    $del->execute(array(':pid' => $_POST['profile_id']));

    insertEdu($pdo, $_POST['profile_id']);

    $_SESSION['success'] = "Record edited";
    header('Location:index.php');
    return;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eec2f94f</title>
    <?php require_once 'heads.php' ?>
</head>

<body>

    <?php
    // Flash pattern
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
        unset($_SESSION['error']);
    }
    ?>
    <div class="container">
        <h1>Editing Profile for UMSI</h1>
        <form method="post" action="edit.php">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= $fn ?>" />
            </p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= $ln ?>" />
            </p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= $em ?>" />
            </p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" value="<?= $hl ?>" />
            </p>
            <p>Summary:<br />
                <textarea name="summary" rows="8" cols="80"><?= $sm ?></textarea>
            <p>

            <p>Education: <input type="submit" id="addEdu" , value="+">
            <div id=edu_fields>

                <?php
                if (!empty($edus)) {
                    for ($e = 0; $e < count($edus); $e++) {
                        echo ('<div id="edu' . $e . '">');
                        echo ('<p>Year: <input type="text" name="edu_year' . $e . '" value="' . $edus[$e]['year'] . '" />');
                        echo ('<input type="button" value="-" onclick="$(\'#edu' . $e . '\').remove();return false;"></p>');
                        echo ('<textarea name="edu_school' . $e . '" rows="8" cols="80">' . $edus[$e]['name'] . '</textarea>');
                    }
                } else {
                    $e = 0;
                }
                ?>
            </div>
            <p>Position: <input type="submit" id="addPos" value="+">
            <div id="position_fields">

                <?php
                if (!empty($positions)) {
                    for ($i = 0; $i < count($positions); $i++) {
                        echo ('<div id="position' . $i . '">');
                        echo ('<p>Year: <input type="text" name="year' . $i . '" value="' . $positions[$i]['year'] . '" />');
                        echo ('<input type="button" value="-" onclick="$(\'#position' . $i . '\').remove();return false;"></p>');
                        echo ('<textarea name="desc' . $i . '" rows="8" cols="80">' . $positions[$i]['description'] . '</textarea>');
                    }
                } else {
                    $i = 0;
                }
                ?>
            </div>
            </p>

            <input type="hidden" name="profile_id" value="<?= $pid ?>" />
            <input type="submit" value="Save" name="edit">
            <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
    </div>


</body>
<script type="text/javascript">
    countPos = <?php echo ($i); ?>;
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
        $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        // Grab some HTML with hot spots and insert into the DOM
        var source  = $("#edu-template").html();
        $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

        // Add the even handler to the new ones
        $('.school').autocomplete({
            source: "school.php"
        });

    });
})
</script>
<script id="edu-template" type="text">
  <div id="edu@COUNT@">
    <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
    <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
    <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
    </p>
  </div>
</script>
</html>