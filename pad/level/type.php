<?php
    
  $padNs = strpos($padTag [$pad], ':');

  if ( $padNs ) {

    $padTypeX = substr ($padTag [$pad], 0, $padNs);
    $padTagX  = substr ($padTag [$pad], $padNs+1);

    if ( file_exists ( PAD . "pad/types/$padType[$pad].php" ) and padTypeCheck ( $padTypeX, $padTagX ) )  {

      $padType [$pad] = $padTypeX;
      $padTag  [$pad] = $padTagX;

      return $padType [$pad]; 

    }

  }

  $padType [$pad] = padTypeGet( $padTag [$pad] );

 # echo 'type: ' . $padTag [$pad] . ' ' . $padType [$pad] . '<br>';

  return $padType [$pad];  

?>