<?php

  $result = padInclude ( $app, "examples/" . $padPrm [$pad] );

  $file = APP . "pages/examples/" . $padPrm [$pad];

  $php    = ( file_exists("$file.php" ) ) ? padColorsFile ("$file.php")  : '';
  $html   = ( file_exists("$file.html") ) ? padColorsFile ("$file.html") : '';

  $php_file  = ( file_exists("$file.php") )  ? str_replace(APP . "pages/examples/", '', "$file.php")  : '';
  $html_file = ( file_exists("$file.html") ) ? str_replace(APP . "pages/examples/", '', "$file.html") : '';

  return TRUE;
   
?>