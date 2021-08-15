<?php
 
  if     ( pad_file_exists ( PAD_APP  . "functions/$name.php" ) ) return include PAD_HOME . 'pad/eval/app.php';
  elseif ( pad_file_exists ( PAD_HOME . "pad/functions/$name.php" ) ) return include PAD_HOME . 'pad/eval/pad.php';
  elseif ( function_exists ( $name                        ) )     return include PAD_HOME . 'pad/eval/php.php';
  else                                                            return '';

?>