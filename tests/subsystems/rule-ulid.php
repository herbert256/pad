<?php
  $root = dirname ( __DIR__, 2 );
  $rule = require "$root/pad/subs/rules/types/ulid.php";
  foreach ( json_decode ( file_get_contents ( __DIR__ . '/rule-ulid.json' ), TRUE ) as $value => $want )
    if ( $rule ['check'] ( $value ) !== $want ) throw new Exception ( 'Failed: ' . $value );
  echo "rule:ulid PHP fixtures passed\n";
?>
