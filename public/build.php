<?php

$filename = 'sqlshell.php';

$scriptBlock = '<script>' . file_get_contents('bundle.js') . '</script>';
$cssBlock = '<style>' . file_get_contents('styles.css') . '</style>';
$bootPhp = file_get_contents('boot.php');

$userLandNotice = '
// --------------------------------------------------------
// All stuff below is SQLShell code. Edit at your own risk!
// --------------------------------------------------------
';

$fromTo = [
  '// /userland' => $userLandNotice
];

$bootPhp = str_replace(array_keys($fromTo), $fromTo, $bootPhp);

$fromTo = [
  '<script src=\'bundle.js\'></script>' => $scriptBlock,
  '<?php require \'boot.php\' ?>' => $bootPhp . ' ?>',
  '<link rel=\'stylesheet\' href=\'styles.css\'>' => $cssBlock
];

$code = str_replace(array_keys($fromTo), $fromTo, file_get_contents('index.php'));

file_put_contents($filename, $code);
