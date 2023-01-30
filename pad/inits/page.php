<?php

  $page = $page ?? $_REQUEST['page'] ?? 'index';

  if ( ! padCheckPage ($app, $page) )
    padBootError ("Page '$page' not found");

  $page = padGetPage ($app, $page);
  
?>