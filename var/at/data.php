<?php

  if ( $name and ! isset ( $padDataStore [$name] ) )
    $padDataStore [$name] = padData ($name);

  if ( $name )
    return padFindNames ( $padDataStore [$name], $names );
  else
    return padFindNames ( $padDataStore, $names );

?>