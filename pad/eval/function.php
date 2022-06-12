<?php
 
  if     ( pad_file_exists ( APP . "functions/$name.php" ) ) return include PAD . 'eval/app.php';
  elseif ( pad_file_exists ( PAD . "functions/$name.php" ) ) return include PAD . 'eval/pad.php';
  elseif ( function_exists ( $name                       ) ) return include PAD . 'eval/php.php';
  else                                                       return '';

?>