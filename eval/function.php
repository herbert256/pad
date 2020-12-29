<?php
 
  if     ( file_exists ( PAD_APP  . "functions/$name.php" ) ) return include PAD_HOME . 'eval/app.php';
  elseif ( file_exists ( PAD_HOME . "functions/$name.php" ) ) return include PAD_HOME . 'eval/pad.php';
  elseif ( function_exists ( $name                        ) ) return include PAD_HOME . 'eval/php.php';
  else                                                        return $name;

?>