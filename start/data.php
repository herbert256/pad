<?php

  if ( ! isset ( $padHome ) ) 
    include 'home.php';

  $padDat = "$padHome/data/";

  if ( ! file_exists ( $padDat ) )
    mkdir ( $padData, 0777, TRUE );

?>