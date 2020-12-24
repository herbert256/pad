<?php
 
  if     ( file_exists ( PAD_APP  . "functions/$name.php" ) ) return include PAD_HOME . 'eval/function_app.php';
  elseif ( file_exists ( PAD_HOME . "functions/$name.php" ) ) return include PAD_HOME . 'eval/function_pad.php';
  elseif ( function_exists ( $name                        ) ) return include PAD_HOME . 'eval/function_php.php';
  else                                                         pad_error ('Internal error');

?>