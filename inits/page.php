<?php

  $padPage = $padPage ?? $_REQUEST['page'] ?? $_REQUEST['padPage'] ?? 'index';

  if ( ! padCheckPage ($padApp, $padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padGetPage ($padApp, $padPage);
  
?>