<?php

$host = '127.0.0.1';
$db = 'jaateloakatemia';
$user = 'homestead';
$pass = 'secret';
$charset = 'utf8';

function getTables($pdo, $db)
{
    $sql = 'show tables';
    $query = $pdo->query($sql);

    return $query->fetchAll();
}

function getFirstTable($pdo, $db)
{
    return getTables($pdo, $db)[0]['Tables_in_' . $db];
}
$baseUrl = str_replace('index.php', '', $_SERVER['PHP_SELF']);
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);
$firstTable = getFirstTable($pdo, $db);

$jsonData = json_encode([
    'tables' => getTables($pdo, $db)
]);

if (isset($_POST['sql']) && ! empty($_POST['sql']))
{
    $sql = trim($_POST['sql']);
    $pdoStatement = $pdo->query($sql);
    $results = $pdoStatement->fetchAll();
    $results = '<pre>' . json_encode($results, JSON_PRETTY_PRINT) . '</pre>';
}

$sql = isset($sql) ? $sql : 'select * from ' . $firstTable . ' limit 50';
