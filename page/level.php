<?php

  $GLOBALS['padPageLevel'] [] = $pad;

  while ( $pad >= end ( $GLOBALS['padPageLevel'] ) ) 
    include 'level/level.php'; 

  array_pop ( $GLOBALS['padPageLevel'] );

?>