<?php

  $result = pad_include ($app, "examples/$pad_parm");

  $file = APP . "pages/examples/$pad_parm";

  $php    = ( file_exists("$file.php" ) ) ? pad_colors_file ("$file.php")  : '';
  $html   = ( file_exists("$file.html") ) ? pad_colors_file ("$file.html") : '';

  $php_file  = ( file_exists("$file.php") )  ? str_replace(APP . "pages/examples/", '', "$file.php")  : '';
  $html_file = ( file_exists("$file.html") ) ? str_replace(APP . "pages/examples/", '', "$file.html") : '';

  return TRUE;
   
?>