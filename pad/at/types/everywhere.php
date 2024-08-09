<?php

  global $padDataStore;

  $current = include pad . 'at/any/tags.php';
  if ( $current !== INF ) 
    return $current;

  return include pad . 'at/types/any.php';
  
?>