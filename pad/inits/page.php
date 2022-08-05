<?php

  $page = $page ?? $_REQUEST['page'] ?? 'index';

  if ( ! pad_check_page ($app, $page) )
    pad_boot_error ("Page not found");

  $page = pad_get_page ($app, $page);
  
?>