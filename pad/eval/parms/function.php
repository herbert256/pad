<?php
 
  if     ( pad_file_exists ( APP . "functions/$name.php" ) ) return include 'app.php';
  elseif ( pad_file_exists ( PAD . "functions/$name.php" ) ) return include 'pad.php';
  elseif ( function_exists ( $name                       ) ) return include 'php.php';
  else                                                       return '';

?>