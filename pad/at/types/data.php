<?php

  if ( $kind )
    if ( $kind and $padDataStore [$kind] ) 
      return padFindNames ( $padDataStore [$kind], $names );
    else
      return INF;

  return padFindNames ( $padDataStore, $names );

?>