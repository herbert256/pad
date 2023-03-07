<?php

  return;
  
  if ( strpos ( $padHtml [$pad], '@start@' ) === FALSE ) return;
  if ( strpos ( $padHtml [$pad], '@end@' )   === FALSE ) return;

  list ( $padPart1, $padPart ) = explode ( '@start@', $padHtml [$pad], 2 );

?>