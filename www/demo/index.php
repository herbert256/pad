<?php

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR )    ) $padHR    = hrtime    ( TRUE );

  $padOS = substr ( strtolower ( php_uname ('s') ), 0, 3 );

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert';
  elseif ( $padOS == 'win' ) $padHome = 'D:\\pad';
  else                       
    die ( "Unsuported OS: $padOS" );

  $padApp = 'demo';

  define ( 'APP', "$padHome/apps/$padApp/"  );
  define ( 'DAT', "$padHome/data/"         );

  include "$padHome/pad/pad.php";
  
?>