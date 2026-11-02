<?php
  $root = dirname ( __DIR__, 2 );
  $rule = require "$root/pad/subs/rules/types/timezone.php";
  foreach ( json_decode ( file_get_contents ( __DIR__ . '/rule-timezone.json' ), TRUE ) as $value => $want )
    if ( $rule ['check'] ( $value ) !== $want ) throw new Exception ( 'Failed: ' . $value );
  echo "rule:timezone PHP fixtures passed\n";
?>
