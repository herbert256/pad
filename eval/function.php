<?php
 
  if     ( file_exists ( PAD_APP  . "functions/$pad_tag.php" ) ) return include PAD_HOME . 'types/function_app.php';
  elseif ( file_exists ( PAD_HOME . "functions/$pad_tag.php" ) ) return include PAD_HOME . 'types/function_pad.php';
  elseif ( function_exists ( $pad_tag                        ) ) return include PAD_HOME . 'types/function_php.php';
  else                                                           pad_error ('Internal error');

?>