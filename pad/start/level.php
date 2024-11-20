<?php

  $GLOBALS ['padLevel'] [] = $pad;

  while ( $pad >= end ( $GLOBALS ['padLevel'] ) ) 
    include 'catch/level.php'; 

  array_pop ( $GLOBALS ['padLevel'] );

?>