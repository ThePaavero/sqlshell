<?php

$shellPassword = 'demo';
$host = '127.0.0.1';
$db = 'mysql';
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

function tablesBarShouldBeOpen()
{
  $sessionKey = 'tablesBarShouldBeOpen';

  return isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] === true;
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
    case 'SET_TABLES_BAR_OPEN_STATUS':
      $_SESSION['tablesBarShouldBeOpen'] = $_GET['status'] === 'open';
      echo json_encode(['success' => true, 'valueSet' => $_SESSION['tablesBarShouldBeOpen']]);
      break;
    case 'LOG_OUT':
      session_destroy();
      break;
    case 'CREATE_DUMP':
      exportDump();
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
  'tables' => getTables($pdo)
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
  .table-button.autocomplete-candidate {
    border: solid 1px greenyellow; }

.results-wrapper {
  max-width: 100%;
  overflow: auto; }
  .results-wrapper.table pre {
    display: none; }
  .results-wrapper nav ul li {
    display: inline-block; }
    .results-wrapper nav ul li a {
      display: block;
      color: inherit;
      opacity: 0.5;
      text-decoration: none;
      padding: 3px 5px;
      margin-right: 1px; }
      .results-wrapper nav ul li a.active {
        background: #000;
        opacity: 1; }

pre, table.results-table {
  color: #4a9aff;
  background: #000;
  padding: 2vh 1vw;
  min-width: 100%; }
  pre th, table.results-table th {
    color: #fff;
    font-size: 11px;
    padding-right: 1vw;
    padding-bottom: 10px; }
  pre td, table.results-table td {
    font-size: 11px;
    vertical-align: top; }
  pre tbody tr:nth-child(odd), table.results-table tbody tr:nth-child(odd) {
    background: rgba(255, 255, 255, 0.1); }

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
          <small data-action='download-dump'>CTRL + E to download a dump</small>
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
          <div class='results-wrapper'>
            <nav>
              <ul>
                <li><a href='#json'>JSON</a></li>
                <li><a href='#table'>Table</a></li>
              </ul>
            </nav>
            <pre><?php echo $results ?></pre>
          </div><!-- results-wrapper -->
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

Object.defineProperty(exports, "__esModule", {
  value: true
});
var TableAutoCompleter = function TableAutoCompleter(prompt, tableNames, links) {

  var offerActive = false;

  var init = function init() {
    listenToPrompt();
    listenToEnter();
  };

  var listenToEnter = function listenToEnter() {
    document.addEventListener('keydown', function (e) {
      if (e.keyCode === 9 && offerActive) {
        // "Tab"
        e.preventDefault();
        injectOffer(offerActive);
      }
    });
  };

  var injectOffer = function injectOffer(tableName) {
    var query = prompt.value;
    prompt.value = query.replace(getLastWord(query), tableName) + ' ';
  };

  var getLastWord = function getLastWord(str) {
    var words = str.split(' ');
    return words[words.length - 1];
  };

  var listenToPrompt = function listenToPrompt() {
    prompt.addEventListener('keyup', function (e) {
      var lastWord = getLastWord(prompt.value);

      if (lastWord.length < 2) {
        offerActive = false;
        renderTableButtons();
        return;
      }

      var matches = tableNames.filter(function (tableName) {
        if (tableName.startsWith(lastWord)) {
          return true;
        }
      });

      if (matches.length < 1) {
        offerActive = false;
        return;
      } else {
        offerActive = matches[0];
      }

      renderTableButtons();
    });
  };

  var renderTableButtons = function renderTableButtons() {
    links.forEach(function (link) {
      var method = link.getAttribute('href').split('#')[1] === offerActive ? 'add' : 'remove';
      link.classList[method]('autocomplete-candidate');
    });
  };

  init();
};

exports.default = TableAutoCompleter;

},{}],2:[function(require,module,exports){
'use strict';

var _TableAutoCompleter = require('./TableAutoCompleter');

var _TableAutoCompleter2 = _interopRequireDefault(_TableAutoCompleter);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var form = void 0;
var sqlPrompt = void 0;
var favorites = void 0;
var favoritesWrapper = void 0;
var tablesSection = void 0;
var tableLinks = void 0;
var resultsWrapper = void 0;
var resultRenderTypeNavLinks = void 0;
var renderStyle = 'table';
var activeTableName = void 0;

var init = function init() {
  if (document.body.classList.contains('logged-out')) {
    return;
  }
  resultsWrapper = document.querySelector('.results-wrapper');
  form = document.querySelector('.sql-form');
  sqlPrompt = form.querySelector('textarea');
  favorites = [];
  favoritesWrapper = document.querySelector('.favorites-wrapper');
  tablesSection = document.querySelector('.tables-section');
  resultRenderTypeNavLinks = document.querySelectorAll('.results-wrapper nav ul li a');
  renderStyle = getRenderStyle();

  tableLinks = printTableButtons(window.sqlshellData.tables);

  activateActiveRenderStyleTab();
  listenToSubmitKeyCombination();
  listenToSubmitTriggers();
  focusOnSqlPrompt();
  populateFavoritesFromDisk();
  toggleFavoritesFromDisk();
  toggleTablesFromDisk();
  listenToLogOutAndCloseLinks();
  listenToLegendLinks();
  attachResultRenderTabs();
  formatResults();

  (0, _TableAutoCompleter2.default)(sqlPrompt, getTablesList(), tableLinks);
};

var getTablesList = function getTablesList() {
  return window.sqlshellData.tables.map(function (obj) {
    return obj[Object.keys(obj)[0]];
  });
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
  sqlPrompt.innerText = 'select * from ' + tableName + ' limit 20';
  activeTableName = tableName;
  focusOnSqlPrompt();
};

var printTableButtons = function printTableButtons(tables) {
  var wrapper = document.querySelector('.tables');
  var links = [];
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
    links.push(link);
  });
  return links;
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
  if (favorites.length < 1) {
    return;
  }
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
  if (favorites.length < 1) {
    return;
  }
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
      // "Enter"
      form.submit();
    } else if (e.keyCode === 83 && ctrlDown) {
      // "S"
      e.preventDefault();
      addQueryToFavorites();
      showFavorites();
    } else if (e.keyCode === 69 && ctrlDown) {
      // "E"
      e.preventDefault();
      downloadDump();
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
    case 'download-dump':
      downloadDump();
      break;
  }
};

var removeResultsTable = function removeResultsTable() {
  var oldTable = document.querySelector('table.results-table');
  if (oldTable) {
    oldTable.remove();
  }
};

var activateActiveRenderStyleTab = function activateActiveRenderStyleTab() {
  Array.from(resultRenderTypeNavLinks).forEach(function (link) {
    link.classList.remove('active');
    if (link.getAttribute('href').split('#')[1] === renderStyle) {
      link.classList.add('active');
    }
  });
};

var formatResults = function formatResults() {
  removeResultsTable();
  if (renderStyle !== 'table') {
    resultsWrapper.classList.remove('table');
    return;
  }
  activateActiveRenderStyleTab();
  var pre = document.querySelector('.results-wrapper > pre');
  if (!pre) {
    return;
  }
  var resultJson = pre.innerText;
  var results = JSON.parse(resultJson);
  var columns = getColumnsAsArray(results);
  var table = document.createElement('table');
  table.classList.add('results-table');
  var thead = document.createElement('thead');
  var tbody = document.createElement('tbody');
  var firstRow = document.createElement('tr');
  columns.forEach(function (colName) {
    var th = document.createElement('th');
    th.innerText = colName;
    firstRow.appendChild(th);
  });
  thead.appendChild(firstRow);
  table.appendChild(thead);
  var rowsFragment = document.createDocumentFragment();
  results.forEach(function (row) {
    var tr = document.createElement('tr');
    columns.forEach(function (colName) {
      var td = document.createElement('td');
      td.innerText = row[colName] || '';
      tr.appendChild(td);
    });
    rowsFragment.appendChild(tr);
  });
  tbody.appendChild(rowsFragment);
  table.appendChild(tbody);
  resultsWrapper.classList.add('table');
  resultsWrapper.appendChild(table);
};

var getColumnsAsArray = function getColumnsAsArray(data) {
  return Object.keys(data.reduce(function (result, obj) {
    return Object.assign(result, obj);
  }, {}));
};

var attachResultRenderTabs = function attachResultRenderTabs() {
  Array.from(resultRenderTypeNavLinks).forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      setRenderStyle(link.getAttribute('href').split('#')[1]);
    });
  });
};

var getRenderStyle = function getRenderStyle() {
  renderStyle = window.localStorage.getItem('render-style') || renderStyle;
  return renderStyle;
};

var setRenderStyle = function setRenderStyle(style) {
  renderStyle = style;
  window.localStorage.setItem('render-style', style);
  formatResults(style);
  activateActiveRenderStyleTab();
};

var downloadDump = function downloadDump() {
  window.location.href = window.sqlshellData.baseUrl + '?ajax=1&action=CREATE_DUMP';
};

init();

},{"./TableAutoCompleter":1}]},{},[2]);
</script>
</body>
</html>