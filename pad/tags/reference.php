<?php

  $result = pad ('reference', "$reference");

  $file = PAD . "pad/reference/pages/$reference";

  $php    = ( file_exists("$file.php" ) ) ? padColorsFile ("$file.php")  : '';
  $html   = ( file_exists("$file.html") ) ? padColorsFile ("$file.html") : '';

  $php_file  = ( file_exists("$file.php") )  ? str_replace(PAD . "pad/reference/pages/", '', "$file.php")  : '';
  $html_file = ( file_exists("$file.html") ) ? str_replace(PAD . "pad/reference/pages/", '', "$file.html") : '';

  $demo = ( strpos($html, '<font color="blue">demo</font>') !== FALSE );

  return TRUE;
   
?>