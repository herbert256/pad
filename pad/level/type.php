<?php
    
  $padTypeResult = padTypeGet( $padTypeCheck );

  if ( $padTypeResult )
    return $padTypeResult;  

  if ( strpos($padTypeCheck, ':') !== FALSE ) {

    list ( $padTypeResult, $padTypeCheck) = explode (':', $padTypeCheck, 2 );

    if ( padTypeCheck ( $padTypeResult, $padTypeCheck ) ) 
      return $padTypeResult;  

  }

  return FALSE;  

?>