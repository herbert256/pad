<?php

  $result = pad_include ("examples/$pad_parm");

  $file = APP . "pages/examples/$pad_parm";

  $php    = ( pad_file_exists("$file.php" ) ) ? pad_colors_file ("$file.php")  : '';
  $html   = ( pad_file_exists("$file.html") ) ? pad_colors_file ("$file.html") : '';

  $php_file  = ( pad_file_exists("$file.php") )  ? str_replace(APP . "pages/examples/", '', "$file.php")  : '';
  $html_file = ( pad_file_exists("$file.html") ) ? str_replace(APP . "pages/examples/", '', "$file.html") : '';

  return TRUE;
   
?>