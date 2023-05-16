<?php

  if ( count($_GET) )
    $padPage = $padPage ?? array_key_first ($_GET);
  else
    $padPage = $padPage ?? 'index' ;

  if ( str_starts_with ( $padPage, '/') )
    $padPage = substr ( $padPage, 1);  

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);

  if ( str_contains($padPage, '/') )
    $padDir = substr ( $padPage, 0, strrpos ($padPage, '/') );
  else
    $padDir = '';

?>