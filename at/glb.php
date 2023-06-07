<?php

  if ( $name ) {

    if ( ! isset ( $GLOBALS [$name] ) )
      return INF;

    return padAtSearch ( $GLOBALS [$name], $names );

  }

 return padAtSearch ( $GLOBALS, $names );

?>