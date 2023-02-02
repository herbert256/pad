<?php
    
  $padType [$pad] = padTypeGet( $padTag [$pad] );

  if ( $padType [$pad] )
    return $padType [$pad];  

  if ( strpos($padTag [$pad], ':') !== FALSE ) {

    list ( $padType [$pad], $padTag [$pad]) = explode (':', $padTag [$pad], 2 );

    if ( padTypeCheck ( $padType [$pad], $padTag [$pad] ) ) 
      return $padType [$pad];  

  }

  return FALSE;  

?>