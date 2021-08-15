<?php

  $file = PAD_APP . "pages/$pad_parm";

  $php    = ( pad_file_exists("$file.php" ) ) ? pad_colors_file ("$file.php")  : '';
  $html   = ( pad_file_exists("$file.html") ) ? pad_colors_file ("$file.html") : '';
  
  $curl   = pad_include ("examples&page=$pad_parm");
  $result = $curl ['data'];

  $php_file  = ( pad_file_exists("$file.php") )  ? str_replace(PAD_APP . "pages/", '', "$file.php")  : '';
  $html_file = ( pad_file_exists("$file.html") ) ? str_replace(PAD_APP . "pages/", '', "$file.html") : '';

  return TRUE;
   
?>