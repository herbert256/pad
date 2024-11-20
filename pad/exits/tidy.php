<?php

  include 'config/tidy.php';

  if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE ) 

    $padOutput = padTidy ( $padOutput );
  
  elseif ( $padMyTidy )
    
    include 'exits/myTidy.php';
  
?>