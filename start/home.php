<?php

  $padOS = substr ( strtolower ( php_uname ('s') ), 0, 3);

  if     ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/pad';
  elseif ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';
  else                       die ( 'unknow host system' );

?>