<?php

  include '/pad/config/tidy.php';

  if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE ) 

    $padOutput = padTidy ( $padOutput );
  
  elseif ( $padMyTidy )
    
    include '/pad/exits/myTidy.php';
  
?>