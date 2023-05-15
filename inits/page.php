<?php

  if ( count($_GET) )
    $padPage = $padPage ?? array_key_first ($_GET);
  else
    $padPage = $padPage ?? 'index' ;

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);
  
?>