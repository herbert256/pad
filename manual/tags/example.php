<?php

  $file = PAD_APP . "pages/examples/$pad_parm";

  $php    = ( pad_file_exists("$file.php" ) ) ? pad_colors_file ("$file.php")  : '';
  $html   = ( pad_file_exists("$file.html") ) ? pad_colors_file ("$file.html") : '';
  
  $curl   = pad_include ("manual&page=examples/$pad_parm");
  $result = $curl ['data'];

  $php_file  = ( pad_file_exists("$file.php") )  ? str_replace(PAD_APP . "pages/examples/", '', "$file.php")  : '';
  $html_file = ( pad_file_exists("$file.html") ) ? str_replace(PAD_APP . "pages/examples/", '', "$file.html") : '';

  return TRUE;
   
?>