<?php

  if ( $kind ) {

    if     ( padIsTag   ( $kind ) ) $padIdx = padIsTag   ( $kind ); 
    elseif ( padIsLevel ( $kind ) ) $padIdx = padIsLevel ( $kind ); 
    else                            return INF;

    return include pad . 'var/tag.php';

  } 

  global $pad;

  for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

    $check = include pad . 'var/tag.php';
    if ( $check !== INF )
      return $check;

  }

  return INF;

?>