<?php

  $padGet [$pad] = $padTag [$pad];

  if ( isset ( $padPrm [$pad] ['union '] ) )
    return padTable       ( $padTag [$pad] ) ;
  else
    return padTableUnion  ( $padTag [$pad] ) ;

?>