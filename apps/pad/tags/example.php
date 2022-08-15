<?php

  $result = pInclude ( $app, "examples/" . $padPrm [$pad] );

  $file = APP . "pages/examples/" . $padPrm [$pad];

  $php    = ( file_exists("$file.php" ) ) ? pColors_file ("$file.php")  : '';
  $html   = ( file_exists("$file.html") ) ? pColors_file ("$file.html") : '';

  $php_file  = ( file_exists("$file.php") )  ? str_replace(APP . "pages/examples/", '', "$file.php")  : '';
  $html_file = ( file_exists("$file.html") ) ? str_replace(APP . "pages/examples/", '', "$file.html") : '';

  return TRUE;
   
?>