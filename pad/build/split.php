<?php
 
  $padOpenClose = padOpenCloseList ( $padBuildTrue) ;

  $padPos = strpos ( $padBuildTrue, '@else@');

  while ( $padPos !== FALSE) {
    
    if  ( padOpenCloseCount ( substr ( $padBuildTrue, 0, $padPos ), $padOpenClose) ) {
      $padBuildFalse = substr ( $padBuildTrue, $padPos+6  );
      $padBuildTrue  = substr ( $padBuildTrue, 0, $padPos );
      if ( padInfo ) 
        include pad . 'info/events/else.php';
      return;
    }

    $padPos = strpos ( $padBuildTrue, '@else@', $padPos+1);

  }

?>