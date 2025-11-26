<?php

  $GLOBALS ['padLevel'] [] = $pad;

  while ( $pad >= end ( $GLOBALS ['padLevel'] ) ) 
    include 'level/level.php'; 

  array_pop ( $GLOBALS ['padLevel'] );

?>