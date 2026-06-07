<?php

  global $padLevel;

  $padLevel [] = $pad;

  while ( $pad >= end ( $padLevel ) )
    include PAD . 'level/level.php';

  array_pop ( $padLevel );

?>