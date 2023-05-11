<?php

  $padPageLevel [] = $pad;

  while ( $pad >= end ( $padPageLevel ) ) 
    include pad . 'level/level.php'; 

  include pad . 'level/level.php'; 

  array_pop ( $padPageLevel );

?>