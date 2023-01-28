<?php
    
  $padNs = strpos($padTag [$pad], ':');

  if ( $padNs ) {

    $padType [$pad] = substr ($padTag [$pad], 0, $padNs);
    $padTag  [$pad] = substr ($padTag [$pad], $padNs+1);

    if ( ! file_exists ( PAD . "pad/types/$padType[$pad].php" ) ) 
      $padType [$pad] = FALSE;
    
  } else

    $padType [$pad] = padGetTypeLvl ( $padTag [$pad] );

  return $padType [$pad];  

?>