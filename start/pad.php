<?php

  if ( ! isset ( $padHome ) ) 
    include 'home.php';

  $padPad = "$padHome/pad/";

  if ( ! file_exists ( $padPad ) )
    die ( 'PAD not found' );

?>