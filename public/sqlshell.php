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

if (isset($_POST['sql']) && ! empty($_POST['sql']))
{
    $sql = trim($_POST['sql']);
    $pdoStatement = $pdo->query($sql);
    $results = $pdoStatement->fetchAll();
    $results = '<pre>' . json_encode($results, JSON_PRETTY_PRINT) . '</pre>';
}

$sql = isset($sql) ? $sql : 'select * from ' . $firstTable . ' limit 50';
?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>SQLShell</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <style>* {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-size: 15px;
      font-family: sans-serif;
      background: #2d2d2d;
      color: #fff;
    }

    textarea {
      width: 100vw;
      height: 20vh;
      background: #000;
      border: solid 1px #fff;
      color: #fff;
      padding: 2vh 2vw;
      font-size: 20px;
    }

    input[type=submit] {
      display: none;
    }
  </style>
</head>
<body>
<div class='app'>
  <form method='post' action='<?php echo $baseUrl ?>' class='sql-form'>
    <textarea name='sql' autofocus required><?php echo $sql ?></textarea>
    <input type='submit' value='Execute'/>
  </form> <!-- sql-form -->
  <p>
    <small>CTRL + Enter to run query</small>
  </p>
    <?php if (isset($results) && ! empty($results)): ?>
      <pre><?php echo $results ?></pre>
    <?php endif; ?>
</div><!-- app -->
<script>(function e(t, n, r) {
    function s(o, u) {
      if (!n[o]) {
        if (!t[o]) {
          var a = typeof require == "function" && require;
          if (!u && a) {
            return a(o, !0);
          }
          if (i) {
            return i(o, !0);
          }
          var f = new Error("Cannot find module '" + o + "'");
          throw f.code = "MODULE_NOT_FOUND", f
        }
        var l = n[o] = {exports: {}};
        t[o][0].call(l.exports, function (e) {
          var n = t[o][1][e];
          return s(n ? n : e)
        }, l, l.exports, e, t, n, r)
      }
      return n[o].exports
    }

    var i = typeof require == "function" && require;
    for (var o = 0; o < r.length; o++)s(r[o]);
    return s
  })({
    1: [function (require, module, exports) {
      'use strict';

      (function () {
        var form = document.querySelector('.sql-form');
        var ctrlDown = false;
        document.addEventListener('keydown', function (e) {
          if (e.keyCode === 17) {
            console.log('Ctrl down!');
            ctrlDown = true;
          }
          if (e.keyCode === 13 && ctrlDown) {
            form.submit();
          }
        });
        document.addEventListener('keyup', function (e) {
          if (e.keyCode === 17) {
            ctrlDown = false;
          }
        });
      })();

    }, {}]
  }, {}, [1]);
</script>
</body>
</html>