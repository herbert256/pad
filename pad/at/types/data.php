<?php

  if ( count ($names) == 1 )
    if ( isset ( $padDataStore [$name] ) )
      return $padDataStore [$name];
    
  padFindNames ( $padDataStore, $names );

?>