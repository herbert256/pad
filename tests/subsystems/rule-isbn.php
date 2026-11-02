<?php
  $root = dirname ( __DIR__, 2 );
  $rule = require "$root/pad/subs/inputs/rules/types/isbn.php";
  foreach ( json_decode ( file_get_contents ( __DIR__ . '/rule-isbn.json' ), TRUE ) as $value => $want )
    if ( $rule ['check'] ( $value ) !== $want ) throw new Exception ( 'Failed: ' . $value );
  echo "rule:isbn PHP fixtures passed\n";
?>
