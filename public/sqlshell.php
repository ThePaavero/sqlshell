<?php

$shellPassword = 'demo';
$host = '127.0.0.1';
$db = 'information_schema';
$user = 'homestead';
$pass = 'secret';
$charset = 'utf8';


// --------------------------------------------------------
// All stuff below is SQLShell code. Edit at your own risk!
// --------------------------------------------------------


session_start();

$loggedIn = isset($_SESSION['sqlshellLoggedIn']) || (isset($_POST['password']) && $_POST['password'] === $shellPassword);
if (isset($_POST['password']) && $_POST['password'] === $shellPassword)
{
  $_SESSION['sqlshellLoggedIn'] = true;
}

function tablesBarShouldBeOpen()
{
  $sessionKey = 'tablesBarShouldBeOpen';

  return isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] === true;
}

function getTables($pdo)
{
  $sql = 'show tables';
  $query = $pdo->query($sql);

  return $query->fetchAll();
}

function getFirstTable($pdo, $db)
{
  return getTables($pdo)[0]['Tables_in_' . $db];
}

if (isset($_GET['ajax']))
{
  switch ($_GET['action'])
  {
    case 'SET_TABLES_BAR_OPEN_STATUS':
      $_SESSION['tablesBarShouldBeOpen'] = $_GET['status'] === 'open';
      echo json_encode(['success' => true, 'valueSet' => $_SESSION['tablesBarShouldBeOpen']]);
      break;
    case 'LOG_OUT':
      session_destroy();
      break;
  }

  exit;
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
  'baseUrl' => $baseUrl,
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
$tablesBarOpen = tablesBarShouldBeOpen();
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
  padding: 0; }

body {
  font-size: 15px;
  font-family: sans-serif;
  background: #2d2d2d;
  color: #fff; }

small {
  opacity: 0.4; }

.in-grid {
  padding: 0 2vw; }

.login-form {
  padding: 2vh 5vw; }

.warning {
  display: block;
  color: #ff4626;
  padding: 20px 0; }

.tables-section {
  width: 20px;
  overflow: hidden;
  margin-right: 2vw; }
  .tables-section h3 {
    display: none; }
  .tables-section.open {
    width: 25%;
    padding-right: 2vw; }
    .tables-section.open h3 {
      display: block; }

.displays {
  display: flex;
  padding: 2vh 2vw;
  flex-direction: row;
  flex-wrap: nowrap;
  align-items: flex-start; }
  .displays section {
    position: relative; }
    .displays section.results {
      width: 100%; }
      body.barTogglerOpen .displays section.results {
        width: 75%; }
    .displays section h3 {
      margin-bottom: 4px; }

textarea {
  display: block;
  width: 100%;
  height: 20vh;
  background: #353535;
  border: none;
  border-bottom: solid 1px rgba(255, 255, 255, 0.3);
  color: greenyellow;
  padding: 2vh 2vw;
  font-size: 20px; }
  textarea:active, textarea:focus {
    outline: none;
    background: #000; }

input[type=submit] {
  display: none; }

.table-button {
  display: block;
  width: 0;
  overflow: hidden;
  text-decoration: none;
  font-size: 11px;
  border-bottom: solid 1px rgba(255, 255, 255, 0.1);
  color: inherit;
  padding: 0.5vh 0; }
  .open .table-button {
    width: auto;
    padding: 0.5vh 1vw; }
  .table-button:hover {
    background: rgba(255, 255, 255, 0.1); }

pre {
  color: #4a9aff;
  background: #000;
  padding: 2vh 1vw;
  max-width: 100%;
  overflow: auto; }

.bar-toggler {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: 0;
  color: inherit;
  text-decoration: none; }
  .open .bar-toggler {
    transform: translateY(-50%) rotate(180deg); }

.prompt-help {
  margin: 1vh 0; }
  .prompt-help small {
    display: inline-block;
    margin-right: 5px;
    padding-right: 10px;
    border-right: solid 1px rgba(255, 255, 255, 0.5); }
    .prompt-help small:last-of-type {
      border-right: none; }

.submit-on-click {
  cursor: pointer; }

.favorites-wrapper {
  display: none; }
  .favorites-wrapper.open {
    display: block;
    padding-bottom: 2vh; }
    .favorites-wrapper.open ol {
      list-style: decimal;
      list-style-position: inside; }
    .favorites-wrapper.open li {
      cursor: pointer;
      display: list-item;
      color: inherit;
      text-decoration: none;
      background: rgba(255, 255, 255, 0.1);
      padding: 3px 5px;
      margin-bottom: 1px; }

.log-out {
  position: absolute;
  top: 2vh;
  right: 2vw;
  color: #fff;
  font-size: 3vw;
  text-decoration: none; }
</style>
</head>
<body class='<?php echo $tablesBarOpen ? 'barTogglerOpen' : '' ?><?php echo $loggedIn ? ' logged-in' : ' logged-out' ?>'>
<div class='app'>
  <?php if ( ! $loggedIn): ?>
    <form method='post' action='<?php echo $baseUrl ?>' class='login-form'>
      <label>
        Password:
        <input type='password' name='password' required/>
      </label>
      <input type='submit' value='Log in'/>
      <?php if ( ! isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on'): ?>
        <strong class='warning'>Warning! You're sending the password string over a non-secure connection.</strong>
      <?php endif ?>
    </form> <!-- login-form -->
  <?php else: ?>
    <a href='#' class='log-out' title='Log out'>✖</a>
    <section>
      <form method='post' action='<?php echo $baseUrl ?>' class='sql-form'>
        <textarea name='sql' spellcheck='false' wrap='off' autofocus required><?php echo $sql ?></textarea>
        <input type='submit' value='Execute'/>
      </form> <!-- sql-form -->
      <div class='in-grid'>
        <div class='prompt-help'>
          <small class='submit-on-click'>CTRL + Enter to run query</small>
          <small>CTRL + S to save query to favorites</small>
          <small>CTRL + L to toggle list of favorite queries</small>
          <small>CTRL + D to delete the last favorite</small>
        </div><!-- prompt-help -->
      </div><!-- in-grid -->
    </section>
    <div class='displays'>
      <section class='tables-section<?php echo $tablesBarOpen ? ' open' : '' ?>'>
        <h3>Tables</h3>
        <div class='tables'>
        </div><!-- tables -->
        <a href='#' class='bar-toggler open' title='Toggle table list'>▸</a>
      </section>
      <section class='results'>
        <div class='favorites-wrapper'>
        </div><!-- favorites-wrapper -->
        <?php if (isset($results) && ! empty($results)): ?>
          <h3>Result</h3>
          <pre><?php echo $results ?></pre>
        <?php endif; ?>
      </section>
    </div><!-- displays -->
  <?php endif ?>
</div><!-- app -->
<script>
  window.sqlshellData = <?php echo $jsonData ?>
</script>
<script>(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var form = void 0;
var sqlPrompt = void 0;
var favorites = void 0;
var favoritesWrapper = void 0;

var init = function init() {
  if (document.body.classList.contains('logged-out')) {
    return;
  }
  form = document.querySelector('.sql-form');
  sqlPrompt = form.querySelector('textarea');
  favorites = [];
  favoritesWrapper = document.querySelector('.favorites-wrapper');

  listenToSubmitKeyCombination();
  printTableButtons(window.sqlshellData.tables);
  listenToSidebarTogglerLinks();
  listenToSubmitTriggers();
  focusOnSqlPrompt();
  populateFavoritesFromDisk();
  toggleFavoritesFromDisk();
  listenToLogOutAndCloseLinks();
};

var listenToSubmitTriggers = function listenToSubmitTriggers() {
  var buttons = document.querySelectorAll('.submit-on-click');
  Array.from(buttons).forEach(function (button) {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      form.submit();
    });
  });
};

var focusOnSqlPrompt = function focusOnSqlPrompt() {
  sqlPrompt.focus();
  sqlPrompt.setSelectionRange(sqlPrompt.value.length, sqlPrompt.value.length);
};

var activateTable = function activateTable(tableName) {
  sqlPrompt.innerText = 'select * from ' + tableName;
  focusOnSqlPrompt();
};

var printTableButtons = function printTableButtons(tables) {
  var wrapper = document.querySelector('.tables');
  tables.map(function (row) {
    var myKey = Object.keys(row)[0];
    var tableName = row[myKey];
    var link = document.createElement('a');
    link.className = 'table-button';
    link.href = '#' + tableName;
    link.innerText = tableName;
    link.addEventListener('click', function (e) {
      e.preventDefault();
      activateTable(tableName);
    });
    wrapper.appendChild(link);
  });
};

var addQueryToFavorites = function addQueryToFavorites() {
  var currentQuery = sqlPrompt.value;
  favorites.push(currentQuery);
  saveFavoritesToDisk();
  renderFavorites();
};

var populateFavoritesFromDisk = function populateFavoritesFromDisk() {
  var fromDisk = window.localStorage.getItem('favorites');
  if (!fromDisk) {
    return;
  }
  var diskFavorites = JSON.parse(fromDisk);
  diskFavorites.forEach(function (query) {
    favorites.push(query);
  });
  renderFavorites();
};

var toggleFavoritesFromDisk = function toggleFavoritesFromDisk() {
  var open = window.localStorage.getItem('favorites-open') || false;
  open = open === 'true';
  if (open === true) {
    showFavorites();
  } else {
    hideFavorites();
  }
};

var renderFavorites = function renderFavorites() {
  favorites = favorites.reverse();
  if (favorites.length < 1) {
    hideFavorites();
    return;
  }
  favoritesWrapper.innerHTML = '<h3>Favorites</h3>';
  var orderedList = document.createElement('ol');
  favorites.map(function (query) {
    var link = document.createElement('li');
    link.setAttribute('data-query', query);
    link.innerText = query;
    orderedList.appendChild(link);
    link.addEventListener('click', function (e) {
      e.preventDefault();
      sqlPrompt.value = link.getAttribute('data-query');
      focusOnSqlPrompt();
    });
  });
  favoritesWrapper.appendChild(orderedList);
  favorites = favorites.reverse();
};

var showFavorites = function showFavorites() {
  window.localStorage.setItem('favorites-open', true);
  favoritesWrapper.classList.add('open');
};
var hideFavorites = function hideFavorites() {
  window.localStorage.setItem('favorites-open', false);
  favoritesWrapper.classList.remove('open');
};

var deleteLastFavorite = function deleteLastFavorite() {
  favorites.shift();
  saveFavoritesToDisk();
};

var saveFavoritesToDisk = function saveFavoritesToDisk() {
  window.localStorage.setItem('favorites', JSON.stringify(favorites));
};

var listenToSubmitKeyCombination = function listenToSubmitKeyCombination() {
  var ctrlDown = false;
  document.addEventListener('keydown', function (e) {
    if (e.keyCode === 17) {
      ctrlDown = true;
    } else if (e.keyCode === 13 && ctrlDown) {
      form.submit();
    } else if (e.keyCode === 83 && ctrlDown) {
      // "S"
      e.preventDefault();
      addQueryToFavorites();
      showFavorites();
    } else if (e.keyCode === 76 && ctrlDown) {
      // "L"
      e.preventDefault();
      if (favoritesWrapper.classList.contains('open')) {
        hideFavorites();
      } else {
        showFavorites();
      }
    } else if (e.keyCode === 68 && ctrlDown) {
      // "D"
      e.preventDefault();
      deleteLastFavorite();
      renderFavorites();
    }
  });
  document.addEventListener('keyup', function (e) {
    if (e.keyCode === 17) {
      ctrlDown = false;
    }
  });
};

var listenToSidebarTogglerLinks = function listenToSidebarTogglerLinks() {
  var links = document.querySelectorAll('.bar-toggler');
  Array.from(links).forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var toToggle = e.currentTarget.parentNode;
      link.classList.toggle('open');
      toToggle.classList.toggle('open');
      document.body.classList.toggle('barTogglerOpen');
      var open = toToggle.classList.contains('open') ? 'open' : '';
      var url = window.sqlshellData.baseUrl + '?ajax=1&action=SET_TABLES_BAR_OPEN_STATUS&status=' + open;
      window.fetch(url, {
        credentials: 'same-origin'
      }).then(console.log).catch(console.error);
    });
  });
};

var logOutAndClose = function logOutAndClose() {
  var url = window.sqlshellData.baseUrl + '?ajax=1&action=LOG_OUT';
  window.fetch(url, {
    credentials: 'same-origin'
  }).then(function () {
    window.location = window.location.href;
  }).catch(console.error);
};

var listenToLogOutAndCloseLinks = function listenToLogOutAndCloseLinks() {
  var links = document.querySelectorAll('.log-out');
  Array.from(links).forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      logOutAndClose();
    });
  });
};

init();

},{}]},{},[1]);
</script>
</body>
</html>