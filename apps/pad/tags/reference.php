<?php

  $result = pad ('reference', "$reference");

  $file = APPS . "reference/pages/$reference";

  $php    = ( pad_file_exists("$file.php" ) ) ? pad_colors_file ("$file.php")  : '';
  $html   = ( pad_file_exists("$file.html") ) ? pad_colors_file ("$file.html") : '';

  $php_file  = ( pad_file_exists("$file.php") )  ? str_replace(APPS . "reference/pages/", '', "$file.php")  : '';
  $html_file = ( pad_file_exists("$file.html") ) ? str_replace(APPS . "reference/pages/", '', "$file.html") : '';

  $demo = ( strpos($html, '<font color="blue">demo</font>') !== FALSE );

  return TRUE;
   
?>