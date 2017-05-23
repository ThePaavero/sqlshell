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
  <form method='post' action='<?php echo $baseUrl ?>' class='sql-form'>
    <textarea name='sql' spellcheck='false' wrap='off' autofocus required><?php echo $sql ?></textarea>
    <input type='submit' value='Execute'/>
  </form> <!-- sql-form -->
  <p>
    <small>CTRL + Enter to run query</small>
  </p>
    <?php if (isset($results) && ! empty($results)): ?>
      <pre><?php echo $results ?></pre>
    <?php endif; ?>
</div><!-- app -->
<script src='bundle.js'></script>
</body>
</html>