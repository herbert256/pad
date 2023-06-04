<?php

  if ( ! isset ( $GLOBALS [$name] ) )
    return INF;

  return padDotSearch ( $GLOBALS [$name], $names, $type );
  
?>