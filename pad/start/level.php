<?php

  $GLOBALS ['padLevel'] [] = $pad;

  while ( $pad >= end ( $GLOBALS ['padLevel'] ) ) 
    include PAD . 'catch/level.php'; 

  array_pop ( $GLOBALS ['padLevel'] );

?>