<?php

  $padPage = $padPage ?? $_REQUEST['page'] ?? $_REQUEST['padPage'] ?? 'index';

  if ( ! padCheckPage ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padGetPage ($padPage);
  
?>