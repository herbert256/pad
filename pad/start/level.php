<?php

  $GLOBALS ['padLevel'] [] = $pad;

  while ( $pad >= end ( $GLOBALS ['padLevel'] ) ) 
    include 'tryCatch/go/level.php'; 

  array_pop ( $GLOBALS ['padLevel'] );

?>