<?php require 'boot.php' ?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>SQLShell</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' href='styles.css'>
</head>
<body class='<?php echo $loggedIn ? ' logged-in' : ' logged-out' ?>'>
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
        <div class='autocomplete-offer-preview'>
        </div><!-- autocomplete-offer-preview -->
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
      <section class='tables-section'>
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
<script src='bundle.js'></script>
</body>
</html>