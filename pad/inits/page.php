<?php

  $page = $page ?? $_REQUEST['page'] ?? 'index';

  if ( ! pCheck_page ($app, $page) )
    pBoot_error ("Page not found");

  $page = pGet_page ($app, $page);
  
?>