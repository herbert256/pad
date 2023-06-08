<?php

  if ( $name ) {

    if ( ! isset ( $padDataStore [$name] ) )
      return INF;

    return padAtSearch ( $padDataStore [$name], $names );

  }

 return padAtSearch ( $padDataStore, $names );

?>