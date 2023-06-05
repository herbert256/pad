<?php

  if ( $name ) {

    if ( ! isset ( $GLOBALS [$name] ) )
      return INF;

    return padDotSearch ( $GLOBALS [$name], $names, $type );

  }

 return padDotSearch ( $GLOBALS, $names, $type );

?>