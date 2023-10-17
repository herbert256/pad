<?php

  if ( count($_GET) )
    $padPage = $padPage ?? padCorrectPath ( array_key_first ($_GET) );
  else
    $padPage = $padPage ?? 'index' ;

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);
  $padDir  = padDir ();
  $padPath = padPath ();

?>