<?php

  $padPage = $padPage ?? 'index';

  if ( count($_GET) )
    $padPage = padCorrectPath ( array_key_first ($_GET) );

  if ( ! count($_GET) and isset ( $_SERVER['argv'] [1] ) )
    $padPage = padCorrectPath ( $_SERVER['argv'] [1] );

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);
  $padDir  = padDir ();
  $padPath = padPath ();

?>