<?php

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR )    ) $padHR    = hrtime    ( TRUE );

  $padOS = substr ( strtolower ( php_uname ('s') ), 0, 3 );

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/apache/htdocs/pad';
  else
    die ( "Unsuported OS: $padOS" );

?>