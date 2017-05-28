<?php

$shellPassword = 'demo';
$host = '127.0.0.1';
$db = 'mysql';
$user = 'homestead';
$pass = 'secret';
$charset = 'utf8';

// /userland

session_start();

$loggedIn = isset($_SESSION['sqlshellLoggedIn']) || (isset($_POST['password']) && $_POST['password'] === $shellPassword);
if (isset($_POST['password']) && $_POST['password'] === $shellPassword)
{
  $_SESSION['sqlshellLoggedIn'] = true;
}

function exportDump()
{
  global $user, $pass, $db;
  $filename = 'backup-' . date('d-m-Y') . '.sql.gz';
  $mime = 'application/x-gzip';
  header('Content-Type: ' . $mime);
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  $cmd = "mysqldump -u $user --password=$pass $db | gzip --best";
  passthru($cmd);
  exit(0);
}

function getTables($pdo)
{
  $sql = 'show tables';

  return $pdo->query($sql)->fetchAll();
}

function getFirstTable($pdo, $db)
{
  return getTables($pdo)[0]['Tables_in_' . $db];
}

if (isset($_GET['ajax']))
{
  switch ($_GET['action'])
  {
    case 'LOG_OUT':
      session_destroy();
      break;
    case 'CREATE_DUMP':
      exportDump();
      break;
    case 'GIVE-LOGGED-IN-STATUS':
      die(json_encode([
        'loggedIn' => $loggedIn
      ]));
      break;
  }

  exit;
}

$baseUrl = str_replace('index.php', '', $_SERVER['PHP_SELF']);

if ($loggedIn)
{
  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  $pdo = new PDO($dsn, $user, $pass, $opt);
  $firstTable = getFirstTable($pdo, $db);

  $jsonData = json_encode([
    'baseUrl' => $baseUrl,
    'tables' => getTables($pdo)
  ]);

  if (isset($_POST['sql']) && ! empty($_POST['sql']))
  {
    $sql = trim($_POST['sql']);
    $pdoStatement = $pdo->query($sql);
    $results = $pdoStatement->fetchAll();
    if (isset($_POST['xhr']))
    {
      die(json_encode($results));
    }
    $results = '<pre>' . json_encode($results, JSON_PRETTY_PRINT) . '</pre>';
  }

  $sql = isset($sql) ? $sql : 'select * from ' . $firstTable . ' limit 50';
}
