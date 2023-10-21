<?php
function flash_msg()
{
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
        unset($_SESSION['error']);
    } elseif (isset($_SESSION['success'])) {
        echo '<p style="color:green">' . $_SESSION['success'] . "</p>\n";
        unset($_SESSION['success']);
    }
}


function validate_input()
{
    if (strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0) {
        return "all fields are required";
    }
    if (strpos($_POST['email'], '@') == False) {
        return  "Email address must contain @";
    }
    return true;
}

function validate_edu(){
    for ($i=1; $i<=9; $i++) {
        if (!isset($_POST['edu_school'.$i])) continue;
        if (!isset($_POST['edu_year'.$i])) continue;
        $edu = $_POST['edu_school'.$i];
        $edu_y = $_POST['edu_year'.$i];

        if (strlen($edu) == 0 or strlen($edu_y) == 0) {
            return 'All fields are required';
        }
        if (!is_numeric($edu_y)) {
            return 'Education year must be muneric';
        }
    }
    return true;
}

function validate_pos(){
    for ($i=1; $i<=9; $i++) {
        if (!isset($_POST['year'.$i])) continue;
        if (!isset($_POST['desc'.$i])) continue;
        $y = $_POST['year'.$i];
        $d = $_POST['year'.$i];

        if (strlen($y) == 0 or strlen($d) == 0) {
            return 'All fields are required';
        }
        if (!is_numeric($y)) {
            return 'Position year must be muneric';
        }
    }
    return true;
}

function loadPos($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
    $stmt->execute(
        array(':prof' => $profile_id)
    );
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

function loadEdu($pdo, $profile_id) {
    $stmt = $pdo->prepare(
        'SELECT name, year FROM Education as e JOIN Institution as i ON e.institution_id = i.institution_id WHERE e.profile_id = :pid ORDER BY e.rank'
    );
    $stmt->execute(
        array(':pid'=>$profile_id)
    );
    $edu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $edu;
}

// 19:40
function insertPositions($pdo, $profile_id) {
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        $y = $_POST['year' . $i];
        $d = $_POST['desc' . $i];

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
                                VALUES(:pid, :rank, :year, :desc);');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $y,
            ':desc' => $d
        ));
        $rank++;
    }
}
function insertEdu($pdo, $profile_id) {
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i])) continue;
        if (!isset($_POST['edu_school' . $i])) continue;

        $y = $_POST['edu_year' . $i];
        $sc = $_POST['edu_school' . $i];
        
        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
        $stmt->execute(array(
            ':name'=> $sc
        ));
        $result = $stmt->fetch(PDO::FETCH_NUM);
        if ($result != false) {
            $institution_id = $result[0];}
        else {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) 
            VALUES (:name)');
            $stmt->execute(array(
                ':name'=>$sc
            ));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year) VALUES (:pid, :sc_id, :r, :y)');
        $stmt->execute(
            array(
                ':sc_id'=>$institution_id,
                ':pid'=>$profile_id,
                ':r'=>$rank,
                ':y'=>$y
            )
        );
    };

}