<?php require 'boot.php' ?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>SQLShell</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' href='styles.css'>
</head>
<body class='<?php echo $tablesBarOpen ? 'barTogglerOpen' : '' ?>'>
<div class='app'>
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
        <small>CTRL + D to empty favorites</small>
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
</div><!-- app -->
<script>
  window.sqlshellData = <?php echo $jsonData ?>
</script>
<script src='bundle.js'></script>
</body>
</html>