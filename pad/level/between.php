<?php

  $padFirst    = substr ( $padBetween , 0, 1 );
  $padWords    = preg_split ( "/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY );
  $padTagCheck = $padWords [0] ?? '';
  $padTagOpts  = $padWords [1] ?? '';
  $padPrmParse = padParseOptions ( $padTagOpts );

  include PAD . 'level/options.php';

?>