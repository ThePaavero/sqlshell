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
  display: none; }
  .tables-section.open {
    display: block;
    width: 25%;
    padding-right: 1vw; }
    @media screen and (max-width: 700px) {
      .tables-section.open {
        width: 100%; } }

.displays {
  display: flex;
  padding: 2vh 2vw;
  flex-direction: row;
  flex-wrap: nowrap;
  align-items: flex-start; }
  @media screen and (max-width: 700px) {
    .displays {
      display: block; } }
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
  /*position: absolute;
  //top: 50%;
  top: 0;
  //transform: translateY(-50%);
  right: 0;
  height: 100%;
  background: blue;*/
  color: inherit;
  text-decoration: none; }
  .open .bar-toggler {
    transform: rotate(180deg); }

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

.prompt-help small {
  cursor: pointer; }
</style>
</head>
<body class='<?php echo $tablesBarOpen ? 'barTogglerOpen' : '' ?><?php echo $loggedIn ? ' logged-in' : ' logged-out' ?>'>
<div class='app'>
  <?php if ( ! $loggedIn): ?>
    <form method='post' action='<?php echo $baseUrl ?>' class='login-form'>
      <label>
        Password:
        <input type='password' name='password' autofocus required/>
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
          <small data-action='run-query'>CTRL + Enter to run query</small>
          <small data-action='toggle-tables'>CTRL + B to toggle list of tables</small>
          <small data-action='toggle-favorites'>CTRL + L to toggle list of favorite queries</small>
          <small data-action='save-query-to-favorites'>CTRL + S to save query to favorites</small>
          <small data-action='delete-last-favorite'>CTRL + D to delete the last favorite</small>
          <small data-action='focus-on-prompt'>ESC to focus on prompt</small>
        </div><!-- prompt-help -->
      </div><!-- in-grid -->
    </section>
    <div class='displays'>
      <section class='tables-section<?php echo $tablesBarOpen ? ' open' : '' ?>'>
        <h3>Tables</h3>
        <div class='tables'>
        </div><!-- tables -->
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
var tablesSection = void 0;

var init = function init() {
  if (document.body.classList.contains('logged-out')) {
    return;
  }
  form = document.querySelector('.sql-form');
  sqlPrompt = form.querySelector('textarea');
  favorites = [];
  favoritesWrapper = document.querySelector('.favorites-wrapper');
  tablesSection = document.querySelector('.tables-section');

  listenToSubmitKeyCombination();
  printTableButtons(window.sqlshellData.tables);
  listenToSubmitTriggers();
  focusOnSqlPrompt();
  populateFavoritesFromDisk();
  toggleFavoritesFromDisk();
  toggleTablesFromDisk();
  listenToLogOutAndCloseLinks();
  listenToLegendLinks();
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

var toggleFavorites = function toggleFavorites() {
  var open = favoritesWrapper.classList.contains('open');
  if (open === true) {
    hideFavorites();
  } else {
    showFavorites();
  }
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

var toggleTablesFromDisk = function toggleTablesFromDisk() {
  var open = window.localStorage.getItem('tables-open') || 'true';
  open = open === 'true';
  if (open === true) {
    showTablesSection();
  } else {
    hideTablesSection();
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
    if (e.keyCode === 27) {
      // "ESC"
      focusOnSqlPrompt();
    }
    if (e.keyCode === 17) {
      ctrlDown = true;
    } else if (e.keyCode === 13 && ctrlDown) {
      form.submit();
    } else if (e.keyCode === 83 && ctrlDown) {
      // "S"
      e.preventDefault();
      addQueryToFavorites();
      showFavorites();
    } else if (e.keyCode === 66 && ctrlDown) {
      // "B"
      e.preventDefault();
      toggleTablesSection();
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

var toggleTablesSection = function toggleTablesSection() {
  var open = document.body.classList.contains('barTogglerOpen');
  if (open) {
    hideTablesSection();
  } else {
    showTablesSection();
  }
};

var showTablesSection = function showTablesSection() {
  tablesSection.classList.add('open');
  document.body.classList.add('barTogglerOpen');
  window.localStorage.setItem('tables-open', true);
};

var hideTablesSection = function hideTablesSection() {
  tablesSection.classList.remove('open');
  document.body.classList.remove('barTogglerOpen');
  window.localStorage.setItem('tables-open', false);
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

var listenToLegendLinks = function listenToLegendLinks() {
  var links = document.querySelectorAll('.prompt-help > small');
  Array.from(links).forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var action = link.getAttribute('data-action');
      dispatchAction(action);
    });
  });
};

var dispatchAction = function dispatchAction(action) {
  switch (action) {
    case 'run-query':
      form.submit();
      break;
    case 'toggle-tables':
      toggleTablesSection();
      break;
    case 'toggle-favorites':
      toggleFavorites();
      break;
    case 'save-query-to-favorites':
      addQueryToFavorites();
      showFavorites();
      break;
    case 'delete-last-favorite':
      deleteLastFavorite();
      renderFavorites();
      break;
    case 'focus-on-prompt':
      focusOnSqlPrompt();
      break;
  }
};

init();

},{}]},{},[1]);
</script>
</body>
</html>