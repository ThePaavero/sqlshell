<?php require 'boot.php' ?>
<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <title>SQLShell</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' href='styles.css'>
</head>
<body>
<div class='app'>
  <section>
    <form method='post' action='<?php echo $baseUrl ?>' class='sql-form'>
      <textarea name='sql' spellcheck='false' wrap='off' autofocus required><?php echo $sql ?></textarea>
      <input type='submit' value='Execute'/>
    </form> <!-- sql-form -->
    <div class='in-grid'>
      <small>CTRL + Enter to run query</small>
    </div><!-- in-grid -->
  </section>
  <div class='displays'>
    <section class='tables'>
      <h3>Tables:</h3>
      <div class='tables'>
      </div><!-- tables -->
    </section>
    <section class='results'>
        <?php if (isset($results) && ! empty($results)): ?>
          <h3>Result:</h3>
          <pre><?php echo $results ?></pre>
        <?php endif; ?>
    </section>
    <a href='#'>▸</a>
  </div><!-- displays -->
</div><!-- app -->
<script>
  window.sqlshellData = <?php echo $jsonData ?>
</script>
<script src='bundle.js'></script>
</body>
</html>