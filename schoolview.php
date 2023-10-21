<?php
require_once 'pdo.php';
session_start();
//Возвращает JSON для автокомпита строки
    header('Content-Type: application/json; charset=utf-8');
    $stmt = $pdo->prepare('SELECT name, year FROM Education as e JOIN Institution as i ON e.institution_id=i.institution_id WHERE profile_id=:pid ORDER BY year DESC');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
    
    $schools = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo(json_encode($schools, JSON_PRETTY_PRINT));
