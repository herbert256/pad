<?php

  if ( count ($names) == 1 ) {

    $item = $names [0];

    for ( $i=$pad; $i >= 0; $i-- ) 
      if ( array_key_exists ( $padOpt [$i] [$item] ) )
        return $padOpt [$i] [$item];

  }

  if ( count ($names) == 2 ) {

    $tag  = $names [0];
    $item = $names [1];

    for ( $i=$pad; $i >= 0; $i-- ) 
      if ( $padName [$i] == $tag )
        if ( array_key_exists ( $padOpt [$i] [$item] ) )
          return $padOpt [$i] [$item];

  }

  return INF;

?>