<?php

  $GLOBALS ['padLevel'] [] = $pad;

  while ( $pad >= end ( $GLOBALS ['padLevel'] ) ) 
    include pad . 'level/level.php'; 

  array_pop ( $GLOBALS ['padLevel'] );

?>