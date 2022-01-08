<?php

  $page = $_REQUEST['page'] ?? 'index';

  if ( ! preg_match ( '/^[A-Za-z0-9\/_-]+$/', $page ) ) pad_boot_error ("Invalid page name: $page");
  if ( strpos($page, '//') !== FALSE                  ) pad_boot_error ("Invalid page name '$page'");
  if ( substr($page, 0, 1) == '/'                     ) pad_boot_error ("Invalid page name '$page'");
  if ( substr($page, -1) == '/'                       ) pad_boot_error ("Invalid page name '$page'");  

?>