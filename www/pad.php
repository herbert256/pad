<?php

  $padOS = strtolower ( substr ( php_uname ('s'), 0, 3 ) );

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert/pad';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert/pad';
  elseif ( $padOS == 'win' ) $padHome = '/pad';
  else                       die ( "Unsuported OS: $padOS" );

  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  $padCheck = explode ( '/' , $_SERVER ['REQUEST_URI'] ?? 'pad' );
  foreach ( $padCheck as $padApp ) 
    if ( $padApp and is_dir ( "$padApps$padApp" ) )
      return include "$padHome/pad/pad.php";

  die ( "No PAD application found" );

?>