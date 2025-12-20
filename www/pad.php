<?php

  if ( ! isset ( $padApp ) )
    die ( "Variable \$padApp must be set.\n" );

  $padOS = strtolower ( substr ( php_uname ('s'), 0, 3 ) );

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/pad';
  else
    die ( "Unsuported OS: $padOS" );

  define ( 'APP', "$padHome/apps/$padApp/"  );
  define ( 'DAT', "$padHome/DATA/$padApp/"  );

  include "$padHome/pad/pad.php";

?>
