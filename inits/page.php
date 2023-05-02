<?php

  $padPage = $padPage ?? $_REQUEST['padPage'] ?? $_REQUEST['page'] ?? 'index';

  if ( ! padPageCheck ($padPage) )
    padBootError ("Page '$padPage' not found");

  $padPage = padPageSet ($padPage);
  
?>